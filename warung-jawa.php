<?php
session_start();

// Cek apakah pengguna sudah login
$logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$username = $logged_in ? $_SESSION['username'] : '';

// Inisialisasi keranjang dan restoran saat ini jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    $_SESSION['current_restaurant'] = '';
}

// Menangani aksi tambah ke keranjang via AJAX
if ($logged_in && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];
    $item_quantity = $_POST['item_quantity'];
    $item_image = $_POST['item_image'];
    $restaurant = "Warung Jawa"; // Nama restoran ini

    // Cek apakah keranjang kosong atau berisi item dari restoran yang sama
    if (empty($_SESSION['cart'])) {
        $_SESSION['current_restaurant'] = $restaurant;
    } 
    elseif ($_SESSION['current_restaurant'] !== $restaurant) {
        // Jika berbeda, kirim pesan error
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => "Anda sudah memiliki pesanan dari ".$_SESSION['current_restaurant'].". Selesaikan atau kosongkan keranjang untuk memesan dari warung ini."
        ]);
        exit;
    }
    
    // Buat ID unik untuk item di keranjang untuk menghindari konflik
    $cart_item_id = 'jawa_' . $item_id;
    
    if (isset($_SESSION['cart'][$cart_item_id])) {
        // Jika sudah ada, tambahkan kuantitasnya
        $_SESSION['cart'][$cart_item_id]['quantity'] += $item_quantity;
    } else {
        // Jika belum ada, tambahkan sebagai item baru
        $_SESSION['cart'][$cart_item_id] = [
            'name' => $item_name,
            'price' => $item_price,
            'quantity' => $item_quantity,
            'image' => $item_image,
            'restaurant' => $restaurant
        ];
    }
    
    // Hitung total item setelah ditambahkan
    $total_items = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
    }
    
    // Kirim respons JSON ke client
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => "{$item_quantity}x {$item_name} telah ditambahkan!",
        'total_items' => $total_items
    ]);
    exit;
}

// Hitung total item untuk ditampilkan di navbar
$total_items = 0;
if ($logged_in && isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Warung Jawa - Pujasera UDS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Style untuk notifikasi toast (disalin dari warung-bu-endang.php) */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            display: none;
            opacity: 0;
            transition: opacity 0.5s, bottom 0.5s;
        }
        .toast.show {
            display: block;
            opacity: 1;
            bottom: 40px;
        }
        .toast.error {
            background-color: #dc3545; /* Warna untuk error */
        }
        .toast i {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div id="toast-container"></div>

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
            <a href="cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count"><?php echo $total_items; ?></span>
            </a>
        </div>
    </nav>
    
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        
        <div class="restaurant-header">
            <div class="restaurant-logo">
                <img src="img/warung_jawa.jpg" alt="Warung Jawa">
            </div>
            <div class="restaurant-details">
                <h1>Warung Jawa</h1>
                <p>Spesialis masakan Jawa dengan cita rasa otentik</p>
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                    <span style="color: rgba(255, 255, 255, 0.8);">(128 ulasan)</span>
                </div>
            </div>
        </div>
        
        <h2 class="section-title">Menu Kami</h2>
        <div class="menu-categories">
            <div class="menu-category active" data-category="all">Semua</div>
            <div class="menu-category" data-category="Makanan">Makanan</div>
        </div>
        
        <div class="menu-items">
            <div class="menu-item" data-category="Makanan">
                <div class="menu-item-image">
                    <img src="img/rujakcingur.jpg" alt="Rujak Cingur">
                </div>
                <div class="menu-item-info">
                    <h3>Rujak Cingur</h3>
                    <p>Potongan cingur, buah, dan sayuran dengan bumbu petis spesial khas Surabaya.</p>
                    <div class="menu-item-price">Rp 20.000</div>
                    <form class="add-to-cart-form">
                        <input type="hidden" name="item_id" value="1">
                        <input type="hidden" name="item_name" value="Rujak Cingur">
                        <input type="hidden" name="item_price" value="20000">
                        <input type="hidden" name="item_image" value="img/rujakcingur.jpg">
                        <div class="add-to-cart">
                            <div class="quantity">
                                <button type="button" class="quantity-btn decrease">-</button>
                                <span>1</span>
                                <input type="hidden" name="item_quantity" value="1" class="quantity-input">
                                <button type="button" class="quantity-btn increase">+</button>
                            </div>
                            <button type="submit" class="add-btn">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="menu-item" data-category="Makanan">
                <div class="menu-item-image">
                    <img src="img/lalapanayamgoreng.jpg" alt="Lalapan Ayam">
                </div>
                <div class="menu-item-info">
                    <h3>Lalapan Ayam Goreng</h3>
                    <p>Ayam goreng empuk disajikan lengkap dengan sambal terasi pedas dan lalapan segar.</p>
                    <div class="menu-item-price">Rp 25.000</div>
                    <form class="add-to-cart-form">
                        <input type="hidden" name="item_id" value="2">
                        <input type="hidden" name="item_name" value="Lalapan Ayam Goreng">
                        <input type="hidden" name="item_price" value="25000">
                        <input type="hidden" name="item_image" value="img/lalapanayamgoreng.jpg">
                        <div class="add-to-cart">
                            <div class="quantity">
                                <button type="button" class="quantity-btn decrease">-</button>
                                <span>1</span>
                                <input type="hidden" name="item_quantity" value="1" class="quantity-input">
                                <button type="button" class="quantity-btn increase">+</button>
                            </div>
                            <button type="submit" class="add-btn">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="menu-item" data-category="Makanan">
                <div class="menu-item-image">
                    <img src="img/penyetankomplit.webp" alt="Penyetan Komplit">
                </div>
                <div class="menu-item-info">
                    <h3>Penyetan Komplit</h3>
                    <p>Paket komplit dengan tempe, tahu, dan telur penyet di atas sambal bawang pedas.</p>
                    <div class="menu-item-price">Rp 18.000</div>
                    <form class="add-to-cart-form">
                        <input type="hidden" name="item_id" value="3">
                        <input type="hidden" name="item_name" value="Penyetan Komplit">
                        <input type="hidden" name="item_price" value="18000">
                        <input type="hidden" name="item_image" value="img/penyetankomplit.webp">
                        <div class="add-to-cart">
                            <div class="quantity">
                                <button type="button" class="quantity-btn decrease">-</button>
                                <span>1</span>
                                <input type="hidden" name="item_quantity" value="1" class="quantity-input">
                                <button type="button" class="quantity-btn increase">+</button>
                            </div>
                            <button type="submit" class="add-btn">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal" id="loginModal" style="display: none;">
        <div class="modal-content">
            <h3>Login Diperlukan</h3>
            <p>Anda perlu login terlebih dahulu untuk menambahkan item ke keranjang.</p>
            <div class="modal-buttons">
                <a href="login.php" class="btn">Login</a>
                <button class="btn btn-secondary" onclick="closeModal()">Batal</button>
            </div>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 Pujasera UDS by Derik. Hak Cipta Dilindungi.</p>
    </footer>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    // Status login dari PHP
    const isLoggedIn = <?php echo $logged_in ? 'true' : 'false'; ?>;
    const modal = document.getElementById('loginModal');
    
    // Fungsi untuk menampilkan notifikasi toast (disalin dari warung-bu-endang.php)
    function showToast(message, isError = false) {
        const toastContainer = document.getElementById('toast-container');
        const notification = document.createElement('div');
        notification.className = 'toast' + (isError ? ' error' : '');
        notification.innerHTML = `<i class="fas ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i> ${message}`;
        toastContainer.appendChild(notification);

        // Tampilkan toast
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Sembunyikan dan hapus toast setelah 3 detik
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                toastContainer.removeChild(notification);
            }, 500);
        }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const categories = document.querySelectorAll('.menu-category');
        const menuItems = document.querySelectorAll('.menu-item');
        const cartCount = document.querySelector('.cart-count');
        
        // Filter menu berdasarkan kategori
        categories.forEach(category => {
            category.addEventListener('click', function() {
                categories.forEach(cat => cat.classList.remove('active'));
                this.classList.add('active');
                
                const selectedCategory = this.getAttribute('data-category');
                
                menuItems.forEach(item => {
                    item.style.display = (selectedCategory === 'all' || item.getAttribute('data-category') === selectedCategory) ? 'block' : 'none';
                });
            });
        });
        
        // Menangani tombol kuantitas dan pengiriman form
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            const decreaseBtn = form.querySelector('.decrease');
            const increaseBtn = form.querySelector('.increase');
            const quantitySpan = form.querySelector('.quantity span');
            const quantityInput = form.querySelector('.quantity-input');
            
            decreaseBtn.addEventListener('click', function() {
                let qty = parseInt(quantityInput.value);
                if (qty > 1) {
                    qty--;
                    quantitySpan.textContent = qty;
                    quantityInput.value = qty;
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                let qty = parseInt(quantityInput.value);
                qty++;
                quantitySpan.textContent = qty;
                quantityInput.value = qty;
            });
            
            // Menangani pengiriman form dengan AJAX
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!isLoggedIn) {
                    modal.style.display = 'flex';
                    return;
                }
                
                const formData = new FormData(form);
                formData.append('action', 'add_to_cart');
                
                $.ajax({
                    url: 'warung-jawa.php', // URL AJAX menunjuk ke file ini
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            cartCount.textContent = response.total_items;
                            showToast(response.message);
                            // Reset kuantitas ke 1
                            quantitySpan.textContent = 1;
                            quantityInput.value = 1;
                        } else {
                            showToast(response.message, true); // Tampilkan pesan error
                        }
                    },
                    error: function() {
                        showToast('Terjadi kesalahan. Silakan coba lagi.', true);
                    }
                });
            });
        });
        
        // Fungsi untuk Modal
        window.closeModal = function() {
            modal.style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }
    });
</script>
</body>
</html>