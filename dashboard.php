<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT student_number FROM logindata WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($student_number);
$stmt->fetch();
$stmt->close();

$userFolder = 'uploads/' . $student_number;

// Check if user folder exists, if not, create it
if (!file_exists($userFolder)) {
    mkdir($userFolder, 0777, true);
}

$files = array_diff(scandir($userFolder), array('.', '..'));

$totalFiles = count($files);
$pendingFiles = 0; // Example logic for pending files
$approvedFiles = 0; // Example logic for approved files

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoomCloud Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 56px;
        }
        .sidebar {
            height: 100%;
            width: 200px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: #333;
            display: block;
        }
        .sidebar a:hover {
            background-color: #ddd;
        }
        .main-content {
            margin-left: 210px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">RoomCloud</a>
        <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="logout.php">Sign out</a>
            </li>
        </ul>
    </nav>

    <div class="sidebar">
        <a href="#">Dashboard</a>
        <a href="#">My Files</a>
    </div>

    <div class="main-content">
        <h2>Dashboard</h2>
        <div class="row">
            <div class="col-sm-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">All</div>
                    <div class="card-body">
                        <h5 class="card-title">Upload Today (<?php echo $totalFiles; ?> Files)</h5>
                        <p class="card-text">Total files: <?php echo $totalFiles; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Pending</div>
                    <div class="card-body">
                        <h5 class="card-title">Upload Today (<?php echo $pendingFiles; ?> Files)</h5>
                        <p class="card-text">Pending files: <?php echo $pendingFiles; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Approved</div>
                    <div class="card-body">
                        <h5 class="card-title">Upload Today (<?php echo $approvedFiles; ?> Files)</h5>
                        <p class="card-text">Approved files: <?php echo $approvedFiles; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <h3>Recent Files</h3>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Last Modified</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                    <?php $filePath = $userFolder . '/' . $file; ?>
                    <tr>
                        <td><?php echo htmlspecialchars($file); ?></td>
                        <td><?php echo htmlspecialchars(pathinfo($filePath, PATHINFO_EXTENSION)); ?></td>
                        <td><?php echo round(filesize($filePath) / 1024 / 1024, 2) . ' MB'; ?></td>
                        <td><?php echo date("M d, Y", filemtime($filePath)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
