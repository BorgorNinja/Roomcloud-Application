<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit();
}

require 'db_connect.php';

// Handle file approval or rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fileId = $_POST['file_id'];
    $action = $_POST['action'];

    $query = "SELECT * FROM uploads WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $fileId);
    $stmt->execute();
    $result = $stmt->get_result();
    $file = $result->fetch_assoc();

    if ($file) {
        $oldFilePath = 'uploads/' . $file['username'] . '/' . $file['filename'];
        $newFilePath = 'uploads/' . $file['username'] . '/' . $file['filename'];

        if ($action == 'approve') {
            // Update the file status to 'approved'
            $updateQuery = "UPDATE uploads SET status='approved' WHERE id=?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('i', $fileId);
            $updateStmt->execute();
        } elseif ($action == 'reject') {
            // Delete the file and its record from the database
            unlink($oldFilePath);
            $deleteQuery = "DELETE FROM uploads WHERE id=?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param('i', $fileId);
            $deleteStmt->execute();
        }
    }
}

// Fetch pending files
$query = "SELECT * FROM uploads WHERE status='pending'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <img src="asset_logo.png" alt="RoomCloud Logo">
                <span>Room|Cloud</span>
            </div>
        </header>
        <main>
            <h1>Admin Dashboard</h1>
            <table>
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($file = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['filename']); ?></td>
                            <td><?php echo htmlspecialchars($file['filetype']); ?></td>
                            <td><?php echo htmlspecialchars($file['filesize']); ?> KB</td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="file_id" value="<?php echo $file['id']; ?>">
                                    <button type="submit" name="action" value="approve">Approve</button>
                                    <button type="submit" name="action" value="reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
        <footer>
            <p>All rights reserved© 2024</p>
        </footer>
    </div>
</body>
</html>
