<?php
include('db_connect.php');
session_start(); // Ensure session is started

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminId = $_POST['admin_id'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM staff WHERE admin_id = ?");
    $stmt->bind_param("s", $adminId); // Bind the adminId parameter
    $stmt->execute();
    $result = $stmt->get_result(); // Get the result set

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc(); // Fetch the row as an associative array
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true; // Set session variable to indicate logged-in state
            $_SESSION['admin_id'] = $admin['admin_id'];
            header("Location: admin-dashboard.php");
            exit();
        } else {
            $error = "Invalid Admin ID or Password.";
        }
    } else {
        $error = "Invalid Admin ID or Password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Admin Login</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="admin-login.php">
            <div class="form-group">
                <label for="admin_id">Admin ID</label>
                <input type="text" class="form-control" id="admin_id" name="admin_id" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
