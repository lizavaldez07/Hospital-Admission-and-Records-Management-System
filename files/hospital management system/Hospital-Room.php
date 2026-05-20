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

    <h1>Edit Room Record</h1>
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

$ROOM_NUM = isset($_POST['ROOM_NUM']) ? $_POST['ROOM_NUM'] : null;
$ROOM_TYPE = isset($_POST['ROOM_TYPE']) ? $_POST['ROOM_TYPE'] : null;
$ROOM_AVAILABILITY = isset($_POST['ROOM_AVAILABILITY']) ? $_POST['ROOM_AVAILABILITY'] : null;


if (isset($_POST['insert'])) {
    // Validate inputs
    $errors = [];

    if (empty($_POST['ROOM_NUM'])) {
        $errors[] = "Room number is required.";
    } else {
        $ROOM_NUM = $_POST['ROOM_NUM'];
        // Ensure room number is between 3 and 8 digits long
        if (!preg_match('/^\d{1,8}$/', $ROOM_NUM)) {
            $errors[] = "Room number must be between 1 to 8 digits (e.g., 123, 12345678).";
        }
    }

    if (empty($_POST['ROOM_TYPE'])) {
        $errors[] = "Room type is required.";
    }
    if (empty($_POST['ROOM_AVAILABILITY'])) {
        $errors[] = "Room availability is required.";
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
    $ROOM_NUM = $_POST['ROOM_NUM'];
    $ROOM_TYPE = $_POST['ROOM_TYPE'];
    $ROOM_AVAILABILITY = $_POST['ROOM_AVAILABILITY'];

    // Check if room number already exists
    $check_sql = "SELECT * FROM Hospital.H_ROOM WHERE ROOM_NUM = '$ROOM_NUM'";
    $result = mysqli_query($mysqli, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        echo "Room number already exists.";
    } else {
        // SQL query to insert a new record
        $sql = "INSERT INTO Hospital.H_ROOM (ROOM_NUM, ROOM_TYPE, ROOM_AVAILABILITY)
                VALUES ('$ROOM_NUM', '$ROOM_TYPE', '$ROOM_AVAILABILITY')";

        // Execute the query
        if (mysqli_query($mysqli, $sql)) {
            echo "Room added successfully!";
        } else {
            echo "Error: " . mysqli_error($mysqli);
        }
    }
}





if (isset($_POST['delete'])) {
    // Get the DR_ID from the hidden input field
    $ROOM_NUM = $_POST['ROOM_NUM'];

    $check_registrar_sql = "SELECT * FROM Hospital.REGISTRAR WHERE ROOM_NUM = '$ROOM_NUM'";
    $registrar_result = mysqli_query($mysqli, $check_registrar_sql);

    if (mysqli_num_rows($registrar_result) > 0) {
        // If registrar is associated with room, show a warning message in red
        echo "<span style='color: red;'>This room cannot be deleted because it is associated with a registrar.</span>";
    } else {
        // If no nurses are associated, proceed with deleting the patient record
        $sql = "DELETE FROM Hospital.H_ROOM WHERE ROOM_NUM = '$ROOM_NUM'";

    // Execute the query
    if (mysqli_query($mysqli, $sql)) {
        echo "Record deleted successfully.";
    } else {
        echo "Error deleting record: " . mysqli_error($mysqli);
    }
}
}


if (isset($_POST['delete'])) {
    // Get the ROOM_NUM from the form
    $ROOM_NUM = $_POST['ROOM_NUM'];

    // Proceed with deleting the room record
    $sql = "DELETE FROM Hospital.H_ROOM WHERE ROOM_NUM = '$ROOM_NUM'";

    if (mysqli_query($mysqli, $sql)) {
        echo "<span style='color: green;'>Room record deleted successfully.</span>";
    } else {
        echo "<span style='color: red;'>Error deleting room record: " . mysqli_error($mysqli) . "</span>";
    }
}


if (isset($_POST['update'])) {
    $ROOM_NUM = $_POST['ROOM_NUM'];
    $ROOM_TYPE = $_POST['ROOM_TYPE'];
    $ROOM_AVAILABILITY = $_POST['ROOM_AVAILABILITY'];

    // SQL query to update the room details
    $update_sql = "UPDATE Hospital.H_ROOM SET 
                    ROOM_TYPE = '$ROOM_TYPE',
                    ROOM_AVAILABILITY = '$ROOM_AVAILABILITY'
                    WHERE ROOM_NUM = '$ROOM_NUM'";

    if (mysqli_query($mysqli, $update_sql)) {
        echo "Record updated successfully!";
    } else {
        echo "Error: " . mysqli_error($mysqli);
    }
}


if (isset($_POST['edit'])) {
    $ROOM_NUM = $_POST['ROOM_NUM'];

    // Fetch the patient's details from the database
    $sql = "SELECT * FROM Hospital.H_ROOM WHERE ROOM_NUM = '$ROOM_NUM'";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        ?>

        <!-- Display Update Form -->
        <form action="Hospital-Room.php" method="post">

            <input type="hidden" name="ROOM_NUM" value="<?php echo $row['ROOM_NUM']; ?>">

            <label for="ROOM_TYPE">Room Type:</label>
            <select name="ROOM_TYPE">
				<option value="Emergency" <?php if ($row['ROOM_TYPE'] == 'Emergency') echo 'selected'; ?>>Emergency</option>
				<option value="Non-emergency" <?php if ($row['ROOM_TYPE'] == 'Non-emergency') echo 'selected'; ?>>Non-emergency</option>
            </select><br><br>

			<label for="ROOM_AVAILABILITY"> Room Availability:</label>
            <select name="ROOM_AVAILABILITY">
				<option value="Available" <?php if ($row['ROOM_AVAILABILITY'] == 'Available') echo 'selected'; ?>>Available</option>
				<option value="Not Available" <?php if ($row['ROOM_AVAILABILITY'] == 'Not Avaibale') echo 'selected'; ?>>Not Available</option>
            </select>

           <br><br><input type="submit" name="update" value="Update">
        </form>

        <?php

    } else {
        echo "No record found with ROOM_NUM = $ROOM_NUM";
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