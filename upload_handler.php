<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $errors = [];
    $path = 'uploads/';
    $extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];

    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type = $_FILES['file']['type'];
    $file_size = $_FILES['file']['size'];
    
    // Correcting the issue by separating the explode and end functions
    $file_name_parts = explode('.', $_FILES['file']['name']);
    $file_ext = strtolower(end($file_name_parts));

    $file = $path . basename($file_name);

    if (!in_array($file_ext, $extensions)) {
        $errors[] = 'Extension not allowed: ' . $file_name . ' ' . $file_type;
    }

    if ($file_size > 2097152) {
        $errors[] = 'File size exceeds limit: ' . $file_name . ' ' . $file_type;
    }

    if (empty($errors)) {
        if (move_uploaded_file($file_tmp, $file)) {
            $_SESSION['result_message'] = "File uploaded successfully";
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