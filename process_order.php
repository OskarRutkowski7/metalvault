<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
    $dbname = "metalvault";
    $username = "root";
    $password = "";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get cart items
        $cart_sql = "SELECT c.*, p.price FROM cart c 
                    JOIN products p ON c.product_id = p.id 
                    WHERE c.user_id = ?";
        $cart_stmt = $conn->prepare($cart_sql);
        $cart_stmt->bind_param("i", $_SESSION['user_id']);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();

        if ($cart_result->num_rows === 0) {
            throw new Exception('Koszyk jest pusty');
        }

        // Calculate total
        $total = 0;
        while ($item = $cart_result->fetch_assoc()) {
            $total += $item['price'] * $item['quantity'];
        }

        // Insert order
        $order_sql = "INSERT INTO orders (user_id, total_amount, full_name, address, postal_code, city, email, phone) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $order_stmt = $conn->prepare($order_sql);
        $order_stmt->bind_param("idssssss", 
            $_SESSION['user_id'],
            $total,
            $_POST['fullName'],
            $_POST['address'],
            $_POST['postalCode'],
            $_POST['city'],
            $_POST['email'],
            $_POST['phone']
        );
        $order_stmt->execute();
        $order_id = $conn->insert_id;

        // Insert order items
        $cart_result->data_seek(0);
        $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $item_stmt = $conn->prepare($item_sql);

        while ($item = $cart_result->fetch_assoc()) {
            $item_stmt->bind_param("iiid", 
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            );
            $item_stmt->execute();
        }

        // Clear cart
        $clear_sql = "DELETE FROM cart WHERE user_id = ?";
        $clear_stmt = $conn->prepare($clear_sql);
        $clear_stmt->bind_param("i", $_SESSION['user_id']);
        $clear_stmt->execute();

        // Commit transaction
        $conn->commit();
        
        // Redirect to success page
        header("Location: order_success.php?order_id=" . $order_id);
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        header("Location: checkout.php?error=" . urlencode($e->getMessage()));
        exit();
    }

    $conn->close();
} else {
    header("Location: checkout.php");
    exit();
}
?> 