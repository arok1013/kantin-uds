<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Return JSON response for AJAX calls
    if (isset($_POST['ajax'])) {
        echo json_encode(['success' => false, 'message' => 'Anda perlu login terlebih dahulu']);
        exit;
    }
    
    // Redirect for direct access
    header("Location: login.php");
    exit;
}

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Process add to cart request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $item_id = isset($_POST['id']) ? $_POST['id'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $price = isset($_POST['price']) ? (int)$_POST['price'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $restaurant = isset($_POST['restaurant']) ? $_POST['restaurant'] : '';
    $image = isset($_POST['image']) ? $_POST['image'] : '';
    
    // Validate required data
    if (empty($item_id) || empty($name) || $price <= 0 || empty($restaurant)) {
        if (isset($_POST['ajax'])) {
            echo json_encode(['success' => false, 'message' => 'Data item tidak lengkap']);
            exit;
        }
        
        header("Location: index.php");
        exit;
    }
    
    // Add or update item in cart
    if (isset($_SESSION['cart'][$item_id])) {
        // If item already in cart, update quantity
        $_SESSION['cart'][$item_id]['quantity'] += $quantity;
    } else {
        // Add new item to cart
        $_SESSION['cart'][$item_id] = [
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'restaurant' => $restaurant,
            'image' => $image
        ];
    }
    
    // Calculate total items in cart
    $total_items = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
    }
    
    // Return success response for AJAX calls
    if (isset($_POST['ajax'])) {
        echo json_encode([
            'success' => true, 
            'message' => "{$quantity}x {$name} telah ditambahkan ke keranjang",
            'total_items' => $total_items
        ]);
        exit;
    }
    
    // Redirect for direct form submission
    header("Location: cart.php");
    exit;
}

// Return cart count for AJAX calls
if (isset($_GET['action']) && $_GET['action'] === 'count') {
    $total_items = 0;
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total_items += $item['quantity'];
        }
    }
    
    echo json_encode(['count' => $total_items]);
    exit;
}

// If no valid action, redirect to index
header("Location: index.php");
exit;