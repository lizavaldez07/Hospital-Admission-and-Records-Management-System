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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Admission Form</title>
    <style>
        /* Modernized UI with Light Blue Theme */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e3f2fd; /* Light blue background */
            margin: 0;
            padding: 0;
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
            max-width: 450px;
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
        input[type="date"],
        input[type="time"],
        select {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 4px; /* Space for error message */
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

        /* Error Message Styling */
        .error-text {
            color: #d32f2f;
            font-size: 12px;
            height: 15px; /* Keeps layout stable even when empty */
            margin-bottom: 10px;
            display: block;
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

        /* Register Button - Primary Blue */
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
        <h1>Patient Admission</h1>

        <form action="" method="post" onsubmit="return validateForm();">
            
            <label for="PATIENT_TYPE">Patient Type</label>
            <select name="PATIENT_TYPE" id="PATIENT_TYPE">
                <option value="">Select Priority</option>
                <option value="Urgent">Urgent</option>
                <option value="Non-Urgent">Non-Urgent</option>
            </select>
            <span id="error_patient_type" class="error-text"></span>

            <label for="PATIENT_FNAME">First Name</label>
            <input type="text" name="PATIENT_FNAME" id="PATIENT_FNAME">
            <span id="error_first_name" class="error-text"></span>

            <label for="PATIENT_LNAME">Last Name</label>
            <input type="text" name="PATIENT_LNAME" id="PATIENT_LNAME">
            <span id="error_last_name" class="error-text"></span>

            <label for="PATIENT_MED_HISTORY">Medical History</label>
            <input type="text" name="PATIENT_MED_HISTORY" id="PATIENT_MED_HISTORY" placeholder="Optional">
            <span id="error_med_history" class="error-text"></span>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label for="PATIENT_ADMI_DATE">Date</label>
                    <input type="date" name="PATIENT_ADMI_DATE" id="PATIENT_ADMI_DATE">
                </div>
                <div style="flex: 1;">
                    <label for="PATIENT_ADMI_TIME">Time</label>
                    <input type="time" name="PATIENT_ADMI_TIME" id="PATIENT_ADMI_TIME">
                </div>
            </div>
            <span id="error_admi_date" class="error-text"></span>
            <span id="error_admi_time" class="error-text"></span>

            <div class="button-group">
                <input type="submit" value="Register Patient">
                <input type="button" value="Back to Dashboard" onclick="window.location.href='End-User.php'" />
            </div>
        </form>
    </div>

    <footer>
        &copy; 2024 Hospital Management System
    </footer>

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

            // Medical History validation
            if (!/^[a-zA-Z0-9 ,.'-]*$/.test(document.getElementById("PATIENT_MED_HISTORY").value)) {
                document.getElementById("error_med_history").innerText = "Invalid characters detected.";
                valid = false;
            }

            // Admission Date validation
            if (document.getElementById("PATIENT_ADMI_DATE").value === "") {
                document.getElementById("error_admi_date").innerText = "Date is required.";
                valid = false;
            }

            return valid;
        }
    </script>
</body>
</html>
