<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Wishlist;
use App\Models\Product;

class WishlistController extends Controller
{
    private $wishlistModel;
    private $productModel;

    public function __construct()
    {
        // Check if user is logged in
        if (!isLoggedIn()) {
            redirect('auth/login');
        }

        $this->wishlistModel = $this->model('Wishlist');
        $this->productModel = $this->model('Product');
    }

    /**
     * Display user's wishlist
     */
    public function index()
    {
        $userId = $_SESSION['user_id'];
        $wishlistItems = $this->wishlistModel->getUserWishlist($userId);

        $data = [
            'title' => 'My Wishlist - ' . SITENAME,
            'wishlist_items' => $wishlistItems,
            'total_items' => count($wishlistItems)
        ];

        $this->view('wishlist/index', $data);
    }

    /**
     * Add item to wishlist
     */
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $productId = intval($_POST['product_id']);
            $userId = $_SESSION['user_id'];

            // Check if product exists
            $product = $this->productModel->getById($productId);
            if (!$product) {
                if ($this->isAjaxRequest()) {
                    echo json_encode(['success' => false, 'message' => 'Product not found']);
                    return;
                }
                flash('wishlist_error', 'Product not found', 'alert-danger');
                redirect('products');
            }

            // Check if already in wishlist
            if ($this->wishlistModel->isInWishlist($userId, $productId)) {
                if ($this->isAjaxRequest()) {
                    echo json_encode(['success' => false, 'message' => 'Product already in wishlist']);
                    return;
                }
                flash('wishlist_error', 'Product already in your wishlist', 'alert-warning');
                redirect('products/show/' . $productId);
            }

            // Add to wishlist
            if ($this->wishlistModel->addToWishlist($userId, $productId)) {
                if ($this->isAjaxRequest()) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Product added to wishlist',
                        'wishlist_count' => $this->wishlistModel->getWishlistCount($userId)
                    ]);
                    return;
                }
                flash('wishlist_success', 'Product added to your wishlist', 'alert-success');
            } else {
                if ($this->isAjaxRequest()) {
                    echo json_encode(['success' => false, 'message' => 'Error adding product to wishlist']);
                    return;
                }
                flash('wishlist_error', 'Error adding product to wishlist', 'alert-danger');
            }
        }

        redirect('products/show/' . ($productId ?? ''));
    }

    /**
     * Remove item from wishlist
     */
    public function remove($wishlistId = null)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $wishlistId = $wishlistId ?? intval($_POST['wishlist_id']);
            $userId = $_SESSION['user_id'];

            // Verify ownership
            if (!$this->wishlistModel->verifyOwnership($wishlistId, $userId)) {
                if ($this->isAjaxRequest()) {
                    echo json_encode(['success' => false, 'message' => 'Unauthorized action']);
                    return;
                }
                flash('wishlist_error', 'Unauthorized action', 'alert-danger');
                redirect('wishlist');
            }

            if ($this->wishlistModel->removeFromWishlist($wishlistId)) {
                if ($this->isAjaxRequest()) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Product removed from wishlist',
                        'wishlist_count' => $this->wishlistModel->getWishlistCount($userId)
                    ]);
                    return;
                }
                flash('wishlist_success', 'Product removed from your wishlist', 'alert-success');
            } else {
                if ($this->isAjaxRequest()) {
                    echo json_encode(['success' => false, 'message' => 'Error removing product from wishlist']);
                    return;
                }
                flash('wishlist_error', 'Error removing product from wishlist', 'alert-danger');
            }
        }

        redirect('wishlist');
    }

    /**
     * Move item from wishlist to cart
     */
    public function moveToCart($wishlistId)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_SESSION['user_id'];

            // Get wishlist item
            $wishlistItem = $this->wishlistModel->getWishlistItem($wishlistId);
            if (!$wishlistItem || $wishlistItem['user_id'] != $userId) {
                if ($this->isAjaxRequest()) {
                    echo json_encode(['success' => false, 'message' => 'Wishlist item not found']);
                    return;
                }
                flash('wishlist_error', 'Wishlist item not found', 'alert-danger');
                redirect('wishlist');
            }

            // Add to cart (you'll need to implement this in CartController)
            $cartModel = $this->model('Cart');
            if ($cartModel->addToCart($userId, $wishlistItem['product_id'], 1)) {
                // Remove from wishlist
                $this->wishlistModel->removeFromWishlist($wishlistId);
                
                if ($this->isAjaxRequest()) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Product moved to cart',
                        'wishlist_count' => $this->wishlistModel->getWishlistCount($userId)
                    ]);
                    return;
                }
                flash('wishlist_success', 'Product moved to cart', 'alert-success');
            } else {
                if ($this->isAjaxRequest()) {
                    echo json_encode(['success' => false, 'message' => 'Error moving product to cart']);
                    return;
                }
                flash('wishlist_error', 'Error moving product to cart', 'alert-danger');
            }
        }

        redirect('wishlist');
    }

    /**
     * Clear entire wishlist
     */
    public function clear()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_SESSION['user_id'];

            if ($this->wishlistModel->clearWishlist($userId)) {
                if ($this->isAjaxRequest()) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Wishlist cleared',
                        'wishlist_count' => 0
                    ]);
                    return;
                }
                flash('wishlist_success', 'Your wishlist has been cleared', 'alert-success');
            } else {
                if ($this->isAjaxRequest()) {
                    echo json_encode(['success' => false, 'message' => 'Error clearing wishlist']);
                    return;
                }
                flash('wishlist_error', 'Error clearing wishlist', 'alert-danger');
            }
        }

        redirect('wishlist');
    }

    /**
     * Get wishlist count for AJAX
     */
    public function count()
    {
        if (!isLoggedIn()) {
            echo json_encode(['count' => 0]);
            return;
        }

        $userId = $_SESSION['user_id'];
        $count = $this->wishlistModel->getWishlistCount($userId);
        
        echo json_encode(['count' => $count]);
    }

    /**
     * Share wishlist
     */
    public function share()
    {
        $userId = $_SESSION['user_id'];
        $shareToken = $this->wishlistModel->generateShareToken($userId);
        
        $data = [
            'title' => 'Share Wishlist - ' . SITENAME,
            'share_token' => $shareToken,
            'share_url' => URLROOT . '/wishlist/shared/' . $shareToken
        ];

        $this->view('wishlist/share', $data);
    }

    /**
     * View shared wishlist
     */
    public function shared($token)
    {
        $wishlistData = $this->wishlistModel->getSharedWishlist($token);
        
        if (!$wishlistData) {
            flash('wishlist_error', 'Shared wishlist not found or expired', 'alert-danger');
            redirect('products');
        }

        $data = [
            'title' => $wishlistData['username'] . "'s Wishlist - " . SITENAME,
            'wishlist_items' => $wishlistData['items'],
            'owner_name' => $wishlistData['username'],
            'is_shared' => true
        ];

        $this->view('wishlist/index', $data);
    }

    /**
     * Check if request is AJAX
     */
    private function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}