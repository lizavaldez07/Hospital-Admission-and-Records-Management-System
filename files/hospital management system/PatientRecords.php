<?php
session_start();  // Start the session to access session variables

$user = 'root';
$password = '';
$database = 'Hospital';
$servername = 'localhost:3306';

$mysqli = new mysqli($servername, $user, $password, $database);

if ($mysqli->connect_error) {
    die('Connect Error(' . $mysqli->connect_errno . ')' . $mysqli->connect_error);
}

// Check if the user is logged in (i.e., if PATIENT_ID is set in the session)
if (!isset($_SESSION['USER_ID'])) {
    echo "<script>alert('You are not logged in. Please log in first.')"; 
    exit;
}

// Get the logged-in patient ID
$logged_in_patient_id = $_SESSION['USER_ID'];

// Fetch data only for the logged-in patient
$sql = "SELECT * FROM PATIENT WHERE PATIENT_ID = '$logged_in_patient_id'";
$result = $mysqli->query($sql);

if ($result->num_rows === 0) {
    echo "<script>alert('No records found for your ID.'); window.location.href='PatientLogin.php';</script>";
    exit;
}

$row = $result->fetch_assoc();  // Get the patient's record

// Handle the update action
if (isset($_POST['update'])) {
    // Get form data
    $patient_id = $_POST['PATIENT_ID'];
    $patient_type = $_POST['PATIENT_TYPE'];
    $patient_lname = $_POST['PATIENT_LNAME'];
    $patient_fname = $_POST['PATIENT_FNAME'];
    $patient_med_history = $_POST['PATIENT_MED_HISTORY'];
    $patient_admi_time = $_POST['PATIENT_ADMI_TIME'];
    $patient_admi_date = $_POST['PATIENT_ADMI_DATE'];

    // Update query
    $update_sql = "UPDATE PATIENT SET 
                    PATIENT_TYPE = ?, 
                    PATIENT_LNAME = ?, 
                    PATIENT_FNAME = ?, 
                    PATIENT_MED_HISTORY = ?, 
                    PATIENT_ADMI_TIME = ?, 
                    PATIENT_ADMI_DATE = ?
                    WHERE PATIENT_ID = ?";

    $stmt = $mysqli->prepare($update_sql);
    $stmt->bind_param("sssssss", $patient_type, $patient_lname, $patient_fname, $patient_med_history, $patient_admi_time, $patient_admi_date, $patient_id);

    if ($stmt->execute()) {
        echo "<script>alert('Record updated successfully.'); window.location.href='PatientRecords.php';</script>";
    } else {
        echo "<script>alert('Error updating record.');</script>";
    }
    $stmt->close();
}

// Handle the delete action
if (isset($_POST['delete'])) {
    // Get patient ID
    $patient_id = $_POST['PATIENT_ID'];

    // Delete query
    $delete_sql = "DELETE FROM PATIENT WHERE PATIENT_ID = ?";
    $stmt = $mysqli->prepare($delete_sql);
    $stmt->bind_param("s", $patient_id);

    if ($stmt->execute()) {
        echo "<script>alert('Record deleted successfully.'); window.location.href='PatientLogin.php';</script>";
    } else {
        echo "<script>alert('Error deleting record.');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Records</title>
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
    <h1>Patient Records</h1>
    <table>
        <tr>
            <th>Patient ID</th>
            <th>Patient Type</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Medical History</th>
            <th>Admission Time</th>
            <th>Admission Date</th>
            <th>Actions</th>
        </tr>
        <tr>
            <form action="PatientRecords.php" method="post">
                <td><input type="hidden" name="PATIENT_ID" value="<?php echo htmlspecialchars($row['PATIENT_ID']); ?>"><?php echo htmlspecialchars($row['PATIENT_ID']); ?></td>
                <td>
                    <select name="PATIENT_TYPE" id="PATIENT_TYPE">
                        <option value="Urgent" <?php if ($row['PATIENT_TYPE'] == 'Urgent') echo 'selected'; ?>>Urgent</option>
                        <option value="Non-Urgent" <?php if ($row['PATIENT_TYPE'] == 'Non-Urgent') echo 'selected'; ?>>Non-Urgent</option>
                    </select>
                </td>
                <td><input type="text" name="PATIENT_LNAME" value="<?php echo htmlspecialchars($row['PATIENT_LNAME']); ?>"></td>
                <td><input type="text" name="PATIENT_FNAME" value="<?php echo htmlspecialchars($row['PATIENT_FNAME']); ?>"></td>
                <td><input type="text" name="PATIENT_MED_HISTORY" value="<?php echo htmlspecialchars($row['PATIENT_MED_HISTORY']); ?>"></td>
                <td><input type="time" name="PATIENT_ADMI_TIME" value="<?php echo htmlspecialchars($row['PATIENT_ADMI_TIME']); ?>"></td>
                <td><input type="date" name="PATIENT_ADMI_DATE" value="<?php echo htmlspecialchars($row['PATIENT_ADMI_DATE']); ?>"></td>
                <td>
                    <input type="submit" name="update" value="Update">
                    <input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure you want to delete this record?');">
                </td>
            </form>
        </tr>
    </table>
</body>
</html>

<?php
$mysqli->close();
?>
