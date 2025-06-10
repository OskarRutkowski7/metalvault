<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $dbname = "metalvault";
    $username = "root";
    $password = "";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $firstName = $conn->real_escape_string($_POST['firstName']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        header("Location: register.php?error=password_mismatch");
        exit();
    }

    // Check if email already exists
    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        header("Location: register.php?error=email_exists");
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $sql = "INSERT INTO users (username, email, password) VALUES ('$firstName', '$email', '$hashedPassword')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: login.html?registered=true");
        exit();
    } else {
        header("Location: register.php?error=registration_failed");
        exit();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja - Metal Vault</title>
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
      .register-container {
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
        <a href="login.html" class="nav-link">Logowanie</a>
      </div>

      <!-- Register Form -->
      <div class="register-container">
        <h2 class="form-title">Rejestracja</h2>
        <?php if (isset($_GET['error'])): ?>
          <div class="error-message">
            <?php
            switch($_GET['error']) {
                case 'password_mismatch':
                    echo 'Hasła nie są identyczne';
                    break;
                case 'email_exists':
                    echo 'Ten email jest już zarejestrowany';
                    break;
                case 'registration_failed':
                    echo 'Wystąpił błąd podczas rejestracji';
                    break;
                default:
                    echo 'Wystąpił nieznany błąd';
            }
            ?>
          </div>
        <?php endif; ?>
        <form action="register.php" method="post" onsubmit="return validateForm()">
          <div class="form-group">
            <label class="form-label">Imię:</label>
            <input type="text" name="firstName" class="input-field" placeholder="Wprowadź swoje imię" required>
          </div>
          
          <div class="form-group">
            <label class="form-label">Email:</label>
            <input type="email" name="email" class="input-field" placeholder="Wprowadź swój email" required>
          </div>
          
          <div class="form-group">
            <label class="form-label">Hasło:</label>
            <input type="password" name="password" id="password" class="input-field" placeholder="Wprowadź swoje hasło" required>
          </div>
          
          <div class="form-group">
            <label class="form-label">Potwierdź hasło:</label>
            <input type="password" name="confirmPassword" id="confirmPassword" class="input-field" placeholder="Potwierdź swoje hasło" required>
          </div>

          <div class="form-group">
            <input type="submit" value="Zarejestruj się" class="btn" style="width: 100%;">
          </div>
        </form>

        <a href="login.html" class="login-link">Masz już konto? Zaloguj się</a>

        <div class="social-login">
          <p style="color: #666666;">LUB KONTYNUUJ PRZEZ</p>
          <button class="social-btn">Google</button>
          <button class="social-btn">Facebook</button>
        </div>
      </div>
    </center>

    <script>
      function validateForm() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirmPassword").value;
        
        if (password !== confirmPassword) {
          alert("Hasła nie są identyczne!");
          return false;
        }
        return true;
      }
    </script>
  </body>
</html> 