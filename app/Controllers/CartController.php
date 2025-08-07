<?php

namespace App\Controllers;

use App\Core\Controller;

class CartController extends Controller
{
    public function __construct()
    {
        
    }

    public function index()
    {
        $this->requireLogin();

        $cartModel = $this->model('Cart');

        // Get cart items
        $cartItems = $cartModel->getCartItems($_SESSION['user_id']);
        
        // Get cart totals
        $cartTotals = $cartModel->getCartTotals($_SESSION['user_id']);

        // Get recommended products
        $recommendedProducts = [];
        if (!empty($cartItems)) {
            $recommendedProducts = $cartModel->getRecommendedProducts($_SESSION['user_id'], 3);
        }

        $data = [
            'title' => 'Shopping Cart - ' . SITENAME,
            'cart_items' => $cartItems,
            'cart_totals' => $cartTotals,
            'recommended_products' => $recommendedProducts
        ];

        $this->view('cart/index', $data);
    }

    public function add()
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('products');
        }

        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        if ($productId <= 0 || $quantity <= 0) {
            $this->setFlash('cart_error', 'Invalid product or quantity', 'alert-danger');
            $this->redirect('products');
        }

        $productModel = $this->model('Product');
        $cartModel = $this->model('Cart');

        // Check if product exists and has stock
        $product = $productModel->getProductById($productId);

        if (!$product) {
            $this->setFlash('cart_error', 'Product not found', 'alert-danger');
            $this->redirect('products');
        }

        if (!$productModel->hasStock($productId, $quantity)) {
            $this->setFlash('cart_error', 'Not enough stock available', 'alert-danger');
            $this->redirect('products/show/' . $productId);
        }

        // Add to cart
        if ($cartModel->addToCart($_SESSION['user_id'], $productId, $quantity)) {
            $this->setFlash('cart_success', 'Product added to cart successfully!');
        } else {
            $this->setFlash('cart_error', 'Failed to add product to cart', 'alert-danger');
        }

        $this->redirect('cart');
    }

    public function update()
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('cart');
        }

        $cartModel = $this->model('Cart');

        if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $cartItemId => $quantity) {
                $cartItemId = (int)$cartItemId;
                $quantity = (int)$quantity;
                
                $cartModel->updateCartItem($cartItemId, $quantity);
            }
            $this->setFlash('cart_success', 'Cart updated successfully!');
        }

        $this->redirect('cart');
    }

    public function remove($cartItemId)
    {
        $this->requireLogin();

        $cartModel = $this->model('Cart');

        if ($cartModel->removeCartItem($cartItemId)) {
            $this->setFlash('cart_success', 'Item removed from cart!');
        } else {
            $this->setFlash('cart_error', 'Failed to remove item', 'alert-danger');
        }

        $this->redirect('cart');
    }

    public function clear()
    {
        $this->requireLogin();

        $cartModel = $this->model('Cart');

        if ($cartModel->clearCart($_SESSION['user_id'])) {
            $this->setFlash('cart_success', 'Cart cleared successfully!');
        } else {
            $this->setFlash('cart_error', 'Failed to clear cart', 'alert-danger');
        }

        $this->redirect('cart');
    }

    public function count()
    {
        // Return JSON response for AJAX calls
        header('Content-Type: application/json');

        $count = 0;
        if ($this->isLoggedIn()) {
            $cartModel = $this->model('Cart');
            $count = $cartModel->getCartCount($_SESSION['user_id']);
        }

        echo json_encode(['count' => $count]);
        exit;
    }

    public function checkout()
    {
        $this->requireLogin();

        $cartModel = $this->model('Cart');
        $userModel = $this->model('User');

        // Get cart items
        $cartItems = $cartModel->getCartItems($_SESSION['user_id']);

        if (empty($cartItems)) {
            $this->setFlash('checkout_error', 'Your cart is empty', 'alert-danger');
            $this->redirect('cart');
        }

        // Validate cart stock
        $stockErrors = $cartModel->validateCartStock($_SESSION['user_id']);
        if (!empty($stockErrors)) {
            $this->setFlash('checkout_error', 'Some items in your cart are no longer available in the requested quantity', 'alert-danger');
            $this->redirect('cart');
        }

        // Get cart totals
        $cartTotals = $cartModel->getCartTotals($_SESSION['user_id']);

        // Get user details
        $user = $userModel->getUserById($_SESSION['user_id']);

        $data = [
            'title' => 'Checkout - ' . SITENAME,
            'cart_items' => $cartItems,
            'cart_totals' => $cartTotals,
            'user' => $user
        ];

        $this->view('cart/checkout', $data);
    }

    public function processCheckout()
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('cart/checkout');
        }

        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $shippingAddress = trim($_POST['shipping_address']);
        $phoneNumber = trim($_POST['phone_number']);

        if (empty($shippingAddress) || empty($phoneNumber)) {
            $this->setFlash('checkout_error', 'Please fill in all required fields', 'alert-danger');
            $this->redirect('cart/checkout');
        }

        $cartModel = $this->model('Cart');
        $orderModel = $this->model('Order');
        $productModel = $this->model('Product');

        // Get cart items and totals
        $cartItems = $cartModel->getCartItems($_SESSION['user_id']);
        $cartTotals = $cartModel->getCartTotals($_SESSION['user_id']);

        if (empty($cartItems)) {
            $this->setFlash('checkout_error', 'Your cart is empty', 'alert-danger');
            $this->redirect('cart');
        }

        // Validate stock again
        $stockErrors = $cartModel->validateCartStock($_SESSION['user_id']);
        if (!empty($stockErrors)) {
            $this->setFlash('checkout_error', 'Some items in your cart are no longer available', 'alert-danger');
            $this->redirect('cart');
        }

        // Begin transaction
        $orderModel->beginTransaction();

        try {
            // Create order
            $orderData = [
                'user_id' => $_SESSION['user_id'],
                'total_amount' => $cartTotals['total'],
                'shipping_address' => $shippingAddress,
                'phone_number' => $phoneNumber,
                'status' => 'pending'
            ];

            $orderId = $orderModel->createOrder($orderData);

            if (!$orderId) {
                throw new Exception('Failed to create order');
            }

            // Add order items and update stock
            foreach ($cartItems as $item) {
                // Add order item
                $orderModel->addOrderItem($orderId, $item->product_id, $item->quantity, $item->price);
                
                // Update product stock
                $productModel->updateStock($item->product_id, $item->quantity);
            }

            // Clear cart
            $cartModel->clearCart($_SESSION['user_id']);

            $orderModel->commit();

            $this->setFlash('order_success', 'Order placed successfully! Order #' . $orderId);
            $this->redirect('orders/confirmation/' . $orderId);

        } catch (Exception $e) {
            $orderModel->rollback();
            $this->setFlash('checkout_error', 'Failed to process order. Please try again.', 'alert-danger');
            $this->redirect('cart/checkout');
        }
    }
}