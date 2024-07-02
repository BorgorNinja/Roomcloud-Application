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

// Handle archive action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['archive'])) {
    $fileId = $_POST['file_id'];
    $archiveQuery = "UPDATE uploads SET status='archived' WHERE id=?";
    $archiveStmt = $conn->prepare($archiveQuery);
    $archiveStmt->bind_param('i', $fileId);
    $archiveStmt->execute();
}

// Fetch pending files
$sort_order = isset($_GET['sort']) && $_GET['sort'] == 'asc' ? 'ASC' : 'DESC';
$query = "SELECT * FROM uploads WHERE status='pending' ORDER BY upload_time $sort_order";
$result = $conn->query($query);

// Fetch file statistics
$filesUploadedTodayQuery = "SELECT COUNT(*) as count FROM uploads WHERE DATE(upload_time) = CURDATE()";
$filesUploadedTodayResult = $conn->query($filesUploadedTodayQuery);
$filesUploadedToday = $filesUploadedTodayResult->fetch_assoc()['count'];

$pendingFilesQuery = "SELECT COUNT(*) as count FROM uploads WHERE status='pending'";
$pendingFilesResult = $conn->query($pendingFilesQuery);
$pendingFiles = $pendingFilesResult->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0A7273;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .header .btn {
            background-color: #6D8A89;
            border: none;
            margin-left: 20px;
        }
        .stat {
            text-align: left;
            background-color: #629492;
            padding: 10px;
            border-radius: 5px;
        }
        .stat h3 {
            margin: 0;
        }
        .table-responsive {
            background-color: #1B4848;
            padding: 10px;
            border-radius: 10px;
        }
        .table th, .table td {
            color: white;
        }
        .btn-view {
            background-color: #6D8A89;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="roomcloudlogo.png" height="100"> <h4>Room|Cloud</h4>
            <div>
                <button class="btn btn-primary">Dashboard</button>
                <button class="btn btn-danger" onclick="window.location.href='admin-logout.php'">Logout</button>
            </div>
        </div>
        <div class="row text-center mb-4">
            <div class="col stat">
                <h3>No. of files uploaded today:</h3>
                <h1><?php echo $filesUploadedToday; ?></h1>
            </div>
            <div class="col stat">
                <h3>No. of files pending:</h3>
                <h1><?php echo $pendingFiles; ?></h1>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Search">
            </div>
            <div class="col-md-4">
                <select class="form-control">
                    <option>Sort by</option>
                    <option value="upload_time" onclick="window.location.href='?sort=asc'">Upload Date Ascending</option>
                    <option value="upload_time" onclick="window.location.href='?sort=desc'">Upload Date Descending</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-control">
                    <option>Filter by</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-white table-striped">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>File Type</th>
                        <th>File Size</th>
                        <th>Date Modified</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($file = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['filename']); ?></td>
                            <td><?php echo htmlspecialchars($file['filetype']); ?></td>
                            <td><?php echo number_format($file['filesize'] / 1048576, 2); ?> MB</td>
                            <td><?php echo htmlspecialchars($file['upload_time']); ?></td>
                            <td><?php echo htmlspecialchars($file['username']); ?></td>
                            <td><?php echo htmlspecialchars($file['status']); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="file_id" value="<?php echo $file['id']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                                    
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>