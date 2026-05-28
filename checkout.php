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

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Determine restaurant name from cart items
$first_item = reset($_SESSION['cart']);
$restaurant_name = $first_item['restaurant'];

// Ensure all items are from the same restaurant
foreach ($_SESSION['cart'] as $item) {
    if ($item['restaurant'] !== $restaurant_name) {
        $_SESSION['cart'] = [];
        header("Location: index.php?error=" . urlencode("Terjadi kesalahan: Item dari warung berbeda terdeteksi."));
        exit;
    }
}

// Calculate total price
$total_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Initialize variables
$name = $username; // Default to logged in username
$table_number = '';
$error = '';

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    // Get form data
    $name = trim($_POST['name']);
    $table_number = trim($_POST['table_number']);
    
    // Validate inputs
    if (empty($name)) {
        $error = "Nama harus diisi";
    } elseif (empty($table_number)) {
        $error = "Nomor meja harus diisi";
    } else {
        // Generate order ID
        $order_id = 'ORD-' . uniqid();
        
        // Prepare order items for success page
        $order_items = [];
        foreach ($_SESSION['cart'] as $id => $item) {
            $order_items[] = [
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'image' => $item['image']
            ];
        }
        
        // Store order data temporarily in session
        $_SESSION['current_order'] = [
            'id' => $order_id,
            'date' => date('d F Y H:i'),
            'table' => $table_number,
            'restaurant' => $restaurant_name,
            'items' => $order_items,
            'total' => $total_price,
            'customer_name' => $name
        ];
        
        // Clear cart
        $_SESSION['cart'] = [];
        $_SESSION['current_restaurant'] = '';
        
        // Redirect to success page
        header("Location: order_success.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo htmlspecialchars($restaurant_name); ?></title>
      <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .checkout-container {
            margin: 30px auto;
            max-width: 900px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .checkout-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .checkout-restaurant {
            background-color: #f5f5f5;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            color: #4E54C8;
            display: flex;
            align-items: center;
        }
        
        .checkout-restaurant i {
            margin-right: 10px;
        }
        
        .checkout-steps {
            display: flex;
            margin-bottom: 30px;
        }
        
        .checkout-step {
            flex: 1;
            text-align: center;
            padding: 10px;
            position: relative;
        }
        
        .checkout-step.active {
            color: #4E54C8;
            font-weight: 500;
        }
        
        .checkout-step.completed {
            color: #4CAF50;
        }
        
        .checkout-step:not(:last-child):after {
            content: '';
            position: absolute;
            top: 50%;
            right: 0;
            width: 100%;
            height: 2px;
            background: #eee;
            z-index: 1;
        }
        
        .checkout-step-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            border-radius: 50%;
            background: #eee;
            margin-bottom: 5px;
            position: relative;
            z-index: 2;
        }
        
        .checkout-step.active .checkout-step-number {
            background: #4E54C8;
            color: white;
        }
        
        .checkout-step.completed .checkout-step-number {
            background: #4CAF50;
            color: white;
        }
        
        .checkout-content {
            display: flex;
            gap: 30px;
        }
        
        .checkout-summary {
            flex: 1;
        }
        
        .checkout-payment {
            flex: 1;
        }
        
        .checkout-items {
            margin-bottom: 20px;
        }
        
        .checkout-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .checkout-total {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #eee;
            font-weight: 700;
            font-size: 1.2rem;
        }
        
        .payment-methods {
            margin-bottom: 20px;
        }
        
        .payment-method {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .payment-method.selected {
            border-color: #4E54C8;
            background-color: #f5f7ff;
        }
        
        .payment-method input {
            margin-right: 10px;
        }
        
        .payment-method-details {
            display: flex;
            align-items: center;
        }
        
        .payment-method-icon {
            font-size: 1.5rem;
            margin-right: 10px;
            color: #4E54C8;
        }
        
        .qris-container {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            border: 1px dashed #ddd;
            border-radius: 8px;
        }
        
        /* Original QRIS code styles */
.qris-code {
    width: auto;
    height: auto;
    margin: 0 auto 15px;
    background-color: #f5f5f5;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #666;
    font-size: 0.9rem;
    padding: 10px;
    border-radius: 8px;
}

/* Make QRIS image responsive */
.qris-code img {
    max-width: 100%;
    height: auto;
    display: block;
}

/* Mobile Phone (Small screens) */
@media only screen and (max-width: 767px) {
    .qris-code {
        padding: 8px;
    }
    
    .qris-code img {
        max-width: 150px;
        max-height: 150px;
    }
    
    .qris-instruction {
        font-size: 0.8rem;
    }
}

/* Tablet & Small Laptop (Medium screens) */
@media only screen and (min-width: 768px) and (max-width: 1023px) {
    .qris-code img {
        max-width: 180px;
        max-height: 180px;
    }
    
    .qris-instruction {
        font-size: 0.85rem;
    }
}

/* Desktop & Large Laptop (Large screens) */
@media only screen and (min-width: 1024px) {
    .qris-code img {
        max-width: 200px;
        max-height: 200px;
    }
}

/* Extra Large Screens */
@media only screen and (min-width: 1440px) {
    .qris-code img {
        max-width: 220px;
        max-height: 220px;
    }
}
        .qris-instruction {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
        }
        
        .checkout-btn {
            width: 100%;
            padding: 15px;
            border-radius: 8px;
            border: none;
            background-color: #4E54C8;
            color: white;
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .checkout-btn:hover {
            background-color: #3a41b5;
        }
        
        .customer-info {
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
        }
        
        .error-message {
            color: #ff5252;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .restaurant-info {
            font-size: 0.8rem;
            color: #666;
            margin-top: 10px;
        }
        /* Base styles stay the same */
.checkout-container {
    margin: 30px auto;
    max-width: 900px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

/* Other existing styles remain unchanged */

/* Mobile Phone (Small screens) */
@media only screen and (max-width: 767px) {
    .checkout-container {
        margin: 15px auto;
        padding: 15px;
        max-width: 100%;
    }
    
    .checkout-content {
        flex-direction: column;
        gap: 20px;
    }
    
    .checkout-steps {
        display: flex;
        margin-bottom: 20px;
    }
    
    .checkout-step {
        padding: 5px;
        font-size: 0.85rem;
    }
    
    .checkout-step-number {
        width: 25px;
        height: 25px;
        line-height: 25px;
        font-size: 0.85rem;
    }
    
    .checkout-restaurant {
        font-size: 0.9rem;
        padding: 8px 10px;
    }
    
    .qris-code {
        width: 150px;
        height: 150px;
    }
    
    .checkout-btn {
        padding: 12px;
    }
    
    .checkout-total {
        font-size: 1.1rem;
    }
    
    .navbar {
        padding: 10px 15px;
    }
    
    .navbar-menu {
        flex-direction: column;
        align-items: flex-end;
    }
    
    .user-greeting {
        font-size: 0.85rem;
        margin-bottom: 5px;
    }
}

/* Tablet & Small Laptop (Medium screens) */
@media only screen and (min-width: 768px) and (max-width: 1023px) {
    .checkout-container {
        max-width: 90%;
        padding: 20px;
    }
    
    .checkout-content {
        gap: 20px;
    }
    
    .qris-code {
        width: 180px;
        height: 180px;
    }
}

/* Desktop & Large Laptop (Large screens) */
@media only screen and (min-width: 1024px) {
    .checkout-container {
        max-width: 900px;
        padding: 25px;
    }
    
    .checkout-content {
        gap: 40px;
    }
    
    /* Enhanced desktop experience */
    .checkout-container:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        transition: box-shadow 0.3s ease;
    }
    
    .payment-method:hover {
        border-color: #4E54C8;
        background-color: #f5f7ff;
        transition: all 0.2s ease;
    }
    
    .checkout-btn:hover {
        background-color: #3a41b5;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(78, 84, 200, 0.2);
        transition: all 0.3s ease;
    }
}

/* Extra Large Screens */
@media only screen and (min-width: 1440px) {
    .checkout-container {
        max-width: 1100px;
    }
    
    .checkout-content {
        gap: 60px;
    }
}

/* Print styles for receipts */
@media print {
    .navbar, .back-btn, .checkout-btn, footer {
        display: none;
    }
    
    .checkout-container {
        box-shadow: none;
        margin: 0;
        padding: 0;
    }
    
    .checkout-payment {
        display: none;
    }
    
    .checkout-summary {
        width: 100%;
    }
}
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-logo">UDS FoodCourt</div>
        <div class="navbar-menu">
            <span class="user-greeting">Halo, <?php echo htmlspecialchars($username); ?></span>
            <a href="logout.php">Logout</a>
            <a href="cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
            </a>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container">
        <a href="cart.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        
        <div class="checkout-container">
            <div class="checkout-header">
                <h1>Checkout</h1>
            </div>
            
            <div class="checkout-restaurant">
                <i class="fas fa-store"></i>
                Pesanan dari: <?php echo htmlspecialchars($restaurant_name); ?>
            </div>
            
            <div class="checkout-steps">
                <div class="checkout-step completed">
                    <div class="checkout-step-number"><i class="fas fa-check"></i></div>
                    <div>Keranjang</div>
                </div>
                <div class="checkout-step active">
                    <div class="checkout-step-number">2</div>
                    <div>Pembayaran</div>
                </div>
                <div class="checkout-step">
                    <div class="checkout-step-number">3</div>
                    <div>Selesai</div>
                </div>
            </div>
            
            <form method="post" action="">
                <div class="checkout-content">
                    <div class="checkout-summary">
                        <h2>Informasi Pelanggan</h2>
                        
                        <div class="customer-info">
                            <?php if($error): ?>
                                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="name">Nama Pemesan</label>
                                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($name); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="table_number">Nomor Meja</label>
                                <input type="text" id="table_number" name="table_number" required 
                                       value="<?php echo htmlspecialchars($table_number); ?>" 
                                       placeholder="Contoh: Meja 1 10">
                            </div>
                        </div>
                        
                        <h3>Pesanan Anda</h3>
                        <div class="checkout-items">
                            <?php foreach($_SESSION['cart'] as $id => $item): ?>
                                <div class="checkout-item">
                                    <span><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                                    <span>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="checkout-total">
                            <span>Total</span>
                            <span>Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    
                    <div class="checkout-payment">
                        <h2>Metode Pembayaran</h2>
                        
                        <div class="payment-methods">
                            <div class="payment-method selected" onclick="selectPaymentMethod('qris')">
                                <input type="radio" name="payment_method" value="qris" id="qris" checked>
                                <div class="payment-method-details">
                                    <div class="payment-method-icon">
                                        <i class="fas fa-qrcode"></i>
                                    </div>
                                    <div>
                                        <h4>QRIS</h4>
                                        <p>Bayar dengan QRIS melalui aplikasi e-wallet atau mobile banking</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="qris-container">
                            <div class="qris-code">
                                <?php if ($restaurant_name === "Warung Jawa"): ?>
                                    <img src="img/saweria1.png" alt="QRIS Warung Jawa">
                                <?php elseif ($restaurant_name === "Kedai Mama Zavan"): ?>
                                    <img src="img/saweria2.png" alt="QRIS Kedai Mama Zavan">
                                    <?php elseif ($restaurant_name === "Warung Bu Endang"): ?>
                                    <img src="img/saweria2.png" alt="QRIS Warung Bu Endang">
                                <?php endif; ?>
                                
                            </div>
                            <p class="qris-instruction">
                                Scan QR code di atas menggunakan aplikasi e-wallet atau mobile banking Anda untuk menyelesaikan pembayaran.
                            </p>
                            <p class="restaurant-info">Pembayaran untuk: <?php echo htmlspecialchars($restaurant_name); ?></p>
                        </div>
                        
                        <button type="submit" name="checkout" class="checkout-btn">
                            Konfirmasi Pembayaran
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <p>&copy; 2025 UDS FoodCourt by Derik Maulana. Hak Cipta Dilindungi.</p>
    </footer>
    
    <script>
        function selectPaymentMethod(method) {
            // Update UI for selected payment method
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            
            // Update radio button
            document.getElementById(method).checked = true;
        }
    </script>
</body>
</html>