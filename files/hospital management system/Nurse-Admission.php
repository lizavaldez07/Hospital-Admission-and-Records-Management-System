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
<html>
<head>
    <title>Nurse Registration</title>
    <style>
        body {
            background-image: url('REGISTRATION.png'); /* Replace with your image file */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        form {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            width: 500px;
            text-align: center;
        }
        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"], button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="Back"], button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="Back"]:hover, button:hover {
            background-color: #0056b3;
        }
        input[type="submit"]:hover, button:hover {
            background-color: #0056b3;
        }
        .message {
            color: blue;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        h1 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <form action="" method="post">
        <h1>Nurse Registration</h1>

        <input type="hidden" name="NURSE_ID" id="NURSE_ID" value="<?php echo isset($_POST['NURSE_ID']) ? $_POST['NURSE_ID'] : ''; ?>">

        <label for="NURSE_LNAME">Nurse Last Name:</label>
        <input type="text" name="NURSE_LNAME" id="NURSE_LNAME" required value="<?php echo isset($_POST['NURSE_LNAME']) ? $_POST['NURSE_LNAME'] : ''; ?>">

        <label for="NURSE_FNAME">Nurse First Name:</label>
        <input type="text" name="NURSE_FNAME" id="NURSE_FNAME" required value="<?php echo isset($_POST['NURSE_FNAME']) ? $_POST['NURSE_FNAME'] : ''; ?>">

        <label for="NURSE_NUM_STATION">Nurse Station Number:</label>
        <input type="text" name="NURSE_NUM_STATION" id="NURSE_NUM_STATION" required value="<?php echo isset($_POST['NURSE_NUM_STATION']) ? $_POST['NURSE_NUM_STATION'] : ''; ?>">

        <label for="NURSE_SPECIALIZATION">Nurse Specialization:</label>
        <select name="NURSE_SPECIALIZATION" id="NURSE_SPECIALIZATION" required>
            <option value="">Select</option>
            <option value="Casualty" <?php echo (isset($_POST['NURSE_SPECIALIZATION']) && $_POST['NURSE_SPECIALIZATION'] == 'Casualty') ? 'selected' : ''; ?>>Casualty</option>
            <option value="Medical" <?php echo (isset($_POST['NURSE_SPECIALIZATION']) && $_POST['NURSE_SPECIALIZATION'] == 'Medical') ? 'selected' : ''; ?>>Medical</option>
            <option value="Surgery" <?php echo (isset($_POST['NURSE_SPECIALIZATION']) && $_POST['NURSE_SPECIALIZATION'] == 'Surgery') ? 'selected' : ''; ?>>Surgery</option>
            <option value="Maternity" <?php echo (isset($_POST['NURSE_SPECIALIZATION']) && $_POST['NURSE_SPECIALIZATION'] == 'Maternity') ? 'selected' : ''; ?>>Maternity</option>
            <option value="Medicine" <?php echo (isset($_POST['NURSE_SPECIALIZATION']) && $_POST['NURSE_SPECIALIZATION'] == 'Medicine') ? 'selected' : ''; ?>>Medicine</option>
        </select>

        <label for="PATIENT_ID">Patient ID:</label>
        <input type="text" name="PATIENT_ID" id="PATIENT_ID" required value="<?php echo isset($_POST['PATIENT_ID']) ? $_POST['PATIENT_ID'] : ''; ?>">

        <label for="DR_ID">Doctor Assigned ID:</label>
        <input type="text" name="DR_ID" id="DR_ID" required value="<?php echo isset($_POST['DR_ID']) ? $_POST['DR_ID'] : ''; ?>">

        <input type="submit" name="submit" value="Register">
        <input type="button" value="Back" onclick="window.location.href='End-User.php'" />

        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <?php if (!empty($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>
    </form>
</body>
</html>
