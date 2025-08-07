<?php

namespace App\Controllers;

use App\Core\Controller;

class ProductController extends Controller
{
    public function __construct()
    {
        
    }

    public function index()
    {
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');

        // Get filters from URL
        $filters = [
            'category' => isset($_GET['category']) ? (int)$_GET['category'] : 0,
            'search' => isset($_GET['q']) ? trim($_GET['q']) : '',
            'sort' => isset($_GET['sort']) ? $_GET['sort'] : 'name'
        ];

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        // Get products and total count
        $products = $productModel->getProducts($filters, $perPage, $offset);
        $totalProducts = $productModel->getProductCount($filters);
        $totalPages = ceil($totalProducts / $perPage);

        // Get categories for sidebar
        $categories = $categoryModel->getCategories();

        // Get current category name if filtering by category
        $currentCategory = null;
        if ($filters['category'] > 0) {
            $currentCategory = $categoryModel->getCategoryById($filters['category']);
        }

        $data = [
            'title' => 'Products - ' . SITENAME,
            'products' => $products,
            'categories' => $categories,
            'current_category' => $currentCategory,
            'filters' => $filters,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_products' => $totalProducts,
                'per_page' => $perPage
            ]
        ];

        $this->view('products/index', $data);
    }

    public function show($id)
    {
        $productModel = $this->model('Product');
        $commentModel = $this->model('Comment');

        // Get product
        $product = $productModel->getProductById($id);

        if (!$product) {
            $this->redirect('products');
        }

        // Update view count
        $productModel->updateViewCount($id);

        // Get comments and rating
        $comments = $commentModel->getProductComments($id);
        $rating = $commentModel->getProductRating($id);

        // Get related products
        $relatedProducts = $productModel->getRelatedProducts($product->category_id, $id, 4);

        // Check if user has already commented
        $hasCommented = false;
        if ($this->isLoggedIn()) {
            $hasCommented = $commentModel->hasUserCommented($id, $_SESSION['user_id']);
        }

        $data = [
            'title' => $product->name . ' - ' . SITENAME,
            'product' => $product,
            'comments' => $comments,
            'rating' => $rating,
            'related_products' => $relatedProducts,
            'has_commented' => $hasCommented
        ];

        $this->view('products/show', $data);
    }

    public function categories()
    {
        $categoryModel = $this->model('Category');

        // Get categories with product counts
        $categories = $categoryModel->getCategoriesWithCounts();

        $data = [
            'title' => 'Categories - ' . SITENAME,
            'categories' => $categories
        ];

        $this->view('products/categories', $data);
    }

    public function addComment($productId)
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('products/show/' . $productId);
        }

        $commentModel = $this->model('Comment');

        // Check if user has already commented
        if ($commentModel->hasUserCommented($productId, $_SESSION['user_id'])) {
            $this->setFlash('comment_error', 'You have already commented on this product', 'alert-danger');
            $this->redirect('products/show/' . $productId);
        }

        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $commentText = trim($_POST['comment_text']);
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

        // Validate
        if (empty($commentText)) {
            $this->setFlash('comment_error', 'Please enter a comment', 'alert-danger');
            $this->redirect('products/show/' . $productId);
        }

        if ($rating < 1 || $rating > 5) {
            $this->setFlash('comment_error', 'Please select a valid rating', 'alert-danger');
            $this->redirect('products/show/' . $productId);
        }

        // Add comment
        $commentData = [
            'product_id' => $productId,
            'user_id' => $_SESSION['user_id'],
            'comment_text' => $commentText,
            'rating' => $rating
        ];

        if ($commentModel->addComment($commentData)) {
            $this->setFlash('comment_success', 'Review added successfully!');
        } else {
            $this->setFlash('comment_error', 'Failed to add review', 'alert-danger');
        }

        $this->redirect('products/show/' . $productId);
    }

    public function search()
    {
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';

        if (empty($query)) {
            $this->redirect('products');
        }

        // Redirect to products with search query
        $this->redirect('products?q=' . urlencode($query));
    }
}