<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rejestracja</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <?php
    session_start();
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])) {
        $servername = "localhost";
        $dbname = "metalvault";
        $userek = "root";
        $pasek = "";

        $conn = new mysqli($servername, $userek, $pasek, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Debug information
        error_log("Registration attempt for user: " . $username);
        error_log("Original password: " . $password);
        error_log("Hashed password: " . $hashed_password);

        // Check if username or email already exists
        $check_sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Nazwa użytkownika lub email już istnieje";
        } else {
            $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $hashed_password, $email);
            
            if ($stmt->execute()) {
                error_log("User registered successfully: " . $username);
                $_SESSION['registration_success'] = true;
                header("Location: login.php");
                exit();
            } else {
                error_log("Registration failed: " . $conn->error);
                $error_message = "Błąd podczas rejestracji: " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
        $conn->close();
    }
    ?>

    <!-- Logo w lewym górnym rogu -->
    <div class="logo">
      <span class="logo-icon">🎵</span> METAL VAULT
    </div>

    <!-- Główny kontener na środek strony -->
    <div class="auth-box">
      <h1>Utwórz Konto</h1>
      <p class="subtitle">Zarejestruj się, aby rozpocząć przygodę</p>

      <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
      <?php endif; ?>

      <!-- Zakładki Logowanie / Rejestracja -->
      <div class="tabs">
        <button onclick="window.location.href='login.php'">Logowanie</button>
        <button class="active">Rejestracja</button>
      </div>

      <!-- Formularz rejestracji -->
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Username</label>
        <input
          type="text"
          id="username"
          name="username"
          placeholder="wybierz nazwę użytkownika"
          required
        />

        <label for="email">Email</label>
        <input
          type="email"
          id="email"
          name="email"
          placeholder="twój@email.com"
          required
        />

        <label for="password">Hasło</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="********"
          required
        />

        <button class="primary" type="submit">Zarejestruj się</button>
      </form>

    
  </body>
</html> 