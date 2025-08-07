<?php
$page_title = "Search";
include 'includes/header.php';

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($search_query)) {
    header('Location: products.php');
    exit;
}

// Redirect to products page with search query
header('Location: products.php?q=' . urlencode($search_query));
exit;
?>