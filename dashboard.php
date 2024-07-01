<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$username = $_SESSION['username'];
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-size: .875rem;
        }

        main {
            margin-top: 3rem;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 20px 0 0;
            background-color: darkcyan;
            height: 100vh;
        }

        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 20px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
            color: white;
        }

        .nav-link img {
            margin-right: 10px;
        }

        .sidebar .nav-link {
            font-weight: 500;
            color: white;
        }

        .sidebar .nav-link .feather {
            margin-right: 4px;
            color: #999;
        }

        .sidebar .nav-link.active {
            color: #007bff;
        }

        .sidebar .nav-link:hover .feather,
        .sidebar .nav-link.active .feather {
            color: inherit;
        }

        .sidebar-heading {
            font-size: .75rem;
            text-transform: uppercase;
        }

        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
            font-size: 1rem;
            background-color: rgba(0, 0, 0, .25);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
        }

        .navbar .form-control {
            padding: .75rem 1rem;
            border-width: 0;
            border-radius: 0;
        }

        .form-control-dark {
            color: #fff;
            background-color: rgba(255, 255, 255, .1);
            border-color: rgba(255, 255, 255, .1);
        }

        .form-control-dark:focus {
            border-color: transparent;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, .25);
        }

        .search-bar {
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .profile-info {
            display: flex;
            align-items: center;
        }

        .profile-info img {
            border-radius: 50%;
            margin-left: 20px;
        }

        .profile-info span {
            margin-left: 10px;
            font-weight: bold;
        }

        .upload-btn {
            background-color: #b2d8d8;
            border: none;
            border-radius: 5px;
            padding: 10px;
            font-size: .875rem;
            margin-left: 20px;
        }

        .upload-btn:hover {
            background-color: #a0cfcf;
        }

        .welcome-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
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
        <!-- Rest of your dashboard content -->
    </div>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item text-center">
                            <img src="roomcloudlogo.png" alt="Logo" class="sidebar-logo" style="width: 40px; height: auto; vertical-align: middle;">
                            <span style="font-size: 25px; font-family: Roboto;">ROOM | CLOUD</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php" style="color: white;">
                                <i class="bi bi-house-door"></i>
                                My Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="StudentProfile.html" style="color: white;">
                                <i class="bi bi-person"></i>
                                Student Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="uploaded-files.php" style="color: white;">
                                <i class="bi bi-file-earmark"></i>
                                Files
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="status_page.php" style="color: white;">
                                <i class="bi bi-bar-chart"></i>
                                Status
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php" style="color: white;">
                                <i class="bi bi-box-arrow-right"></i>
                                Log out
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1>Dashboard</h1>
                    <div class="profile-info">
                        <img src="https://via.placeholder.com/100" class="rounded-circle mr-2" height="50px" width="50px" alt="Profile Picture">
                        <span><?php echo htmlspecialchars($username); ?></span>
                    </div>
                </div>

                <div class="card mb-4 welcome-card">
                    <div class="card-body">
                        <h2 class="card-title">Welcome Back, <?php echo htmlspecialchars($username); ?>!</h2>
                        <p class="card-text">Check and update your dashboard!</p>
                    </div>
                </div>

                <h3>Status</h3>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card text-white bg-danger mb-3">
                            <div class="card-header">All</div>
                            <div class="card-body">
                                <h5 class="card-title">Upload Today (<?php echo $totalFiles; ?> Files)</h5>
                                <p class="card-text">Total files: <?php echo $totalFiles; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-header">Pending</div>
                            <div class="card-body">
                                <h5 class="card-title">Upload Today (<?php echo $pendingFiles; ?> Files)</h5>
                                <p class="card-text">Pending files: <?php echo $pendingFiles; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-header">Approved</div>
                            <div class="card-body">
                                <h5 class="card-title">Upload Today (<?php echo $approvedFiles; ?> Files)</h5>
                                <p class="card-text">Approved files: <?php echo $approvedFiles; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <h3>Recent Files</h3>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Last Modified</th>
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
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
