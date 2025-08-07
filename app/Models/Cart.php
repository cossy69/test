<?php

namespace App\Models;

use App\Core\Model;

class Cart extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Get user's cart
    public function getUserCart($userId)
    {
        $this->query('SELECT cart_id FROM carts WHERE user_id = :user_id');
        $this->bind(':user_id', $userId);
        return $this->single();
    }

    // Create cart for user
    public function createCart($userId)
    {
        $this->query('INSERT INTO carts (user_id) VALUES (:user_id)');
        $this->bind(':user_id', $userId);
        
        if ($this->execute()) {
            return $this->lastInsertId();
        }
        return false;
    }

    // Get cart items with product details
    public function getCartItems($userId)
    {
        $this->query('SELECT ci.*, p.name, p.price, p.image_url, p.stock_quantity 
                     FROM cart_items ci 
                     JOIN carts c ON ci.cart_id = c.cart_id 
                     JOIN products p ON ci.product_id = p.product_id 
                     WHERE c.user_id = :user_id 
                     ORDER BY ci.cart_item_id DESC');
        $this->bind(':user_id', $userId);
        return $this->resultSet();
    }

    // Add item to cart
    public function addToCart($userId, $productId, $quantity)
    {
        // First get or create cart
        $cart = $this->getUserCart($userId);
        if (!$cart) {
            $cartId = $this->createCart($userId);
        } else {
            $cartId = $cart->cart_id;
        }

        // Check if item already exists in cart
        $this->query('SELECT cart_item_id, quantity FROM cart_items 
                     WHERE cart_id = :cart_id AND product_id = :product_id');
        $this->bind(':cart_id', $cartId);
        $this->bind(':product_id', $productId);
        $existingItem = $this->single();

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem->quantity + $quantity;
            $this->query('UPDATE cart_items SET quantity = :quantity 
                         WHERE cart_item_id = :cart_item_id');
            $this->bind(':quantity', $newQuantity);
            $this->bind(':cart_item_id', $existingItem->cart_item_id);
        } else {
            // Add new item
            $this->query('INSERT INTO cart_items (cart_id, product_id, quantity) 
                         VALUES (:cart_id, :product_id, :quantity)');
            $this->bind(':cart_id', $cartId);
            $this->bind(':product_id', $productId);
            $this->bind(':quantity', $quantity);
        }

        return $this->execute();
    }

    // Update cart item quantity
    public function updateCartItem($cartItemId, $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeCartItem($cartItemId);
        }

        $this->query('UPDATE cart_items SET quantity = :quantity 
                     WHERE cart_item_id = :cart_item_id');
        $this->bind(':quantity', $quantity);
        $this->bind(':cart_item_id', $cartItemId);
        return $this->execute();
    }

    // Remove cart item
    public function removeCartItem($cartItemId)
    {
        $this->query('DELETE FROM cart_items WHERE cart_item_id = :cart_item_id');
        $this->bind(':cart_item_id', $cartItemId);
        return $this->execute();
    }

    // Clear user's cart
    public function clearCart($userId)
    {
        $this->query('DELETE ci FROM cart_items ci 
                     JOIN carts c ON ci.cart_id = c.cart_id 
                     WHERE c.user_id = :user_id');
        $this->bind(':user_id', $userId);
        return $this->execute();
    }

    // Get cart item count
    public function getCartCount($userId)
    {
        $this->query('SELECT SUM(ci.quantity) as total 
                     FROM carts c 
                     JOIN cart_items ci ON c.cart_id = ci.cart_id 
                     WHERE c.user_id = :user_id');
        $this->bind(':user_id', $userId);
        $result = $this->single();
        return $result ? (int)$result->total : 0;
    }

    // Get cart totals
    public function getCartTotals($userId)
    {
        $this->query('SELECT 
                        SUM(ci.quantity * p.price) as subtotal,
                        SUM(ci.quantity) as total_items
                     FROM cart_items ci 
                     JOIN carts c ON ci.cart_id = c.cart_id 
                     JOIN products p ON ci.product_id = p.product_id 
                     WHERE c.user_id = :user_id');
        $this->bind(':user_id', $userId);
        $result = $this->single();
        
        if ($result) {
            $subtotal = (float)$result->subtotal;
            $shipping = $subtotal >= 50 ? 0 : 9.99;
            $tax = $subtotal * 0.08;
            $total = $subtotal + $shipping + $tax;
            
            return [
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'tax' => $tax,
                'total' => $total,
                'total_items' => (int)$result->total_items
            ];
        }
        
        return [
            'subtotal' => 0,
            'shipping' => 0,
            'tax' => 0,
            'total' => 0,
            'total_items' => 0
        ];
    }

    // Validate cart items stock
    public function validateCartStock($userId)
    {
        $this->query('SELECT ci.cart_item_id, ci.quantity, p.stock_quantity, p.name 
                     FROM cart_items ci 
                     JOIN carts c ON ci.cart_id = c.cart_id 
                     JOIN products p ON ci.product_id = p.product_id 
                     WHERE c.user_id = :user_id AND ci.quantity > p.stock_quantity');
        $this->bind(':user_id', $userId);
        return $this->resultSet();
    }

    // Get recommended products based on cart
    public function getRecommendedProducts($userId, $limit = 3)
    {
        $this->query('SELECT DISTINCT p.* 
                     FROM products p 
                     JOIN products p2 ON p.category_id = p2.category_id 
                     JOIN cart_items ci ON p2.product_id = ci.product_id
                     JOIN carts c ON ci.cart_id = c.cart_id
                     WHERE c.user_id = :user_id 
                     AND p.product_id NOT IN (
                         SELECT ci2.product_id FROM cart_items ci2 
                         JOIN carts c2 ON ci2.cart_id = c2.cart_id 
                         WHERE c2.user_id = :user_id
                     )
                     ORDER BY RAND() 
                     LIMIT :limit');
        $this->bind(':user_id', $userId);
        $this->bind(':limit', $limit);
        return $this->resultSet();
    }
}