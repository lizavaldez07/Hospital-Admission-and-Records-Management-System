<?php
$user = 'root';
$password = '';
$database = 'Hospital';
$servername = 'localhost:3306';

$mysqli = new mysqli($servername, $user, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nurse_id = isset($_POST['NURSE_ID']) ? $_POST['NURSE_ID'] : null;
    $nurse_lname = $mysqli->real_escape_string(trim($_POST['NURSE_LNAME']));
    $nurse_fname = $mysqli->real_escape_string(trim($_POST['NURSE_FNAME']));
    $nurse_num_station = $mysqli->real_escape_string(trim($_POST['NURSE_NUM_STATION']));
    $nurse_specialization = $mysqli->real_escape_string(trim($_POST['NURSE_SPECIALIZATION']));
    $patient_id = $mysqli->real_escape_string(trim($_POST['PATIENT_ID']));
    $dr_id = $mysqli->real_escape_string(trim($_POST['DR_ID']));

    // Validate that Patient ID and Doctor ID exist
    $patient_exists = $mysqli->query("SELECT 1 FROM PATIENT WHERE PATIENT_ID = '$patient_id'")->num_rows > 0;
    $dr_exists = $mysqli->query("SELECT 1 FROM DOCTOR WHERE DR_ID = '$dr_id'")->num_rows > 0;

    // Check for duplicate pairing of PATIENT_ID and DR_ID in the NURSE table
    $duplicate_check = $mysqli->query("SELECT 1 FROM NURSE WHERE PATIENT_ID = '$patient_id' AND DR_ID = '$dr_id'")->num_rows > 0;

    if ($duplicate_check) {
        $error = "Error: This Patient ID and Doctor ID pair is already assigned to a nurse.";
    } elseif (!$patient_exists) {
        $error = "Error: Patient ID '$patient_id' does not exist.";
    } elseif (!$dr_exists) {
        $error = "Error: Doctor ID '$dr_id' does not exist.";
    } else {
        if ($nurse_id) {
            // Update existing record
            $stmt = $mysqli->prepare("
                UPDATE NURSE 
                SET NURSE_LNAME = ?, NURSE_FNAME = ?, NURSE_NUM_STATION = ?, 
                    NURSE_SPECIALIZATION = ?, PATIENT_ID = ?, DR_ID = ?
                WHERE NURSE_ID = ?
            ");
            $stmt->bind_param(
                "ssssssi", 
                $nurse_lname, $nurse_fname, $nurse_num_station, 
                $nurse_specialization, $patient_id, $dr_id, $nurse_id
            );

            if ($stmt->execute()) {
                $message = "Nurse details updated successfully!";
            } else {
                $error = "Error: " . $stmt->error;
            }
        } else {
            // Insert new record
            $stmt = $mysqli->prepare("
                INSERT INTO NURSE (NURSE_LNAME, NURSE_FNAME, NURSE_NUM_STATION, 
                                   NURSE_SPECIALIZATION, PATIENT_ID, DR_ID) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssssss", 
                $nurse_lname, $nurse_fname, $nurse_num_station, 
                $nurse_specialization, $patient_id, $dr_id
            );

            if ($stmt->execute()) {
                echo "<script>alert('Nurse registered successfully!');</script>";
            } else {
                echo "Error: " . $stmt->error;
            }
        }

        $stmt->close();
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Registration</title>
    <style>
        /* Modernized UI with Light Blue Theme */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e3f2fd; /* Light blue background */
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            box-sizing: border-box;
        }

        h1 {
            font-size: 26px;
            color: #1565c0; /* Deep blue for the title */
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            color: #546e7a;
            margin-bottom: 5px;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border: 2px solid #bbdefb;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.3s;
        }

        input:focus, select:focus {
            border-color: #1976d2;
        }

        /* PHP Message Styling */
        .message {
            background-color: #e3f2fd;
            color: #1565c0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
        }

        .error {
            background-color: #ffcdd2;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }

        input[type="submit"], 
        input[type="button"] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        /* Submit Button - Primary Blue */
        input[type="submit"] {
            background-color: #1976d2;
            color: white;
        }

        input[type="submit"]:hover {
            background-color: #1565c0;
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
        }

        /* Back Button - Subtle Outline */
        input[type="button"] {
            background-color: #f5f5f5;
            color: #1976d2;
            border: 1px solid #1976d2;
        }

        input[type="button"]:hover {
            background-color: #e3f2fd;
        }

        footer {
            margin-top: 20px;
            color: #78909c;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h1>Nurse Registration</h1>

        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <?php if (!empty($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <form action="" method="post">
            <input type="hidden" name="NURSE_ID" id="NURSE_ID" value="<?php echo isset($_POST['NURSE_ID']) ? $_POST['NURSE_ID'] : ''; ?>">

            <label for="NURSE_FNAME">First Name</label>
            <input type="text" name="NURSE_FNAME" id="NURSE_FNAME" required value="<?php echo isset($_POST['NURSE_FNAME']) ? $_POST['NURSE_FNAME'] : ''; ?>">

            <label for="NURSE_LNAME">Last Name</label>
            <input type="text" name="NURSE_LNAME" id="NURSE_LNAME" required value="<?php echo isset($_POST['NURSE_LNAME']) ? $_POST['NURSE_LNAME'] : ''; ?>">

            <label for="NURSE_NUM_STATION">Station Number</label>
            <input type="text" 
                name="NURSE_NUM_STATION" 
                id="NURSE_NUM_STATION" 
                required 
                pattern="\d{6}" 
                title="Station Number must be exactly 6 digits" 
                placeholder="6-digit station ID"
                value="<?php echo isset($_POST['NURSE_NUM_STATION']) ? $_POST['NURSE_NUM_STATION'] : ''; ?>">

            <label for="NURSE_SPECIALIZATION">Specialization</label>
            <select name="NURSE_SPECIALIZATION" id="NURSE_SPECIALIZATION" required>
                <option value="">Select Ward</option>
                <option value="Casualty" <?php echo (isset($_POST['NURSE_SPECIALIZATION']) && $_POST['NURSE_SPECIALIZATION'] == 'Casualty') ? 'selected' : ''; ?>>Casualty</option>
                <option value="Medical" <?php echo (isset($_POST['NURSE_SPECIALIZATION']) && $_POST['NURSE_SPECIALIZATION'] == 'Medical') ? 'selected' : ''; ?>>Medical</option>
                <option value="Surgery" <?php echo (isset($_POST['NURSE_SPECIALIZATION']) && $_POST['NURSE_SPECIALIZATION'] == 'Surgery') ? 'selected' : ''; ?>>Surgery</option>
                <option value="Maternity" <?php echo (isset($_POST['NURSE_SPECIALIZATION']) && $_POST['NURSE_SPECIALIZATION'] == 'Maternity') ? 'selected' : ''; ?>>Maternity</option>
                <option value="Medicine" <?php echo (isset($_POST['NURSE_SPECIALIZATION']) && $_POST['NURSE_SPECIALIZATION'] == 'Medicine') ? 'selected' : ''; ?>>Medicine</option>
            </select>

            <label for="PATIENT_ID">Assigned Patient ID</label>
            <input type="text" name="PATIENT_ID" id="PATIENT_ID" required placeholder="Enter Patient ID" value="<?php echo isset($_POST['PATIENT_ID']) ? $_POST['PATIENT_ID'] : ''; ?>">

            <label for="DR_ID">Assigned Doctor ID</label>
            <input type="text" name="DR_ID" id="DR_ID" required placeholder="Enter Doctor ID" value="<?php echo isset($_POST['DR_ID']) ? $_POST['DR_ID'] : ''; ?>">

            <div class="button-group">
                <input type="submit" name="submit" value="Register Nurse">
                <input type="button" value="Back to Dashboard" onclick="window.location.href='End-User.php'" />
            </div>
        </form>
    </div>

    <footer>
        &copy; 2024 Hospital Management System
    </footer>

</body>
</html>
