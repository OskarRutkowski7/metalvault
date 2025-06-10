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

// Create admins table
$sql = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Admins table created successfully<br>";
    
    // Check if default admin exists
    $stmt = $conn->prepare("SELECT id FROM admins WHERE username = 'admin'");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Create default admin user
        $default_username = "admin";
        $default_password = "admin123";
        $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $default_username, $hashed_password);
        
        if ($stmt->execute()) {
            echo "Default admin user created successfully<br>";
            echo "Username: admin<br>";
            echo "Password: admin123<br>";
        } else {
            echo "Error creating default admin user: " . $stmt->error . "<br>";
        }
    } else {
        echo "Default admin user already exists<br>";
    }
} else {
    echo "Error creating admins table: " . $conn->error . "<br>";
}

$conn->close();
?> 