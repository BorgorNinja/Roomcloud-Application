<?php
session_start();
include('db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$userFolder = 'uploads/' . $username;

// Ensure the user's folder exists
if (!file_exists($userFolder)) {
    mkdir($userFolder, 0777, true);
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
    $targetFile = $userFolder . '/' . basename($_FILES['fileToUpload']['name']);
    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetFile)) {
        // Insert file details into the database with status 'pending'
        $fileName = basename($_FILES['fileToUpload']['name']);
        $fileSize = $_FILES['fileToUpload']['size'];
        $fileType = $_FILES['fileToUpload']['type'];
        $status = 'pending';

        $query = "INSERT INTO uploads (username, filename, filesize, filetype, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssiss', $username, $fileName, $fileSize, $fileType, $status);
        $stmt->execute();

        $uploadSuccess = true;
    } else {
        $uploadError = "Sorry, there was an error uploading your file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
                        <a class="nav-link" href="dashboard.php">
                            <span data-feather="home"></span>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="uploaded-files.php">
                            <span data-feather="file"></span>
                            Uploaded Files
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="upload.php">
                            <span data-feather="upload"></span>
                            Upload
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <span data-feather="log-out"></span>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Upload File</h1>
            </div>

            <div class="profile-info">
                <span>Welcome, <?php echo htmlspecialchars($username); ?>!</span>
            </div>

            <?php if (isset($uploadSuccess) && $uploadSuccess): ?>
                <div class="alert alert-success" role="alert">
                    File uploaded successfully and pending admin approval!
                </div>
            <?php elseif (isset($uploadError)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($uploadError); ?>
                </div>
            <?php endif; ?>

            <form action="upload.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="fileToUpload">Select file to upload:</label>
                    <input type="file" name="fileToUpload" id="fileToUpload" class="form-control-file">
                </div>
                <button type="submit" class="btn btn-primary">Upload File</button>
            </form>

            <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script>
    feather.replace();
</script>
</body>
</html>
