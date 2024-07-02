<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$username = $_SESSION['username'];

// Initialize counts
$all_files_count = 0;
$pending_files_count = 0;
$approved_files_count = 0;
$recent_files = [];

// Fetch counts for each status
$query = "SELECT COUNT(*) as count, status FROM uploads WHERE username = ? GROUP BY status";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        switch ($row['status']) {
            case 'pending':
                $pending_files_count = $row['count'];
                break;
            case 'approved':
                $approved_files_count = $row['count'];
                break;
            default:
                $all_files_count += $row['count'];
                break;
        }
    }
}

// Fetch recent files
$query = "SELECT filename, filetype, filesize, upload_time, status FROM uploads WHERE username = ? ORDER BY upload_time DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Convert file size to MB
        $row['filesize'] = number_format($row['filesize'] / 1048576, 2) . ' MB';
        $recent_files[] = $row;
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: .875rem;
            background-color: #f8f9fa;
        }

        main {
            margin-top: 0;
            background-color: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 0;
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

        .status-card {
            border-radius: 10px;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .status-card h5 {
            margin: 0;
        }

        .status-bar {
            height: 10px;
            background-color: white;
            border-radius: 5px;
        }

        .btn-edit {
            float: right;
            margin-top: -10px;
        }

        .btn-filter {
            margin-bottom: 20px;
            background-color: white;
        }

        .table th,
        .table td {
            color: black;
            font-family: 'Times New Roman', Times, serif;
            font-size: 0.875rem;
        }

        .table th {
            background-color: black;
            font-weight: bold;
            padding-left: 5px;
        }

        .table td {
            font-weight: normal;
        }

        .table {
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .navbar-brand {
                width: 100%;
                text-align: center;
            }

            main {
                margin-top: 1rem;
                padding: 0 15px;
            }
        }

        .status-card-rounded {
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item text-center">
                            <img src="roomcloudlogo.png" alt="Logo" class="sidebar-logo" style="width: 40px; height: auto; vertical-align: middle; padding-bottom: 5px;margin-right: -5px;margin-left: -8px;">
                            <span style="font-size: 28px; font-family: Arial">ROOM | CLOUD</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php" style="color: white;">
                                <img src="/icon-files/without-bg/dashboard.png" width="20" height="20"> My Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="studentprofile.php" style="color: white;">
                                <img src="/icon-files/without-bg/studentprofile.png" width="20" height="20"> Student Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="upload.php" style="color: white;">
                                <img src="/icon-files/without-bg/files.png" width="20" height="20"> Files
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="status1.php" style="color: white;">
                                <img src="/icon-files/without-bg/status.png" width="20" height="20"> Status
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php" style="color: white;">
                                <img src="/icon-files/without-bg/logout.png" width="20" height="20"> Log out
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                    <h1 class="h1">Status</h1>
                </div>

                <div class="btn-group btn-filter" role="group" aria-label="Basic example">
                    <button type="button" class="btn btn-secondary" onclick="filterFiles('all')">All</button>
                    <button type="button" class="btn btn-secondary" onclick="filterFiles('pending')">Pending</button>
                    <button type="button" class="btn btn-secondary" onclick="filterFiles('approved')">Approved</button>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="status-card bg-danger">
                            <h5>All</h5>
                            <p>Upload Today (<?php echo $all_files_count; ?> Files)</p>
                            <div class="status-bar"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="status-card bg-warning">
                            <h5>Pending</h5>
                            <p>Pending Today (<?php echo $pending_files_count; ?> Files)</p>
                            <div class="status-bar"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="status-card bg-primary">
                            <h5>Approved</h5>
                            <p>Approved Today (<?php echo $approved_files_count; ?> Files)</p>
                            <div class="status-bar"></div>
                        </div>
                    </div>
                </div>

                <div class="uploads">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="color: white; font-family: arial;">File Name</th>
                                <th style="color: white; font-family: arial;">Type</th>
                                <th style="color: white; font-family: arial;">Size</th>
                                <th style="color: white; font-family: arial;">Date</th>
                                <th style="color: white; font-family: arial;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="file-table">
                            <?php foreach ($recent_files as $file): ?>
                            <tr class="file-row" data-status="<?php echo htmlspecialchars($file['status']); ?>">
                                <td><?php echo htmlspecialchars($file['filename']); ?></td>
                                <td><?php echo htmlspecialchars($file['filetype']); ?></td>
                                <td><?php echo htmlspecialchars($file['filesize']); ?></td>
                                <td><?php echo htmlspecialchars($file['upload_time']); ?></td>
                                <td class="<?php echo ($file['status'] == 'approved') ? 'text-success' : 'text-warning'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($file['status'])); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function filterFiles(status) {
            var rows = document.querySelectorAll('.file-row');
            rows.forEach(row => {
                if (status === 'all') {
                    row.style.display = '';
                } else {
                    if (row.getAttribute('data-status') === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }
    </script>
</body>
</html>
