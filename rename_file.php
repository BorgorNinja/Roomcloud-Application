<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username']) || !isset($_POST['old_name']) || !isset($_POST['new_name'])) {
    echo "Invalid request.";
    exit();
}

$username = $_SESSION['username'];
$userFolder = 'uploads/' . $username;
$oldName = $_POST['old_name'];
$newName = $_POST['new_name'];

if (file_exists($userFolder . '/' . $oldName)) {
    rename($userFolder . '/' . $oldName, $userFolder . '/' . $newName);
    echo "File renamed successfully.";
} else {
    echo "File does not exist.";
}
?>
