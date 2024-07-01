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
    echo json_encode(["status" => "error", "message" => "Filename not specified"]);
    exit();
}

$username = $_SESSION['username'];
$filename = $_GET['file'];

$filePath = 'uploads/' . $username . '/' . $filename;

// Delete the file
if (file_exists($filePath)) {
    if (unlink($filePath)) {
        // Remove the file record from the database
        $query = "DELETE FROM uploads WHERE username=? AND filename=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $username, $filename);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "File deleted successfully"]);
            header('Location: uploaded-files.php')
        } else {
            echo json_encode(["status" => "error", "message" => "Database update failed"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "File deletion failed"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "File not found"]);
}

$stmt->close();
$conn->close();
exit();
?>
