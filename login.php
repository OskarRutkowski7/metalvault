<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Logowanie</title>
    <!-- Połącz plik CSS -->
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <?php
    session_start();
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
        $servername = "localhost";
        $dbname = "metalvault";
        $userek = "root";
        $pasek = "";

        $conn = new mysqli($servername, $userek, $pasek, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $username = $conn->real_escape_string($_POST['username']);
        $password = $_POST['password'];

        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Debug information
            error_log("Attempting login for user: " . $username);
            error_log("Stored hash: " . $user['password']);
            error_log("Input password: " . $password);
            
            if (password_verify($password, $user['password'])) {
                error_log("Password verification successful");
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: main.php");
                exit();
            } else {
                error_log("Password verification failed");
                $error_message = "Nieprawidłowa nazwa użytkownika lub hasło";
            }
        } else {
            error_log("User not found: " . $username);
            $error_message = "Nieprawidłowa nazwa użytkownika lub hasło";
        }

        $stmt->close();
        $conn->close();
    }
    ?>
    <!-- Logo w lewym górnym rogu -->
    <div class="logo">
      <span class="logo-icon">🎵</span> METAL VAULT
    </div>

    <!-- Główny kontener na środek strony -->
    <div class="auth-box">
      <h1>Witaj Ponownie</h1>
      <p class="subtitle">Zaloguj się na swoje konto, aby kontynuować</p>

      <?php if (isset($_SESSION['registration_success'])): ?>
        <div class="success-message">Rejestracja zakończona sukcesem! Możesz się teraz zalogować.</div>
        <?php unset($_SESSION['registration_success']); ?>
      <?php endif; ?>

      <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
      <?php endif; ?>

      <!-- Zakładki Logowanie / Rejestracja -->
      <div class="tabs">
        <button class="active">Logowanie</button>
        <button onclick="window.location.href='register.php'">Rejestracja</button>
      </div>

      <!-- Formularz logowania -->
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Username</label>
        <input
          type="text"
          id="username"
          name="username"
          placeholder="twoj user"
          required
        />

        <div class="password-label">
          <label for="password">Hasło</label>
          <a href="forgot-password.php" class="forgot-pass">Zapomniałeś hasła?</a>
        </div>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="********"
          required
        />

        <button class="primary" type="submit">Zaloguj się</button>
      </form>

    
    </div>
  </body>
</html>