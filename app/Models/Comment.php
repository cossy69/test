<?php

namespace App\Models;

use App\Core\Model;

class Comment extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Get comments for a product
    public function getProductComments($productId)
    {
        $this->query('SELECT c.*, u.username 
                     FROM comments c 
                     JOIN users u ON c.user_id = u.user_id 
                     WHERE c.product_id = :product_id 
                     ORDER BY c.created_at DESC');
        $this->bind(':product_id', $productId);
        return $this->resultSet();
    }

    // Add new comment
    public function addComment($data)
    {
        $this->query('INSERT INTO comments (product_id, user_id, comment_text, rating) 
                     VALUES (:product_id, :user_id, :comment_text, :rating)');
        
        $this->bind(':product_id', $data['product_id']);
        $this->bind(':user_id', $data['user_id']);
        $this->bind(':comment_text', $data['comment_text']);
        $this->bind(':rating', $data['rating']);
        
        if ($this->execute()) {
            return $this->lastInsertId();
        }
        return false;
    }

    // Check if user already commented on product
    public function hasUserCommented($productId, $userId)
    {
        $this->query('SELECT comment_id FROM comments 
                     WHERE product_id = :product_id AND user_id = :user_id');
        $this->bind(':product_id', $productId);
        $this->bind(':user_id', $userId);
        return $this->rowCount() > 0;
    }

    // Get average rating for product
    public function getProductRating($productId)
    {
        $this->query('SELECT 
                        AVG(rating) as avg_rating,
                        COUNT(rating) as total_ratings
                     FROM comments 
                     WHERE product_id = :product_id AND rating IS NOT NULL');
        $this->bind(':product_id', $productId);
        return $this->single();
    }

    // Get user's comments
    public function getUserComments($userId, $limit = null)
    {
        $sql = 'SELECT c.*, p.name as product_name, p.image_url as product_image
                FROM comments c 
                JOIN products p ON c.product_id = p.product_id
                WHERE c.user_id = :user_id 
                ORDER BY c.created_at DESC';
        
        if ($limit) {
            $sql .= ' LIMIT :limit';
        }
        
        $this->query($sql);
        $this->bind(':user_id', $userId);
        
        if ($limit) {
            $this->bind(':limit', $limit);
        }
        
        return $this->resultSet();
    }

    // Update comment
    public function updateComment($commentId, $commentText, $rating = null)
    {
        if ($rating !== null) {
            $this->query('UPDATE comments SET comment_text = :comment_text, rating = :rating 
                         WHERE comment_id = :comment_id');
            $this->bind(':rating', $rating);
        } else {
            $this->query('UPDATE comments SET comment_text = :comment_text 
                         WHERE comment_id = :comment_id');
        }
        
        $this->bind(':comment_text', $commentText);
        $this->bind(':comment_id', $commentId);
        return $this->execute();
    }

    // Delete comment
    public function deleteComment($commentId, $userId = null)
    {
        if ($userId) {
            $this->query('DELETE FROM comments WHERE comment_id = :comment_id AND user_id = :user_id');
            $this->bind(':user_id', $userId);
        } else {
            $this->query('DELETE FROM comments WHERE comment_id = :comment_id');
        }
        
        $this->bind(':comment_id', $commentId);
        return $this->execute();
    }

    // Get comment by ID
    public function getCommentById($commentId)
    {
        $this->query('SELECT c.*, u.username, p.name as product_name 
                     FROM comments c 
                     JOIN users u ON c.user_id = u.user_id 
                     JOIN products p ON c.product_id = p.product_id 
                     WHERE c.comment_id = :comment_id');
        $this->bind(':comment_id', $commentId);
        return $this->single();
    }

    // Get recent comments (admin)
    public function getRecentComments($limit = 10)
    {
        $this->query('SELECT c.*, u.username, p.name as product_name 
                     FROM comments c 
                     JOIN users u ON c.user_id = u.user_id 
                     JOIN products p ON c.product_id = p.product_id 
                     ORDER BY c.created_at DESC 
                     LIMIT :limit');
        $this->bind(':limit', $limit);
        return $this->resultSet();
    }

    // Get all comments (admin)
    public function getAllComments($limit = 20, $offset = 0)
    {
        $this->query('SELECT c.*, u.username, p.name as product_name 
                     FROM comments c 
                     JOIN users u ON c.user_id = u.user_id 
                     JOIN products p ON c.product_id = p.product_id 
                     ORDER BY c.created_at DESC 
                     LIMIT :limit OFFSET :offset');
        $this->bind(':limit', $limit);
        $this->bind(':offset', $offset);
        return $this->resultSet();
    }

    // Get comment statistics
    public function getCommentStats()
    {
        $this->query('SELECT 
                        COUNT(*) as total_comments,
                        AVG(rating) as avg_rating,
                        COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star,
                        COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star,
                        COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star,
                        COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star,
                        COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star
                     FROM comments 
                     WHERE rating IS NOT NULL');
        return $this->single();
    }

    // Get top rated products
    public function getTopRatedProducts($limit = 5)
    {
        $this->query('SELECT p.product_id, p.name, AVG(c.rating) as avg_rating, COUNT(c.rating) as review_count
                     FROM products p 
                     JOIN comments c ON p.product_id = c.product_id 
                     WHERE c.rating IS NOT NULL 
                     GROUP BY p.product_id 
                     HAVING review_count >= 3 
                     ORDER BY avg_rating DESC, review_count DESC 
                     LIMIT :limit');
        $this->bind(':limit', $limit);
        return $this->resultSet();
    }

    // Get products needing reviews (no comments yet)
    public function getProductsNeedingReviews($limit = 10)
    {
        $this->query('SELECT p.* 
                     FROM products p 
                     LEFT JOIN comments c ON p.product_id = c.product_id 
                     WHERE c.comment_id IS NULL 
                     ORDER BY p.created_at DESC 
                     LIMIT :limit');
        $this->bind(':limit', $limit);
        return $this->resultSet();
    }
}