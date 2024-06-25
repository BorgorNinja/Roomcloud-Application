<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$userFolder = 'Uploaded Files/' . $username . ' Files';

if (isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $filePath = $userFolder . '/' . $file;

    if (file_exists($filePath)) {
        unlink($filePath);
        echo "File deleted successfully.";
    } else {
        echo "File not found.";
    }
} else {
    echo "No file specified.";
}

header("Location: upload.php");
exit();
?>