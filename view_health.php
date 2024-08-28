<?php
include("conekt.php");
$sql = "SELECT * FROM health";
$result = mysqli_query($conn_users, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Records</title>
    <link rel="stylesheet" href=".css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }
        table {
            width: 75%;
            border-collapse: collapse;
            margin: 20px auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .delete-link {
            color: red;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>

<center><h1>HOLY SPIRIT Children View</h1></center>
<table>
    <tr>
        <th>Age</th>
        <th>Name</th>
        <th>Gender</th>
        <th>Letter</th>
        <th>Action</th>
    </tr>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['age']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
        echo "<td><a href='uploads/" . htmlspecialchars($row['letter']) . "' download>Download</a></td>";
        echo "<td>
                <a href='delete.php?age=" . htmlspecialchars($row['age']) . "' class='delete-link' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a>
              </td>"; // Delete link with age
        echo "</tr>";
    }
    ?>
</table>
</body>
</html>
