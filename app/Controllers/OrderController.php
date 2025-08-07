<?php

namespace App\Controllers;

use App\Core\Controller;

class OrderController extends Controller
{
    public function __construct()
    {
        
    }

    public function index()
    {
        $this->requireLogin();

        $orderModel = $this->model('Order');

        // Get user orders with pagination
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $orders = $orderModel->getUserOrders($_SESSION['user_id'], $perPage, $offset);
        $totalOrders = $orderModel->getOrderCount(['user_id' => $_SESSION['user_id']]);
        $totalPages = ceil($totalOrders / $perPage);

        $data = [
            'title' => 'My Orders - ' . SITENAME,
            'orders' => $orders,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_orders' => $totalOrders
            ]
        ];

        $this->view('orders/index', $data);
    }

    public function show($orderId)
    {
        $this->requireLogin();

        $orderModel = $this->model('Order');

        // Get order details
        $order = $orderModel->getOrderById($orderId, $_SESSION['user_id']);

        if (!$order) {
            $this->setFlash('order_error', 'Order not found', 'alert-danger');
            $this->redirect('orders');
        }

        // Get order items
        $orderItems = $orderModel->getOrderItems($orderId);

        $data = [
            'title' => 'Order #' . $orderId . ' - ' . SITENAME,
            'order' => $order,
            'order_items' => $orderItems
        ];

        $this->view('orders/show', $data);
    }

    public function confirmation($orderId)
    {
        $this->requireLogin();

        $orderModel = $this->model('Order');

        // Get order details
        $order = $orderModel->getOrderById($orderId, $_SESSION['user_id']);

        if (!$order) {
            $this->setFlash('order_error', 'Order not found', 'alert-danger');
            $this->redirect('orders');
        }

        // Get order items
        $orderItems = $orderModel->getOrderItems($orderId);

        $data = [
            'title' => 'Order Confirmation - ' . SITENAME,
            'order' => $order,
            'order_items' => $orderItems
        ];

        $this->view('orders/confirmation', $data);
    }

    public function cancel($orderId)
    {
        $this->requireLogin();

        // Return JSON response for AJAX
        header('Content-Type: application/json');

        if (!$this->isPost()) {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $orderModel = $this->model('Order');

        if ($orderModel->cancelOrder($orderId, $_SESSION['user_id'])) {
            echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Unable to cancel order']);
        }
        exit;
    }
}