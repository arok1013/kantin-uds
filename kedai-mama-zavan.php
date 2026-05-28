<?php
session_start();

// Cek apakah pengguna sudah login
$logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$username = $logged_in ? $_SESSION['username'] : '';

// Inisialisasi keranjang jika belum ada
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
    $restaurant = "Kedai Mama Zavan"; // Nama restoran diubah
    
    // Cek jika keranjang kosong atau dari restoran yang sama
    if (empty($_SESSION['cart'])) {
        $_SESSION['current_restaurant'] = $restaurant;
    } 
    elseif ($_SESSION['current_restaurant'] !== $restaurant) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => "Anda sudah punya pesanan dari ".$_SESSION['current_restaurant'].". Selesaikan atau kosongkan keranjang dulu."
        ]);
        exit;
    }
    
    // Prefix ID item diubah agar unik
    $cart_item_id = 'zavan_' . $item_id;
    
    if (isset($_SESSION['cart'][$cart_item_id])) {
        $_SESSION['cart'][$cart_item_id]['quantity'] += $item_quantity;
    } else {
        $_SESSION['cart'][$cart_item_id] = [
            'name' => $item_name,
            'price' => $item_price,
            'quantity' => $item_quantity,
            'image' => $item_image,
            'restaurant' => $restaurant
        ];
    }
    
    // Hitung total item
    $total_items = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
    }
    
    // Kirim respons JSON
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
    <title>Menu Kedai Mama Zavan - Pujasera UDS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* (Style untuk Toast dan Modal sama seperti sebelumnya) */
        .toast {
            position: fixed; top: 20px; right: 20px; background-color: #4CAF50; color: white; padding: 15px 25px;
            border-radius: 30px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); z-index: 9999; display: none;
            animation: slideIn 0.5s, fadeOut 0.5s 2.5s;
        }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
    </style>
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
                <img src="img/kedai_mama_zavan.jpg" alt="Kedai Mama Zavan">
            </div>
            <div class="restaurant-details">
                <h1>Kedai Mama Zavan</h1>
                <p>Aneka jajanan kekinian dan camilan favorit semua</p>
                <div class="rating">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
                    <span style="color: rgba(255, 255, 255, 0.8);">(96 ulasan)</span>
                </div>
            </div>
        </div>
        
        <h2 class="section-title">Menu Kami</h2>
        <div class="menu-categories">
            <div class="menu-category active" data-category="all">Semua</div>
            <div class="menu-category" data-category="makanan-berat">Makanan Berat</div>
            <div class="menu-category" data-category="camilan">Camilan</div>
        </div>
        
        <div class="menu-items">
            <div class="menu-item" data-category="makanan-berat">
                <div class="menu-item-image"><img src="img/seblak.jpg" alt="Seblak"></div>
                <div class="menu-item-info">
                    <h3>Seblak</h3>
                    <p>Seblak komplit dengan kerupuk, bakso, sosis, dan ceker, level pedas bisa diatur.</p>
                    <div class="menu-item-price">Rp 15.000</div>
                    <form class="add-to-cart-form">
                        <input type="hidden" name="item_id" value="1"><input type="hidden" name="item_name" value="Seblak">
                        <input type="hidden" name="item_price" value="15000"><input type="hidden" name="item_image" value="img/seblak.jpg">
                        <div class="add-to-cart">
                            <div class="quantity"><button type="button" class="quantity-btn decrease">-</button><span>1</span><input type="hidden" name="item_quantity" value="1" class="quantity-input"><button type="button" class="quantity-btn increase">+</button></div>
                            <button type="submit" class="add-btn">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="menu-item" data-category="makanan-berat">
                <div class="menu-item-image"><img src="img/basoaci.webp" alt="Baso Aci"></div>
                <div class="menu-item-info">
                    <h3>Baso Aci</h3>
                    <p>Baso aci kenyal dengan kuah pedas gurih, lengkap dengan cuanki dan pilus.</p>
                    <div class="menu-item-price">Rp 15.000</div>
                    <form class="add-to-cart-form">
                        <input type="hidden" name="item_id" value="2"><input type="hidden" name="item_name" value="Baso Aci">
                        <input type="hidden" name="item_price" value="15000"><input type="hidden" name="item_image" value="img/basoaci.webp">
                        <div class="add-to-cart">
                            <div class="quantity"><button type="button" class="quantity-btn decrease">-</button><span>1</span><input type="hidden" name="item_quantity" value="1" class="quantity-input"><button type="button" class="quantity-btn increase">+</button></div>
                            <button type="submit" class="add-btn">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="menu-item" data-category="camilan">
                <div class="menu-item-image"><img src="img/kentangcrispy.jpg" alt="Kentang Krispy"></div>
                <div class="menu-item-info">
                    <h3>Kentang Krispy</h3>
                    <p>Kentang goreng renyah dengan bumbu tabur pilihan (BBQ, keju, balado).</p>
                    <div class="menu-item-price">Rp 10.000</div>
                    <form class="add-to-cart-form">
                        <input type="hidden" name="item_id" value="3"><input type="hidden" name="item_name" value="Kentang Krispy">
                        <input type="hidden" name="item_price" value="10000"><input type="hidden" name="item_image" value="img/kentangcrispy.jpg">
                        <div class="add-to-cart">
                            <div class="quantity"><button type="button" class="quantity-btn decrease">-</button><span>1</span><input type="hidden" name="item_quantity" value="1" class="quantity-input"><button type="button" class="quantity-btn increase">+</button></div>
                            <button type="submit" class="add-btn">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="menu-item" data-category="camilan">
                <div class="menu-item-image"><img src="img/tempurabumbutabur.avif" alt="Tempura Bumbu Tabur"></div>
                <div class="menu-item-info">
                    <h3>Tempura Bumbu Tabur</h3>
                    <p>Tempura ikan goreng disajikan dengan bumbu tabur gurih dan nikmat.</p>
                    <div class="menu-item-price">Rp 12.000</div>
                    <form class="add-to-cart-form">
                        <input type="hidden" name="item_id" value="4"><input type="hidden" name="item_name" value="Tempura Bumbu Tabur">
                        <input type="hidden" name="item_price" value="12000"><input type="hidden" name="item_image" value="img/tempurabumbutabur.avif">
                        <div class="add-to-cart">
                            <div class="quantity"><button type="button" class="quantity-btn decrease">-</button><span>1</span><input type="hidden" name="item_quantity" value="1" class="quantity-input"><button type="button" class="quantity-btn increase">+</button></div>
                            <button type="submit" class="add-btn">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="menu-item" data-category="camilan">
                <div class="menu-item-image"><img src="img/tempurabumbuspicy.jpg" alt="Tempura Bumbu Spicy"></div>
                <div class="menu-item-info">
                    <h3>Tempura Bumbu Spicy</h3>
                    <p>Tempura ikan goreng dengan saus pedas manis yang menggugah selera.</p>
                    <div class="menu-item-price">Rp 13.000</div>
                    <form class="add-to-cart-form">
                        <input type="hidden" name="item_id" value="5"><input type="hidden" name="item_name" value="Tempura Bumbu Spicy">
                        <input type="hidden" name="item_price" value="13000"><input type="hidden" name="item_image" value="img/tempurabumbuspicy.jpg">
                        <div class="add-to-cart">
                            <div class="quantity"><button type="button" class="quantity-btn decrease">-</button><span>1</span><input type="hidden" name="item_quantity" value="1" class="quantity-input"><button type="button" class="quantity-btn increase">+</button></div>
                            <button type="submit" class="add-btn">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="menu-item" data-category="camilan">
                <div class="menu-item-image"><img src="img/tahucrispy.png" alt="Tahu Crispy"></div>
                <div class="menu-item-info">
                    <h3>Tahu Crispy</h3>
                    <p>Tahu goreng tepung super renyah dengan taburan bumbu cabai garam.</p>
                    <div class="menu-item-price">Rp 10.000</div>
                    <form class="add-to-cart-form">
                        <input type="hidden" name="item_id" value="6"><input type="hidden" name="item_name" value="Tahu Crispy">
                        <input type="hidden" name="item_price" value="10000"><input type="hidden" name="item_image" value="img/tahucrispy.png">
                        <div class="add-to-cart">
                            <div class="quantity"><button type="button" class="quantity-btn decrease">-</button><span>1</span><input type="hidden" name="item_quantity" value="1" class="quantity-input"><button type="button" class="quantity-btn increase">+</button></div>
                            <button type="submit" class="add-btn">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="menu-item" data-category="camilan">
                <div class="menu-item-image"><img src="img/enokicrispy.jpg" alt="Enoki Crispy"></div>
                <div class="menu-item-info">
                    <h3>Enoki Crispy</h3>
                    <p>Jamur enoki yang digoreng krispi, renyah dan bikin nagih untuk camilan.</p>
                    <div class="menu-item-price">Rp 12.000</div>
                    <form class="add-to-cart-form">
                        <input type="hidden" name="item_id" value="7"><input type="hidden" name="item_name" value="Enoki Crispy">
                        <input type="hidden" name="item_price" value="12000"><input type="hidden" name="item_image" value="img/enokicrispy.jpg">
                        <div class="add-to-cart">
                            <div class="quantity"><button type="button" class="quantity-btn decrease">-</button><span>1</span><input type="hidden" name="item_quantity" value="1" class="quantity-input"><button type="button" class="quantity-btn increase">+</button></div>
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
    
    document.addEventListener('DOMContentLoaded', function() {
        const categories = document.querySelectorAll('.menu-category');
        const menuItems = document.querySelectorAll('.menu-item');
        const cartCount = document.querySelector('.cart-count');
        
        // Filter menu
        categories.forEach(category => {
            category.addEventListener('click', function() {
                categories.forEach(cat => cat.classList.remove('active'));
                this.classList.add('active');
                const selectedCategory = this.getAttribute('data-category');
                menuItems.forEach(item => {
                    item.style.display = 'none';
                    if (selectedCategory === 'all' || item.getAttribute('data-category') === selectedCategory) {
                        item.style.display = 'block';
                    }
                });
            });
        });
        
        // Kuantitas dan Tambah ke Keranjang
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
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!isLoggedIn) {
                    modal.style.display = 'flex';
                    return;
                }
                const formData = new FormData(form);
                formData.append('action', 'add_to_cart');
                
                $.ajax({
                    url: 'kedai-mama-zavan.php', // URL AJAX diubah ke file ini
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            cartCount.textContent = response.total_items;
                            // Anda bisa menambahkan notifikasi toast di sini
                            alert(response.message); // Pesan sederhana
                            quantitySpan.textContent = 1;
                            quantityInput.value = 1;
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                });
            });
        });
        
        // Fungsi Modal
        window.closeModal = function() { modal.style.display = 'none'; }
        window.onclick = function(event) { if (event.target == modal) { closeModal(); } }
    });
</script>
</body>
</html>