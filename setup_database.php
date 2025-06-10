<?php
$servername = "localhost";
$username = "root";
$password = "";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS metalvault";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully<br>";
    } else {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select database
    $conn->select_db("metalvault");
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Users table created successfully<br>";
    } else {
        throw new Exception("Error creating users table: " . $conn->error);
    }
    
    // Create products table
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        artist VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Products table created successfully<br>";
    } else {
        throw new Exception("Error creating products table: " . $conn->error);
    }
    
    // Create cart table
    $sql = "CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Cart table created successfully<br>";
    } else {
        throw new Exception("Error creating cart table: " . $conn->error);
    }
    
    // Insert sample products
    $sql = "INSERT INTO products (title, artist, price, image) VALUES 
        ('Damnation', 'Opeth', 99.99, 'album1.jpg'),
        ('Jeszcze Nie Mamy Na Was Pomysłu', 'Gruzja', 79.99, 'album2.jpg'),
        ('Litourgiya', 'Batushka', 89.99, 'album3.jpg')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Sample products inserted successfully<br>";
    } else {
        throw new Exception("Error inserting sample products: " . $conn->error);
    }
    
    echo "Database setup completed successfully!";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 