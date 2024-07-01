<?php
session_start();
include('db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Validate request parameters
if (!isset($_GET['file'])) {
    header("Location: uploaded-files.php?error=Filename not specified");
    exit();
}

$username = $_SESSION['username'];
$file = $_GET['file'];
$userFolder = 'uploads/' . $username . '/';
$filePath = $userFolder . $file;

// Check if the file exists
if (!file_exists($filePath)) {
    header("Location: uploaded-files.php?error=File not found");
    exit();
}

// Delete the file
if (unlink($filePath)) {
    // Delete the file record from the database
    $query = "DELETE FROM uploads WHERE filename=? AND username=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $file, $username);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: uploaded-files.php?success=File deleted successfully");
    } else {
        header("Location: uploaded-files.php?error=Failed to delete file record from database");
    }
} else {
    header("Location: uploaded-files.php?error=Failed to delete file");
}

$stmt->close();
$conn->close();
?>
