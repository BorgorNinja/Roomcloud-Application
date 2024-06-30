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

// Check if 'logindata' table exists
$tableCheck = "SELECT 1 FROM logindata LIMIT 1";
$tableExists = $conn->query($tableCheck);

if ($tableExists === FALSE) {
    // SQL to create 'logindata' table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS logindata (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        middle_name VARCHAR(50),
        last_name VARCHAR(50) NOT NULL,
        no_middle_name BOOLEAN DEFAULT FALSE,
        suffix VARCHAR(10),
        no_suffix BOOLEAN DEFAULT FALSE,
        student_number VARCHAR(20) NOT NULL,
        course VARCHAR(50) NOT NULL,
        section VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        contact_number VARCHAR(20) NOT NULL,
        new_password VARCHAR(255) NOT NULL,
        confirm_password VARCHAR(255) NOT NULL
    )";
    if ($conn->query($sql) === TRUE) {
        echo "Table 'logindata' created successfully<br>";
    } else {
        echo "Error creating 'logindata' table: " . $conn->error;
    }
} else {
    echo "Table 'logindata' already exists<br>";
}

// Check if 'staff' table exists
$staffTableCheck = "SELECT 1 FROM staff LIMIT 1";
$staffTableExists = $conn->query($staffTableCheck);

if ($staffTableExists === FALSE) {
    // SQL to create 'staff' table if it doesn't exist
    $sqlstaff = "CREATE TABLE IF NOT EXISTS staff (
        admin_id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        age INT NOT NULL,
        date_of_birth VARCHAR(20) NOT NULL,
        password VARCHAR(255) NOT NULL,
        confirm_password VARCHAR(255) NOT NULL
    )";
    if ($conn->query($sqlstaff) === TRUE) {
        echo "Table 'staff' created successfully<br>";
    } else {
        echo "Error creating 'staff' table: " . $conn->error;
    }
} else {
    echo "Table 'staff' already exists<br>";
}

$conn->close();
header('Location: dashboard.php');
?>
