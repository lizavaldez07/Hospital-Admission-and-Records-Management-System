<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Admission</title>
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
            max-width: 550px;
            box-sizing: border-box;
        }

        h1 {
            font-size: 24px;
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
            background-color: #c8e6c9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
        }

        .error {
            background-color: #ffcdd2;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
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
        
        /* Grid for small side-by-side fields */
        .input-row {
            display: flex;
            gap: 15px;
        }
        .input-row > div {
            flex: 1;
        }
    </style>
</head>
<body>

    <?php
    // Original Logic Preserved
    $user = 'root';
    $password = '';
    $database = 'Hospital';
    $servername = 'localhost:3306';

    $mysqli = new mysqli($servername, $user, $password, $database);
    $message = "";
    $error = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert'])) {
        $reg_lname = trim($_POST['REG_LNAME']);
        $reg_fname = trim($_POST['REG_FNAME']);
        $reg_num_station = trim($_POST['REG_NUM_STATION']);
        $reg_mop = trim($_POST['REG_MOP']);
        $reg_shift = trim($_POST['REG_SHIFT']);
        $patient_id = trim($_POST['PATIENT_ID']);
        $nurse_id = trim($_POST['NURSE_ID']);
        $room_num = trim($_POST['ROOM_NUM']);

        $validation_error = false;
        $patient_exists = $mysqli->query("SELECT 1 FROM PATIENT WHERE PATIENT_ID = '$patient_id'")->num_rows > 0;
        $nurse_exists = $mysqli->query("SELECT 1 FROM NURSE WHERE NURSE_ID = '$nurse_id'")->num_rows > 0;
        $room_exists = $mysqli->query("SELECT 1 FROM H_ROOM WHERE ROOM_NUM = '$room_num'")->num_rows > 0;

        if (!$patient_exists) { $error = "Patient ID $patient_id does not exist."; $validation_error = true; }
        elseif (!$nurse_exists) { $error = "Nurse ID $nurse_id does not exist."; $validation_error = true; }
        elseif (!$room_exists) { $error = "Room Number $room_num does not exist."; $validation_error = true; }

        if (!$validation_error) {
            $duplicate_check = $mysqli->query("SELECT 1 FROM REGISTRAR WHERE PATIENT_ID = '$patient_id' AND ROOM_NUM = '$room_num'");
            if ($duplicate_check->num_rows > 0) {
                $error = "Duplicate entry found for Patient ID $patient_id in Room $room_num.";
            } else {
                $stmt = $mysqli->prepare("INSERT INTO REGISTRAR (REG_LNAME, REG_FNAME, REG_NUM_STATION, REG_MOP, REG_SHIFT, PATIENT_ID, NURSE_ID, ROOM_NUM) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssss", $reg_lname, $reg_fname, $reg_num_station, $reg_mop, $reg_shift, $patient_id, $nurse_id, $room_num);
                if ($stmt->execute()) { $message = "Registrar admission successful!"; } 
                else { $error = "Database error: " . $stmt->error; }
                $stmt->close();
            }
        }
    }
    $mysqli->close();
    ?>

    <div class="form-container">
        <h1>Registrar Admission</h1>

        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <?php if (!empty($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <form action="" method="post">
            <div class="input-row">
                <div>
                    <label for="REG_FNAME">First Name</label>
                    <input type="text" name="REG_FNAME" id="REG_FNAME" required placeholder="Registrar First Name">
                </div>
                <div>
                    <label for="REG_LNAME">Last Name</label>
                    <input type="text" name="REG_LNAME" id="REG_LNAME" required placeholder="Registrar Last Name">
                </div>
            </div>

            <label for="REG_NUM_STATION">Station Number</label>
            <input type="text" name="REG_NUM_STATION" id="REG_NUM_STATION" required pattern="\d{6}" 
                title="Station Number must be exactly 6 digits" placeholder="Enter 6-digit station number"
                value="<?php echo isset($_POST['REG_NUM_STATION']) ? $_POST['REG_NUM_STATION'] : ''; ?>">

            <div class="input-row">
                <div>
                    <label for="REG_MOP">Mode of Payment</label>
                    <select name="REG_MOP" id="REG_MOP" required>
                        <option value="">Select</option>
                        <option value="Cash">Cash</option>
                        <option value="Credit/Debit Card">Credit/Debit Card</option>
                        <option value="Digital Wallet">Digital Wallet</option>
                    </select>
                </div>
                <div>
                    <label for="REG_SHIFT">Shift</label>
                    <select name="REG_SHIFT" id="REG_SHIFT" required>
                        <option value="">Select</option>
                        <option value="Day">Day</option>
                        <option value="Swing">Swing</option>
                        <option value="Graveyard">Graveyard</option>
                    </select>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #bbdefb; margin: 10px 0 20px 0;">

            <div class="input-row">
                <div>
                    <label for="PATIENT_ID">Patient ID</label>
                    <input type="text" name="PATIENT_ID" id="PATIENT_ID" required placeholder="PID-XXXX">
                </div>
                <div>
                    <label for="NURSE_ID">Nurse ID</label>
                    <input type="text" name="NURSE_ID" id="NURSE_ID" required placeholder="NID-XXXX">
                </div>
            </div>

            <label for="ROOM_NUM">Room Number</label>
            <input type="text" name="ROOM_NUM" id="ROOM_NUM" required placeholder="Enter Assigned Room Number">

            <div class="button-group">
                <input type="submit" name="insert" value="Complete Admission">
                <input type="button" value="Back to Dashboard" onclick="window.location.href='End-User.php'" />
            </div>
        </form>
    </div>

    <footer>
        &copy; 2024 Hospital Management System
    </footer>

</body>
</html>