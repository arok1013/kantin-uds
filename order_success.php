<?php
session_start();

// Set timezone to WIB
date_default_timezone_set('Asia/Jakarta');

// Check if there's a current order
if (!isset($_SESSION['current_order'])) {
    header("Location: index.php");
    exit;
}

// Get order data from session
$order = $_SESSION['current_order'];
$order_id = $order['id'];

// Format tanggal lengkap dengan bulan dan tahun
$current_datetime = date('d-m-Y H:i:s') . ' WIB';
$current_date = date('d-m-Y');
$current_time = date('H:i:s');

// Update order date to include full date and current time
if (isset($order['date'])) {
    // Ensure we have a complete date with month and year
    if (strpos($order['date'], '-') !== false) {
        // If date already has proper format, keep it but update the time
        $date_parts = explode(' ', $order['date']);
        $order_date = $date_parts[0] . ' ' . $current_time . ' WIB';
    } else {
        // If date doesn't have proper format, use today's complete date
        $order_date = $current_date . ' ' . $current_time . ' WIB';
    }
} else {
    // If no date exists, use current complete date and time
    $order_date = $current_datetime;
}

$table_number = $order['table'];
$restaurant = $order['restaurant'];
$order_items = $order['items'];
$total = $order['total'];
$customer_name = $order['customer_name'];

// Clear the current order from session
unset($_SESSION['current_order']);

// Prepare WhatsApp message
$whatsapp_message = "Halo, saya $customer_name telah melakukan pembayaran dengan detail berikut:\n\n";
$whatsapp_message .= "📋 *Nomor Pesanan*: $order_id\n";
$whatsapp_message .= "🪑 *Nomor Meja*: $table_number\n";
$whatsapp_message .= "📅 *Tanggal & Waktu Pesanan*: $order_date\n";
$whatsapp_message .= "🏪 *Warung*: $restaurant\n\n";
$whatsapp_message .= "🍽️ *Detail Pesanan*:\n";
foreach ($order_items as $item) {
    $whatsapp_message .= "- {$item['quantity']}x {$item['name']} (@Rp " . number_format($item['price'], 0, ',', '.') . ")\n";
}
$whatsapp_message .= "\n💵 *Total Pembayaran*: Rp " . number_format($total, 0, ',', '.') . "\n\n";
$whatsapp_message .= "Berikut bukti pembayarannya:";

// Determine WhatsApp number based on restaurant
$whatsapp_number = '';
if ($restaurant === 'Warung Jawa') {
    $whatsapp_number = '6281226696721'; // Warung Jawa
} elseif ($restaurant === 'Kedai Mama Zavan') {
    $whatsapp_number = '6285117315388'; // Kedai Mama Zavan
}
elseif ($restaurant === 'Warung Bu Endang') {
    $whatsapp_number = '6282264431605'; // Warung Bu Endang
}
$encoded_message = urlencode($whatsapp_message);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - Food Court</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Base styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .success-container {
            margin: 30px auto;
            max-width: 800px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
        }
        
        .success-icon {
            font-size: 4rem;
            color: #4CAF50;
            margin-bottom: 15px;
        }
        
        .success-title {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #4CAF50;
        }
        
        .order-details {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            text-align: left;
        }
        
        .order-detail {
            display: flex;
            margin-bottom: 10px;
        }
        
        .order-detail-label {
            font-weight: 600;
            width: 140px;
        }
        
        .order-items {
            margin: 20px 0;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .order-total {
            font-weight: 600;
            border-top: 1px solid #eee;
            padding-top: 10px;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
        }
        
        .whatsapp-section {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        
        .whatsapp-btn {
            display: inline-block;
            background-color: #25D366;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            margin: 10px 0;
            transition: all 0.3s ease;
        }
        
        .whatsapp-btn:hover {
            background-color: #128C7E;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .whatsapp-btn i {
            margin-right: 8px;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
            flex-wrap: wrap;
        }
        
        .home-btn {
            display: inline-block;
            background-color: #6c757d;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .home-btn:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .home-btn i {
            margin-right: 8px;
        }
        
        .continue-btn {
            display: inline-block;
            background-color: #4E54C8;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .continue-btn:hover {
            background-color: #3a41b5;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .screenshot-instruction {
            margin: 20px 0;
            padding: 15px;
            background: #fff8e1;
            border-left: 4px solid #ffc107;
        }
        
        .message-preview {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin: 15px 0;
            text-align: left;
            white-space: pre-line;
            font-family: Arial, sans-serif;
            font-size: 0.9rem;
        }
        
        .copy-btn {
            background-color: #4E54C8;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .copy-btn:hover {
            background-color: #3a41b5;
        }
        
        /* Mobile phones - Small screens (up to 576px) */
        @media only screen and (max-width: 576px) {
            .success-container {
                margin: 10px auto;
                padding: 15px;
                border-radius: 8px;
            }
            
            .success-icon {
                font-size: 3rem;
                margin-bottom: 10px;
            }
            
            .success-title {
                font-size: 1.4rem;
            }
            
            .order-detail {
                flex-direction: column;
                margin-bottom: 15px;
            }
            
            .order-detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
            
            .order-items h4 {
                font-size: 1rem;
            }
            
            .order-item {
                font-size: 0.9rem;
                flex-direction: column;
                border-bottom: 1px dashed #eee;
                padding-bottom: 8px;
                margin-bottom: 8px;
            }
            
            .order-total {
                font-size: 1.1rem;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 10px;
            }
            
            .home-btn, .continue-btn, .whatsapp-btn {
                width: 100%;
                padding: 10px;
                font-size: 0.9rem;
                text-align: center;
            }
            
            .whatsapp-section h3 {
                font-size: 1rem;
            }
            
            .screenshot-instruction h3 {
                font-size: 1rem;
            }
            
            .message-preview {
                font-size: 0.8rem;
                padding: 10px;
                max-height: 150px;
                overflow-y: auto;
            }
        }
        
        /* Tablets and small laptops (577px - 991px) */
        @media only screen and (min-width: 577px) and (max-width: 991px) {
            .success-container {
                margin: 20px auto;
                padding: 25px;
                max-width: 90%;
            }
            
            .success-icon {
                font-size: 3.5rem;
            }
            
            .success-title {
                font-size: 1.6rem;
            }
            
            .order-details {
                padding: 15px;
            }
            
            .order-detail-label {
                width: 130px;
            }
            
            .message-preview {
                font-size: 0.85rem;
                max-height: 180px;
                overflow-y: auto;
            }
            
            .action-buttons {
                flex-wrap: wrap;
            }
            
            .home-btn, .continue-btn {
                padding: 10px 20px;
            }
        }
        
        /* Laptops and desktops (992px and above) */
        @media only screen and (min-width: 992px) {
            .success-container {
                padding: 40px;
                margin: 50px auto;
            }
            
            .success-icon {
                font-size: 5rem;
                margin-bottom: 20px;
            }
            
            .success-title {
                font-size: 2.2rem;
            }
            
            .order-details {
                padding: 25px;
            }
            
            .order-detail-label {
                width: 150px;
            }
            
            .order-items {
                margin: 25px 0;
            }
            
            .message-preview {
                padding: 20px;
                font-size: 1rem;
                max-height: 250px;
                overflow-y: auto;
            }
            
            .action-buttons {
                margin-top: 30px;
            }
            
            .home-btn, .continue-btn, .whatsapp-btn {
                padding: 15px 30px;
                font-size: 1rem;
            }
            
            .whatsapp-section, .screenshot-instruction {
                padding: 25px;
                margin: 30px 0;
            }
        }
        
        /* Large desktop screens (1200px and above) */
        @media only screen and (min-width: 1200px) {
            .container {
                padding: 30px;
            }
            
            .success-container {
                max-width: 900px;
                padding: 50px;
            }
            
            .order-details {
                padding: 30px;
            }
            
            .whatsapp-section, .screenshot-instruction {
                padding: 30px;
            }
        }
        
        
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-logo">UDS FoodCourt</div>
        <div class="navbar-menu">
            <a href="logout.php">Logout</a>
            <a href="cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count">0</span>
            </a>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="success-title">Pesanan Berhasil Diproses!</h1>
            <p>Terima kasih telah memesan di UDS FoodCourt. Berikut detail pesanan Anda:</p>
            
            <div class="order-details">
                <div class="order-detail">
                    <div class="order-detail-label">Nomor Pesanan</div>
                    <div>: <?php echo htmlspecialchars($order_id); ?></div>
                </div>
                <div class="order-detail">
                    <div class="order-detail-label">Nomor Meja</div>
                    <div>: <?php echo htmlspecialchars($table_number); ?></div>
                </div>
                <div class="order-detail">
                    <div class="order-detail-label">Tanggal & Waktu</div>
                    <div>: <?php echo $order_date; ?></div>
                </div>
                <div class="order-detail">
                    <div class="order-detail-label">Warung</div>
                    <div>: <?php echo htmlspecialchars($restaurant); ?></div>
                </div>
                
                <div class="order-items">
                    <h4>Detail Pesanan:</h4>
                    <?php foreach ($order_items as $item): ?>
                        <div class="order-item">
                            <span><?php echo $item['quantity']; ?>x <?php echo htmlspecialchars($item['name']); ?></span>
                            <span>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></span>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="order-total">
                        <span>Total Pembayaran</span>
                        <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="screenshot-instruction">
                <h3><i class="fas fa-camera"></i> Screenshot Bukti Pembayaran</h3>
                <p>Silakan screenshot bukti pembayaran Anda untuk dikirim ke warung.</p>
            </div>
            
            <div class="whatsapp-section">
                <h3><i class="fab fa-whatsapp"></i> Konfirmasi ke Warung</h3>
                
                <div class="message-preview">
                    <?php echo nl2br(htmlspecialchars($whatsapp_message)); ?>
                </div>
                
                <?php if ($whatsapp_number): ?>
                    <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=<?php echo $encoded_message; ?>" 
                       class="whatsapp-btn" target="_blank">
                        <i class="fab fa-whatsapp"></i> Kirim ke <?php echo htmlspecialchars($restaurant); ?>
                    </a>
                    <p>Nomor WhatsApp: <?php echo substr($whatsapp_number, 0, 4) . '-' . substr($whatsapp_number, 4, 4) . '-' . substr($whatsapp_number, 8); ?></p>
                <?php else: ?>
                    <p>Silakan hubungi admin untuk konfirmasi pembayaran.</p>
                <?php endif; ?>
                
                <button onclick="copyToClipboard()" class="copy-btn">
                    <i class="fas fa-copy"></i> Salin Pesan
                </button>
            </div>
            
            <div class="action-buttons">
                <a href="index.php" class="home-btn">
                    <i class="fas fa-home"></i> Kembali ke Beranda
                </a>
                <a href="index.php" class="continue-btn">
                    <i class="fas fa-utensils"></i> Pesan Lagi
                </a>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <p>&copy; 2025 UDS FoodCourt by Derik Maulana. Hak Cipta Dilindungi.</p>
    </footer>

    <script>
        function copyToClipboard() {
            const message = `<?php echo str_replace(["\r", "\n"], ['', '\\n'], addslashes($whatsapp_message)); ?>`;
            navigator.clipboard.writeText(message).then(() => {
                alert('Pesan telah disalin ke clipboard!');
            }).catch(err => {
                console.error('Gagal menyalin pesan: ', err);
            });
        }
    </script>
</body>
</html>