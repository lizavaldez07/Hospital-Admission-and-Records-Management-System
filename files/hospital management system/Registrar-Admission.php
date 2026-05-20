<!DOCTYPE html>
<html>
<head>
    <title>Registrar Admission</title>
    <style>
        body {
            background-image: url('HospitalAdmissionBG.png'); /* Replace with your image file */
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
    <?php
    $user = 'root';
    $password = '';
    $database = 'Hospital';
    $servername = 'localhost:3306';

    $mysqli = new mysqli($servername, $user, $password, $database);

    // Display messages
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

        // Validation checks
        $validation_error = false;

        // Check foreign key constraints
        $patient_exists = $mysqli->query("SELECT 1 FROM PATIENT WHERE PATIENT_ID = '$patient_id'")->num_rows > 0;
        $nurse_exists = $mysqli->query("SELECT 1 FROM NURSE WHERE NURSE_ID = '$nurse_id'")->num_rows > 0;
        $room_exists = $mysqli->query("SELECT 1 FROM H_ROOM WHERE ROOM_NUM = '$room_num'")->num_rows > 0;

        if (!$patient_exists) {
            $error = "Patient ID $patient_id does not exist.";
            $validation_error = true;
        }
        if (!$nurse_exists) {
            $error = "Nurse ID $nurse_id does not exist.";
            $validation_error = true;
        }
        if (!$room_exists) {
            $error = "Room Number $room_num does not exist.";
            $validation_error = true;
        }

        // Check for duplicate records
        if (!$validation_error) {
            $duplicate_check = $mysqli->query(
                "SELECT 1 FROM REGISTRAR WHERE PATIENT_ID = '$patient_id' AND ROOM_NUM = '$room_num'"
            );

            if ($duplicate_check->num_rows > 0) {
                $error = "Duplicate entry found for Patient ID $patient_id in Room $room_num.";
            } else {
                // Insert data into REGISTRAR table
                $stmt = $mysqli->prepare(
                    "INSERT INTO REGISTRAR 
                        (REG_LNAME, REG_FNAME, REG_NUM_STATION, REG_MOP, REG_SHIFT, PATIENT_ID, NURSE_ID, ROOM_NUM) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->bind_param("ssssssss", $reg_lname, $reg_fname, $reg_num_station, $reg_mop, $reg_shift, $patient_id, $nurse_id, $room_num);

                if ($stmt->execute()) {
                    $message = "Registrar admission successful!";
                } else {
                    $error = "Database error: " . $stmt->error;
                }

                $stmt->close();
            }
        }
    }

    $mysqli->close();
    ?>

    <form action="" method="post">
        <h1>Registrar Admission Form</h1>
        
        <label for="REG_LNAME">Registrar Last Name:</label>
        <input type="text" name="REG_LNAME" id="REG_LNAME" required>

        <label for="REG_FNAME">Registrar First Name:</label>
        <input type="text" name="REG_FNAME" id="REG_FNAME" required>

        <label for="REG_NUM_STATION">Registrar Station Number:</label>
        <input type="text" name="REG_NUM_STATION" id="REG_NUM_STATION" required>

        <label for="REG_MOP">Registrar Mode of Payment:</label>
        <select name="REG_MOP" id="REG_MOP" required>
            <option value="">Select</option>
            <option value="Cash">Cash</option>
            <option value="Credit/Debit Card">Credit/Debit Card</option>
            <option value="Digital Wallet">Digital Wallet</option>
        </select>

        <label for="REG_SHIFT">Registrar Shift:</label>
        <select name="REG_SHIFT" id="REG_SHIFT" required>
            <option value="">Select</option>
            <option value="Day">Day</option>
            <option value="Swing">Swing</option>
            <option value="Graveyard">Graveyard</option>
        </select>

        <label for="PATIENT_ID">Patient ID:</label>
        <input type="text" name="PATIENT_ID" id="PATIENT_ID" required>

        <label for="NURSE_ID">Nurse ID:</label>
        <input type="text" name="NURSE_ID" id="NURSE_ID" required>

        <label for="ROOM_NUM">Room Number:</label>
        <input type="text" name="ROOM_NUM" id="ROOM_NUM" required>

        <input type="submit" name="insert" value="Submit">

        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <?php if (!empty($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>
    </form>
</body>
</html>
