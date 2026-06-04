<?php
session_start();

// Check if user is logged in
$logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$username = $logged_in ? $_SESSION['username'] : '';

// Get cart count
$cart_count = 0;
if ($logged_in && isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Court</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-logo">Pujasera UDS</div>
        <div class="navbar-menu">
            <?php if($logged_in): ?>
                <span class="user-greeting">Halo, <?php echo htmlspecialchars($username); ?></span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
            <a href="<?php echo $logged_in ? 'cart.php' : 'login.php'; ?>" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count"><?php echo $cart_count; ?></span>
            </a>
        </div>
    </nav>
    
    <div class="hero">
        <h1>Selamat Datang di Pujasera UDS</h1>
        <?php if($logged_in): ?>
            <p>Selamat berbelanja, <?php echo htmlspecialchars($username); ?>!</p>
        <?php else: ?>
            <p>Nikmati berbagai pilihan makanan dan minuman lezat dari warung-warung terbaik kami</p>
        <?php endif; ?>
    </div>
    
    <div class="container">
        <div class="how-to-order">
            <h2>Tata Cara Pemesanan</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3>Lihat Menu</h3>
                    <p>Jelajahi menu dari berbagai warung favorit tanpa perlu login terlebih dahulu</p>
                </div>
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3>Buat Akun dan Login</h3>
                    <p>Daftar dan masuk ke akun Anda untuk mulai memesan dan nikmati berbagai promo eksklusif</p>
                </div>
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h3>Pilih Menu dan Bayar</h3>
                    <p>Tambahkan menu ke keranjang dan bayar pesanan dengan mudah menggunakan QRIS</p>
                </div>
            </div>
        </div>
        
        <h2 class="section-title">Warung Tersedia</h2>
        <div class="restaurants">
            <div class="restaurant">
                <div class="restaurant-image">
                    <img src="img/warung_jawa.jpg" alt="Warung Jawa">
                </div>
                <div class="restaurant-info">
                    <h3>Warung Jawa</h3>
                    <p>Spesialis masakan Jawa dengan cita rasa otentik</p>
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <span style="color: #666;">(128 ulasan)</span>
                    </div>
                    <a href="warung-jawa.php" class="btn">Lihat Menu</a>
                </div>
            </div>
            
            <div class="restaurant">
                <div class="restaurant-image">
                    <img src="img/kedai_mama_zavan.jpg" alt="Kedai Mama Zavan">
                </div>
                <div class="restaurant-info">
                    <h3>Kedai Mama Zavan</h3>
                    <p>Menu khas Sunda dengan sentuhan modern</p>
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                        <span style="color: #666;">(96 ulasan)</span>
                    </div>
                    <a href="kedai-mama-zavan.php" class="btn">Lihat Menu</a>
                </div>
            </div>

            <div class="restaurant">
                <div class="restaurant-image">
                    <img src="img/gadogadodo.webp" alt="Warung Bu Endang">
                </div>
                <div class="restaurant-info">
                    <h3>Warung Bu Endang</h3>
                    <p>Menyediakan berbagai macam penyetan dan aneka jus</p>
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <span style="color: #666;">(150 ulasan)</span>
                    </div>
                    <a href="warung-bu-endang.php" class="btn">Lihat Menu</a>
                </div>
            </div>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 Derik. Hak Cipta Dilindungi.</p>
    </footer>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartCount = document.querySelector('.cart-count');
            
            // Cart icon functionality
            document.querySelector('.cart-icon').addEventListener('click', function(e) {
                <?php if(!$logged_in): ?>
                // If user is not logged in, redirect to login page
                window.location.href = 'login.php';
                e.preventDefault();
                <?php endif; ?>
            });
        });
    </script>
</body>
</html>