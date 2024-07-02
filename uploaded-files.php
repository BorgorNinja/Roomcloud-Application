<?php
session_start();
include('db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Retrieve the list of approved files
$query = "SELECT id, filename, filetype, filesize, upload_time, comment FROM uploads WHERE username=? AND status='approved'";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$files = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Files</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-size: .875rem;
            background-color: #f8f9fa;
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

        .profile-info {
            display: flex;
            align-items: center;
        }

        .profile-info img {
            border-radius: 50%;
            margin-left: 20px;
        }

        .profile-info span {
            margin-left: 2px;
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

        .table-shadow {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
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

        .modal-content {
            width: 100%;
            height: 100%;
        }

        .modal-body {
            padding: 0;
        }

        .file-viewer {
            width: 100%;
            height: 80vh;
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
                            <a class="nav-link active" href="studentprofile.php" style="color: white;">
                                <img src="/icon-files/without-bg/studentprofile.png" width="20" height="20"> Student Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="uploaded-files.php" style="color: white;">
                                <img src="/icon-files/without-bg/files.png" width="20" height="20"> Uploaded Files
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="status1.php" style="color: white;">
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
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Uploaded Files</h1> <a href="upload.php" class="btn btn-primary">Upload File +</a>
                </div>

                <div class="profile-info">
                    <span>Welcome, <?php echo htmlspecialchars($username); ?>!</span>
                </div>

                <div class="table-responsive table-shadow">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Last Modified</th>
                                <th>Actions</th>
                                <th>Comment</th>
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
                                        <td>
                                            <button class="view-button btn btn-info btn-sm" data-filename="<?php echo htmlspecialchars($file['filename']); ?>" data-filetype="<?php echo htmlspecialchars($file['filetype']); ?>">View File</button>
                                            <a href="uploads/<?php echo htmlspecialchars($username); ?>/<?php echo htmlspecialchars($file['filename']); ?>" download class="btn btn-secondary btn-sm">Download</a>
                                            <button class="rename-button btn btn-secondary btn-sm" data-filename="<?php echo htmlspecialchars($file['filename']); ?>">Rename</button>
                                            <button class="delete-button btn btn-danger btn-sm" data-filename="<?php echo htmlspecialchars($file['filename']); ?>">Delete</button>
                                        </td>
                                        <td>
                                            <input type="text" class="comment-box form-control" placeholder="Add comment" value="<?php echo htmlspecialchars($file['comment']); ?>">
                                            <button class="ok-button btn btn-primary btn-sm" data-fileid="<?php echo $file['id']; ?>">OK</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">No files found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            </main>
        </div>
    </div>

    <!-- Modal for viewing files -->
    <div class="modal fade" id="fileViewerModal" tabindex="-1" role="dialog" aria-labelledby="fileViewerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileViewerModalLabel">View File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="fileViewer" class="file-viewer" style="display: none;"></iframe>
                    <img id="fileImage" class="file-viewer" style="display: none; max-width: 100%;" />
                    <video id="fileVideo" class="file-viewer" controls style="display: none; width: 100%;"></video>
                    <audio id="fileAudio" class="file-viewer" controls style="display: none; width: 100%;"></audio>
                    <pre id="fileText" class="file-viewer" style="display: none; white-space: pre-wrap;"></pre>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        // Handle view file button click
        $('.view-button').click(function() {
            var fileName = $(this).data('filename');
            var fileType = $(this).data('filetype');
            var filePath = 'uploads/<?php echo $username; ?>/' + fileName;
            var fileViewer = $('#fileViewer');
            var fileImage = $('#fileImage');
            var fileVideo = $('#fileVideo');
            var fileAudio = $('#fileAudio');
            var fileText = $('#fileText');

            // Reset viewer
            fileViewer.hide().attr('src', '');
            fileImage.hide().attr('src', '');
            fileVideo.hide().attr('src', '');
            fileAudio.hide().attr('src', '');
            fileText.hide().text('');

            if (fileType === 'application/pdf') {
                fileViewer.show().attr('src', filePath);
            } else if (fileType === 'image/jpeg' || fileType === 'image/png' || fileType === 'image/gif') {
                fileImage.show().attr('src', filePath);
            } else if (fileType === 'video/mp4' || fileType === 'video/m4a' || fileType === 'video/avi' || fileType === 'video/mkv') {
                fileVideo.show().attr('src', filePath);
            } else if (fileType === 'audio/mp3' || fileType === 'audio/wav') {
                fileAudio.show().attr('src', filePath);
            } else if (fileType === 'text/plain') {
                $.get(filePath, function(data) {
                    fileText.show().text(data);
                });
            } else {
                alert('Viewing this file type is not supported.');
            }

            $('#fileViewerModal').modal('show');
        });

        // Handle rename button click
        $('.rename-button').click(function() {
            var fileName = $(this).data('filename');
            var newFileName = prompt('Enter new file name:', fileName);
            if (newFileName !== null && newFileName !== '') {
                window.location.href = 'rename_file.php?old_name=' + encodeURIComponent(fileName) + '&new_name=' + encodeURIComponent(newFileName);
            }
        });

        // Handle delete button click
        $('.delete-button').click(function() {
            var fileName = $(this).data('filename');
            if (confirm('Are you sure you want to delete this file?')) {
                window.location.href = 'delete_file.php?file=' + encodeURIComponent(fileName);
            }
        });

        // Handle comment OK button click
        $('.ok-button').click(function() {
            var fileId = $(this).data('fileid');
            var comment = $(this).siblings('.comment-box').val();
            $.post('update_comment.php', { file_id: fileId, comment: comment }, function(response) {
                if(response.status === 'success') {
                    alert('Comment updated successfully');
                } else {
                    alert('Failed to update comment');
                }
            }, 'json');
        });
    });
    </script>
</body>
</html>
