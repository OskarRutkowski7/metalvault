<?php
session_start();

// Get cart count if user is logged in
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $servername = "localhost";
    $dbname = "metalvault";
    $username = "root";
    $password = "";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if (!$conn->connect_error) {
        $sql = "SELECT SUM(quantity) as count FROM cart WHERE user_id = " . $_SESSION['user_id'];
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            $cart_count = $row['count'] ?: 0;
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metal Vault - Sklep z Muzyką Metalową</title>
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
      .product-card {
        background-color: #2a2a2a;
        border: 2px solid #ff0000;
        padding: 15px;
        margin: 10px;
        width: 200px;
        display: inline-block;
        vertical-align: top;
        transition: transform 0.3s ease;
      }
      .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(255, 0, 0, 0.3);
      }
      .product-image {
        border: 2px solid #ff0000;
        width: 100%;
        height: 200px;
        object-fit: cover;
      }
      .nav-link {
        color: #ff0000;
        text-decoration: none;
        margin: 0 15px;
        font-weight: bold;
        font-size: 18px;
        transition: color 0.3s ease;
        display: inline-flex;
        align-items: center;
      }
      .nav-link:hover {
        color: #ffffff;
        text-decoration: none;
      }
      .search-bar {
        background-color: #2a2a2a;
        color: #ffffff;
        border: 2px solid #ff0000;
        padding: 8px 15px;
        width: 300px;
      }
      .search-bar::placeholder {
        color: #666666;
      }
      .section-title {
        color: #ff0000;
        font-size: 28px;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin: 30px 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
      }
      .price {
        color: #ff0000;
        font-size: 20px;
        font-weight: bold;
      }
      .artist-name {
        color: #999999;
        font-style: italic;
      }
      .album-title {
        color: #ffffff;
        font-size: 18px;
        margin: 10px 0;
      }
      .user-welcome {
        color: #ffffff;
        margin-left: 20px;
      }
      .cart-count {
        background-color: #ff0000;
        color: #ffffff;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 12px;
        margin-left: 5px;
      }
      .cart-icon {
        font-size: 20px;
        margin-right: 5px;
      }
      .success-message {
        background-color: #2a2a2a;
        border: 2px solid #00ff00;
        color: #00ff00;
        padding: 10px;
        margin: 10px auto;
        text-align: center;
        width: 80%;
        max-width: 600px;
        border-radius: 5px;
        animation: fadeOut 5s forwards;
      }
      @keyframes fadeOut {
        0% { opacity: 1; }
        70% { opacity: 1; }
        100% { opacity: 0; }
      }
      .user-profile {
        background-color: #2a2a2a;
        border: 2px solid #ff0000;
        padding: 20px;
        margin: 20px auto;
        width: 80%;
        max-width: 800px;
        border-radius: 5px;
      }
      .user-profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
      }
      .user-avatar {
        width: 80px;
        height: 80px;
        background-color: #ff0000;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: #ffffff;
        margin-right: 20px;
      }
      .user-info {
        flex-grow: 1;
      }
      .user-name {
        color: #ff0000;
        font-size: 24px;
        margin: 0;
      }
      .user-email {
        color: #999999;
        margin: 5px 0;
      }
      .user-stats {
        display: flex;
        justify-content: space-around;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #333333;
      }
      .stat-item {
        text-align: center;
      }
      .stat-value {
        color: #ff0000;
        font-size: 24px;
        font-weight: bold;
      }
      .stat-label {
        color: #999999;
        font-size: 14px;
      }
      .recent-activity {
        margin-top: 20px;
      }
      .activity-title {
        color: #ff0000;
        font-size: 18px;
        margin-bottom: 10px;
      }
      .activity-item {
        background-color: #1a1a1a;
        padding: 10px;
        margin: 5px 0;
        border-radius: 3px;
      }
      .activity-date {
        color: #666666;
        font-size: 12px;
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

    <?php if (isset($_SESSION['login_success'])): ?>
      <div class="success-message">
        Zalogowano pomyślnie! Witaj w Metal Vault!
      </div>
      <?php unset($_SESSION['login_success']); ?>
    <?php endif; ?>

    <!-- Navigation -->
    <center>
      <div style="margin: 30px; display: flex; justify-content: center; align-items: center; gap: 20px;">
        <div>
          <a href="index.php" class="nav-link">Strona Główna</a>
          <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php" class="nav-link">Profil</a>
            <a href="checkout.php" class="nav-link">Koszyk</a>
            <a href="logout.php" class="nav-link">Wyloguj</a>
          <?php else: ?>
            <a href="login.php" class="nav-link">Logowanie</a>
            <a href="register.php" class="nav-link">Rejestracja</a>
          <?php endif; ?>
        </div>
        <div style="margin-left: 40px;">
          <a href="admin_login.php" class="nav-link" style="border: 2px solid #ff0000; padding: 6px 16px; border-radius: 6px; background: #2a2a2a; color: #ff0000; font-weight: bold;">Panel Administracyjny</a>
        </div>
      </div>

      <?php if (isset($_SESSION['user_id'])): ?>
        <!-- User Profile Section -->
        <div class="user-profile">
          <div class="user-profile-header">
            <div class="user-avatar">
              <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
            </div>
            <div class="user-info">
              <h2 class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
              <?php
              $servername = "localhost";
              $dbname = "metalvault";
              $username = "root";
              $password = "";

              $conn = new mysqli($servername, $username, $password, $dbname);
              if ($conn->connect_error) {
                  die("Connection failed: " . $conn->connect_error);
              }

              // Get user email
              $sql = "SELECT email FROM users WHERE id = " . $_SESSION['user_id'];
              $result = $conn->query($sql);
              if ($result->num_rows > 0) {
                  $user = $result->fetch_assoc();
                  echo '<p class="user-email">' . htmlspecialchars($user['email']) . '</p>';
              }

              // Get total spent
              $sql = "SELECT SUM(p.price * c.quantity) as total_spent 
                     FROM cart c 
                     JOIN products p ON c.product_id = p.id 
                     WHERE c.user_id = " . $_SESSION['user_id'];
              $result = $conn->query($sql);
              $total_spent = $result->fetch_assoc()['total_spent'] ?? 0;
              ?>
            </div>
          </div>

          <div class="user-stats">
            <div class="stat-item">
              <div class="stat-value"><?php echo $cart_count; ?></div>
              <div class="stat-label">Produkty w koszyku</div>
            </div>
            <div class="stat-item">
              <div class="stat-value"><?php echo number_format($total_spent, 2); ?> PLN</div>
              <div class="stat-label">Wartość koszyka</div>
            </div>
          </div>

          <div class="recent-activity">
            <h3 class="activity-title">Ostatnia aktywność</h3>
            <?php
            // Get recent cart activity
            $sql = "SELECT p.title, c.created_at 
                   FROM cart c 
                   JOIN products p ON c.product_id = p.id 
                   WHERE c.user_id = " . $_SESSION['user_id'] . " 
                   ORDER BY c.created_at DESC 
                   LIMIT 3";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="activity-item">';
                    echo 'Dodano do koszyka: ' . htmlspecialchars($row['title']);
                    echo '<div class="activity-date">' . date('d.m.Y H:i', strtotime($row['created_at'])) . '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="activity-item">Brak ostatniej aktywności</div>';
            }
            $conn->close();
            ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Search Bar -->
      <div style="margin: 30px;">
        <input type="text" placeholder="Szukaj albumów..." class="search-bar">
        <input type="button" value="Szukaj" class="btn">
      </div>

      <!-- Featured Products -->
      <div style="margin: 30px;">
        <h2 class="section-title">🔥 Gorące Nowości 🔥</h2>

        <!-- Product Grid -->
        <div style="width: 800px; margin: 0 auto;">
          <?php
          $servername = "localhost";
          $dbname = "metalvault";
          $username = "root";
          $password = "";

          $conn = new mysqli($servername, $username, $password, $dbname);

          if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
          }

          $sql = "SELECT * FROM products ORDER BY created_at DESC LIMIT 3";
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  echo '<div class="product-card">';
                  echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['title']) . '" class="product-image">';
                  echo '<h3 class="album-title">' . htmlspecialchars($row['title']) . '</h3>';
                  echo '<p class="artist-name">' . htmlspecialchars($row['artist']) . '</p>';
                  echo '<p class="price">' . number_format($row['price'], 2) . ' PLN</p>';
                  if (isset($_SESSION['user_id'])) {
                      echo '<button onclick="addToCart(' . intval($row['id']) . ')" class="btn">Dodaj do koszyka</button>';
                  } else {
                      echo '<a href="login.php" class="btn">Zaloguj się, aby kupić</a>';
                  }
                  echo '</div>';
              }
          }
          $conn->close();
          ?>
        </div>
      </div>
    </center>

    <script>
    function addToCart(productId) {
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Produkt dodany do koszyka!');
                location.reload(); // Reload to update cart count
            } else {
                alert(data.message || 'Wystąpił błąd podczas dodawania do koszyka.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Wystąpił błąd podczas dodawania do koszyka.');
        });
    }
    </script>
  </body>
</html> 