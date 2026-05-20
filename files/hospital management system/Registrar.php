<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient Record</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f1f4f7;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            font-size: 32px;
            color: #2f4f4f;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h1>Edit Registrar Record</h1>
</body>
</html>



<?php

$user = 'root';
$password = '';
$database = 'Hospital';
$servername = 'localhost:3306';

$mysqli = new mysqli($servername, $user, $password, $database);

// Ensure the connection is successful
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}


$REG_LNAME = isset($_POST['REG_LNAME']) ? $_POST['REG_LNAME'] : null;
$REG_FNAME = isset($_POST['REG_FNAME']) ? $_POST['REG_FNAME'] : null;
$REG_NUM_STATION = isset($_POST['REG_NUM_STATION']) ? $_POST['REG_NUM_STATION'] : null;
$REG_MOP = isset($_POST['REG_MOP']) ? $_POST['REG_MOP'] : null;
$REG_SHIFT = isset($_POST['REG_SHIFT']) ? $_POST['REG_SHIFT'] : null;
$PATIENT_ID = isset($_POST['PATIENT_ID']) ? $_POST['PATIENT_ID'] : null;
$NURSE_ID = isset($_POST['NURSE_ID']) ? $_POST['NURSE_ID'] : null;
$ROOM_NUM = isset($_POST['ROOM_NUM']) ? $_POST['ROOM_NUM'] : null;

# --------------------------------------------------------

// Check if the patient is already assigned by another registrar
$check_patient_assigned_sql = "SELECT * FROM Hospital.REGISTRAR WHERE PATIENT_ID = '$PATIENT_ID'";
$result_patient_assigned = mysqli_query($mysqli, $check_patient_assigned_sql);

if (mysqli_num_rows($result_patient_assigned) > 0) {
    echo "<p style='color: red;'>Error: The Patient ID '$PATIENT_ID' is already admitted by another registrar.</p>";
    return; // Stop further processing
}

// Check if the nurse is already assigned by another registrar
$check_nurse_assigned_sql = "SELECT * FROM Hospital.REGISTRAR WHERE NURSE_ID = '$NURSE_ID'";
$result_nurse_assigned = mysqli_query($mysqli, $check_nurse_assigned_sql);

if (mysqli_num_rows($result_nurse_assigned) > 0) {
    echo "<p style='color: red;'>Error: The Nurse ID '$NURSE_ID' is already assigned by another registrar.</p>";
    return; // Stop further processing
}

// Check if the room is already assigned by another registrar
$check_room_assigned_sql = "SELECT * FROM Hospital.REGISTRAR WHERE ROOM_NUM = '$ROOM_NUM'";
$result_room_assigned = mysqli_query($mysqli, $check_room_assigned_sql);

if (mysqli_num_rows($result_room_assigned) > 0) {
    echo "<p style='color: red;'>Error: The Room Number '$ROOM_NUM' is already allocated by another registrar.</p>";
    return; // Stop further processing
}

# -------------------------------------------------

if (isset($_POST['insert'])) {

    // Validate inputs
    $errors = [];

    if (empty($_POST['REG_LNAME'])) {
        $errors[] = "Registrar last name is required.";
    }
    if (empty($_POST['REG_FNAME'])) {
        $errors[] = "Registrar first name is required.";
    }

	if (empty($_POST['REG_NUM_STATION'])) {
        $errors[] = "Registrar station number is required.";
    } else {
        $REG_NUM_STATION = $_POST['REG_NUM_STATION'];
        if (!preg_match('/^R\d{3}$/', $REG_NUM_STATION)) {
            $errors[] = "Registrar station number must start with 'R' and be followed by exactly 3 digits (e.g., R123).";
        }
    }

    if (empty($_POST['REG_MOP'])) {
        $errors[] = "Registrar Mode of Payment is required.";
    }
    if (empty($_POST['REG_SHIFT'])) {
        $errors[] = "Registrar shift is required.";
    }
    if (empty($_POST['PATIENT_ID'])) {
        $errors[] = "Patient ID is required.";
    }
	if (empty($_POST['NURSE_ID'])) {
        $errors[] = "Nurse ID is required.";
    }
	if (empty($_POST['ROOM_NUM'])) {
        $errors[] = "Room number is required.";
    }

    // If there are errors, display them and stop the process
    if (!empty($errors)) {
        echo "<ul style='color: red;'>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        return; // Stop further processing
    }

    // Validate and format form inputs
    $REG_LNAME = ucwords(strtolower($_POST['REG_LNAME']));
    $REG_FNAME = ucwords(strtolower($_POST['REG_FNAME']));
    $REG_NUM_STATION = $_POST['REG_NUM_STATION'];
    $REG_MOP = $_POST['REG_MOP'];
    $REG_SHIFT = $_POST['REG_SHIFT'];
    $PATIENT_ID = $_POST['PATIENT_ID'];
	$NURSE_ID = $_POST['NURSE_ID'];
    $ROOM_NUM = $_POST['ROOM_NUM'];


    // Check if the patient exists
    $check_patient_sql = "SELECT * FROM Hospital.PATIENT WHERE PATIENT_ID = '$PATIENT_ID'";
    $result_patient = mysqli_query($mysqli, $check_patient_sql);

    if (mysqli_num_rows($result_patient) == 0) {
        // If the patient doesn't exist, display an error
        echo "<p style='color: red;'>Error: The Patient ID '$PATIENT_ID' does not exist in the system.</p>";
        return;
    }

    // Check if the nurse exists
    $check_nurse_sql = "SELECT * FROM Hospital.NURSE WHERE NURSE_ID = '$NURSE_ID'";
    $result_nurse = mysqli_query($mysqli, $check_nurse_sql);

    if (mysqli_num_rows($result_nurse) == 0) {
        // If the doctor doesn't exist, display an error
        echo "<p style='color: red;'>Error: The Nurse ID '$NURSE_ID' does not exist in the system.</p>";
        return;
    }

	// Check if the room exists
	$check_room_sql = "SELECT * FROM Hospital.H_ROOM WHERE ROOM_NUM = '$ROOM_NUM'";
	$result_room = mysqli_query($mysqli, $check_room_sql);

	if (mysqli_num_rows($result_room) == 0) {
		// If the doctor doesn't exist, display an error
		echo "<p style='color: red;'>Error: The Room Number '$ROOM_NUM' does not exist in the system.</p>";
		return;
	}

    // Check if the registrar already exists by last name and first name
    $check_registrar_sql = "SELECT * FROM Hospital.REGISTRAR WHERE REG_LNAME = '$REG_LNAME' AND REG_FNAME = '$REG_FNAME'";
    $result_registrar = mysqli_query($mysqli, $check_registrar_sql);

    if (mysqli_num_rows($result_registrar) > 0) {
        // If a duplicate is found, display "Record already exists."
        echo "Record already exists.";
    } else {
        // SQL query to insert a new record (REG_ID is auto-incremented)
        $sql = "INSERT INTO Hospital.REGISTRAR (REG_LNAME, REG_FNAME, REG_NUM_STATION, REG_MOP, REG_SHIFT, PATIENT_ID, NURSE_ID, ROOM_NUM) 
                VALUES ('$REG_LNAME', '$REG_FNAME', '$REG_NUM_STATION', '$REG_MOP', '$REG_SHIFT', '$PATIENT_ID', '$NURSE_ID', '$ROOM_NUM')";

        // Execute the query
        if (mysqli_query($mysqli, $sql)) {
            // Get the newly generated NURSE_ID
            $new_registrar_id = mysqli_insert_id($mysqli);
            echo "Data stored successfully! Generated REG_ID: $new_registrar_id";
        } else {
            echo "Error: " . mysqli_error($mysqli);
        }
    }
}



if (isset($_POST['delete'])) {
    // Get the REG_ID from the form
    $REG_ID = $_POST['REG_ID'];

    // Fetch the related patient and nurse IDs
    $query = "SELECT PATIENT_ID, NURSE_ID FROM Hospital.REGISTRAR WHERE REG_ID = '$REG_ID'";
    $result = mysqli_query($mysqli, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $patient_id = $row['PATIENT_ID'];
        $nurse_id = $row['NURSE_ID'];

        // Delete the patient record
        $delete_patient_sql = "DELETE FROM Hospital.PATIENT WHERE PATIENT_ID = '$patient_id'";
        mysqli_query($mysqli, $delete_patient_sql);

        // Delete the nurse record
        $delete_nurse_sql = "DELETE FROM Hospital.NURSE WHERE NURSE_ID = '$nurse_id'";
        mysqli_query($mysqli, $delete_nurse_sql);

        // Delete the registrar record
        $delete_registrar_sql = "DELETE FROM Hospital.REGISTRAR WHERE REG_ID = '$REG_ID'";
        if (mysqli_query($mysqli, $delete_registrar_sql)) {
            echo "<span style='color: green;'>Registrar record and associated patient/nurse records deleted successfully.</span>";
        } else {
            echo "<span style='color: red;'>Error deleting Registrar record: " . mysqli_error($mysqli) . "</span>";
        }
    } else {
        echo "<span style='color: red;'>No matching Registrar record found.</span>";
    }
}




elseif (isset($_POST['update'])) {

    $REG_ID = isset($_POST['REG_ID']) ? $_POST['REG_ID'] : null;

    // Check if REG_ID is set, else show an error
    if ($REG_ID === null) {
        echo "Error: REG_ID is required to update the record.";
        return;  // Exit the script if REG_ID is not set
    }

    $REG_LNAME = ucwords(strtolower($_POST['REG_LNAME']));
    $REG_FNAME = ucwords(strtolower($_POST['REG_FNAME']));
    $REG_NUM_STATION = $_POST['REG_NUM_STATION'];
    $REG_MOP = $_POST['REG_MOP'];
    $REG_SHIFT = $_POST['REG_SHIFT'];
    $PATIENT_ID = $_POST['PATIENT_ID'];
    $NURSE_ID = $_POST['NURSE_ID'];
    $ROOM_NUM = $_POST['ROOM_NUM'];

    # ------------------------------------------------------
    // Check if required fields are empty
    if (empty($REG_LNAME) || empty($REG_FNAME) || empty($REG_NUM_STATION)) {
        echo "<p style='color: red;'>Error: Last name, first name, and station number are required and cannot be left blank.</p>";
        return; // Stop further processing
    }

    // Check if the patient is already assigned by another registrar
    $check_patient_assigned_sql = "SELECT * FROM Hospital.REGISTRAR WHERE PATIENT_ID = '$PATIENT_ID'";
    $result_patient_assigned = mysqli_query($mysqli, $check_patient_assigned_sql);

    if (mysqli_num_rows($result_patient_assigned) > 0) {
        echo "<p style='color: red;'>Error: The Patient ID '$PATIENT_ID' is already admitted by another registrar.</p>";
        return; // Stop further processing
    }

    // Check if the nurse is already assigned by another registrar
    $check_nurse_assigned_sql = "SELECT * FROM Hospital.REGISTRAR WHERE NURSE_ID = '$NURSE_ID'";
    $result_nurse_assigned = mysqli_query($mysqli, $check_nurse_assigned_sql);

    if (mysqli_num_rows($result_nurse_assigned) > 0) {
        echo "<p style='color: red;'>Error: The Nurse ID '$NURSE_ID' is already assigned by another registrar.</p>";
        return; // Stop further processing
    }

    // Check if the room is already assigned by another registrar
    $check_room_assigned_sql = "SELECT * FROM Hospital.REGISTRAR WHERE ROOM_NUM = '$ROOM_NUM'";
    $result_room_assigned = mysqli_query($mysqli, $check_room_assigned_sql);

    if (mysqli_num_rows($result_room_assigned) > 0) {
        echo "<p style='color: red;'>Error: The Room Number '$ROOM_NUM' is already allocated by another registrar.</p>";
        return; // Stop further processing
    }

    # ----------------------------------------------------------

    // Validate REG_NUM_STATION format (must start with 'R' and be followed by 3 digits)
    if (!preg_match('/^R\d{3}$/', $REG_NUM_STATION)) {
        echo "<span style='color: red;'>Registrar station number must start with 'R' and be followed by exactly 3 digits (e.g., R123).</span><br>";
        return; // Stop further processing if validation fails
    }

    // Check if the patient exists
    $check_patient_sql = "SELECT * FROM Hospital.PATIENT WHERE PATIENT_ID = '$PATIENT_ID'";
    $result_patient = mysqli_query($mysqli, $check_patient_sql);

    if (mysqli_num_rows($result_patient) == 0) {
        echo "<p style='color: red;'>Error: The Patient ID '$PATIENT_ID' does not exist in the system.</p>";
        return;
    }

    // Check if the nurse exists
    $check_nurse_sql = "SELECT * FROM Hospital.NURSE WHERE NURSE_ID = '$NURSE_ID'";
    $result_nurse = mysqli_query($mysqli, $check_nurse_sql);

    if (mysqli_num_rows($result_nurse) == 0) {
        echo "<p style='color: red;'>Error: The Nurse ID '$NURSE_ID' does not exist in the system.</p>";
        return;
    }

    // Check if the room exists
    $check_room_sql = "SELECT * FROM Hospital.H_ROOM WHERE ROOM_NUM = '$ROOM_NUM'";
    $result_room = mysqli_query($mysqli, $check_room_sql);

    if (mysqli_num_rows($result_room) == 0) {
        echo "<p style='color: red;'>Error: The Room Number '$ROOM_NUM' does not exist in the system.</p>";
        return;
    }

    // Check if the name already exists, excluding the current record
    $check_sql = "SELECT * FROM Hospital.REGISTRAR WHERE REG_LNAME = '$REG_LNAME' AND REG_FNAME = '$REG_FNAME' AND REG_ID != '$REG_ID'";
    $result = mysqli_query($mysqli, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        echo "A registrar with this name already exists.";
    } else {
        // Corrected SQL query to update the registrar details
        $update_sql = "UPDATE Hospital.REGISTRAR SET 
                        REG_LNAME = '$REG_LNAME',
                        REG_FNAME = '$REG_FNAME',
                        REG_NUM_STATION = '$REG_NUM_STATION',
                        REG_MOP = '$REG_MOP',
                        REG_SHIFT = '$REG_SHIFT',
                        PATIENT_ID = '$PATIENT_ID',
                        NURSE_ID = '$NURSE_ID',
                        ROOM_NUM = '$ROOM_NUM'
                        WHERE REG_ID = '$REG_ID'";  // Make sure you're updating the correct record

        if (mysqli_query($mysqli, $update_sql)) {
            echo "Record updated successfully!";
        } else {
            echo "Error: " . mysqli_error($mysqli);
        }
    }
}





if (isset($_POST['edit'])) {
    $REG_ID = $_POST['REG_ID'];

    // Fetch the patient's details from the database
    $sql = "SELECT * FROM Hospital.REGISTRAR WHERE REG_ID = '$REG_ID'";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        ?>

        <!-- Display Update Form -->
        <form action="Registrar.php" method="post">
            <input type="hidden" name="REG_ID" value="<?php echo $row['REG_ID']; ?>">

			<label for="REG_LNAME">Last Name:</label><br>
            <input type="text" name="REG_LNAME" value="<?php echo $row['REG_LNAME']; ?>"><br>

            <label for="REG_FNAME">First Name:</label><br>
            <input type="text" name="REG_FNAME" value="<?php echo $row['REG_FNAME']; ?>"><br>

			<label for="REG_NUM_STATION"> Registrar Station Number:</label><br>
            <input type="text" name="REG_NUM_STATION" value="<?php echo $row['REG_NUM_STATION']; ?>"><br>

			<label for="REG_MOP">Registrar Mode of Payment</label><br>
			<select name="REG_MOP">
				<option value="Cash" <?php if ($row['REG_MOP'] == 'Cash') echo 'selected'; ?>>Cash</option>
				<option value="Credit Card" <?php if ($row['REG_MOP'] == 'Credit Card') echo 'selected'; ?>>Credit Card</option>
				<option value="Debit Card" <?php if ($row['REG_MOP'] == 'Debit Card') echo 'selected'; ?>>Debit Card</option>
				<option value="Digital Wallet" <?php if ($row['REG_MOP'] == 'Digital Wallet') echo 'selected'; ?>>Digital Wallet</option>
			</select>

			<br>

			<label for="REG_SHIFT">Registrar Shift</label><br>
			<select name="REG_SHIFT">
				<option value="Day" <?php if ($row['REG_SHIFT'] == 'Day') echo 'selected'; ?>>Day</option>
				<option value="Swing" <?php if ($row['REG_SHIFT'] == 'Swing') echo 'selected'; ?>>Swing</option>
				<option value="Graveyard" <?php if ($row['REG_SHIFT'] == 'Graveyard') echo 'selected'; ?>>Graveyard</option>
			</select>

			<br>

            <label for="PATIENT_ID">Patient ID:</label><br>
            <input type="text" name="PATIENT_ID" value="<?php echo $row['PATIENT_ID']; ?>"><br>

            <label for="NURSE_ID">Nurse ID:</label><br>
            <input type="text" name="NURSE_ID" value="<?php echo $row['NURSE_ID']; ?>"><br>

			<label for="ROOM_NUM">Room Number:</label><br>
            <input type="text" name="ROOM_NUM" value="<?php echo $row['ROOM_NUM']; ?>"><br>

			<input type="hidden" name="REG_ID" value="<?php echo isset($REG_ID) ? $REG_ID : ''; ?>">

            <input type="submit" name="update" value="Update">
        </form>

        <?php

    } else {
        echo "No record found with REG_ID = $REG_ID";
    }
}


$mysqli -> close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient Record</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f1f4f7;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        form {
            width: 90%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="time"],
        input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            transition: all 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .button-container {
            text-align: right;
            margin-top: 20px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="button-container">
        <button onclick="window.location.href='HOSPITAL-RECORDS.php'">Back to Hospital Records</button>
    </div>

</body>
</html>