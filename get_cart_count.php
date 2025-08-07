<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

$count = 0;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT SUM(ci.quantity) as total FROM carts c JOIN cart_items ci ON c.cart_id = ci.cart_id WHERE c.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    $count = $result['total'] ? (int)$result['total'] : 0;
}

echo json_encode(['count' => $count]);
?>