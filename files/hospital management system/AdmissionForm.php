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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_type = $_POST['PATIENT_TYPE'];
    $patient_lname = $_POST['PATIENT_LNAME'];
    $patient_fname = $_POST['PATIENT_FNAME'];
    $patient_med_history = $_POST['PATIENT_MED_HISTORY'];
    $patient_admi_time = $_POST['PATIENT_ADMI_TIME'];
    $patient_admi_date = $_POST['PATIENT_ADMI_DATE'];

    // Server-side validation
    if (empty($patient_type) || empty($patient_lname) || empty($patient_fname) || empty($patient_admi_date)) {
        die("All required fields must be filled out.");
    }

    // Insert data
    $stmt = $mysqli->prepare("INSERT INTO PATIENT (PATIENT_TYPE, PATIENT_LNAME, PATIENT_FNAME, PATIENT_MED_HISTORY, PATIENT_ADMI_TIME, PATIENT_ADMI_DATE) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $patient_type, $patient_lname, $patient_fname, $patient_med_history, $patient_admi_time, $patient_admi_date);

    if ($stmt->execute()) {
        echo "<script>alert('Patient registered successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Patient Admission Form</title>
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
            width: 400px;
            text-align: left;
        }
        input[type="text"],
        select,
        input[type="date"],
        input[type = "time"] {
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
        h1 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <form action="" method="post" onsubmit="return validateForm();">
        <h1>Patient Admission</h1>

        <label for="PATIENT_TYPE">Patient Type:</label>
        <select name="PATIENT_TYPE" id="PATIENT_TYPE">
            <option value="">Select</option>
            <option value="Urgent">Urgent</option>
            <option value="Non-Urgent">Non-Urgent</option>
        </select>
        <span id="error_patient_type" style="color: red;"></span><br>

        <label for="PATIENT_LNAME">Last Name:</label>
        <input type="text" name="PATIENT_LNAME" id="PATIENT_LNAME" required>
        <span id="error_last_name" style="color: red;"></span><br>

        <label for="PATIENT_FNAME">First Name:</label>
        <input type="text" name="PATIENT_FNAME" id="PATIENT_FNAME" required>
        <span id="error_first_name" style="color: red;"></span><br>

        <label for="PATIENT_MED_HISTORY">Medical History:</label>
        <input type="text" name="PATIENT_MED_HISTORY" id="PATIENT_MED_HISTORY">
        <span id="error_med_history" style="color: red;"></span><br>

        <label for="PATIENT_ADMI_TIME">Admission Time:</label>
        <input type="time" name ="PATIENT_ADMI_TIME" id = "PATIENT_ADMI_TIME"  required>
        <span id="error_admi_time" style="color: red;"></span><br>

        <label for="PATIENT_ADMI_DATE">Admission Date:</label>
        <input type="date" name="PATIENT_ADMI_DATE" id="PATIENT_ADMI_DATE" required>
        <span id="error_admi_date" style="color: red;"></span><br>

        <input type="submit" value="Register">
        <input type="button" value="Back" onclick="window.location.href='End-User.php'" />
    
    </form>

    <script>
        function validateForm() {
            let valid = true;

            // Reset error messages
            document.getElementById("error_patient_type").innerText = "";
            document.getElementById("error_last_name").innerText = "";
            document.getElementById("error_first_name").innerText = "";
            document.getElementById("error_med_history").innerText = "";
            document.getElementById("error_admi_time").innerText = "";
            document.getElementById("error_admi_date").innerText = "";

            // Patient Type validation
            if (document.getElementById("PATIENT_TYPE").value === "") {
                document.getElementById("error_patient_type").innerText = "Please select a patient type.";
                valid = false;
            }

            // Last Name validation
            if (document.getElementById("PATIENT_LNAME").value.trim() === "") {
                document.getElementById("error_last_name").innerText = "Last name is required.";
                valid = false;
            }

            // First Name validation
            if (document.getElementById("PATIENT_FNAME").value.trim() === "") {
                document.getElementById("error_first_name").innerText = "First name is required.";
                valid = false;
            }

            // Medical History validation (optional but proper format)
            if (!/^[a-zA-Z0-9 ,.'-]*$/.test(document.getElementById("PATIENT_MED_HISTORY").value)) {
                document.getElementById("error_med_history").innerText = "Invalid characters in medical history.";
                valid = false;
            }

            // Admission Date validation
            if (document.getElementById("PATIENT_ADMI_DATE").value === "") {
                document.getElementById("error_admi_date").innerText = "Admission date is required.";
                valid = false;
            }

            return valid;
        }
    </script>
</body>
</html>
