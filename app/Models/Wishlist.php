<?php

namespace App\Models;

use App\Core\Model;

class Wishlist extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get user's wishlist with product details
     */
    public function getUserWishlist($userId)
    {
        $this->query("
            SELECT 
                w.wishlist_id,
                w.user_id,
                w.product_id,
                w.created_at,
                p.name,
                p.description,
                p.price,
                p.image_url,
                p.stock_quantity,
                c.name as category_name,
                COALESCE(AVG(r.rating), 0) as average_rating,
                COUNT(r.comment_id) as review_count
            FROM wishlist w
            JOIN products p ON w.product_id = p.product_id
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN comments r ON p.product_id = r.product_id
            WHERE w.user_id = :user_id
            GROUP BY w.wishlist_id, w.user_id, w.product_id, w.created_at, 
                     p.name, p.description, p.price, p.image_url, p.stock_quantity, c.name
            ORDER BY w.created_at DESC
        ");
        
        $this->bind(':user_id', $userId);
        return $this->resultSet();
    }

    /**
     * Add product to wishlist
     */
    public function addToWishlist($userId, $productId)
    {
        $this->query("INSERT INTO wishlist (user_id, product_id, created_at) VALUES (:user_id, :product_id, NOW())");
        $this->bind(':user_id', $userId);
        $this->bind(':product_id', $productId);
        
        return $this->execute();
    }

    /**
     * Remove product from wishlist
     */
    public function removeFromWishlist($wishlistId)
    {
        $this->query("DELETE FROM wishlist WHERE wishlist_id = :wishlist_id");
        $this->bind(':wishlist_id', $wishlistId);
        
        return $this->execute();
    }

    /**
     * Check if product is in user's wishlist
     */
    public function isInWishlist($userId, $productId)
    {
        $this->query("SELECT wishlist_id FROM wishlist WHERE user_id = :user_id AND product_id = :product_id");
        $this->bind(':user_id', $userId);
        $this->bind(':product_id', $productId);
        
        return $this->single() ? true : false;
    }

    /**
     * Get wishlist item by ID
     */
    public function getWishlistItem($wishlistId)
    {
        $this->query("SELECT * FROM wishlist WHERE wishlist_id = :wishlist_id");
        $this->bind(':wishlist_id', $wishlistId);
        
        return $this->single();
    }

    /**
     * Verify wishlist item ownership
     */
    public function verifyOwnership($wishlistId, $userId)
    {
        $this->query("SELECT wishlist_id FROM wishlist WHERE wishlist_id = :wishlist_id AND user_id = :user_id");
        $this->bind(':wishlist_id', $wishlistId);
        $this->bind(':user_id', $userId);
        
        return $this->single() ? true : false;
    }

    /**
     * Get wishlist count for user
     */
    public function getWishlistCount($userId)
    {
        $this->query("SELECT COUNT(*) as count FROM wishlist WHERE user_id = :user_id");
        $this->bind(':user_id', $userId);
        
        $result = $this->single();
        return $result ? $result->count : 0;
    }

    /**
     * Clear entire wishlist for user
     */
    public function clearWishlist($userId)
    {
        $this->query("DELETE FROM wishlist WHERE user_id = :user_id");
        $this->bind(':user_id', $userId);
        
        return $this->execute();
    }

    /**
     * Generate share token for wishlist
     */
    public function generateShareToken($userId)
    {
        // First, remove any existing share token for this user
        $this->query("DELETE FROM wishlist_shares WHERE user_id = :user_id");
        $this->bind(':user_id', $userId);
        $this->execute();

        // Generate new token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

        $this->query("INSERT INTO wishlist_shares (user_id, share_token, expires_at, created_at) VALUES (:user_id, :token, :expires_at, NOW())");
        $this->bind(':user_id', $userId);
        $this->bind(':token', $token);
        $this->bind(':expires_at', $expiresAt);
        
        if ($this->execute()) {
            return $token;
        }
        
        return false;
    }

    /**
     * Get shared wishlist by token
     */
    public function getSharedWishlist($token)
    {
        // First check if token is valid and not expired
        $this->query("
            SELECT ws.user_id, u.username 
            FROM wishlist_shares ws
            JOIN users u ON ws.user_id = u.user_id
            WHERE ws.share_token = :token AND ws.expires_at > NOW()
        ");
        $this->bind(':token', $token);
        $shareData = $this->single();
        
        if (!$shareData) {
            return false;
        }

        // Get wishlist items
        $items = $this->getUserWishlist($shareData->user_id);
        
        return [
            'username' => $shareData->username,
            'items' => $items
        ];
    }

    /**
     * Get popular wishlist items (most wishlisted products)
     */
    public function getPopularWishlistItems($limit = 10)
    {
        $this->query("
            SELECT 
                p.product_id,
                p.name,
                p.price,
                p.image_url,
                COUNT(w.wishlist_id) as wishlist_count
            FROM products p
            JOIN wishlist w ON p.product_id = w.product_id
            GROUP BY p.product_id, p.name, p.price, p.image_url
            ORDER BY wishlist_count DESC
            LIMIT :limit
        ");
        $this->bind(':limit', $limit);
        
        return $this->resultSet();
    }

    /**
     * Get wishlist statistics for admin
     */
    public function getWishlistStats()
    {
        $stats = [];

        // Total wishlist items
        $this->query("SELECT COUNT(*) as total FROM wishlist");
        $result = $this->single();
        $stats['total_items'] = $result ? $result->total : 0;

        // Users with wishlists
        $this->query("SELECT COUNT(DISTINCT user_id) as users FROM wishlist");
        $result = $this->single();
        $stats['users_with_wishlists'] = $result ? $result->users : 0;

        // Average wishlist size
        if ($stats['users_with_wishlists'] > 0) {
            $stats['average_wishlist_size'] = round($stats['total_items'] / $stats['users_with_wishlists'], 2);
        } else {
            $stats['average_wishlist_size'] = 0;
        }

        // Most wishlisted product
        $this->query("
            SELECT 
                p.name,
                COUNT(w.wishlist_id) as count
            FROM products p
            JOIN wishlist w ON p.product_id = w.product_id
            GROUP BY p.product_id, p.name
            ORDER BY count DESC
            LIMIT 1
        ");
        $result = $this->single();
        $stats['most_wishlisted'] = $result ? $result : null;

        return $stats;
    }

    /**
     * Get user's wishlist product IDs (for quick checking)
     */
    public function getUserWishlistProductIds($userId)
    {
        $this->query("SELECT product_id FROM wishlist WHERE user_id = :user_id");
        $this->bind(':user_id', $userId);
        
        $results = $this->resultSet();
        return array_column($results, 'product_id');
    }

    /**
     * Move multiple items from wishlist to cart
     */
    public function moveMultipleToCart($userId, $wishlistIds)
    {
        if (empty($wishlistIds)) {
            return false;
        }

        $placeholders = str_repeat('?,', count($wishlistIds) - 1) . '?';
        
        // Get wishlist items
        $this->query("
            SELECT w.wishlist_id, w.product_id 
            FROM wishlist w 
            WHERE w.user_id = ? AND w.wishlist_id IN ($placeholders)
        ");
        
        $params = array_merge([$userId], $wishlistIds);
        $this->stmt = $this->pdo->prepare($this->query);
        $this->stmt->execute($params);
        $items = $this->stmt->fetchAll();

        if (empty($items)) {
            return false;
        }

        // Add to cart (assuming Cart model has bulk insert method)
        $cartModel = new \App\Models\Cart();
        $success = true;

        foreach ($items as $item) {
            if (!$cartModel->addToCart($userId, $item['product_id'], 1)) {
                $success = false;
            }
        }

        if ($success) {
            // Remove from wishlist
            $this->query("
                DELETE FROM wishlist 
                WHERE user_id = ? AND wishlist_id IN ($placeholders)
            ");
            $this->stmt = $this->pdo->prepare($this->query);
            return $this->stmt->execute($params);
        }

        return false;
    }

    /**
     * Get recent wishlist activity
     */
    public function getRecentActivity($userId, $limit = 5)
    {
        $this->query("
            SELECT 
                w.created_at,
                p.name as product_name,
                p.image_url,
                'added' as action
            FROM wishlist w
            JOIN products p ON w.product_id = p.product_id
            WHERE w.user_id = :user_id
            ORDER BY w.created_at DESC
            LIMIT :limit
        ");
        $this->bind(':user_id', $userId);
        $this->bind(':limit', $limit);
        
        return $this->resultSet();
    }
}