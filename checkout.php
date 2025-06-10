<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$dbname = "metalvault";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get cart items with product details
$sql = "SELECT c.id as cart_id, c.product_id, c.quantity, p.title, p.artist, p.price, p.image 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $total += $row['price'] * $row['quantity'];
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koszyk - Metal Vault</title>
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
      .cart-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
      }
      .cart-item {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 15px;
        border-bottom: 1px solid #333;
        background-color: #2a2a2a;
        margin-bottom: 10px;
        border-radius: 5px;
      }
      .cart-item-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border: 2px solid #ff0000;
      }
      .cart-item-details {
        flex-grow: 1;
      }
      .cart-item h3 {
        color: #ffffff;
        margin: 0 0 5px 0;
      }
      .artist-name {
        color: #999999;
        margin: 5px 0;
      }
      .price {
        color: #ff0000;
        font-size: 18px;
        font-weight: bold;
      }
      .quantity-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
      }
      .quantity-btn {
        background-color: #2a2a2a;
        color: #ff0000;
        border: 1px solid #ff0000;
        padding: 5px 10px;
        cursor: pointer;
      }
      .quantity-btn:hover {
        background-color: #ff0000;
        color: white;
      }
      .quantity {
        color: white;
        font-weight: bold;
      }
      .remove-btn {
        background-color: #ff0000;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
      }
      .remove-btn:hover {
        background-color: #cc0000;
      }
      .order-summary {
        background-color: #2a2a2a;
        padding: 20px;
        border-radius: 5px;
        margin-top: 20px;
        border: 2px solid #ff0000;
      }
      .order-summary h2 {
        color: #ff0000;
        margin-top: 0;
      }
      .total {
        color: #ff0000;
        font-size: 24px;
        font-weight: bold;
        margin: 20px 0;
      }
      .checkout-form {
        background-color: #2a2a2a;
        padding: 20px;
        border-radius: 5px;
        margin-top: 20px;
        border: 2px solid #ff0000;
      }
      .form-group {
        margin-bottom: 15px;
      }
      .form-label {
        display: block;
        color: #ffffff;
        margin-bottom: 5px;
      }
      .form-input {
        width: 100%;
        padding: 8px;
        background-color: #1a1a1a;
        border: 1px solid #ff0000;
        color: #ffffff;
        border-radius: 4px;
      }
      .empty-cart {
        text-align: center;
        padding: 50px;
        background-color: #2a2a2a;
        border-radius: 5px;
        border: 2px solid #ff0000;
      }
      .empty-cart h2 {
        color: #ff0000;
      }
    </style>
  </head>
  <body>
    <!-- Header -->
    <div align="left" style="margin: 20px;">
      <font size="6" color="#ff0000"><b>🎵 METAL <font color="#ffffff">VAULT</font></b></font>
      <?php if (isset($_SESSION['username'])): ?>
        <span class="user-welcome">Witaj, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
      <?php endif; ?>
    </div>

    <!-- Navigation -->
    <center>
      <div style="margin: 30px;">
        <a href="index.php" class="nav-link">Strona Główna</a>
        <a href="profile.php" class="nav-link">Profil</a>
        <a href="logout.php" class="nav-link">Wyloguj</a>
      </div>

      <div class="cart-container">
        <h2 style="color: #ff0000;">Twój Koszyk</h2>
        
        <?php if (empty($items)): ?>
          <div class="empty-cart">
            <h2>Twój koszyk jest pusty</h2>
            <p>Dodaj produkty do koszyka, aby kontynuować zakupy.</p>
            <a href="index.php" class="btn">Kontynuuj zakupy</a>
          </div>
        <?php else: ?>
          <div class="cart-items">
            <?php foreach ($items as $item): ?>
              <div class="cart-item" id="cart-item-<?php echo $item['cart_id']; ?>">
                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="cart-item-image">
                <div class="cart-item-details">
                  <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                  <p class="artist-name"><?php echo htmlspecialchars($item['artist']); ?></p>
                  <p class="price"><?php echo number_format($item['price'], 2); ?> PLN</p>
                  <div class="quantity-controls">
                    <button onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo ($item['quantity'] - 1); ?>)" class="quantity-btn">-</button>
                    <span class="quantity"><?php echo $item['quantity']; ?></span>
                    <button onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo ($item['quantity'] + 1); ?>)" class="quantity-btn">+</button>
                  </div>
                </div>
                <button onclick="removeFromCart(<?php echo $item['product_id']; ?>)" class="remove-btn" data-product-id="<?php echo $item['product_id']; ?>">🗑️ Usuń</button>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="order-summary">
            <h2>Podsumowanie zamówienia</h2>
            <div class="total">
              Razem: <?php echo number_format($total, 2); ?> PLN
            </div>
          </div>

          <div class="checkout-form">
            <h2 style="color: #ff0000;">Dane do wysyłki</h2>
            <form action="process_order.php" method="post">
              <div class="form-group">
                <label class="form-label">Imię i nazwisko:</label>
                <input type="text" name="full_name" class="form-input" required>
              </div>
              <div class="form-group">
                <label class="form-label">Adres:</label>
                <input type="text" name="address" class="form-input" required>
              </div>
              <div class="form-group">
                <label class="form-label">Kod pocztowy:</label>
                <input type="text" name="postal_code" class="form-input" required pattern="[0-9]{2}-[0-9]{3}" placeholder="00-000">
              </div>
              <div class="form-group">
                <label class="form-label">Miasto:</label>
                <input type="text" name="city" class="form-input" required>
              </div>
              <div class="form-group">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-input" required>
              </div>
              <div class="form-group">
                <label class="form-label">Telefon:</label>
                <input type="tel" name="phone" class="form-input" required pattern="[0-9]{9}" placeholder="123456789">
              </div>
              <button type="submit" class="btn" style="width: 100%;">Złóż zamówienie</button>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </center>

    <script>
    function removeFromCart(productId) {
        console.log('Attempting to remove product:', productId);
        if (confirm('Czy na pewno chcesz usunąć ten produkt z koszyka?')) {
            const formData = new FormData();
            formData.append('product_id', productId);

            fetch('remove_from_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Server response:', data);
                if (data.success) {
                    location.reload(); // Reload to update the page
                } else {
                    alert(data.message || 'Wystąpił błąd podczas usuwania produktu.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Wystąpił błąd podczas usuwania produktu.');
            });
        }
    }

    function updateQuantity(productId, newQuantity) {
        if (newQuantity < 1) {
            removeFromCart(productId);
            return;
        }
        
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', newQuantity);

        fetch('update_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Wystąpił błąd podczas aktualizacji koszyka.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Wystąpił błąd podczas aktualizacji koszyka.');
        });
    }
    </script>
  </body>
</html> 