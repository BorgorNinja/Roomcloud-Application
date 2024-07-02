<?php
if (!isset($_GET['admin_id'])) {
    header("Location: admin-register.php");
    exit();
}

$adminId = $_GET['admin_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Confirmation</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .confirmation-container {
            text-align: center;
            margin-top: 100px;
        }
        .confirmation-container .checkmark {
            font-size: 50px;
            color: green;
        }
    </style>
</head>
<body>
    <div class="container confirmation-container">
        <div class="checkmark">✔</div>
        <h2>Registration Successful</h2>
        <p>Your Admin ID is: <strong><?php echo htmlspecialchars($adminId); ?></strong></p>
        <a href="admin-login.php" class="btn btn-primary">Go to Login</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
