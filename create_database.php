<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$dbname = "websitedata";
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error;
}

// Use the newly created database
$conn->select_db($dbname);

// Check if 'uploads' table exists
$uploadsTableCheck = "SELECT 1 FROM uploads LIMIT 1";
$uploadsTableExists = $conn->query($uploadsTableCheck);

if ($uploadsTableExists === FALSE) {
    // SQL to create 'uploads' table if it doesn't exist
    $sqlUploads = "CREATE TABLE IF NOT EXISTS uploads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        size INT NOT NULL,
        type VARCHAR(50) NOT NULL,
        status VARCHAR(50) NOT NULL,
        path VARCHAR(255) NOT NULL,
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if ($conn->query($sqlUploads) === TRUE) {
        echo "Table 'uploads' created successfully<br>";
    } else {
        echo "Error creating 'uploads' table: " . $conn->error;
    }
} else {
    echo "Table 'uploads' already exists<br>";
}

$conn->close();
header('Location: dashboard.php');
?>
