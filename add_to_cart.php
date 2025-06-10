<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Musisz być zalogowany']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $servername = "localhost";
    $dbname = "metalvault";
    $username = "root";
    $password = "";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych']);
        exit();
    }

    $product_id = (int)$_POST['product_id'];
    $user_id = $_SESSION['user_id'];

    // First check if the product exists
    $check_product_sql = "SELECT id FROM products WHERE id = ?";
    $check_product_stmt = $conn->prepare($check_product_sql);
    $check_product_stmt->bind_param("i", $product_id);
    $check_product_stmt->execute();
    $product_result = $check_product_stmt->get_result();

    if ($product_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Produkt nie istnieje']);
        $check_product_stmt->close();
        $conn->close();
        exit();
    }
    $check_product_stmt->close();

    // Check if the item is already in the cart
    $check_cart_sql = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
    $check_cart_stmt = $conn->prepare($check_cart_sql);
    $check_cart_stmt->bind_param("ii", $user_id, $product_id);
    $check_cart_stmt->execute();
    $cart_result = $check_cart_stmt->get_result();

    if ($cart_result->num_rows > 0) {
        // Update quantity if item exists
        $row = $cart_result->fetch_assoc();
        $new_quantity = $row['quantity'] + 1;
        
        $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
        
        if ($update_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Zaktualizowano ilość w koszyku']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Błąd podczas aktualizacji koszyka']);
        }
        $update_stmt->close();
    } else {
        // Insert new item if it doesn't exist
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $user_id, $product_id);
        
        if ($insert_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Dodano do koszyka']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Błąd podczas dodawania do koszyka']);
        }
        $insert_stmt->close();
    }

    $check_cart_stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowe żądanie']);
}
?> 