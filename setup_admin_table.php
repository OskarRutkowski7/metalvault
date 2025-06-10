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

// Read and execute SQL file
$sql = file_get_contents('create_admins_table.sql');

if ($conn->multi_query($sql)) {
    echo "Admin table created successfully!<br>";
    echo "Default admin credentials:<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?> 