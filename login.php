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
    $servername = "localhost";
    $dbname = "metalvault";
    $userek = "root";
    $pasek = "";

    $conn = new mysqli($servername, $userek, $pasek, $dbname);

    if ($conn->connect_error) {
      die("connection failed: " . $conn->connect_error);
    }
    $username = $_POST['username'];
    $password = $_POST['password'];
    echo $username."<br />";
    echo $password;
    $insert = "INSERT INTO users(username,password,email";
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password' ";
    $result = $conn -> query($sql);
    $tak = $result -> fetch_row();
    ?>
    <!-- Logo w lewym górnym rogu -->
    <div class="logo">
      <span class="logo-icon">🎵</span> METAL VAULT
    </div>

    <!-- Główny kontener na środek strony -->
    <div class="auth-box">
      <h1>Witaj Ponownie</h1>
      <p class="subtitle">Zaloguj się na swoje konto, aby kontynuować</p>

      <!-- Zakładki Logowanie / Rejestracja -->
      <div class="tabs">
        <button class="active">Logowanie</button>
        <button>Rejestracja</button>
      </div>

      <!-- Formularz logowania -->
      <form action="/frontendszo/main.php" method="post">
        <label for="username">Username</label>
        <input
          type="username"
          id="username"
          name="username"
          placeholder="twoj user"
          required
        />

        <div class="password-label">
          <label for="password">Hasło</label>
          <a href="#" class="forgot-pass">Zapomniałeś hasła?</a>
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

      <!-- Separator -->
      <div class="divider">LUB KONTYNUUJ PRZEZ</div>

      <!-- Przyciski logowania społecznościowego -->
      <div class="social-buttons">
        <button class="social">Google</button>
        <button class="social">Facebook</button>
      </div>
    </div>
  </body>
</html>