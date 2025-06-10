<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "metalvault";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Drop the existing table
$conn->query("DROP TABLE IF EXISTS products");

// Create the table with correct structure
$sql = "CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Products table has been recreated successfully!<br>";
    
    // Verify the structure
    $result = $conn->query("DESCRIBE products");
    echo "<h3>Table Structure:</h3>";
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "Error creating table: " . $conn->error;
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

$conn->close();
?> 