<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

if (isset($_POST['filename'])) {
    $username = $_SESSION['username'];
    $userFolder = 'uploads/' . $username;
    $filePath = $userFolder . '/' . $_POST['filename'];

    if (file_exists($filePath)) {
        unlink($filePath);
        echo json_encode(['status' => 'success', 'message' => 'File deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File not found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Filename not specified']);
}
?>