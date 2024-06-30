<?php
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = $_POST['full_name'];
    $age = $_POST['age'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password != $confirmPassword) {
        echo "Passwords do not match.";
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO staff (full_name, age, date_of_birth, password, confirm_password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $fullName, $age, $dob, $hashedPassword, $hashedPassword);

    if ($stmt->execute()) {
        $adminId = $conn->insert_id;
        header("Location: registration-confirmation.php?admin_id=$adminId");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .modal-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Admin Registration</h2>
        <form method="post" action="admin-register.php">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" class="form-control" id="age" name="age" required>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="text" class="form-control" id="dob" name="dob" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="contract" required>
                <label class="form-check-label" for="contract">I agree to the company's contract</label>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>

    <div class="modal" id="contractModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <h5 class="modal-title">Company Contract</h5>
                <p>Legal contents telling the employee or admin to not disclose information...</p>
                <button type="button" class="btn btn-primary" id="okButton">OK</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#contract').change(function() {
                if (this.checked) {
                    $('#contractModal').modal('show');
                }
            });

            $('#okButton').click(function() {
                $('#contractModal').modal('hide');
            });

            $('#contractModal').on('hidden.bs.modal', function () {
                if (!$('#contract').is(':checked')) {
                    $('#contract').prop('checked', false);
                }
            });
        });
    </script>
</body>
</html>
