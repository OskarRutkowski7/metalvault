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

// Check if table exists
$result = $conn->query("SHOW TABLES LIKE 'products'");
if ($result->num_rows == 0) {
    echo "Products table does not exist. Creating it now...<br>";
    
    // Create the table
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
        echo "Products table created successfully!<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
} else {
    echo "Products table exists. Checking structure...<br>";
    
    // Get table structure
    $result = $conn->query("DESCRIBE products");
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
}

$conn->close();
?> 