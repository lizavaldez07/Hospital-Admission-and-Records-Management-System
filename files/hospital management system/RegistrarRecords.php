<?php
// Database Configuration
$user = 'root';
$password = '';
$database = 'Hospital';
$servername = 'localhost:3306';

$mysqli = new mysqli($servername, $user, $password, $database);

// Check for connection error
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle Delete Operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $reg_id = intval($_POST['REG_ID']);
    $stmt = $mysqli->prepare("DELETE FROM REGISTRAR WHERE REG_ID = ?");
    $stmt->bind_param("i", $reg_id);

    if ($stmt->execute()) {
        echo "<script>alert('Registrar record deleted successfully!'); window.location.href='RegistrarRecords.php';</script>";
    } else {
        echo "<script>alert('Error deleting record.');</script>";
    }
    $stmt->close();
}

// Handle Update Operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $reg_id = intval($_POST['REG_ID']);
    $reg_lname = trim($_POST['REG_LNAME']);
    $reg_fname = trim($_POST['REG_FNAME']);
    $reg_num_station = trim($_POST['REG_NUM_STATION']);
    $reg_mop = trim($_POST['REG_MOP']);
    $reg_shift = trim($_POST['REG_SHIFT']);
    $patient_id = trim($_POST['PATIENT_ID']);
    $nurse_id = trim($_POST['NURSE_ID']);
    $room_num = trim($_POST['ROOM_NUM']);

    // Validate numeric fields
    if (!ctype_digit($reg_num_station) || !ctype_digit($room_num)) {
        echo "<script>alert('Station Number and Room Number must be numeric.');</script>";
    } else {
        $stmt = $mysqli->prepare(
            "UPDATE REGISTRAR 
            SET REG_LNAME = ?, REG_FNAME = ?, REG_NUM_STATION = ?, REG_MOP = ?, REG_SHIFT = ?, PATIENT_ID = ?, NURSE_ID = ?, ROOM_NUM = ? 
            WHERE REG_ID = ?"
        );
        $stmt->bind_param("sssssiisi", $reg_lname, $reg_fname, $reg_num_station, $reg_mop, $reg_shift, $patient_id, $nurse_id, $room_num, $reg_id);

        if ($stmt->execute()) {
            echo "<script>alert('Registrar record updated successfully!'); window.location.href='RegistrarRecords.php';</script>";
        } else {
            echo "<script>alert('Error updating record.');</script>";
        }
        $stmt->close();
    }
}

// Fetch Registrar Records
$sql = "SELECT * FROM REGISTRAR";
$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrar Records</title>
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
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 5px;
        }
    </style>
</head>
<body>
    <h1>Registrar Records</h1>
    <table>
        <thead>
            <tr>
                <th>Registrar ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Station Number</th>
                <th>Mode of Payment</th>
                <th>Shift</th>
                <th>Patient ID</th>
                <th>Nurse ID</th>
                <th>Room Number</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <form action="RegistrarRecords.php" method="post">
                            <td>
                                <input type="hidden" name="REG_ID" value="<?php echo htmlspecialchars($row['REG_ID']); ?>">
                                <?php echo htmlspecialchars($row['REG_ID']); ?>
                            </td>
                            <td><input type="text" name="REG_LNAME" value="<?php echo htmlspecialchars($row['REG_LNAME']); ?>" required></td>
                            <td><input type="text" name="REG_FNAME" value="<?php echo htmlspecialchars($row['REG_FNAME']); ?>" required></td>
                            <td>
                                <input type="text" name="REG_NUM_STATION" value="<?php echo htmlspecialchars($row['REG_NUM_STATION']); ?>" pattern="\d+" title="Only numbers are allowed" required>
                            </td>
                            <td><input type="text" name="REG_MOP" value="<?php echo htmlspecialchars($row['REG_MOP']); ?>" required></td>
                            <td><input type="text" name="REG_SHIFT" value="<?php echo htmlspecialchars($row['REG_SHIFT']); ?>" required></td>
                            <td>
                                <input type="text" name="PATIENT_ID" value="<?php echo htmlspecialchars($row['PATIENT_ID']); ?>" required>
                            </td>
                            <td>
                                <input type="text" name="NURSE_ID" value="<?php echo htmlspecialchars($row['NURSE_ID']); ?>" required>
                            </td>
                            <td>
                                <input type="text" name="ROOM_NUM" value="<?php echo htmlspecialchars($row['ROOM_NUM']); ?>" pattern="\d+" title="Only numbers are allowed" required>
                            </td>
                            <td class="action-buttons">
                                <input type="submit" name="update" value="Update">
                                <input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure you want to delete this record?');">
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10">No records found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <button onclick="window.location.href='RegistrarAdmission.php';">Back to Admission Form</button>
</body>
</html>
<?php
$mysqli->close();
?>
