<?php
session_start();

// Check if already logged in as admin
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_panel.php");
    exit();
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $servername = "localhost";
    $dbname = "metalvault";
    $username_db = "root";
    $password_db = "";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: admin_panel.php");
            exit();
        }
    }

    $error = "Nieprawidłowa nazwa użytkownika lub hasło";
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administracyjny - Logowanie</title>
    <style>
        body {
            background-color: #1a1a1a;
            font-family: "Comic Sans MS", cursive, sans-serif;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #2a2a2a;
            padding: 30px;
            border-radius: 10px;
            border: 2px solid #ff0000;
            width: 100%;
            max-width: 400px;
        }
        .login-title {
            color: #ff0000;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 5px;
            color: #ffffff;
        }
        .form-input {
            width: 100%;
            padding: 10px;
            background-color: #1a1a1a;
            border: 1px solid #ff0000;
            color: #ffffff;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background-color: #ff0000;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #cc0000;
        }
        .error-message {
            color: #ff0000;
            text-align: center;
            margin-bottom: 20px;
        }
        .back-link {
            display: block;
            text-align: center;
            color: #ff0000;
            text-decoration: none;
            margin-top: 20px;
        }
        .back-link:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">Panel Administracyjny</h2>
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label">Nazwa użytkownika:</label>
                <input type="text" name="username" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Hasło:</label>
                <input type="password" name="password" class="form-input" required>
            </div>
            <button type="submit" class="btn">Zaloguj się</button>
        </form>
        <a href="index.php" class="back-link">Powrót do sklepu</a>
    </div>
</body>
</html> 