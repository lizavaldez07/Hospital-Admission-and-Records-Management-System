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

    <h1>Edit Patient Record</h1>
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



# -----------------------------

$PATIENT_TYPE = isset($_POST['PATIENT_TYPE']) ? $_POST['PATIENT_TYPE'] : null;
$PATIENT_LNAME = isset($_POST['PATIENT_LNAME']) ? $_POST['PATIENT_LNAME'] : null;
$PATIENT_FNAME = isset($_POST['PATIENT_FNAME']) ? $_POST['PATIENT_FNAME'] : null;
$PATIENT_MED_HISTORY = isset($_POST['PATIENT_MED_HISTORY']) ? $_POST['PATIENT_MED_HISTORY'] : null;
$PATIENT_ADMI_TIME = isset($_POST['PATIENT_ADMI_TIME']) ? $_POST['PATIENT_ADMI_TIME'] : null;
$PATIENT_ADMI_DATE = isset($_POST['PATIENT_ADMI_DATE']) ? $_POST['PATIENT_ADMI_DATE'] : null;
$PATIENT_ID = isset($_POST['PATIENT_ID']) ? $_POST['PATIENT_ID'] : null;


if (isset($_POST['insert'])) {

    // Validate inputs
    $errors = [];

    if (empty($_POST['PATIENT_TYPE'])) {
        $errors[] = "Patient type is required.";
    }
    if (empty($_POST['PATIENT_LNAME'])) {
        $errors[] = "Patient last name is required.";
    }
    if (empty($_POST['PATIENT_FNAME'])) {
        $errors[] = "Patient first name is required.";
    }
    if (empty($_POST['PATIENT_MED_HISTORY'])) {
        $errors[] = "Patient medical history is required.";
    }
    if (empty($_POST['PATIENT_ADMI_TIME'])) {
        $errors[] = "Patient admission time is required.";
    }
    if (empty($_POST['PATIENT_ADMI_DATE'])) {
        $errors[] = "Patient admission date is required.";
    }

    // If there are errors, display them and stop the process
    if (!empty($errors)) {
        echo "<ul style='color: red;'>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        echo "<button onclick='window.history.back()'>Back</button>";
        return; // Stop further processing
    }
    // Validate and format form inputs
    $PATIENT_TYPE = $_POST['PATIENT_TYPE'];
    $PATIENT_LNAME = ucwords(strtolower($_POST['PATIENT_LNAME']));
    $PATIENT_FNAME = ucwords(strtolower($_POST['PATIENT_FNAME']));
    $PATIENT_MED_HISTORY = $_POST['PATIENT_MED_HISTORY'];
    $PATIENT_ADMI_TIME = $_POST['PATIENT_ADMI_TIME'];
    $PATIENT_ADMI_DATE = $_POST['PATIENT_ADMI_DATE'];

    // Check if patient already exists by last name and first name
    $check_sql = "SELECT * FROM Hospital.PATIENT WHERE PATIENT_LNAME = '$PATIENT_LNAME' AND PATIENT_FNAME = '$PATIENT_FNAME'";
    $result = mysqli_query($mysqli, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        // If a duplicate is found, display "Record already exists."
        echo "Record already exists.";
    } else {
        // SQL query to insert a new record (PATIENT_ID is auto-incremented)
        $sql = "INSERT INTO Hospital.PATIENT (PATIENT_TYPE, PATIENT_LNAME, PATIENT_FNAME, PATIENT_MED_HISTORY, PATIENT_ADMI_TIME, PATIENT_ADMI_DATE) 
                VALUES ('$PATIENT_TYPE', '$PATIENT_LNAME', '$PATIENT_FNAME', '$PATIENT_MED_HISTORY', '$PATIENT_ADMI_TIME', '$PATIENT_ADMI_DATE')";

        // Execute the query
        if (mysqli_query($mysqli, $sql)) {
            // Get the newly generated PATIENT_ID
            $new_patient_id = mysqli_insert_id($mysqli);
            echo "Data stored successfully!";
        } else {
            echo "Error: " . mysqli_error($mysqli);
        }
    }
}



if (isset($_POST['delete'])) {
    // Get the PATIENT_ID from the form
    $PATIENT_ID = $_POST['PATIENT_ID'];

    // Proceed with deleting the patient record
    $sql = "DELETE FROM Hospital.PATIENT WHERE PATIENT_ID = '$PATIENT_ID'";

    if (mysqli_query($mysqli, $sql)) {
        echo "<span style='color: green;'>Patient record deleted successfully.</span>";
    } else {
        echo "<span style='color: red;'>Error deleting patient record: " . mysqli_error($mysqli) . "</span>";
    }
}




elseif (isset($_POST['update'])) {
    $PATIENT_ID = $_POST['PATIENT_ID'];
    $PATIENT_TYPE = $_POST['PATIENT_TYPE'];
    $PATIENT_LNAME = ucwords(strtolower($_POST['PATIENT_LNAME']));
    $PATIENT_FNAME = ucwords(strtolower($_POST['PATIENT_FNAME']));
    $PATIENT_MED_HISTORY = $_POST['PATIENT_MED_HISTORY'];
    $PATIENT_ADMI_TIME = $_POST['PATIENT_ADMI_TIME'];
    $PATIENT_ADMI_DATE = $_POST['PATIENT_ADMI_DATE'];

    // Check if any required field is empty
    if (empty($PATIENT_LNAME) || empty($PATIENT_FNAME) || empty($PATIENT_TYPE) || empty($PATIENT_MED_HISTORY) || empty($PATIENT_ADMI_TIME) || empty($PATIENT_ADMI_DATE)) {
        echo "Error: All fields are required and must not be empty.";
    
    } else {
        // Check if the name already exists, excluding the current record
        $check_sql = "SELECT * FROM Hospital.PATIENT WHERE PATIENT_LNAME = '$PATIENT_LNAME' AND PATIENT_FNAME = '$PATIENT_FNAME' AND PATIENT_ID != '$PATIENT_ID'";
        $result = mysqli_query($mysqli, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            // Show an error if the record exists
            echo "A patient with this name already exists.";
        } else {
            // Update the patient details if no duplicates are found
            $update_sql = "UPDATE Hospital.PATIENT SET 
                            PATIENT_TYPE = '$PATIENT_TYPE',
                            PATIENT_LNAME = '$PATIENT_LNAME',
                            PATIENT_FNAME = '$PATIENT_FNAME',
                            PATIENT_MED_HISTORY = '$PATIENT_MED_HISTORY',
                            PATIENT_ADMI_TIME = '$PATIENT_ADMI_TIME',
                            PATIENT_ADMI_DATE = '$PATIENT_ADMI_DATE' 
                            WHERE PATIENT_ID = '$PATIENT_ID'";

            if (mysqli_query($mysqli, $update_sql)) {
                echo "Record updated successfully!";
            } else {
                echo "Error: " . mysqli_error($mysqli);
            }
        }
    }
}




if (isset($_POST['edit'])) {
    $PATIENT_ID = $_POST['PATIENT_ID'];

    // Fetch the patient's details from the database
    $sql = "SELECT * FROM Hospital.PATIENT WHERE PATIENT_ID = '$PATIENT_ID'";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        ?>

        <!-- Display Update Form -->
        <form action="Admission.php" method="post">
            <input type="hidden" name="PATIENT_ID" value="<?php echo $row['PATIENT_ID']; ?>">

            <label for="PATIENT_TYPE">Patient Type:</label><br>
            <input type="radio" name="PATIENT_TYPE" value="Urgent" <?php if ($row['PATIENT_TYPE'] == 'Urgent') echo 'checked'; ?>> Urgent
            <input type="radio" name="PATIENT_TYPE" value="Non-urgent" <?php if ($row['PATIENT_TYPE'] == 'Non-urgent') echo 'checked'; ?>> Non-urgent<br>

            <label for="PATIENT_LNAME">Last Name:</label><br>
            <input type="text" name="PATIENT_LNAME" value="<?php echo $row['PATIENT_LNAME']; ?>"><br>

            <label for="PATIENT_FNAME">First Name:</label><br>
            <input type="text" name="PATIENT_FNAME" value="<?php echo $row['PATIENT_FNAME']; ?>"><br>

            <label for="PATIENT_MED_HISTORY">Medical History:</label><br>
            <input type="text" name="PATIENT_MED_HISTORY" value="<?php echo $row['PATIENT_MED_HISTORY']; ?>"><br>

            <label for="PATIENT_ADMI_TIME">Admission Time:</label><br>
            <input type="time" name="PATIENT_ADMI_TIME" value="<?php echo $row['PATIENT_ADMI_TIME']; ?>"><br>

            <label for="PATIENT_ADMI_DATE">Admission Date:</label><br>
            <input type="date" name="PATIENT_ADMI_DATE" value="<?php echo $row['PATIENT_ADMI_DATE']; ?>"><br>

			<input type="hidden" name="PATIENT_ID" value="<?php echo isset($PATIENT_ID) ? $PATIENT_ID : ''; ?>">

            <input type="submit" name="update" value="Update">
        </form>

        <?php
    } else {
        echo "No record found with PATIENT_ID = $PATIENT_ID";
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
