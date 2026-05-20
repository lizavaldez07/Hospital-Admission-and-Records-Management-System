<?php
$user = 'root';
$password = '';
$database = 'Hospital';
$servername = 'localhost:3306';

$mysqli = new mysqli($servername, $user, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    function sanitize_input($mysqli, $data) {
        return mysqli_real_escape_string($mysqli, trim($data));
    }

    // Handle delete action
    if (isset($_POST['delete'])) {
        $dr_id = sanitize_input($mysqli, $_POST['DR_ID']);
        $sql = "DELETE FROM DOCTOR WHERE DR_ID = '$dr_id'";
        $mysqli->query($sql) or die($mysqli->error);
        echo "<script>alert('Doctor record deleted successfully.'); window.location.href='DoctorRecords.php';</script>";
    }
    // Handle update action
    if (isset($_POST['update'])) {
        $dr_id = sanitize_input($mysqli, $_POST['DR_ID']);
        $dr_lname = sanitize_input($mysqli, $_POST['DR_LNAME']);
        $dr_fname = sanitize_input($mysqli, $_POST['DR_FNAME']);
        $dr_cnum = sanitize_input($mysqli, $_POST['DR_CNUM']);
        $dr_specialization = sanitize_input($mysqli, $_POST['DR_SPECIALIZATION']);
        $dr_num_station = sanitize_input($mysqli, $_POST['DR_NUM_STATION']);

        $sql = "UPDATE DOCTOR SET 
                DR_LNAME = '$dr_lname',
                DR_FNAME = '$dr_fname',
                DR_CNUM = '$dr_cnum',
                DR_SPECIALIZATION = '$dr_specialization',
                DR_NUM_STATION = '$dr_num_station'
                WHERE DR_ID = '$dr_id'";
        $mysqli->query($sql) or die($mysqli->error);
        echo "<script>alert('Doctor record updated successfully.'); window.location.href='DoctorRecords.php';</script>";
    }
}

$sql = "SELECT * FROM DOCTOR";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        form {
            display: inline-block;
        }
        input[type="text"],
        select {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            padding: 5px 10px;
            margin-top: 5px;
            background-color: #ffc107;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>
    <h1>Doctor Records</h1>
    <table>
        <tr>
            <th>Doctor ID</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Contact Number</th>
            <th>Specialization</th>
            <th>Station Number</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <form action="DoctorRecords.php" method="post">
                <td>
                    <input type="hidden" name="DR_ID" value="<?php echo htmlspecialchars($row['DR_ID']); ?>">
                    <?php echo htmlspecialchars($row['DR_ID']); ?>
                </td>
                <td><input type="text" name="DR_LNAME" value="<?php echo htmlspecialchars($row['DR_LNAME']); ?>" required></td>
                <td><input type="text" name="DR_FNAME" value="<?php echo htmlspecialchars($row['DR_FNAME']); ?>" required></td>
                <td>
                    <input type="text" name="DR_CNUM" value="<?php echo htmlspecialchars($row['DR_CNUM']); ?>" pattern="\d+" title="Only numbers are allowed" required>
                </td>
                <td><input type="text" name="DR_SPECIALIZATION" value="<?php echo htmlspecialchars($row['DR_SPECIALIZATION']); ?>" required></td>
                <td>
                    <input type="text" name="DR_NUM_STATION" value="<?php echo htmlspecialchars($row['DR_NUM_STATION']); ?>" required>
                </td>
                <td>
                    <input type="submit" name="update" value="Update">
                    <input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure you want to delete this record?');">
                </td>
            </form>
        </tr>
        <?php } ?>
    </table>
</body>
</html>

<?php
$mysqli->close();
?>
