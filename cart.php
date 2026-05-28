<?php
session_start();

// Check if user is logged in
$logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$username = $logged_in ? $_SESSION['username'] : '';

// Redirect to login if not logged in
if (!$logged_in) {
    header("Location: login.php");
    exit;
}

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if cart contains items from different restaurants
if (!empty($_SESSION['cart'])) {
    $first_item = reset($_SESSION['cart']);
    $restaurant_name = $first_item['restaurant'];
    
    foreach ($_SESSION['cart'] as $item) {
        if ($item['restaurant'] !== $restaurant_name) {
            // Clear cart and show error
            $_SESSION['cart'] = [];
            $_SESSION['current_restaurant'] = '';
            header("Location: index.php?error=" . urlencode("Anda hanya bisa memesan dari satu warung sekaligus."));
            exit;
        }
    }
}

// Tampilkan pesan error jika ada
if (isset($_GET['error'])) {
    echo "<script>alert('" . htmlspecialchars($_GET['error']) . "');</script>";
}

// Handle remove item action
if (isset($_GET['remove']) && isset($_SESSION['cart'][$_GET['remove']])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    
    // If cart is empty, reset current restaurant
    if (empty($_SESSION['cart'])) {
        $_SESSION['current_restaurant'] = '';
    }
    
    header("Location: cart.php");
    exit;
}

// Handle update quantity action
if (isset($_POST['update'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        if ($qty > 0) {
            $_SESSION['cart'][$id]['quantity'] = $qty;
        } else {
            unset($_SESSION['cart'][$id]);
        }
    }
    
    // If cart is empty after update, reset current restaurant
    if (empty($_SESSION['cart'])) {
        $_SESSION['current_restaurant'] = '';
    }
}

// Calculate total items and price
$total_items = 0;
$total_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_items += $item['quantity'];
    $total_price += $item['price'] * $item['quantity'];
}

// Determine restaurant name from cart items
$restaurant_name = '';
if (!empty($_SESSION['cart'])) {
    $first_item = reset($_SESSION['cart']);
    $restaurant_name = $first_item['restaurant'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Food Court</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .cart-container {
            margin: 30px auto;
            max-width: 900px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .cart-restaurant {
            background-color: #f5f5f5;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            color: #4E54C8;
            display: flex;
            align-items: center;
        }
        
        .cart-restaurant i {
            margin-right: 10px;
        }
        
        .cart-items {
            margin-bottom: 30px;
        }
        
        .cart-item {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #f1f1f1;
            position: relative;
        }
        
        .cart-item-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            margin-right: 15px;
        }
        
        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .cart-item-info {
            flex: 1;
        }
        
        .cart-item-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .cart-item-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
        }
        
        .cart-item-price {
            font-weight: 600;
            color: #4E54C8;
        }
        
        .cart-item-quantity {
            width: 60px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }
        
        .cart-item-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        
        .cart-item-remove {
            color: #ff5252;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
        }
        
        .cart-item-remove:hover {
            text-decoration: underline;
        }
        
        .cart-summary {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
        }
        
        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .cart-summary-total {
            font-weight: 700;
            font-size: 1.2rem;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #eee;
        }
        
        .cart-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .cart-update-btn, .cart-checkout-btn {
            padding: 12px 24px;
            border-radius: 30px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .cart-update-btn {
            background-color: #f1f1f1;
            color: #333;
        }
        
        .cart-checkout-btn {
            background-color: #4E54C8;
            color: white;
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-cart-icon {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        .continue-shopping {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 30px;
            background-color: #4E54C8;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .cart-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .empty-cart-btn, .update-btn {
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .empty-cart-btn {
            background-color: #ff5252;
            color: white;
        }
        
        .update-btn {
            background-color: #4E54C8;
            color: white;
            border: none;
            cursor: pointer;
        }
        
        /* Media Queries */
        
        /* Mobile Devices (Phones) */
        @media screen and (max-width: 767px) {
            .cart-container {
                margin: 15px auto;
                padding: 15px;
                max-width: 95%;
            }
            
            .cart-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .cart-header h1 {
                margin-bottom: 10px;
                font-size: 1.5rem;
            }
            
            .cart-item {
                flex-direction: column;
            }
            
            .cart-item-image {
                width: 100%;
                height: 150px;
                margin-right: 0;
                margin-bottom: 10px;
            }
            
            .cart-item-header {
                flex-direction: column;
            }
            
            .cart-item-price {
                margin-top: 5px;
            }
            
            .cart-item-actions {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .cart-buttons {
                flex-direction: column;
                gap: 10px;
            }
            
            .cart-update-btn, .cart-checkout-btn {
                width: 100%;
                text-align: center;
            }
            
            .navbar-menu {
                gap: 10px;
            }
            
            .back-btn {
                margin-left: 10px;
            }
        }
        
        /* Tablet and Small Laptops */
        @media screen and (min-width: 768px) and (max-width: 1023px) {
            .cart-container {
                max-width: 90%;
                padding: 15px;
            }
            
            .cart-buttons {
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .cart-update-btn, .cart-checkout-btn {
                flex: 1;
                min-width: 200px;
                text-align: center;
            }
        }
        
        /* Desktop and Large Screens */
        @media screen and (min-width: 1024px) {
            .cart-container {
                max-width: 900px;
                padding: 25px;
            }
            
            .cart-item-image {
                width: 100px;
                height: 100px;
            }
            
            .cart-item-title {
                font-size: 1.2rem;
            }
            
            .cart-buttons {
                justify-content: flex-end;
                gap: 15px;
            }
            
            .cart-update-btn, .cart-checkout-btn {
                min-width: 180px;
            }
        }
        
        /* Extra Large Screens */
        @media screen and (min-width: 1440px) {
            .cart-container {
                max-width: 1100px;
            }
            
            .container {
                max-width: 1200px;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-logo">UDS FoodCourt</div>
        <div class="navbar-menu">
            <?php if($logged_in): ?>
                <span class="user-greeting">Halo, <?php echo htmlspecialchars($username); ?></span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
            <a href="cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count"><?php echo $total_items; ?></span>
            </a>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        
        <div class="cart-container">
            <div class="cart-header">
                <h1>Keranjang Belanja</h1>
                <span><?php echo $total_items; ?> items</span>
            </div>
            
            <?php if(empty($_SESSION['cart'])): ?>
                <div class="empty-cart">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h2>Keranjang Anda Kosong</h2>
                    <p>Tambahkan beberapa item menu dari warung favorit kami.</p>
                    <a href="index.php" class="continue-shopping">Mulai Belanja</a>
                </div>
            <?php else: ?>
                <?php if(!empty($restaurant_name)): ?>
                    <div class="cart-restaurant">
                        <i class="fas fa-store"></i>
                        Pesanan dari: <?php echo htmlspecialchars($restaurant_name); ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <div class="cart-items">
                        <?php foreach($_SESSION['cart'] as $id => $item): ?>
                            <div class="cart-item">
                                <div class="cart-item-image">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="cart-item-info">
                                    <div class="cart-item-header">
                                        <h3 class="cart-item-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                        <div class="cart-item-price">Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></div>
                                    </div>
                                    <div class="cart-item-actions">
                                        <div>
                                            <input type="number" name="qty[<?php echo $id; ?>]" class="cart-item-quantity" value="<?php echo $item['quantity']; ?>" min="1" max="10">
                                        </div>
                                        <a href="cart.php?remove=<?php echo $id; ?>" class="cart-item-remove">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-summary">
                        <div class="cart-summary-total cart-summary-row">
                            <span>Total</span>
                            <span>Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    
                    <div class="cart-buttons">
                        <a href="empty_cart.php" class="cart-update-btn">Kosongkan Keranjang</a>
                        <button type="submit" name="update" class="cart-update-btn">Perbarui Keranjang</button>
                        <button type="button" class="cart-checkout-btn" onclick="window.location.href='checkout.php'">Lanjut ke Pembayaran</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <p>&copy; 2025 UDS FoodCourt by Derik Maulana. Hak Cipta Dilindungi.</p>
    </footer>
</body>
</html>