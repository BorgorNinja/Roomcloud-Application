<?php
session_start();
include('db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit();
}

// Validate request parameters
if (!isset($_POST['file_id']) || !isset($_POST['comment'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

$fileId = $_POST['file_id'];
$comment = $_POST['comment'];

// Update the comment in the database
$query = "UPDATE uploads SET comment=? WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param('si', $comment, $fileId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Comment updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update comment"]);
}

$stmt->close();
$conn->close();
?>
