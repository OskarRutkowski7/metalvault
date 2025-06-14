<?php
session_start();

// If user is already logged in, redirect to index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - Metal Vault</title>
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
      .login-container {
        background-color: #2a2a2a;
        border: 2px solid #ff0000;
        padding: 30px;
        width: 400px;
        margin: 50px auto;
      }
      .input-field {
        background-color: #1a1a1a;
        color: #ffffff;
        border: 2px solid #ff0000;
        padding: 10px;
        width: 100%;
        margin: 10px 0;
        box-sizing: border-box;
      }
      .input-field::placeholder {
        color: #666666;
      }
      .form-title {
        color: #ff0000;
        font-size: 24px;
        text-align: center;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 2px;
      }
      .form-group {
        margin-bottom: 20px;
      }
      .form-label {
        color: #ffffff;
        display: block;
        margin-bottom: 5px;
      }
      .forgot-password {
        color: #666666;
        font-size: 14px;
        text-decoration: none;
        display: block;
        text-align: right;
        margin-top: 5px;
      }
      .forgot-password:hover {
        color: #ff0000;
      }
      .social-login {
        text-align: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #333333;
      }
      .social-btn {
        background-color: #2a2a2a;
        color: #ff0000;
        border: 2px solid #ff0000;
        padding: 8px 20px;
        margin: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
      }
      .social-btn:hover {
        background-color: #ff0000;
        color: #ffffff;
      }
      .login-link {
        color: #666666;
        font-size: 14px;
        text-decoration: none;
        display: block;
        text-align: center;
        margin-top: 10px;
      }
      .login-link:hover {
        color: #ff0000;
      }
      .error-message {
        color: #ff0000;
        text-align: center;
        margin-bottom: 15px;
        padding: 10px;
        background-color: rgba(255, 0, 0, 0.1);
        border-radius: 5px;
      }
      .success-message {
        color: #00ff00;
        text-align: center;
        margin-bottom: 15px;
        padding: 10px;
        background-color: rgba(0, 255, 0, 0.1);
        border-radius: 5px;
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
        <a href="index.php" class="nav-link">Strona Główna</a>
        <a href="login.php" class="nav-link">Logowanie</a>
        <a href="register.php" class="nav-link">Rejestracja</a>
      </div>

      <!-- Login Form -->
      <div class="login-container">
        <h2 class="form-title">Logowanie</h2>
        <?php if (isset($_GET['registered']) && $_GET['registered'] == 'true'): ?>
          <div class="success-message">
            Rejestracja zakończona pomyślnie! Możesz się teraz zalogować.
          </div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
          <div class="error-message">
            Nieprawidłowy email lub hasło
          </div>
        <?php endif; ?>
        <form action="login_process.php" method="post">
          <div class="form-group">
            <label class="form-label">Email:</label>
            <input type="email" name="email" class="input-field" placeholder="Wprowadź swój email" required>
          </div>
          
          <div class="form-group">
            <label class="form-label">Hasło:</label>
            <input type="password" name="password" class="input-field" placeholder="Wprowadź swoje hasło" required>
            <a href="#" class="forgot-password">Zapomniałeś hasła?</a>
          </div>

          <div class="form-group">
            <input type="submit" value="Zaloguj się" class="btn" style="width: 100%;">
          </div>
        </form>

        <a href="register.php" class="login-link">Nie masz konta? Zarejestruj się</a>

        <div class="social-login">
          <p style="color: #666666;">LUB KONTYNUUJ PRZEZ</p>
          <button class="social-btn">Google</button>
          <button class="social-btn">Facebook</button>
        </div>
      </div>
    </center>
  </body>
</html>