<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$username = $_SESSION['username'];
$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';

// Fetch the first name from the database if not already in session
if (empty($first_name)) {
    $stmt = $conn->prepare("SELECT first_name FROM logindata WHERE email = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($first_name);
    $stmt->fetch();
    $stmt->close();
    $_SESSION['first_name'] = $first_name;
}

$userFolder = 'uploads/' . $username;

// Check if user folder exists, if not, create it
if (!file_exists($userFolder)) {
    mkdir($userFolder, 0777, true);
}

// Retrieve all files for the user
$query = "SELECT COUNT(*) as total_files FROM uploads WHERE username=?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($totalFiles);
$stmt->fetch();
$stmt->close();

// Retrieve pending and approved files counts
$query = "SELECT status, COUNT(*) as count FROM uploads WHERE username=? GROUP BY status";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$pendingFiles = 0;
$approvedFiles = 0;
while ($row = $result->fetch_assoc()) {
    if ($row['status'] == 'pending') {
        $pendingFiles = $row['count'];
    } elseif ($row['status'] == 'approved') {
        $approvedFiles = $row['count'];
    }
}

// Retrieve rejected files
$query = "SELECT filename FROM uploads WHERE username=? AND status='rejected'";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$rejectedFiles = $result->fetch_all(MYSQLI_ASSOC);

// Retrieve all files for the user to display
$query = "SELECT filename, filetype, filesize, upload_time FROM uploads WHERE username=?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$files = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: .875rem;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 240px;
            background-color: darkcyan;
            color: #fff;
            z-index: 1000;
        }

        .sidebar a {
            color: #fff;
            padding: 10px 15px;
            display: block;
            text-decoration: none;
        }

        .main-content {
            margin-left: 240px;
            padding: 20px;
        }

        .status-card {
            color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .status-card.red {
            background-color: #ff6b6b;
        }

        .status-card.yellow {
            background-color: #ffe66d;
        }

        .status-card.blue {
            background-color: #4ecdc4;
        }

        .table {
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .profile-info {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .profile-info img {
            border-radius: 50%;
            margin-left: 10px;
        }

        .profile-info span {
            margin-left: 10px;
            font-weight: bold;
        }

        .welcome-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            background-color: lightblue;
        }

    </style>
</head>
<body>
    <div class="sidebar">
        <h3 class="text-center text-white mt-3 mb-3">
            <img src="roomcloudlogo.png" alt="Logo" style="width: 40px; height: auto; vertical-align: middle; margin-right: -10px; margin-left: -10px; margin-top: -10px;">
            ROOM | CLOUD
        </h3>
        <a href="dashboard.php">My Dashboard</a>
        <a href="studentprofile.php">Student Profile</a>
        <a href="uploaded-files.php">Files</a>
        <a href="status_page.php">Status</a>
        <a href="logout.php">Log out</a>
    </div>
    <div class="main-content">
        <div class="profile-info">
            <span><?php echo htmlspecialchars($first_name); ?></span>
            <img src="https://via.placeholder.com/100" class="rounded-circle" height="50px" width="50px" alt="Profile Picture">
        </div>

        <br>

        <div class="card mb-4 welcome-card">
            <div class="card-body">
                <h2 class="card-title">Welcome Back, <?php echo htmlspecialchars($first_name); ?>!</h2>
                <p class="card-text">Check and update your dashboard!</p>
            </div>
        </div>

        <?php if (!empty($rejectedFiles)): ?>
            <div class="alert alert-warning" role="alert">
                <?php foreach ($rejectedFiles as $file): ?>
                    <p><?php echo htmlspecialchars($file['filename']); ?> has been rejected by the admin.</p>
                <?php endforeach; ?>
            </div>
            <script>
                alert('One or more of your files have been rejected by the admin.');
            </script>
        <?php endif; ?>

        <div class="d-flex justify-content-around">
            <div class="status-card red">
                <h3>All</h3>
                <p>Upload Today (<?php echo $totalFiles; ?> Files)</p>
            </div>
            <div class="status-card yellow">
                <h3>Pending</h3>
                <p>Pending Today (<?php echo $pendingFiles; ?> Files)</p>
            </div>
            <div class="status-card blue">
                <h3>Approved</h3>
                <p>Approved Today (<?php echo $approvedFiles; ?> Files)</p>
            </div>
        </div>

        <h2>Recent Files</h2>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Upload Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($files)): ?>
                    <?php foreach ($files as $file): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['filename']); ?></td>
                            <td><?php echo htmlspecialchars($file['filetype']); ?></td>
                            <td><?php echo htmlspecialchars($file['filesize']); ?> KB</td>
                            <td><?php echo htmlspecialchars($file['upload_time']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No files found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
