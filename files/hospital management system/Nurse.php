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

    <h1>Edit Nurse Record</h1>
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


$NURSE_LNAME = isset($_POST['NURSE_LNAME']) ? $_POST['NURSE_LNAME'] : null;
$NURSE_FNAME = isset($_POST['NURSE_FNAME']) ? $_POST['NURSE_FNAME'] : null;
$NURSE_NUM_STATION = isset($_POST['NURSE_NUM_STATION']) ? $_POST['NURSE_NUM_STATION'] : null;
$DR_SPECIALIZATION = isset($_POST['DR_SPECIALIZATION']) ? $_POST['DR_SPECIALIZATION'] : null;
$PATIENT_ID = isset($_POST['PATIENT_ID']) ? $_POST['PATIENT_ID'] : null;
$DR_ID = isset($_POST['DR_ID']) ? $_POST['DR_ID'] : null;



if (isset($_POST['insert'])) {

    // Validate inputs
    $errors = [];

    if (empty($_POST['NURSE_LNAME'])) {
        $errors[] = "Nurse last name is required.";
    }
    if (empty($_POST['NURSE_FNAME'])) {
        $errors[] = "Nurse first name is required.";
    }

	if (empty($_POST['NURSE_NUM_STATION'])) {
        $errors[] = "Nurse station number is required.";
    } else {
        $NURSE_NUM_STATION = $_POST['NURSE_NUM_STATION'];
        if (!preg_match('/^NS\d{4}$/', $NURSE_NUM_STATION)) {
            $errors[] = "Nurse station number must start with 'NS' and be followed by exactly 4 digits (e.g., NS1234).";
        }
    }

    if (empty($_POST['NURSE_SPECIALIZATION'])) {
        $errors[] = "Nurse specialization is required.";
    }
    if (empty($_POST['PATIENT_ID'])) {
        $errors[] = "Patient ID is required.";
    }
    if (empty($_POST['DR_ID'])) {
        $errors[] = "Doctor ID is required.";
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
    $NURSE_LNAME = ucwords(strtolower($_POST['NURSE_LNAME']));
    $NURSE_FNAME = ucwords(strtolower($_POST['NURSE_FNAME']));
    $NURSE_NUM_STATION = $_POST['NURSE_NUM_STATION'];
    $NURSE_SPECIALIZATION = $_POST['NURSE_SPECIALIZATION'];
    $PATIENT_ID = $_POST['PATIENT_ID'];
    $DR_ID = $_POST['DR_ID'];

    // Check if the patient exists
    $check_patient_sql = "SELECT * FROM Hospital.PATIENT WHERE PATIENT_ID = '$PATIENT_ID'";
    $result_patient = mysqli_query($mysqli, $check_patient_sql);

    if (mysqli_num_rows($result_patient) == 0) {
        // If the patient doesn't exist, display an error
        echo "<p style='color: red;'>Error: The Patient ID '$PATIENT_ID' does not exist in the system.</p>";
        return;
    }

    // Check if the doctor exists
    $check_doctor_sql = "SELECT * FROM Hospital.DOCTOR WHERE DR_ID = '$DR_ID'";
    $result_doctor = mysqli_query($mysqli, $check_doctor_sql);

    if (mysqli_num_rows($result_doctor) == 0) {
        // If the doctor doesn't exist, display an error
        echo "<p style='color: red;'>Error: The Doctor ID '$DR_ID' does not exist in the system.</p>";
        return;
    }
# -----------------------------------------------------------------
    $check_patient_assignment = "SELECT * FROM Hospital.NURSE WHERE PATIENT_ID = '$PATIENT_ID'";
    $patient_assigned_result = mysqli_query($mysqli, $check_patient_assignment);

    if (mysqli_num_rows($patient_assigned_result) > 0) {
        echo "<p style = 'color: red;'> This patient is already assigned to a nurse.</p>";
        return;
    }

    // Check if the nurse is already assigned to another doctor
    $check_doctor_assignment = "SELECT * FROM Hospital.NURSE WHERE NURSE_ID = '$NURSE_ID' AND DR_ID != '$DR_ID'";
    $doctor_assigned_result = mysqli_query($mysqli, $check_doctor_assignment);

    if (mysqli_num_rows($doctor_assigned_result) > 0) {
        echo "<p style='color: red;'>This nurse is already assigned to another doctor.</p>";
    }

# -----------------------------------------------------------------
    // Check if the nurse already exists by last name and first name
    $check_nurse_sql = "SELECT * FROM Hospital.NURSE WHERE NURSE_LNAME = '$NURSE_LNAME' AND NURSE_FNAME = '$NURSE_FNAME'";
    $result_nurse = mysqli_query($mysqli, $check_nurse_sql);

    if (mysqli_num_rows($result_nurse) > 0) {
        // If a duplicate is found, display "Record already exists."
        echo "Record already exists.";
    } else {
        // SQL query to insert a new record (DR_ID is auto-incremented)
        $sql = "INSERT INTO Hospital.NURSE (NURSE_LNAME, NURSE_FNAME, NURSE_NUM_STATION, NURSE_SPECIALIZATION, PATIENT_ID, DR_ID) 
                VALUES ('$NURSE_LNAME', '$NURSE_FNAME', '$NURSE_NUM_STATION', '$NURSE_SPECIALIZATION', '$PATIENT_ID', '$DR_ID')";

        // Execute the query
        if (mysqli_query($mysqli, $sql)) {
            // Get the newly generated NURSE_ID
            $new_nurse_id = mysqli_insert_id($mysqli);
            echo "Data stored successfully! Generated NURSE_ID: $new_nurse_id";
        } else {
            echo "Error: " . mysqli_error($mysqli);
        }
    }
}




if (isset($_POST['delete'])) {
    // Get the PATIENT_ID from the form
    $NURSE_ID = $_POST['NURSE_ID'];

    // Proceed with deleting the patient record
    $sql = "DELETE FROM Hospital.NURSE WHERE NURSE_ID = '$NURSE_ID'";

    if (mysqli_query($mysqli, $sql)) {
        echo "<span style='color: green;'>Nurserecord deleted successfully.</span>";
    } else {
        echo "<span style='color: red;'>Error deleting Nurse record: " . mysqli_error($mysqli) . "</span>";
    }
}


elseif (isset($_POST['update'])) {

    $NURSE_ID = isset($_POST['NURSE_ID']) ? $_POST['NURSE_ID'] : null;

    // Check if NURSE_ID is set, else show an error
    if ($NURSE_ID === null) {
        echo "Error: NURSE_ID is required to update the record.";
        return;  // Exit the script if NURSE_ID is not set
    }

    $NURSE_LNAME = ucwords(strtolower($_POST['NURSE_LNAME']));
    $NURSE_FNAME = ucwords(strtolower($_POST['NURSE_FNAME']));
    $NURSE_NUM_STATION = $_POST['NURSE_NUM_STATION'];
    $NURSE_SPECIALIZATION = $_POST['NURSE_SPECIALIZATION'];
    $PATIENT_ID = $_POST['PATIENT_ID'];
    $DR_ID = $_POST['DR_ID'];

    if (empty($NURSE_LNAME) || empty($NURSE_FNAME) || empty($NURSE_NUM_STATION) || empty($PATIENT_ID) || empty($DR_ID)) {
        echo "<p style='color: red;'>Error: last name, first name, station number, patient ID, and doctor ID are required and cannot be left blank.</p>";
        return; // Stop further processing
    }

    if (!preg_match('/^NS\d{4}$/', $NURSE_NUM_STATION)) {
        echo "<span style='color: red;'>Nurse station number must start with 'NS' and be followed by exactly 4 digits (e.g., NS1234).</span><br>";
        return; // Stop further processing if validation fails
    }

    // Check if the patient exists in the PATIENT table
    $check_patient_sql = "SELECT * FROM Hospital.PATIENT WHERE PATIENT_ID = '$PATIENT_ID'";
    $patient_result = mysqli_query($mysqli, $check_patient_sql);

    if (mysqli_num_rows($patient_result) == 0) {
        echo "The specified Patient ID does not exist in the system.";
    } else {
        // Check if the doctor exists in the DOCTOR table
        $check_doctor_sql = "SELECT * FROM Hospital.DOCTOR WHERE DR_ID = '$DR_ID'";
        $doctor_result = mysqli_query($mysqli, $check_doctor_sql);

        if (mysqli_num_rows($doctor_result) == 0) {
            echo "The specified Doctor ID does not exist in the system.";
        } else {
            // Check if the patient is already assigned to another nurse (excluding the current NURSE_ID)
            $check_patient_assignment = "SELECT * FROM Hospital.NURSE WHERE PATIENT_ID = '$PATIENT_ID' AND NURSE_ID != '$NURSE_ID'";
            $patient_assigned_result = mysqli_query($mysqli, $check_patient_assignment);

            if (mysqli_num_rows($patient_assigned_result) > 0) {
                echo "<p style='color: red;'>This patient is already assigned to another nurse.</p>";
                return;
            }

            // Check if the nurse is already assigned to another doctor
            $check_doctor_assignment = "SELECT * FROM Hospital.NURSE WHERE NURSE_ID = '$NURSE_ID' AND DR_ID != '$DR_ID'";
            $doctor_assigned_result = mysqli_query($mysqli, $check_doctor_assignment);

            if (mysqli_num_rows($doctor_assigned_result) > 0) {
                echo "<p style='color: red;'>This nurse is already assigned to another doctor.</p>";
                return;
            }

            // Check if the name already exists, excluding the current record
            $check_sql = "SELECT * FROM Hospital.NURSE WHERE NURSE_LNAME = '$NURSE_LNAME' AND NURSE_FNAME = '$NURSE_FNAME' AND NURSE_ID != '$NURSE_ID'";
            $result = mysqli_query($mysqli, $check_sql);

            if (mysqli_num_rows($result) > 0) {
                echo "A nurse with this name already exists.";
            } else {
                // Corrected SQL query to update the nurse details
                $update_sql = "UPDATE Hospital.NURSE SET 
                                NURSE_LNAME = '$NURSE_LNAME',
                                NURSE_FNAME = '$NURSE_FNAME',
                                NURSE_NUM_STATION = '$NURSE_NUM_STATION',
                                NURSE_SPECIALIZATION = '$NURSE_SPECIALIZATION',
                                PATIENT_ID = '$PATIENT_ID',
                                DR_ID = '$DR_ID'
                                WHERE NURSE_ID = '$NURSE_ID'";  // Make sure you're updating the correct record

                if (mysqli_query($mysqli, $update_sql)) {
                    echo "Record updated successfully!";
                } else {
                    echo "Error: " . mysqli_error($mysqli);
                }
            }
        }
    }
}




if (isset($_POST['edit'])) {
    $NURSE_ID = $_POST['NURSE_ID'];

    // Fetch the patient's details from the database
    $sql = "SELECT * FROM Hospital.NURSE WHERE NURSE_ID = '$NURSE_ID'";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        ?>

        <!-- Display Update Form -->
        <form action="Nurse.php" method="post">
            <input type="hidden" name="NURSE_ID" value="<?php echo $row['NURSE_ID']; ?>">

			<label for="NURSE_LNAME">Last Name:</label><br>
            <input type="text" name="NURSE_LNAME" value="<?php echo $row['NURSE_LNAME']; ?>"><br>

            <label for="NURSE_FNAME">First Name:</label><br>
            <input type="text" name="NURSE_FNAME" value="<?php echo $row['NURSE_FNAME']; ?>"><br>

			<label for="NURSE_NUM_STATION"> Nurse Station Number:</label><br>
            <input type="text" name="NURSE_NUM_STATION" value="<?php echo $row['NURSE_NUM_STATION']; ?>"><br>

			<label for="NURSE_SPECIALIZATION">Nurse Specialization</label><br>
			<select name="NURSE_SPECIALIZATION">
				<option value="Casualty" <?php if ($row['NURSE_SPECIALIZATION'] == 'Casualty') echo 'selected'; ?>>Casualty</option>
				<option value="Medical" <?php if ($row['NURSE_SPECIALIZATION'] == 'Medical') echo 'selected'; ?>>Medical</option>
				<option value="Surgery" <?php if ($row['NURSE_SPECIALIZATION'] == 'Surgery') echo 'selected'; ?>>Surgery</option>
				<option value="Metrnity" <?php if ($row['NURSE_SPECIALIZATION'] == 'Metrnity') echo 'selected'; ?>>Metrnity</option>
				<option value="Medicine" <?php if ($row['NURSE_SPECIALIZATION'] == 'Medicine') echo 'selected'; ?>>Medicine</option>
			</select>

			<br>

            <label for="PATIENT_ID">Patient ID:</label><br>
            <input type="text" name="PATIENT_ID" value="<?php echo $row['PATIENT_ID']; ?>"><br>

            <label for="DR_ID">Doctor ID:</label><br>
            <input type="text" name="DR_ID" value="<?php echo $row['DR_ID']; ?>"><br>

			<input type="hidden" name="NURSE_ID" value="<?php echo isset($NURSE_ID) ? $NURSE_ID : ''; ?>">

            <input type="submit" name="update" value="Update">
        </form>

        <?php

    } else {
        echo "No record found with NURSE_ID = $NURSE_ID";
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