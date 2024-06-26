<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username']) || !isset($_POST['name'])) {
    echo "Invalid request.";
    exit();
}

$username = $_SESSION['username'];
$userFolder = 'uploads/' . $username;
$fileName = $_POST['name'];

if (file_exists($userFolder . '/' . $fileName)) {
    unlink($userFolder . '/' . $fileName);
    echo "File deleted successfully.";
} else {
    echo "File does not exist.";
}
?>
