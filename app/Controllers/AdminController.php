<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\Comment;

class AdminController extends Controller
{
    private $userModel;
    private $productModel;
    private $orderModel;
    private $categoryModel;
    private $commentModel;

    public function __construct()
    {
        // Check if user is admin
        if (!isLoggedIn() || !isAdmin()) {
            redirect('auth/login');
        }

        $this->userModel = $this->model('User');
        $this->productModel = $this->model('Product');
        $this->orderModel = $this->model('Order');
        $this->categoryModel = $this->model('Category');
        $this->commentModel = $this->model('Comment');
    }

    /**
     * Admin Dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Admin Dashboard - ' . SITENAME,
            'stats' => $this->getDashboardStats(),
            'recent_orders' => $this->orderModel->getRecentOrders(5),
            'low_stock_products' => $this->productModel->getLowStockProducts(5),
            'recent_users' => $this->userModel->getRecentUsers(5),
            'sales_data' => $this->getSalesData(),
            'revenue_data' => $this->getRevenueData(),
            'category_data' => $this->getCategoryData()
        ];

        $this->view('admin/dashboard', $data);
    }

    /**
     * Products Management
     */
    public function products($action = null, $id = null)
    {
        switch ($action) {
            case 'add':
                $this->addProduct();
                break;
            case 'edit':
                $this->editProduct($id);
                break;
            case 'delete':
                $this->deleteProduct($id);
                break;
            case 'toggle_status':
                $this->toggleProductStatus($id);
                break;
            default:
                $this->listProducts();
        }
    }

    /**
     * Orders Management
     */
    public function orders($action = null, $id = null)
    {
        switch ($action) {
            case 'view':
                $this->viewOrder($id);
                break;
            case 'update_status':
                $this->updateOrderStatus($id);
                break;
            case 'export':
                $this->exportOrders();
                break;
            default:
                $this->listOrders();
        }
    }

    /**
     * Users Management
     */
    public function users($action = null, $id = null)
    {
        switch ($action) {
            case 'view':
                $this->viewUser($id);
                break;
            case 'toggle_status':
                $this->toggleUserStatus($id);
                break;
            case 'delete':
                $this->deleteUser($id);
                break;
            default:
                $this->listUsers();
        }
    }

    /**
     * Categories Management
     */
    public function categories($action = null, $id = null)
    {
        switch ($action) {
            case 'add':
                $this->addCategory();
                break;
            case 'edit':
                $this->editCategory($id);
                break;
            case 'delete':
                $this->deleteCategory($id);
                break;
            default:
                $this->listCategories();
        }
    }

    /**
     * Reviews Management
     */
    public function reviews($action = null, $id = null)
    {
        switch ($action) {
            case 'approve':
                $this->approveReview($id);
                break;
            case 'delete':
                $this->deleteReview($id);
                break;
            default:
                $this->listReviews();
        }
    }

    /**
     * Analytics & Reports
     */
    public function analytics($type = 'overview')
    {
        $data = [
            'title' => 'Analytics - ' . SITENAME,
            'type' => $type
        ];

        switch ($type) {
            case 'sales':
                $data['sales_data'] = $this->getDetailedSalesData();
                break;
            case 'products':
                $data['product_analytics'] = $this->getProductAnalytics();
                break;
            case 'customers':
                $data['customer_analytics'] = $this->getCustomerAnalytics();
                break;
            default:
                $data['overview_data'] = $this->getAnalyticsOverview();
        }

        $this->view('admin/analytics', $data);
    }

    /**
     * Settings
     */
    public function settings($section = 'general')
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->updateSettings($section);
        }

        $data = [
            'title' => 'Settings - ' . SITENAME,
            'section' => $section,
            'settings' => $this->getSettings($section)
        ];

        $this->view('admin/settings', $data);
    }

    // ==========================================
    // PRIVATE METHODS
    // ==========================================

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        return [
            'total_products' => $this->productModel->getTotalCount(),
            'total_users' => $this->userModel->getTotalCount(),
            'total_orders' => $this->orderModel->getTotalCount(),
            'total_revenue' => $this->orderModel->getTotalRevenue(),
            'pending_orders' => $this->orderModel->getCountByStatus('pending'),
            'low_stock_count' => $this->productModel->getLowStockCount(),
            'new_users_today' => $this->userModel->getNewUsersToday(),
            'orders_today' => $this->orderModel->getOrdersToday()
        ];
    }

    /**
     * Get sales data for charts
     */
    private function getSalesData()
    {
        return $this->orderModel->getSalesDataByMonth(12);
    }

    /**
     * Get revenue data for charts
     */
    private function getRevenueData()
    {
        return $this->orderModel->getRevenueDataByWeek(4);
    }

    /**
     * Get category distribution data
     */
    private function getCategoryData()
    {
        return $this->categoryModel->getCategoryDistribution();
    }

    /**
     * List all products
     */
    private function listProducts()
    {
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $status = $_GET['status'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = 20;

        $filters = [
            'search' => $search,
            'category' => $category,
            'status' => $status
        ];

        $products = $this->productModel->getAdminProducts($filters, $page, $limit);
        $totalProducts = $this->productModel->getAdminProductsCount($filters);
        $categories = $this->categoryModel->getAll();

        $data = [
            'title' => 'Manage Products - ' . SITENAME,
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($totalProducts / $limit),
                'total_items' => $totalProducts
            ]
        ];

        $this->view('admin/products/index', $data);
    }

    /**
     * Add new product
     */
    private function addProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'price' => floatval($_POST['price']),
                'category_id' => intval($_POST['category_id']),
                'stock_quantity' => intval($_POST['stock_quantity']),
                'image_url' => $this->uploadProductImage()
            ];

            if ($this->productModel->create($data)) {
                flash('product_message', 'Product added successfully', 'alert-success');
                redirect('admin/products');
            } else {
                flash('product_message', 'Something went wrong', 'alert-danger');
            }
        }

        $data = [
            'title' => 'Add Product - ' . SITENAME,
            'categories' => $this->categoryModel->getAll()
        ];

        $this->view('admin/products/add', $data);
    }

    /**
     * Edit product
     */
    private function editProduct($id)
    {
        $product = $this->productModel->getById($id);
        if (!$product) {
            flash('product_message', 'Product not found', 'alert-danger');
            redirect('admin/products');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'price' => floatval($_POST['price']),
                'category_id' => intval($_POST['category_id']),
                'stock_quantity' => intval($_POST['stock_quantity'])
            ];

            // Handle image upload
            if (!empty($_FILES['image']['name'])) {
                $data['image_url'] = $this->uploadProductImage();
            }

            if ($this->productModel->update($id, $data)) {
                flash('product_message', 'Product updated successfully', 'alert-success');
                redirect('admin/products');
            } else {
                flash('product_message', 'Something went wrong', 'alert-danger');
            }
        }

        $data = [
            'title' => 'Edit Product - ' . SITENAME,
            'product' => $product,
            'categories' => $this->categoryModel->getAll()
        ];

        $this->view('admin/products/edit', $data);
    }

    /**
     * Delete product
     */
    private function deleteProduct($id)
    {
        if ($this->productModel->delete($id)) {
            flash('product_message', 'Product deleted successfully', 'alert-success');
        } else {
            flash('product_message', 'Error deleting product', 'alert-danger');
        }
        redirect('admin/products');
    }

    /**
     * List all orders
     */
    private function listOrders()
    {
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = 20;

        $filters = [
            'status' => $status,
            'search' => $search
        ];

        $orders = $this->orderModel->getAdminOrders($filters, $page, $limit);
        $totalOrders = $this->orderModel->getAdminOrdersCount($filters);

        $data = [
            'title' => 'Manage Orders - ' . SITENAME,
            'orders' => $orders,
            'filters' => $filters,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($totalOrders / $limit),
                'total_items' => $totalOrders
            ]
        ];

        $this->view('admin/orders/index', $data);
    }

    /**
     * View order details
     */
    private function viewOrder($id)
    {
        $order = $this->orderModel->getOrderWithDetails($id);
        if (!$order) {
            flash('order_message', 'Order not found', 'alert-danger');
            redirect('admin/orders');
        }

        $data = [
            'title' => 'Order #' . $id . ' - ' . SITENAME,
            'order' => $order
        ];

        $this->view('admin/orders/view', $data);
    }

    /**
     * Update order status
     */
    private function updateOrderStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $status = $_POST['status'];
            
            if ($this->orderModel->updateStatus($id, $status)) {
                flash('order_message', 'Order status updated successfully', 'alert-success');
            } else {
                flash('order_message', 'Error updating order status', 'alert-danger');
            }
        }
        
        redirect('admin/orders/view/' . $id);
    }

    /**
     * List all users
     */
    private function listUsers()
    {
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = 20;

        $filters = [
            'search' => $search,
            'role' => $role
        ];

        $users = $this->userModel->getAdminUsers($filters, $page, $limit);
        $totalUsers = $this->userModel->getAdminUsersCount($filters);

        $data = [
            'title' => 'Manage Users - ' . SITENAME,
            'users' => $users,
            'filters' => $filters,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($totalUsers / $limit),
                'total_items' => $totalUsers
            ]
        ];

        $this->view('admin/users/index', $data);
    }

    /**
     * Upload product image
     */
    private function uploadProductImage()
    {
        if (empty($_FILES['image']['name'])) {
            return null;
        }

        $uploadDir = APPROOT . '/../public/assets/images/products/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = time() . '_' . $_FILES['image']['name'];
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            return '/assets/images/products/' . $fileName;
        }

        return null;
    }

    /**
     * Get detailed analytics data
     */
    private function getAnalyticsOverview()
    {
        return [
            'sales_trend' => $this->orderModel->getSalesTrend(30),
            'top_products' => $this->productModel->getTopSellingProducts(10),
            'customer_growth' => $this->userModel->getCustomerGrowth(12),
            'revenue_by_category' => $this->orderModel->getRevenueByCategory()
        ];
    }

    /**
     * Get product analytics
     */
    private function getProductAnalytics()
    {
        return [
            'top_selling' => $this->productModel->getTopSellingProducts(20),
            'low_performing' => $this->productModel->getLowPerformingProducts(20),
            'stock_levels' => $this->productModel->getStockLevels(),
            'category_performance' => $this->productModel->getCategoryPerformance()
        ];
    }

    /**
     * Get customer analytics
     */
    private function getCustomerAnalytics()
    {
        return [
            'customer_segments' => $this->userModel->getCustomerSegments(),
            'top_customers' => $this->userModel->getTopCustomers(20),
            'customer_retention' => $this->userModel->getCustomerRetention(),
            'geographic_distribution' => $this->userModel->getGeographicDistribution()
        ];
    }
}