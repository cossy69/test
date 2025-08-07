<?php

namespace App\Models;

use App\Core\Model;

class Order extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Create new order
    public function createOrder($data)
    {
        $this->query('INSERT INTO orders (user_id, total_amount, shipping_address, phone_number, status) 
                     VALUES (:user_id, :total_amount, :shipping_address, :phone_number, :status)');
        
        $this->bind(':user_id', $data['user_id']);
        $this->bind(':total_amount', $data['total_amount']);
        $this->bind(':shipping_address', $data['shipping_address']);
        $this->bind(':phone_number', $data['phone_number']);
        $this->bind(':status', $data['status'] ?? 'pending');
        
        if ($this->execute()) {
            return $this->lastInsertId();
        }
        return false;
    }

    // Add order item
    public function addOrderItem($orderId, $productId, $quantity, $price)
    {
        $this->query('INSERT INTO order_items (order_id, product_id, quantity, price) 
                     VALUES (:order_id, :product_id, :quantity, :price)');
        
        $this->bind(':order_id', $orderId);
        $this->bind(':product_id', $productId);
        $this->bind(':quantity', $quantity);
        $this->bind(':price', $price);
        
        return $this->execute();
    }

    // Get user orders
    public function getUserOrders($userId, $limit = null, $offset = 0)
    {
        $sql = 'SELECT o.*, COUNT(oi.order_item_id) as item_count 
                FROM orders o 
                LEFT JOIN order_items oi ON o.order_id = oi.order_id 
                WHERE o.user_id = :user_id 
                GROUP BY o.order_id 
                ORDER BY o.created_at DESC';
        
        if ($limit) {
            $sql .= ' LIMIT :limit OFFSET :offset';
        }
        
        $this->query($sql);
        $this->bind(':user_id', $userId);
        
        if ($limit) {
            $this->bind(':limit', $limit);
            $this->bind(':offset', $offset);
        }
        
        return $this->resultSet();
    }

    // Get order by ID
    public function getOrderById($orderId, $userId = null)
    {
        if ($userId) {
            $this->query('SELECT * FROM orders WHERE order_id = :order_id AND user_id = :user_id');
            $this->bind(':user_id', $userId);
        } else {
            $this->query('SELECT * FROM orders WHERE order_id = :order_id');
        }
        
        $this->bind(':order_id', $orderId);
        return $this->single();
    }

    // Get order items
    public function getOrderItems($orderId)
    {
        $this->query('SELECT oi.*, p.name, p.image_url 
                     FROM order_items oi 
                     JOIN products p ON oi.product_id = p.product_id 
                     WHERE oi.order_id = :order_id');
        $this->bind(':order_id', $orderId);
        return $this->resultSet();
    }

    // Update order status
    public function updateOrderStatus($orderId, $status)
    {
        $this->query('UPDATE orders SET status = :status, updated_at = CURRENT_TIMESTAMP 
                     WHERE order_id = :order_id');
        $this->bind(':status', $status);
        $this->bind(':order_id', $orderId);
        return $this->execute();
    }

    // Cancel order
    public function cancelOrder($orderId, $userId)
    {
        // Check if order can be cancelled (must be pending)
        $this->query('SELECT status FROM orders WHERE order_id = :order_id AND user_id = :user_id');
        $this->bind(':order_id', $orderId);
        $this->bind(':user_id', $userId);
        $order = $this->single();
        
        if (!$order || $order->status !== 'pending') {
            return false;
        }
        
        // Begin transaction
        $this->beginTransaction();
        
        try {
            // Get order items to restore stock
            $this->query('SELECT product_id, quantity FROM order_items WHERE order_id = :order_id');
            $this->bind(':order_id', $orderId);
            $orderItems = $this->resultSet();
            
            // Restore stock for each item
            foreach ($orderItems as $item) {
                $this->query('UPDATE products SET stock_quantity = stock_quantity + :quantity 
                             WHERE product_id = :product_id');
                $this->bind(':quantity', $item->quantity);
                $this->bind(':product_id', $item->product_id);
                $this->execute();
            }
            
            // Update order status
            $this->query('UPDATE orders SET status = :status, updated_at = CURRENT_TIMESTAMP 
                         WHERE order_id = :order_id');
            $this->bind(':status', 'cancelled');
            $this->bind(':order_id', $orderId);
            $this->execute();
            
            $this->commit();
            return true;
            
        } catch (Exception $e) {
            $this->rollback();
            return false;
        }
    }

    // Get all orders (admin)
    public function getAllOrders($filters = [], $limit = 20, $offset = 0)
    {
        $sql = 'SELECT o.*, u.username, COUNT(oi.order_item_id) as item_count 
                FROM orders o 
                JOIN users u ON o.user_id = u.user_id 
                LEFT JOIN order_items oi ON o.order_id = oi.order_id 
                WHERE 1=1';
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= ' AND o.status = :status';
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['user_id'])) {
            $sql .= ' AND o.user_id = :user_id';
            $params[':user_id'] = $filters['user_id'];
        }
        
        $sql .= ' GROUP BY o.order_id ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset';
        
        $this->query($sql);
        
        foreach ($params as $key => $value) {
            $this->bind($key, $value);
        }
        
        $this->bind(':limit', $limit);
        $this->bind(':offset', $offset);
        
        return $this->resultSet();
    }

    // Get order statistics
    public function getOrderStats()
    {
        $this->query('SELECT 
                        COUNT(*) as total_orders,
                        COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_orders,
                        COUNT(CASE WHEN status = "processing" THEN 1 END) as processing_orders,
                        COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_orders,
                        COUNT(CASE WHEN status = "cancelled" THEN 1 END) as cancelled_orders,
                        COALESCE(SUM(CASE WHEN status = "completed" THEN total_amount END), 0) as total_revenue,
                        AVG(CASE WHEN status = "completed" THEN total_amount END) as avg_order_value
                     FROM orders');
        return $this->single();
    }

    // Get recent orders (admin dashboard)
    public function getRecentOrders($limit = 5)
    {
        $this->query('SELECT o.*, u.username 
                     FROM orders o 
                     JOIN users u ON o.user_id = u.user_id 
                     ORDER BY o.created_at DESC 
                     LIMIT :limit');
        $this->bind(':limit', $limit);
        return $this->resultSet();
    }

    // Get order count
    public function getOrderCount($filters = [])
    {
        $sql = 'SELECT COUNT(*) as count FROM orders WHERE 1=1';
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= ' AND status = :status';
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['user_id'])) {
            $sql .= ' AND user_id = :user_id';
            $params[':user_id'] = $filters['user_id'];
        }
        
        $this->query($sql);
        
        foreach ($params as $key => $value) {
            $this->bind($key, $value);
        }
        
        $result = $this->single();
        return $result->count;
    }

    // Get order with details (admin)
    public function getOrderWithDetails($orderId)
    {
        $this->query('SELECT o.*, u.username, u.email, u.full_name 
                     FROM orders o 
                     JOIN users u ON o.user_id = u.user_id 
                     WHERE o.order_id = :order_id');
        $this->bind(':order_id', $orderId);
        return $this->single();
    }
}