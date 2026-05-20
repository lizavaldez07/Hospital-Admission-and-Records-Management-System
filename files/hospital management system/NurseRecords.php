<?php
// Database Configuration
$user = 'root';
$password = '';
$database = 'Hospital';
$servername = 'localhost:3306';

$mysqli = new mysqli($servername, $user, $password, $database);

// Check for connection errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle Delete Operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $nurse_id = intval($_POST['NURSE_ID']);
    $stmt = $mysqli->prepare("DELETE FROM NURSE WHERE NURSE_ID = ?");
    $stmt->bind_param("i", $nurse_id);

    if ($stmt->execute()) {
        echo "<script>alert('Nurse record deleted successfully!'); window.location.href='NurseRecords.php';</script>";
    } else {
        echo "<script>alert('Error deleting record: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle Update Operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $nurse_id = intval($_POST['NURSE_ID']);
    $nurse_lname = trim($_POST['NURSE_LNAME']);
    $nurse_fname = trim($_POST['NURSE_FNAME']);
    $nurse_num_station = trim($_POST['NURSE_NUM_STATION']);
    $nurse_specialization = trim($_POST['NURSE_SPECIALIZATION']);
    $patient_id = trim($_POST['PATIENT_ID']);
    $dr_id = trim($_POST['DR_ID']);

    // Validate numeric fields
    if (!ctype_digit($nurse_num_station)) {
        echo "<script>alert('Station Number must be numeric.');</script>";
    } else {
        $stmt = $mysqli->prepare(
            "UPDATE NURSE 
            SET NURSE_LNAME = ?, NURSE_FNAME = ?, NURSE_NUM_STATION = ?, NURSE_SPECIALIZATION = ?, PATIENT_ID = ?, DR_ID = ? 
            WHERE NURSE_ID = ?"
        );
        $stmt->bind_param("sssssii", $nurse_lname, $nurse_fname, $nurse_num_station, $nurse_specialization, $patient_id, $dr_id, $nurse_id);

        if ($stmt->execute()) {
            echo "<script>alert('Nurse record updated successfully!'); window.location.href='NurseRecords.php';</script>";
        } else {
            echo "<script>alert('Error updating record: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}

// Fetch Nurse Records
$sql = "SELECT * FROM NURSE";
$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nurse Records</title>
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
    <h1>Nurse Records</h1>
    <table>
        <thead>
            <tr>
                <th>Nurse ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Station Number</th>
                <th>Specialization</th>
                <th>Patient ID</th>
                <th>Doctor ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <form action="NurseRecords.php" method="post">
                            <td>
                                <input type="hidden" name="NURSE_ID" value="<?php echo htmlspecialchars($row['NURSE_ID']); ?>">
                                <?php echo htmlspecialchars($row['NURSE_ID']); ?>
                            </td>
                            <td><input type="text" name="NURSE_LNAME" value="<?php echo htmlspecialchars($row['NURSE_LNAME']); ?>" required></td>
                            <td><input type="text" name="NURSE_FNAME" value="<?php echo htmlspecialchars($row['NURSE_FNAME']); ?>" required></td>
                            <td>
                                <input type="text" name="NURSE_NUM_STATION" value="<?php echo htmlspecialchars($row['NURSE_NUM_STATION']); ?>" pattern="\d+" title="Only numbers are allowed" required>
                            </td>
                            <td><input type="text" name="NURSE_SPECIALIZATION" value="<?php echo htmlspecialchars($row['NURSE_SPECIALIZATION']); ?>" required></td>
                            <td>
                                <input type="text" name="PATIENT_ID" value="<?php echo htmlspecialchars($row['PATIENT_ID']); ?>" required>
                            </td>
                            <td>
                                <input type="text" name="DR_ID" value="<?php echo htmlspecialchars($row['DR_ID']); ?>" required>
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
                    <td colspan="8">No records found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <button onclick="window.location.href='NurseAdmission.php';">Back to Nurse Admission Form</button>
</body>
</html>
<?php
$mysqli->close();
?>
