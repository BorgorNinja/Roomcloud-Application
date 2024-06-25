<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$result_message = "";

// Checking if there is a result message from the upload handler script
if (isset($_SESSION['result_message'])) {
    $result_message = $_SESSION['result_message'];
    unset($_SESSION['result_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <link rel="stylesheet" href="upload.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <img src="logo.png" alt="Logo">
                <span>Room|Cloud</span>
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard.php">My Dashboard</a></li>
                    <li><a href="profile.php">Student Profile</a></li>
                    <li><a href="upload.php">Uploaded Files</a></li>
                    <li><a href="status.php">Status</a></li>
                    <li><a href="logout.php">Log out</a></li>
                </ul>
            </nav>
        </div>
        <main>
            <header>
                <h1>Files</h1>
                <div class="user-profile">
                    <img src="profile.jpg" alt="Profile Picture">
                    <span><?php echo htmlspecialchars($username); ?></span>
                </div>
            </header>
            <section class="upload-section">
                <form action="upload_handler.php" method="post" enctype="multipart/form-data">
                    <label for="file">Drop files here</label>
                    <p>or</p>
                    <input type="file" id="file" name="file">
                    <button type="button" onclick="document.getElementById('file').click();">Browse</button>
                    <button type="submit">Upload</button>
                </form>
            </section>
        </main>
    </div>

    <!-- Overlay Popup -->
    <div class="overlay" id="overlay">
        <div class="popup" id="popup">
            <button class="close-btn" id="close-btn">X</button>
            <p id="popup-message"></p>
            <button class="ok-btn" id="ok-btn">OK</button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const overlay = document.getElementById('overlay');
            const popup = document.getElementById('popup');
            const closeBtn = document.getElementById('close-btn');
            const okBtn = document.getElementById('ok-btn');
            const popupMessage = document.getElementById('popup-message');
            const resultMessage = "<?php echo addslashes($result_message); ?>";

            if (resultMessage) {
                popupMessage.innerText = resultMessage;
                if (resultMessage.includes("successfully")) {
                    popup.classList.add('success');
                } else {
                    popup.classList.add('error');
                }
                overlay.style.display = 'flex';
            }

            closeBtn.addEventListener('click', function() {
                overlay.style.display = 'none';
            });

            okBtn.addEventListener('click', function() {
                overlay.style.display = 'none';
            });
        });
    </script>
</body>
</html>