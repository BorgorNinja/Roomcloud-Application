<?php
$servername = "localhost";
$username = "root"; // replace with your database username
$password = ""; // replace with your database password

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS fbmsystem";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db("fbmsystem");

// Create table
$sql = "CREATE TABLE IF NOT EXISTS logindata (
    userID INT(255) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(25) NOT NULL,
    password VARCHAR(25) NOT NULL,
    canRead VARCHAR(5) NOT NULL DEFAULT 'False',
    canWrite VARCHAR(5) NOT NULL DEFAULT 'False'
)";

if ($conn->query($sql) === TRUE) {
    echo "Table logindata created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
