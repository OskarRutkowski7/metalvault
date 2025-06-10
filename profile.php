<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$servername = "localhost";
$dbname = "metalvault";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user information
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get user's cart items
$cart_sql = "SELECT c.*, p.title, p.artist, p.price, p.image 
             FROM cart c 
             JOIN products p ON c.product_id = p.id 
             WHERE c.user_id = ?";
$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Metal Vault</title>
    <style type="text/css">
        body {
            background-color: #1a1a1a;
            font-family: "Comic Sans MS", cursive, sans-serif;
            color: #ffffff;
        }
        .btn {
            border: 2px solid #ff0000;
            padding: 8px 20px;
            font-weight: bold;
            cursor: pointer;
            background-color: #2a2a2a;
            color: #ff0000;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background-color: #ff0000;
            color: #ffffff;
        }
        .nav-link {
            color: #ff0000;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
            font-size: 18px;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #ffffff;
            text-decoration: none;
        }
        .profile-container {
            background-color: #2a2a2a;
            border: 2px solid #ff0000;
            padding: 30px;
            width: 800px;
            margin: 50px auto;
        }
        .profile-header {
            color: #ff0000;
            font-size: 24px;
            text-align: center;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .profile-info {
            margin-bottom: 30px;
        }
        .profile-label {
            color: #ff0000;
            font-weight: bold;
            margin-right: 10px;
        }
        .cart-item {
            background-color: #1a1a1a;
            border: 1px solid #ff0000;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 20px;
        }
        .cart-item-info {
            flex-grow: 1;
        }
        .cart-item-title {
            color: #ffffff;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .cart-item-artist {
            color: #999999;
            font-style: italic;
        }
        .cart-item-price {
            color: #ff0000;
            font-size: 20px;
            font-weight: bold;
        }
        .cart-total {
            text-align: right;
            margin-top: 20px;
            font-size: 24px;
            color: #ff0000;
        }
        .remove-btn {
            background-color: #ff0000;
            color: #ffffff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            margin-left: 10px;
        }
        .remove-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div align="left" style="margin: 20px;">
        <font size="6" color="#ff0000"><b>🎵 METAL <font color="#ffffff">VAULT</font></b></font>
    </div>

    <!-- Navigation -->
    <center>
        <div style="margin: 30px;">
            <a href="index.html" class="nav-link">Strona Główna</a>
            <a href="profile.php" class="nav-link">Profil</a>
            <a href="logout.php" class="nav-link">Wyloguj</a>
        </div>

        <!-- Profile Content -->
        <div class="profile-container">
            <h2 class="profile-header">Twój Profil</h2>
            
            <div class="profile-info">
                <p><span class="profile-label">Nazwa użytkownika:</span> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><span class="profile-label">Email:</span> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>

            <h3 class="profile-header">Twój Koszyk</h3>
            
            <?php if ($cart_result->num_rows > 0): ?>
                <?php 
                $total = 0;
                while ($item = $cart_result->fetch_assoc()): 
                    $total += $item['price'];
                ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="cart-item-info">
                            <div class="cart-item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                            <div class="cart-item-artist"><?php echo htmlspecialchars($item['artist']); ?></div>
                            <div class="cart-item-price"><?php echo number_format($item['price'], 2); ?> PLN</div>
                        </div>
                        <button class="remove-btn" onclick="removeFromCart(<?php echo $item['id']; ?>)">Usuń</button>
                    </div>
                <?php endwhile; ?>
                
                <div class="cart-total">
                    Razem: <?php echo number_format($total, 2); ?> PLN
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <button class="btn" onclick="checkout()">Zamów</button>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666666;">Twój koszyk jest pusty</p>
            <?php endif; ?>
        </div>
    </center>

    <script>
        function removeFromCart(itemId) {
            if (confirm('Czy na pewno chcesz usunąć ten przedmiot z koszyka?')) {
                fetch('remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'item_id=' + itemId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Wystąpił błąd podczas usuwania przedmiotu z koszyka');
                    }
                });
            }
        }

        function checkout() {
            if (confirm('Czy chcesz złożyć zamówienie?')) {
                fetch('checkout.php', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Zamówienie zostało złożone!');
                        location.reload();
                    } else {
                        alert('Wystąpił błąd podczas składania zamówienia');
                    }
                });
            }
        }
    </script>
</body>
</html>
<?php
$conn->close();
?> 