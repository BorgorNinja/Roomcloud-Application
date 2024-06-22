<?php
$dir = "C:\\xampp\\htdocs\\Files"; // Specify your directory
$pdfFiles = glob($dir . "/*.pdf");
$AllFiles = glob($dir . "/*");


?>

<!DOCTYPE html>


<html lang="en">
   
<head>
    <meta charset="UTF-8">
    <title>PDF List</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <br><br><br>
    
    <!--Show only PDF Files-->
    <div class="leftside">
    <link rel="stylesheet" href="file_dashboard.css">
    <aside>
        <table>
            <tr>
                <th>PDF Files</th>
            </tr>
            <?php foreach ($pdfFiles as $file): ?>
                <tr>
                    <td><?php echo basename($file); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
            </aside>
            </div>

    <!--Show all Files-->
    <div class="rightside">
    <link rel="stylesheet" href="file_dashboard.css">
        <aside>
        <table>
            <tr>
                <th>All Files</th>
            </tr>
            <?php foreach ($AllFiles as $file): ?>
                <tr>
                    <td><?php echo basename($file); ?> </td>
            </tr>
            <?php endforeach; ?>
            </table>
        </div>
            </aside>
</body>
</html>
