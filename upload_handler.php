<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $errors = [];
    $base_path = 'uploads/';
    $extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];

    $username = $_SESSION['username'];
    $user_path = $base_path . $username . '/';

    // Create user-specific folder if it doesn't exist
    if (!is_dir($user_path)) {
        mkdir($user_path, 0777, true);
    }

    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type = $_FILES['file']['type'];
    $file_size = $_FILES['file']['size'];
    
    // Correcting the issue by separating the explode and end functions
    $file_name_parts = explode('.', $_FILES['file']['name']);
    $file_ext = strtolower(end($file_name_parts));

    $file = $user_path . basename($file_name);

    if (!in_array($file_ext, $extensions)) {
        $errors[] = 'Extension not allowed: ' . $file_name . ' ' . $file_type;
    }

    if ($file_size > 2097152) {
        $errors[] = 'File size exceeds limit: ' . $file_name . ' ' . $file_type;
    }

    if (empty($errors)) {
        if (move_uploaded_file($file_tmp, $file)) {
            // Insert file details into the database with status 'pending'
            $status = 'pending';
            $query = "INSERT INTO uploads (username, name, size, type, status, path) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssisss', $username, $file_name, $file_size, $file_type, $status, $file);
            $stmt->execute();

            $_SESSION['result_message'] = "File uploaded successfully and is pending approval";
        } else {
            $_SESSION['result_message'] = "There was an error uploading your file.";
        }
    } else {
        $_SESSION['result_message'] = implode('<br>', $errors);
    }

    header("Location: upload.php");
    exit();
}
?>
