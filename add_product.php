<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
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

    // Get form data
    $title = $_POST['title'] ?? '';
    $artist = $_POST['artist'] ?? '';
    $price = $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';

    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = $upload_path;
            }
        }
    }

    if ($image_path) {
        // Insert product into database
        $stmt = $conn->prepare("INSERT INTO products (title, artist, price, description, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $title, $artist, $price, $description, $image_path);
        
        if ($stmt->execute()) {
            header("Location: admin_panel.php?success=1");
        } else {
            header("Location: admin_panel.php?error=1");
        }
        
        $stmt->close();
    } else {
        header("Location: admin_panel.php?error=2");
    }

    $conn->close();
    exit();
}

// If not POST request, redirect to admin panel
header("Location: admin_panel.php");
exit();
?> 