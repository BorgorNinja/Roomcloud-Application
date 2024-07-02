<?php
include('db_connect.php');

// Create logindata table
$createLoginDataTable = "CREATE TABLE IF NOT EXISTS logindata (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    no_middle_name TINYINT(1) NOT NULL DEFAULT 0,
    last_name VARCHAR(50) NOT NULL,
    suffix VARCHAR(10),
    no_suffix TINYINT(1) NOT NULL DEFAULT 0,
    student_number VARCHAR(50) NOT NULL,
    course VARCHAR(50) NOT NULL,
    section VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    new_password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT 'https://via.placeholder.com/100'
)";
if ($conn->query($createLoginDataTable) === TRUE) {
    echo "Table logindata created successfully\n";
} else {
    echo "Error creating table logindata: " . $conn->error . "\n";
}

// Create logindatastaff table
$createLoginDataStaffTable = "CREATE TABLE IF NOT EXISTS logindatastaff (
    staffID INT(12) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(14),
    password VARCHAR(32)
)";
if ($conn->query($createLoginDataStaffTable) === TRUE) {
    echo "Table logindatastaff created successfully\n";
} else {
    echo "Error creating table logindatastaff: " . $conn->error . "\n";
}

// Create staff table
$createStaffTable = "CREATE TABLE IF NOT EXISTS staff (
    admin_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    age INT(11) NOT NULL,
    date_of_birth VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    confirm_password VARCHAR(255) NOT NULL
)";
if ($conn->query($createStaffTable) === TRUE) {
    echo "Table staff created successfully\n";
} else {
    echo "Error creating table staff: " . $conn->error . "\n";
}

// Create uploads table
$createUploadsTable = "CREATE TABLE IF NOT EXISTS uploads (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    filetype VARCHAR(50) NOT NULL,
    filesize BIGINT(20) NOT NULL,
    upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved') DEFAULT 'pending',
    comment VARCHAR(255)
)";
if ($conn->query($createUploadsTable) === TRUE) {
    echo "Table uploads created successfully\n";
} else {
    echo "Error creating table uploads: " . $conn->error . "\n";
}

// Create files table (if needed)
$createFilesTable = "CREATE TABLE IF NOT EXISTS files (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size BIGINT(20) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved') DEFAULT 'pending'
)";
if ($conn->query($createFilesTable) === TRUE) {
    echo "Table files created successfully\n";
} else {
    echo "Error creating table files: " . $conn->error . "\n";
}

$conn->close();

// Redirect to login.php after tables are created
header("Location: login.php");
exit();
?>
