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

// Retrieve the list of files
$files = array_diff(scandir($userFolder), array('.', '..'));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Files</title>
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

        .three-dots-menu {
            position: absolute;
            top: 40px;
            right: 10px;
            display: none;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .three-dots-menu a {
            display: block;
            padding: 8px 12px;
            text-decoration: none;
            color: black;
        }

        .three-dots-menu a:hover {
            background-color: #ddd;
        }

        .table thead th {
            position: relative;
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
                            <a class="nav-link active" href="uploaded-files.php">
                                <span data-feather="file"></span>
                                Uploaded Files
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="upload.php">
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
                    <h1 class="h2">Uploaded Files</h1> <a href="upload.php" class="btn btn-primary">Upload File +</a>
                </div>

                <div class="profile-info">
                    <span>Welcome, <?php echo htmlspecialchars($username); ?>!</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>File Name
                                    <button id="three-dots-btn" class="btn btn-link"><span>&#8942;</span></button>
                                    <div id="three-dots-menu" class="three-dots-menu">
                                        <a href="#" id="sort-btn">Sort</a>
                                    </div>
                                </th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Last Modified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($files as $file): ?>
                                <?php $filePath = $userFolder . '/' . $file; ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($file); ?></td>
                                    <td><?php echo htmlspecialchars(pathinfo($filePath, PATHINFO_EXTENSION)); ?></td>
                                    <td><?php echo round(filesize($filePath) / 1024, 2) . ' KB'; ?></td>
                                    <td><?php echo date("M d, Y", filemtime($filePath)); ?></td>
                                    <td>
                                        <button class="rename-button btn btn-secondary btn-sm" data-filename="<?php echo htmlspecialchars($file); ?>">Rename</button>
                                        <button class="delete-button btn btn-danger btn-sm" data-filename="<?php echo htmlspecialchars($file); ?>">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#three-dots-btn').click(function() {
                $('#three-dots-menu').toggleClass('show');
            });

            $('.rename-button').click(function() {
                var filename = $(this).data('filename');
                var newFilename = prompt("Enter new name for the file:", filename);
                if (newFilename !== null && newFilename !== filename) {
                    // Perform rename operation via AJAX
                    $.post('rename_file.php', { old_name: filename, new_name: newFilename }, function(response) {
                        alert(response);
                        location.reload();
                    });
                }
            });

            $('.delete-button').click(function() {
                var filename = $(this).data('filename');
                if (confirm("Are you sure you want to delete this file?")) {
                    // Perform delete operation via AJAX
                    $.post('delete_file.php', { name: filename }, function(response) {
                        alert(response);
                        location.reload();
                    });
                }
            });

            $('#sort-btn').click(function() {
                // Perform sort operation via AJAX or client-side sorting
                alert("Sort function clicked!");
            });
        });
    </script>
</body>
</html>