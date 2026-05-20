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

    <h1>Edit Doctor Record</h1>
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

$DR_LNAME = isset($_POST['DR_LNAME']) ? $_POST['DR_LNAME'] : null;
$DR_FNAME = isset($_POST['DR_FNAME']) ? $_POST['DR_FNAME'] : null;
$DR_CNUM = isset($_POST['DR_CNUM']) ? $_POST['DR_CNUM'] : null;
$DR_SPECIALIZATION = isset($_POST['DR_SPECIALIZATION']) ? $_POST['DR_SPECIALIZATION'] : null;
$DR_NUM_STATION = isset($_POST['DR_NUM_STATION']) ? $_POST['DR_NUM_STATION'] : null;
$DR_ID = isset($_POST['DR_ID']) ? $_POST['DR_ID'] : null;


if (isset($_POST['insert'])) {

    // Validate inputs
    $errors = [];

    if (empty($_POST['DR_LNAME'])) {
        $errors[] = "Doctor last name is required.";
    }
    if (empty($_POST['DR_FNAME'])) {
        $errors[] = "Doctor first name is required.";
    }
	if (empty($_POST['DR_CNUM'])) {
        $errors[] = "Doctor contact number is required.";
    } else {
        $DR_CNUM = $_POST['DR_CNUM'];
        
        // Check if DR_CNUM is exactly 11 digits and starts with '09'
        if (!preg_match('/^09\d{9}$/', $DR_CNUM)) {
            $errors[] = "<span style='color: red;'>Doctor contact number must be 11 digits and start with '09'.</span>";
        }
    }
    if (empty($_POST['DR_SPECIALIZATION'])) {
        $errors[] = "Doctor specialization is required.";
    }
	if (empty($_POST['DR_NUM_STATION'])) {
        $errors[] = "Doctor station number is required.";
    } else {
        $DR_NUM_STATION = $_POST['DR_NUM_STATION'];
        if (!preg_match('/^DS\d{4}$/', $DR_NUM_STATION)) {
            $errors[] = "Doctor station number must start with 'DS' and be followed by exactly 4 digits (e.g., DS1234).";
        }
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
    $DR_LNAME = ucwords(strtolower($_POST['DR_LNAME']));
    $DR_FNAME = ucwords(strtolower($_POST['DR_FNAME']));
    $DR_CNUM = $_POST['DR_CNUM'];
    $DR_SPECIALIZATION = $_POST['DR_SPECIALIZATION'];
    $DR_NUM_STATION = $_POST['DR_NUM_STATION'];

    // Check if patient already exists by last name and first name
    $check_sql = "SELECT * FROM Hospital.DOCTOR WHERE DR_LNAME = '$DR_LNAME' AND DR_FNAME = '$DR_FNAME'";
    $result = mysqli_query($mysqli, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        // If a duplicate is found, display "Record already exists."
        echo "Record already exists.";
    } else {
        // SQL query to insert a new record (DR_ID is auto-incremented)
        $sql = "INSERT INTO Hospital.DOCTOR (DR_LNAME, DR_FNAME, DR_CNUM, DR_SPECIALIZATION, DR_NUM_STATION) 
                VALUES ('$DR_LNAME', '$DR_FNAME', '$DR_CNUM', '$DR_SPECIALIZATION', '$DR_NUM_STATION')";

        // Execute the query
        if (mysqli_query($mysqli, $sql)) {
            // Get the newly generated DR_ID
            $new_dr_id = mysqli_insert_id($mysqli);
            echo "Data stored successfully! Generated DR_ID: $new_dr_id";
        } else {
            echo "Error: " . mysqli_error($mysqli);
        }
    }
}



if (isset($_POST['delete'])) {
    // Get the PATIENT_ID from the form
    $PATIENT_ID = $_POST['DR_ID'];

    // Proceed with deleting the patient record
    $sql = "DELETE FROM Hospital.DOCTOR WHERE DR_ID = '$DR_ID'";

    if (mysqli_query($mysqli, $sql)) {
        echo "<span style='color: green;'>Patient record deleted successfully.</span>";
    } else {
        echo "<span style='color: red;'>Error deleting patient record: " . mysqli_error($mysqli) . "</span>";
    }
}



elseif (isset($_POST['update'])) {

	$DR_CNUM = $_POST['DR_CNUM'];

    // Check if DR_CNUM is numeric, exactly 11 digits long, and starts with '09'
    if (!is_numeric($DR_CNUM) || strlen($DR_CNUM) != 11 || substr($DR_CNUM, 0, 2) != '09') {
        echo "<span style='color: red;'>Doctor contact number must be 11 digits long, numeric, and start with '09'.</span><br>";
        return; // Stop further processing
    }

	$DR_NUM_STATION = $_POST['DR_NUM_STATION'];

    // Validate DR_NUM_STATION format (must start with 'DS' and be followed by 4 digits)
    if (!preg_match('/^DS\d{4}$/', $DR_NUM_STATION)) {
        echo "<span style='color: red;'>Doctor station number must start with 'DS' and be followed by exactly 4 digits (e.g., DS1234).</span><br>";
        return; // Stop further processing if validation fails
    }

    $DR_ID =  $_POST['DR_ID'];
    $DR_LNAME = ucwords(strtolower($_POST['DR_LNAME']));
    $DR_FNAME = ucwords(strtolower($_POST['DR_FNAME']));
    $DR_CNUM = $_POST['DR_CNUM'];
    $DR_SPECIALIZATION = $_POST['DR_SPECIALIZATION'];
    $DR_NUM_STATION = $_POST['DR_NUM_STATION'];

    if (empty($DR_LNAME) || empty($DR_FNAME) || empty($DR_CNUM) || empty($DR_NUM_STATION)) {
        echo "<p style='color: red;'>Error: Last name, first name, contact number and station number are required and cannot be left blank.</p>";
        return; // Stop further processing

    // Check if the name already exists, excluding the current record
    $check_sql = "SELECT * FROM Hospital.DOCTOR WHERE DR_LNAME = '$DR_LNAME' AND DR_FNAME = '$DR_FNAME' AND DR_ID != '$DR_ID'";
    $result = mysqli_query($mysqli, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        echo "A doctor with this name already exists.";
    } else {
        // Corrected SQL query to update the doctor details
        $update_sql = "UPDATE Hospital.DOCTOR SET 
                        DR_LNAME = '$DR_LNAME',
                        DR_FNAME = '$DR_FNAME',
                        DR_CNUM = '$DR_CNUM',
                        DR_SPECIALIZATION = '$DR_SPECIALIZATION',
                        DR_NUM_STATION = '$DR_NUM_STATION'
                        WHERE DR_ID = '$DR_ID'";

        if (mysqli_query($mysqli, $update_sql)) {
            echo "Record updated successfully!";
        } else {
            echo "Error: " . mysqli_error($mysqli);
        }
    }
}




if (isset($_POST['edit'])) {
    $DR_ID = $_POST['DR_ID'];

    // Fetch the patient's details from the database
    $sql = "SELECT * FROM Hospital.DOCTOR WHERE DR_ID = '$DR_ID'";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        ?>

        <!-- Display Update Form -->
        <form action="Doctor.php" method="post">
            <input type="hidden" name="DR_ID" value="<?php echo $row['DR_ID']; ?>">

			<label for="DR_LNAME">Last Name:</label><br>
            <input type="text" name="DR_LNAME" value="<?php echo $row['DR_LNAME']; ?>"><br>

            <label for="DR_FNAME">First Name:</label><br>
            <input type="text" name="DR_FNAME" value="<?php echo $row['DR_FNAME']; ?>"><br>

			<label for="DR_CNUM"> Contact Number:</label><br>
            <input type="text" name="DR_CNUM" value="<?php echo $row['DR_CNUM']; ?>"><br>

			<label for="DR_SPECIALIZATION">Dr Specialization</label><br>
			<select name="DR_SPECIALIZATION">
				<option value="Cardiology" <?php if ($row['DR_SPECIALIZATION'] == 'Cardiology') echo 'selected'; ?>>Cardiology</option>
				<option value="Neurology" <?php if ($row['DR_SPECIALIZATION'] == 'Neurology') echo 'selected'; ?>>Neurology</option>
				<option value="Oncology" <?php if ($row['DR_SPECIALIZATION'] == 'Oncology') echo 'selected'; ?>>Oncology</option>
				<option value="Pediatrics" <?php if ($row['DR_SPECIALIZATION'] == 'Pediatrics') echo 'selected'; ?>>Pediatrics</option>
				<option value="Orthopedics" <?php if ($row['DR_SPECIALIZATION'] == 'Orthopedics') echo 'selected'; ?>>Orthopedics</option>
				<option value="Endocrinology" <?php if ($row['DR_SPECIALIZATION'] == 'Endocrinology') echo 'selected'; ?>>Endocrinology</option>
				<option value="Gastroenterology" <?php if ($row['DR_SPECIALIZATION'] == 'Gastroenterology') echo 'selected'; ?>>Gastroenterology</option>
				<option value="Nephrology" <?php if ($row['DR_SPECIALIZATION'] == 'Nephrology') echo 'selected'; ?>>Nephrology</option>
				<option value="Urology" <?php if ($row['DR_SPECIALIZATION'] == 'Urology') echo 'selected'; ?>>Urology</option>
				<option value="Pulmonology" <?php if ($row['DR_SPECIALIZATION'] == 'Pulmonology') echo 'selected'; ?>>Pulmonology</option>
				<option value="Rheumatology" <?php if ($row['DR_SPECIALIZATION'] == 'Rheumatology') echo 'selected'; ?>>Rheumatology</option>
				<option value="Emergency Medicine" <?php if ($row['DR_SPECIALIZATION'] == 'Emergency Medicine') echo 'selected'; ?>>Emergency Medicine</option>
				<option value="Anesthesiology" <?php if ($row['DR_SPECIALIZATION'] == 'Anesthesiology') echo 'selected'; ?>>Anesthesiology</option>
				<option value="Opthalmology" <?php if ($row['DR_SPECIALIZATION'] == 'Opthalmology') echo 'selected'; ?>>Opthalmology</option>
			</select>

			<br>

            <label for="DR_NUM_STATION">Dr Station Number:</label><br>
            <input type="text" name="DR_NUM_STATION" value="<?php echo $row['DR_NUM_STATION']; ?>"><br>

			<input type="hidden" name="DR_ID" value="<?php echo isset($DR_ID) ? $DR_ID : ''; ?>">

            <input type="submit" name="update" value="Update">
        </form>

        <?php

    } else {
        echo "No record found with DR_ID = $DR_ID";
    }
}


$mysqli -> close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor Record</title>
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