<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$username = $_SESSION['username'];
echo "Username from session: " . htmlspecialchars($username) . "<br>";

// Verify database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connected successfully.<br>";
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
$conn->close();

if ($student) {
    echo "Student information found: " . htmlspecialchars(print_r($student, true)) . "<br>";
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
    echo "No student information found in the database.<br>";
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

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .profile-info img {
            border-radius: 50%;
        }

        .profile-info {
            display: flex;
            align-items: center;
        }

        .profile-info span {
            margin-left: 10px;
            font-weight: bold;
        }

    </style>
</head>
<body>
    <div class="sidebar">
        <h3 class="text-center text-white mt-3 mb-3">
            <img src="roomcloudlogo.png" alt="Logo" style="width: 40px; height: auto; vertical-align: middle; margin-right: 10px;">
            ROOM | CLOUD
        </h3>
        <a href="dashboard.php">My Dashboard</a>
        <a href="studentprofile.php">Student Profile</a>
        <a href="uploaded-files.php">Files</a>
        <a href="status_page.php">Status</a>
        <a href="logout.php">Log out</a>
    </div>
    <div class="main-content">
        <div class="container">
            <div class="profile-info mb-4">
                <h1>Student Profile</h1>
            </div>
            <?php if ($student): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="profile-info">
                        <img src="https://via.placeholder.com/100" class="rounded-circle" height="100px" width="100px" alt="Profile Picture">
                        <div>
                            <h3 id="student-name"><?php echo $fullName; ?></h3>
                            <p id="student-id"><?php echo $studentId; ?></p>
                            <p id="student-email"><?php echo $email; ?></p>
                        </div>
                        <button class="btn btn-primary ml-auto">Edit</button>
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
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
