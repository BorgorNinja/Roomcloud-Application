<?php
if (isset($_POST['submit'])) {

    //Declare the variables
    $servername = 'localhost';
    $serverusername = 'root';
    $serverpassword = '';
    $serverpassword = '';
    $serverdatabase = 'websitedata';

    $conn = mysqli_connect($servername, $serverusername,$serverpassword, $serverdatabase);
    $username = $_POST['studentID'];
    $password = $_POST['password'];

    //Create the DB and check if it is not created yet
    $sql = "CREATE DATABASE IF NOT EXISTS $serverdatabase";//enter command here if not entered yet;


    //If-else Condition to check if the database does not exist yet
    if (mysqli_query($conn,$sql)) {
        echo "Database Created";
    } 
    else {
        echo "Databse Failed to Create ".mysqli_error($conn);
    }

    $sqlCreateTBStudent = "CREATE TABLE IF NOT EXISTS logindataStudent (studentID int(12) auto_increment, email varchar(14), password varchar(32), primary key(studentID))";
    if (mysqli_query($conn,$sqlCreateTBStudent)) {
        echo " ";
    }
    else {
        echo "Table Creation Error". mysqli_error($conn);
    }
    $sqlCreateTBStaff = "CREATE TABLE IF NOT EXISTS logindataStaff (staffID int(12) auto_increment, email varchar(14),password varchar(32), primary key(staffID))";
    if (mysqli_query($conn,$sqlCreateTBStaff)) {
        echo " ";
    }
    else {
        echo "Table Creation Error". mysqli_error($conn);
    }


    //Login Credentials check
    $sqlLoginCheck = "SELECT studentID, password FROM logindataStudent WHERE studentID=? AND password=?";
    $stmt = mysqli_prepare($conn, $sqlLoginCheck);
    mysqli_stmt_bind_param($stmt, 'ss', $username, $password);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $count = mysqli_stmt_num_rows($stmt);
    
    if ($count > 0) {
        echo "Database check Validated! Welcome $username!";

    } else {
        echo "Wrong Password or E-mail";
    }
}

?>