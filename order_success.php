<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
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

// Get order details
$sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $_GET['order_id'], $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$order = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zamówienie złożone - Metal Vault</title>
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
      .success-container {
        background-color: #2a2a2a;
        border: 2px solid #ff0000;
        padding: 30px;
        width: 80%;
        max-width: 800px;
        margin: 50px auto;
        text-align: center;
      }
      .success-title {
        color: #00ff00;
        font-size: 28px;
        margin-bottom: 20px;
      }
      .success-message {
        color: #ffffff;
        font-size: 18px;
        margin-bottom: 30px;
      }
      .order-details {
        background-color: #1a1a1a;
        padding: 20px;
        border-radius: 5px;
        margin: 20px 0;
        text-align: left;
      }
      .order-detail {
        margin: 10px 0;
      }
      .order-detail span {
        color: #ff0000;
        font-weight: bold;
      }
    </style>
  </head>
  <body>
    <!-- Header -->
    <div align="left" style="margin: 20px;">
      <font size="6" color="#ff0000"><b>🎵 METAL <font color="#ffffff">VAULT</font></b></font>
      <span class="user-welcome">Witaj, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
    </div>

    <!-- Navigation -->
    <center>
      <div style="margin: 30px;">
        <a href="index.php" class="nav-link">Strona Główna</a>
        <a href="profile.php" class="nav-link">Profil</a>
        <a href="logout.php" class="nav-link">Wyloguj</a>
      </div>

      <!-- Success Container -->
      <div class="success-container">
        <h2 class="success-title">✓ Zamówienie złożone pomyślnie!</h2>
        <p class="success-message">Dziękujemy za zakupy w Metal Vault!</p>

        <div class="order-details">
          <div class="order-detail">
            <span>Numer zamówienia:</span> #<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?>
          </div>
          <div class="order-detail">
            <span>Data zamówienia:</span> <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?>
          </div>
          <div class="order-detail">
            <span>Wartość zamówienia:</span> <?php echo number_format($order['total_amount'], 2); ?> PLN
          </div>
          <div class="order-detail">
            <span>Status:</span> Przyjęte do realizacji
          </div>
        </div>

        <p class="success-message">
          Potwierdzenie zamówienia zostało wysłane na adres email: <?php echo htmlspecialchars($order['email']); ?>
        </p>

        <a href="index.php" class="btn">Kontynuuj zakupy</a>
      </div>
    </center>
  </body>
</html>
<?php $conn->close(); ?> 