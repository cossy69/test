<?php

namespace App\Models;

use App\Core\Model;

class Product extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Get all products with pagination and filters
    public function getProducts($filters = [], $limit = 12, $offset = 0)
    {
        $sql = "SELECT p.*, c.name as category_name FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id WHERE 1=1";
        
        $params = [];
        
        // Apply filters
        if (!empty($filters['category'])) {
            $sql .= " AND p.category_id = :category";
            $params[':category'] = $filters['category'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Apply sorting
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_low':
                    $sql .= " ORDER BY p.price ASC";
                    break;
                case 'price_high':
                    $sql .= " ORDER BY p.price DESC";
                    break;
                case 'newest':
                    $sql .= " ORDER BY p.created_at DESC";
                    break;
                case 'popular':
                    $sql .= " ORDER BY p.view_count DESC";
                    break;
                default:
                    $sql .= " ORDER BY p.name ASC";
            }
        } else {
            $sql .= " ORDER BY p.name ASC";
        }
        
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $this->query($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $this->bind($key, $value);
        }
        
        $this->bind(':limit', $limit);
        $this->bind(':offset', $offset);
        
        return $this->resultSet();
    }

    // Get product count with filters
    public function getProductCount($filters = [])
    {
        $sql = "SELECT COUNT(*) as count FROM products p WHERE 1=1";
        $params = [];
        
        if (!empty($filters['category'])) {
            $sql .= " AND p.category_id = :category";
            $params[':category'] = $filters['category'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $this->query($sql);
        
        foreach ($params as $key => $value) {
            $this->bind($key, $value);
        }
        
        $result = $this->single();
        return $result->count;
    }

    // Get product by ID
    public function getProductById($id)
    {
        $this->query('SELECT p.*, c.name as category_name FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.category_id 
                     WHERE p.product_id = :id');
        $this->bind(':id', $id);
        return $this->single();
    }

    // Get featured products
    public function getFeaturedProducts($limit = 8)
    {
        $this->query('SELECT p.*, c.name as category_name FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.category_id 
                     ORDER BY p.view_count DESC LIMIT :limit');
        $this->bind(':limit', $limit);
        return $this->resultSet();
    }

    // Get related products
    public function getRelatedProducts($categoryId, $excludeId, $limit = 4)
    {
        $this->query('SELECT * FROM products WHERE category_id = :category_id 
                     AND product_id != :exclude_id ORDER BY RAND() LIMIT :limit');
        $this->bind(':category_id', $categoryId);
        $this->bind(':exclude_id', $excludeId);
        $this->bind(':limit', $limit);
        return $this->resultSet();
    }

    // Update product view count
    public function updateViewCount($id)
    {
        $this->query('UPDATE products SET view_count = view_count + 1 WHERE product_id = :id');
        $this->bind(':id', $id);
        return $this->execute();
    }

    // Add new product
    public function addProduct($data)
    {
        $this->query('INSERT INTO products (name, description, price, stock_quantity, category_id, image_url) 
                     VALUES (:name, :description, :price, :stock_quantity, :category_id, :image_url)');
        
        $this->bind(':name', $data['name']);
        $this->bind(':description', $data['description']);
        $this->bind(':price', $data['price']);
        $this->bind(':stock_quantity', $data['stock_quantity']);
        $this->bind(':category_id', $data['category_id']);
        $this->bind(':image_url', $data['image_url']);
        
        if ($this->execute()) {
            return $this->lastInsertId();
        }
        return false;
    }

    // Update product
    public function updateProduct($data)
    {
        $this->query('UPDATE products SET name = :name, description = :description, 
                     price = :price, stock_quantity = :stock_quantity, 
                     category_id = :category_id, image_url = :image_url 
                     WHERE product_id = :product_id');
        
        $this->bind(':name', $data['name']);
        $this->bind(':description', $data['description']);
        $this->bind(':price', $data['price']);
        $this->bind(':stock_quantity', $data['stock_quantity']);
        $this->bind(':category_id', $data['category_id']);
        $this->bind(':image_url', $data['image_url']);
        $this->bind(':product_id', $data['product_id']);
        
        return $this->execute();
    }

    // Delete product
    public function deleteProduct($id)
    {
        $this->query('DELETE FROM products WHERE product_id = :id');
        $this->bind(':id', $id);
        return $this->execute();
    }

    // Update stock quantity
    public function updateStock($productId, $quantity)
    {
        $this->query('UPDATE products SET stock_quantity = stock_quantity - :quantity 
                     WHERE product_id = :product_id');
        $this->bind(':quantity', $quantity);
        $this->bind(':product_id', $productId);
        return $this->execute();
    }

    // Restore stock quantity
    public function restoreStock($productId, $quantity)
    {
        $this->query('UPDATE products SET stock_quantity = stock_quantity + :quantity 
                     WHERE product_id = :product_id');
        $this->bind(':quantity', $quantity);
        $this->bind(':product_id', $productId);
        return $this->execute();
    }

    // Get low stock products
    public function getLowStockProducts($threshold = 10)
    {
        $this->query('SELECT * FROM products WHERE stock_quantity < :threshold 
                     ORDER BY stock_quantity ASC');
        $this->bind(':threshold', $threshold);
        return $this->resultSet();
    }

    // Check if product has enough stock
    public function hasStock($productId, $requestedQuantity)
    {
        $this->query('SELECT stock_quantity FROM products WHERE product_id = :product_id');
        $this->bind(':product_id', $productId);
        $product = $this->single();
        
        return $product && $product->stock_quantity >= $requestedQuantity;
    }

    // Get product stock
    public function getStock($productId)
    {
        $this->query('SELECT stock_quantity FROM products WHERE product_id = :product_id');
        $this->bind(':product_id', $productId);
        $product = $this->single();
        
        return $product ? $product->stock_quantity : 0;
    }
}