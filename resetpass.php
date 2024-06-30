<?php
session_start();
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate if passwords match
    if ($new_password !== $confirm_password) {
        die("New password and confirm password do not match.");
    }

    // Prepare SQL statement to check old password and update new password
    $stmt = $conn->prepare("SELECT new_password FROM logindata WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verify old password
        if (password_verify($old_password, $hashed_password)) {
            // Hash the new password
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $update_stmt = $conn->prepare("UPDATE logindata SET new_password = ? WHERE email = ?");
            $update_stmt->bind_param("ss", $hashed_new_password, $email);
            if ($update_stmt->execute()) {
                echo "Password updated successfully.";
            } else {
                echo "Error updating password: " . $update_stmt->error;
            }
        } else {
            echo "Old password is incorrect.";
        }
    } else {
        echo "User with this email does not exist.";
    }

    $stmt->close();
    $update_stmt->close();
}

$conn->close();
?>
