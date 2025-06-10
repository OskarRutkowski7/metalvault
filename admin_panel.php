<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost";
$dbname = "metalvault";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

// Handle product deletion
if (isset($_POST['delete_product'])) {
    $product_id = (int)$_POST['delete_product'];
    
    // Get image path before deleting
    $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    // Delete the product
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        // Delete the image file if it exists
        if ($product && $product['image_path'] && file_exists($product['image_path'])) {
            unlink($product['image_path']);
        }
        $message = "Produkt został usunięty pomyślnie.";
    }
    $stmt->close();
}

// Handle product addition/update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $artist = $_POST['artist'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image_path = '';
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = $target_path;
        }
    }
    
    if (isset($_POST['product_id'])) {
        // Update existing product
        $stmt = $conn->prepare("UPDATE products SET title = ?, artist = ?, description = ?, price = ?" . 
                              ($image_path ? ", image_path = ?" : "") . 
                              " WHERE id = ?");
        if ($image_path) {
            $stmt->bind_param("sssdsi", $title, $artist, $description, $price, $image_path, $_POST['product_id']);
        } else {
            $stmt->bind_param("sssdi", $title, $artist, $description, $price, $_POST['product_id']);
        }
    } else {
        // Add new product
        $stmt = $conn->prepare("INSERT INTO products (title, artist, description, price, image_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssds", $title, $artist, $description, $price, $image_path);
    }
    
    if ($stmt->execute()) {
        $message = isset($_POST['product_id']) ? "Produkt został zaktualizowany pomyślnie." : "Produkt został dodany pomyślnie.";
    } else {
        $message = "Wystąpił błąd podczas zapisywania produktu.";
    }
    $stmt->close();
}

// Get all products
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administracyjny - Metal Vault</title>
    <style>
        body {
            background-color: #1a1a1a;
            font-family: "Comic Sans MS", cursive, sans-serif;
            color: #ffffff;
            margin: 0;
            padding: 20px;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ff0000;
        }
        .admin-title {
            color: #ff0000;
            margin: 0;
        }
        .admin-nav {
            display: flex;
            gap: 20px;
        }
        .nav-link {
            color: #ff0000;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: #ffffff;
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .admin-section {
            background-color: #2a2a2a;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 2px solid #ff0000;
        }
        .section-title {
            color: #ff0000;
            margin-top: 0;
        }
        .product-form {
            display: grid;
            gap: 15px;
        }
        .form-group {
            display: grid;
            gap: 5px;
        }
        .form-label {
            color: #ffffff;
        }
        .form-input {
            padding: 8px;
            background-color: #1a1a1a;
            border: 1px solid #ff0000;
            color: #ffffff;
            border-radius: 5px;
        }
        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }
        .btn {
            padding: 10px 20px;
            background-color: #ff0000;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #cc0000;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .product-card {
            background-color: #1a1a1a;
            border: 1px solid #ff0000;
            border-radius: 5px;
            padding: 15px;
            position: relative;
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .product-title {
            color: #ffffff;
            margin: 0 0 5px 0;
        }
        .product-artist {
            color: #999999;
            margin: 0 0 10px 0;
        }
        .product-price {
            color: #ff0000;
            font-weight: bold;
            font-size: 18px;
        }
        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .btn-edit {
            background-color: #2a2a2a;
            color: #ff0000;
            border: 1px solid #ff0000;
        }
        .btn-edit:hover {
            background-color: #ff0000;
            color: #ffffff;
        }
        .btn-delete {
            background-color: #2a2a2a;
            color: #ff0000;
            border: 1px solid #ff0000;
        }
        .btn-delete:hover {
            background-color: #ff0000;
            color: #ffffff;
        }
        .success-message {
            background-color: #2a2a2a;
            color: #00ff00;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #00ff00;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">Panel Administracyjny</h1>
            <div class="admin-nav">
                <a href="index.php" class="nav-link">Przejdź do sklepu</a>
                <a href="admin_logout.php" class="nav-link">Wyloguj się</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="admin-section">
            <h2 class="section-title">Dodaj nowy produkt</h2>
            <form method="POST" enctype="multipart/form-data" class="product-form">
                <div class="form-group">
                    <label class="form-label">Tytuł:</label>
                    <input type="text" name="title" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Artysta:</label>
                    <input type="text" name="artist" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Opis:</label>
                    <textarea name="description" class="form-input form-textarea" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Cena (PLN):</label>
                    <input type="number" name="price" step="0.01" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Zdjęcie:</label>
                    <input type="file" name="image" class="form-input" accept="image/*" required>
                </div>
                <button type="submit" class="btn">Dodaj produkt</button>
            </form>
        </div>

        <div class="admin-section">
            <h2 class="section-title">Lista produktów</h2>
            <div class="products-grid">
                <?php while ($product = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <?php if ($product['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="product-image">
                        <?php endif; ?>
                        <h3 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p class="product-artist"><?php echo htmlspecialchars($product['artist']); ?></p>
                        <p class="product-price"><?php echo number_format($product['price'], 2); ?> PLN</p>
                        <div class="product-actions">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="delete_product" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-delete" onclick="return confirm('Czy na pewno chcesz usunąć ten produkt?')">Usuń</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?> 