<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Get all categories
    public function getCategories()
    {
        $this->query('SELECT * FROM categories ORDER BY name ASC');
        return $this->resultSet();
    }

    // Get categories with product counts
    public function getCategoriesWithCounts()
    {
        $this->query('SELECT c.*, COUNT(p.product_id) as product_count 
                     FROM categories c 
                     LEFT JOIN products p ON c.category_id = p.category_id 
                     GROUP BY c.category_id 
                     ORDER BY c.name ASC');
        return $this->resultSet();
    }

    // Get category by ID
    public function getCategoryById($id)
    {
        $this->query('SELECT * FROM categories WHERE category_id = :id');
        $this->bind(':id', $id);
        return $this->single();
    }

    // Add new category
    public function addCategory($data)
    {
        $this->query('INSERT INTO categories (name, description, image_url) 
                     VALUES (:name, :description, :image_url)');
        
        $this->bind(':name', $data['name']);
        $this->bind(':description', $data['description']);
        $this->bind(':image_url', $data['image_url']);
        
        if ($this->execute()) {
            return $this->lastInsertId();
        }
        return false;
    }

    // Update category
    public function updateCategory($data)
    {
        $this->query('UPDATE categories SET name = :name, description = :description, 
                     image_url = :image_url WHERE category_id = :category_id');
        
        $this->bind(':name', $data['name']);
        $this->bind(':description', $data['description']);
        $this->bind(':image_url', $data['image_url']);
        $this->bind(':category_id', $data['category_id']);
        
        return $this->execute();
    }

    // Delete category
    public function deleteCategory($id)
    {
        // First check if category has products
        $this->query('SELECT COUNT(*) as count FROM products WHERE category_id = :id');
        $this->bind(':id', $id);
        $result = $this->single();
        
        if ($result->count > 0) {
            return false; // Cannot delete category with products
        }
        
        $this->query('DELETE FROM categories WHERE category_id = :id');
        $this->bind(':id', $id);
        return $this->execute();
    }

    // Check if category name exists
    public function categoryExists($name, $excludeId = null)
    {
        if ($excludeId) {
            $this->query('SELECT category_id FROM categories WHERE name = :name AND category_id != :id');
            $this->bind(':id', $excludeId);
        } else {
            $this->query('SELECT category_id FROM categories WHERE name = :name');
        }
        
        $this->bind(':name', $name);
        return $this->rowCount() > 0;
    }

    // Get category statistics
    public function getCategoryStats($id)
    {
        $this->query('SELECT 
                        COUNT(p.product_id) as total_products,
                        AVG(p.price) as avg_price,
                        SUM(p.stock_quantity) as total_stock
                     FROM products p 
                     WHERE p.category_id = :id');
        $this->bind(':id', $id);
        return $this->single();
    }
}