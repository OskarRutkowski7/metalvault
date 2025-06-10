<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Musisz być zalogowany']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $servername = "localhost";
    $dbname = "metalvault";
    $username = "root";
    $password = "";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych']);
        exit();
    }

    $product_id = $conn->real_escape_string($_POST['product_id']);
    $quantity = (int)$_POST['quantity'];
    $user_id = $_SESSION['user_id'];

    if ($quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Nieprawidłowa ilość']);
        exit();
    }

    $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $quantity, $user_id, $product_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Błąd podczas aktualizacji koszyka']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowe żądanie']);
}
?> 