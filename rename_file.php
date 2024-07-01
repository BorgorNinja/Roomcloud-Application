<?php
session_start();
include('db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Validate request parameters
if (!isset($_GET['old_name']) || !isset($_GET['new_name'])) {
    echo "Invalid request";
    exit();
}

$username = $_SESSION['username'];
$oldName = $_GET['old_name'];
$newName = $_GET['new_name'];

$oldFilePath = 'uploads/' . $username . '/' . $oldName;
$newFilePath = 'uploads/' . $username . '/' . $newName;

// Rename the file
if (file_exists($oldFilePath)) {
    if (rename($oldFilePath, $newFilePath)) {
        // Update the filename in the database
        $query = "UPDATE uploads SET filename=? WHERE username=? AND filename=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sss', $newName, $username, $oldName);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            echo "File renamed successfully";
        } else {
            echo "Database update failed";
        }
    } else {
        echo "File rename failed";
    }
} else {
    echo "File not found";
}

$stmt->close();
$conn->close();

header("Location: uploaded-files.php");
exit();
?>
