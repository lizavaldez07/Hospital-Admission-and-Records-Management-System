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

// Check if the user is logged in
if (!isset($_SESSION['USER_ID'])) {
    // Updated to point to your End-User.php
    echo "<script>alert('You are not logged in. Please log in first.'); window.location.href='End-User.php';</script>"; 
    exit;
}

$logged_in_patient_id = $_SESSION['USER_ID'];

// Handle the update action
if (isset($_POST['update'])) {
    $patient_id = $_POST['PATIENT_ID'];
    $patient_type = $_POST['PATIENT_TYPE'];
    // Formatting names for consistency
    $patient_lname = ucwords(strtolower($_POST['PATIENT_LNAME']));
    $patient_fname = ucwords(strtolower($_POST['PATIENT_FNAME']));
    $patient_med_history = $_POST['PATIENT_MED_HISTORY'];
    $patient_admi_time = $_POST['PATIENT_ADMI_TIME'];
    $patient_admi_date = $_POST['PATIENT_ADMI_DATE'];

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

// Fetch current data for the form
$sql = "SELECT * FROM PATIENT WHERE PATIENT_ID = '$logged_in_patient_id'";
$result = $mysqli->query($sql);

if ($result->num_rows === 0) {
    // Updated to point to your End-User.php
    echo "<script>alert('No records found for your ID.'); window.location.href='End-User.php';</script>";
    exit;
}
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Patient Profile</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .header-container {
            width: 95%;
            max-width: 750px;
            background: #ffffff; 
            padding: 20px 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            border-bottom: 4px solid #1976d2;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
            margin-top: 10px;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 750px;
            box-sizing: border-box;
            margin-bottom: 30px;
        }

        h1 { font-size: 24px; color: #1565c0; margin: 0; }
        
        label { 
            display: block; 
            font-size: 11px; 
            font-weight: bold; 
            color: #546e7a; 
            margin-bottom: 8px; 
            margin-top: 20px; 
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        input[type="text"], 
        input[type="date"], 
        input[type="time"], 
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #bbdefb;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
            transition: all 0.3s ease;
        }

        input:focus, select:focus { border-color: #1976d2; background-color: #fff; }

        .readonly-field {
            background-color: #f1f8ff;
            color: #1976d2;
            font-weight: bold;
            border: 2px solid #e3f2fd;
            cursor: not-allowed;
        }

        .input-row { display: flex; gap: 20px; }
        .input-row > div { flex: 1; }

        .btn-update {
            width: 100%;
            padding: 15px;
            margin-top: 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            border-radius: 8px;
            background-color: #1976d2;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-update:hover { 
            background-color: #1565c0; 
            transform: translateY(-1px); 
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
        }

        .btn-back {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            border-radius: 8px;
            background-color: #546e7a;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn-back:hover { background-color: #455a64; }

        footer {
            margin-top: auto;
            padding: 20px;
            color: #90a4ae;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <div>
            <h1>My Patient Records</h1>
            <p style="margin:0; color: #78909c; font-size: 11px; font-weight: bold;">HOSPITAL INFORMATION PORTAL</p>
        </div>
        <a href="End-User.php" class="btn-back">Logout</a>
    </div>

    <div class="form-container">
        <form action="PatientRecords.php" method="post">
            <label>Patient Identification Number</label>
            <input type="text" value="<?php echo htmlspecialchars($row['PATIENT_ID']); ?>" class="readonly-field" readonly>
            <input type="hidden" name="PATIENT_ID" value="<?php echo htmlspecialchars($row['PATIENT_ID']); ?>">

            <div class="input-row">
                <div>
                    <label>First Name</label>
                    <input type="text" name="PATIENT_FNAME" value="<?php echo htmlspecialchars($row['PATIENT_FNAME']); ?>" required>
                </div>
                <div>
                    <label>Last Name</label>
                    <input type="text" name="PATIENT_LNAME" value="<?php echo htmlspecialchars($row['PATIENT_LNAME']); ?>" required>
                </div>
            </div>

            <label>Admission Triage Level</label>
            <select name="PATIENT_TYPE">
                <option value="Urgent" <?php if ($row['PATIENT_TYPE'] == 'Urgent') echo 'selected'; ?>>Urgent</option>
                <option value="Non-Urgent" <?php if ($row['PATIENT_TYPE'] == 'Non-Urgent') echo 'selected'; ?>>Non-Urgent</option>
            </select>

            <label>Medical History & Notes</label>
            <input type="text" name="PATIENT_MED_HISTORY" value="<?php echo htmlspecialchars($row['PATIENT_MED_HISTORY']); ?>" placeholder="Enter previous conditions...">

            <div class="input-row">
                <div>
                    <label>Admission Time</label>
                    <input type="time" name="PATIENT_ADMI_TIME" value="<?php echo htmlspecialchars($row['PATIENT_ADMI_TIME']); ?>">
                </div>
                <div>
                    <label>Admission Date</label>
                    <input type="date" name="PATIENT_ADMI_DATE" value="<?php echo htmlspecialchars($row['PATIENT_ADMI_DATE']); ?>">
                </div>
            </div>

            <input type="submit" name="update" value="Save My Changes" class="btn-update">
        </form>
    </div>

    <footer>
        &copy; 2026 Hospital Management System | Confidential Record Access
    </footer>

</body>
</html>

<?php
$mysqli->close();
?>