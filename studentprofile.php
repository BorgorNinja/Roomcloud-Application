<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$username = $_SESSION['username'];
$profilePicturePath = "uploads/profile_pictures/$username.jpg"; // Path to save profile pictures

// Verify database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student information from logindata table (case-insensitive query)
$query = "SELECT * FROM logindata WHERE LOWER(email) = LOWER(?)";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result === false) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}
$student = $result->fetch_assoc();
$stmt->close();

// Update student information
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);
    $middleName = htmlspecialchars($_POST['middle_name']);
    $suffix = htmlspecialchars($_POST['suffix']);
    $contactNumber = htmlspecialchars($_POST['contact_number']);
    $course = htmlspecialchars($_POST['course']);
    $section = htmlspecialchars($_POST['section']);

    $updateQuery = "UPDATE logindata SET first_name=?, last_name=?, middle_name=?, suffix=?, contact_number=?, course=?, section=? WHERE LOWER(email)=LOWER(?)";
    $updateStmt = $conn->prepare($updateQuery);
    if ($updateStmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $updateStmt->bind_param('ssssssss', $firstName, $lastName, $middleName, $suffix, $contactNumber, $course, $section, $username);
    $updateStmt->execute();
    $updateStmt->close();

    // Refresh student data
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result === false) {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    }
    $student = $result->fetch_assoc();
    $stmt->close();
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $targetDir = "uploads/profile_pictures/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $targetFile = $targetDir . basename($_FILES['profile_picture']['name']);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $newFileName = $username . '.' . $imageFileType;

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES['profile_picture']['tmp_name']);
    if ($check !== false) {
        // Allow certain file formats
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetDir . $newFileName)) {
                // Resize image to 100x100
                $imageResource = null;
                switch ($imageFileType) {
                    case 'jpg':
                    case 'jpeg':
                        $imageResource = imagecreatefromjpeg($targetDir . $newFileName);
                        break;
                    case 'png':
                        $imageResource = imagecreatefrompng($targetDir . $newFileName);
                        break;
                    case 'gif':
                        $imageResource = imagecreatefromgif($targetDir . $newFileName);
                        break;
                }
                if ($imageResource) {
                    $resizedImage = imagescale($imageResource, 100, 100);
                    switch ($imageFileType) {
                        case 'jpg':
                        case 'jpeg':
                            imagejpeg($resizedImage, $targetDir . $newFileName);
                            break;
                        case 'png':
                            imagepng($resizedImage, $targetDir . $newFileName);
                            break;
                        case 'gif':
                            imagegif($resizedImage, $targetDir . $newFileName);
                            break;
                    }
                    imagedestroy($imageResource);
                    imagedestroy($resizedImage);
                }
                $profilePicturePath = $targetDir . $newFileName;
            }
        }
    }
}
$conn->close();

if ($student) {
    $fullName = htmlspecialchars($student['last_name'] . ', ' . $student['first_name'] . ' ' . ($student['no_middle_name'] ? '' : $student['middle_name']));
    $studentId = htmlspecialchars($student['student_number']);
    $email = htmlspecialchars($student['email']);
    $contactNumber = htmlspecialchars($student['contact_number']);
    $course = htmlspecialchars($student['course']);
    $section = htmlspecialchars($student['section']);
    $lastName = htmlspecialchars($student['last_name']);
    $firstName = htmlspecialchars($student['first_name']);
    $middleName = htmlspecialchars($student['no_middle_name'] ? '' : $student['middle_name']);
    $suffix = htmlspecialchars($student['no_suffix'] ? '' : $student['suffix']);
} else {
    $fullName = $studentId = $email = $contactNumber = $course = $section = $lastName = $firstName = $middleName = $suffix = 'N/A';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: .875rem;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
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

        .welcome-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: lightblue;
        }

        .card-shadow {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            margin-top: 20px;
        }

        .card-text {
            margin-top: 0px;
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

        .table-shadow {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
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

        .moved-slightly-right {
            margin-left: 15px;
        }
    </style>
    <script>
        function showEditForm() {
            document.getElementById('edit-form').style.display = 'block';
            document.getElementById('view-profile').style.display = 'none';
        }

        function hideEditForm() {
            document.getElementById('edit-form').style.display = 'none';
            document.getElementById('view-profile').style.display = 'block';
        }
    </script>
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
                            <a class="nav-link" href="upload.php" style="color: white;">
                                <img src="/icon-files/without-bg/files.png" width="20" height="20"> Files
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

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div id="view-profile">
                    <div class="profile-info mb-4">
                        <h1>Student Profile</h1>
                    </div>
                    <?php if ($student): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="profile-info">
                                <img src="<?php echo file_exists($profilePicturePath) ? $profilePicturePath : 'https://via.placeholder.com/100'; ?>" class="rounded-circle" height="100px" width="100px" alt="Profile Picture">
                                <div class="moved-slightly-right">
                                    <h3 id="student-name"><?php echo $fullName; ?></h3>
                                    <p id="student-id"><?php echo $studentId; ?></p>
                                    <p id="student-email"><?php echo $email; ?></p>
                                </div>
                                <button class="btn btn-primary ml-auto" onclick="showEditForm()">Edit</button>
                                <form action="studentprofile.php" method="POST" enctype="multipart/form-data" style="margin-left: 20px;">
                                    <input type="file" name="profile_picture" accept="image/*" class="form-control-file">
                                    <button type="submit" class="btn btn-secondary mt-2">Change Profile Picture</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>Student Information</h3>
                            <p><strong>Last Name:</strong> <?php echo $lastName; ?></p>
                            <p><strong>First Name:</strong> <?php echo $firstName; ?></p>
                            <p><strong>Middle Name:</strong> <?php echo $middleName; ?></p>
                            <p><strong>Suffix:</strong> <?php echo $suffix; ?></p>
                            <p><strong>Contact Number:</strong> <?php echo $contactNumber; ?></p>
                            <p><strong>Email Address:</strong> <?php echo $email; ?></p>
                            <p><strong>Course:</strong> <?php echo $course; ?></p>
                            <p><strong>Section:</strong> <?php echo $section; ?></p>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="academic-status-tab" data-toggle="tab" href="#academic-status" role="tab" aria-controls="academic-status" aria-selected="true">Academic Status</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="files-tab" data-toggle="tab" href="#files" role="tab" aria-controls="files" aria-selected="false">Files</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="academic-status" role="tabpanel" aria-labelledby="academic-status-tab">
                                    <h3>Academic Status</h3>
                                    <p>Details about academic status...</p>
                                </div>
                                <div class="tab-pane fade" id="files" role="tabpanel" aria-labelledby="files-tab">
                                    <h3>Files</h3>
                                    <p>Details about files...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-danger" role="alert">
                        No student information found.
                    </div>
                    <?php endif; ?>
                </div>

                <div id="edit-form" style="display: none;">
                    <div class="profile-info mb-4">
                        <h1>Edit Student Profile</h1>
                    </div>
                    <?php if ($student): ?>
                    <form method="POST" action="studentprofile.php">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $firstName; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $lastName; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="middle_name">Middle Name</label>
                                    <input type="text" class="form-control" id="middle_name" name="middle_name" value="<?php echo $middleName; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="suffix">Suffix</label>
                                    <input type="text" class="form-control" id="suffix" name="suffix" value="<?php echo $suffix; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="contact_number">Contact Number</label>
                                    <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?php echo $contactNumber; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="course">Course</label>
                                    <input type="text" class="form-control" id="course" name="course" value="<?php echo $course; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="section">Section</label>
                                    <input type="text" class="form-control" id="section" name="section" value="<?php echo $section; ?>">
                                </div>
                                <button type="submit" class="btn btn-primary" name="update">Save Changes</button>
                                <button type="button" class="btn btn-secondary" onclick="hideEditForm()">Cancel</button>
                            </div>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>