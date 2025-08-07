<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($product_id <= 0 || $quantity <= 0) {
        $_SESSION['cart_error'] = 'Invalid product or quantity.';
        header('Location: products.php');
        exit;
    }
    
    // Check if product exists and has stock
    $stmt = $pdo->prepare("SELECT product_id, name, price, stock_quantity FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        $_SESSION['cart_error'] = 'Product not found.';
        header('Location: products.php');
        exit;
    }
    
    if ($product['stock_quantity'] < $quantity) {
        $_SESSION['cart_error'] = 'Not enough stock available.';
        header('Location: product.php?id=' . $product_id);
        exit;
    }
    
    // Get user's cart
    $stmt = $pdo->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart = $stmt->fetch();
    
    if (!$cart) {
        // Create cart if doesn't exist
        $stmt = $pdo->prepare("INSERT INTO carts (user_id) VALUES (?)");
        $stmt->execute([$_SESSION['user_id']]);
        $cart_id = $pdo->lastInsertId();
    } else {
        $cart_id = $cart['cart_id'];
    }
    
    // Check if product already in cart
    $stmt = $pdo->prepare("SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
    $stmt->execute([$cart_id, $product_id]);
    $existing_item = $stmt->fetch();
    
    try {
        if ($existing_item) {
            // Update quantity
            $new_quantity = $existing_item['quantity'] + $quantity;
            
            if ($new_quantity > $product['stock_quantity']) {
                $_SESSION['cart_error'] = 'Cannot add more items. Stock limit reached.';
                header('Location: product.php?id=' . $product_id);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
            $stmt->execute([$new_quantity, $existing_item['cart_item_id']]);
        } else {
            // Add new item
            $stmt = $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$cart_id, $product_id, $quantity]);
        }
        
        $_SESSION['cart_success'] = 'Product added to cart successfully!';
        header('Location: cart.php');
        exit;
        
    } catch (PDOException $e) {
        $_SESSION['cart_error'] = 'Failed to add product to cart.';
        header('Location: product.php?id=' . $product_id);
        exit;
    }
} else {
    header('Location: products.php');
    exit;
}
?>