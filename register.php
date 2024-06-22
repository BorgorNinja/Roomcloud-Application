<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "websitedata";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    // Select the database
    $conn->select_db($dbname);

    // Create table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS logindata (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        middle_name VARCHAR(50),
        no_middle_name BOOLEAN NOT NULL DEFAULT 0,
        last_name VARCHAR(50) NOT NULL,
        suffix VARCHAR(10),
        no_suffix BOOLEAN NOT NULL DEFAULT 0,
        student_number VARCHAR(50) NOT NULL,
        course VARCHAR(50) NOT NULL,
        section VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        contact_number VARCHAR(20) NOT NULL,
        new_password VARCHAR(255) NOT NULL,
        confirm_password VARCHAR(255) NOT NULL
    )";

    if ($conn->query($sql) !== TRUE) {
        die("Error creating table: " . $conn->error);
    }
} else {
    die("Error creating database: " . $conn->error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $no_middle_name = isset($_POST['no_middle_name']) ? 1 : 0;
    $last_name = $_POST['last_name'];
    $suffix = $_POST['suffix'];
    $no_suffix = isset($_POST['no_suffix']) ? 1 : 0;
    $student_number = $_POST['student_number'];
    $course = $_POST['course'];
    $section = $_POST['section'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    $confirm_password = password_hash($_POST['confirm_password'], PASSWORD_BCRYPT);

    // Insert data into table
    $sql = "INSERT INTO logindata (first_name, middle_name, no_middle_name, last_name, suffix, no_suffix, student_number, course, section, email, contact_number, new_password, confirm_password)
            VALUES ('$first_name', '$middle_name', $no_middle_name, '$last_name', '$suffix', $no_suffix, '$student_number', '$course', '$section', '$email', '$contact_number', '$new_password', '$confirm_password')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close connection
$conn->close();
?>
