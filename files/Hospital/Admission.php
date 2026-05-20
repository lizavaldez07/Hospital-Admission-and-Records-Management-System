<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient Record</title>
    <style>
        /* Modernized Dashboard UI */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e3f2fd; /* Light blue background */
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Header Card Style */
        .header-container {
            width: 95%;
            max-width: 600px;
            background: #ffffff; 
            padding: 20px 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            border-bottom: 4px solid #1976d2;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
            margin-top: 10px;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            box-sizing: border-box;
            margin-bottom: 30px;
        }

        h1 { font-size: 24px; color: #1565c0; margin: 0; }
        
        label { 
            display: block; 
            font-size: 14px; 
            font-weight: bold; 
            color: #546e7a; 
            margin-bottom: 8px; 
            margin-top: 20px; 
        }

        input[type="text"], 
        input[type="time"], 
        input[type="date"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #bbdefb;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.3s;
        }

        input:focus { border-color: #1976d2; }

        /* Radio Group Styling */
        .radio-group {
            display: flex;
            gap: 20px;
            background: #f8fbff;
            padding: 15px;
            border-radius: 8px;
            border: 1px dashed #bbdefb;
            margin-bottom: 10px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 15px;
            color: #333;
        }

        /* Button Styling */
        .btn-main {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            border-radius: 8px;
            background-color: #1976d2;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-main:hover { 
            background-color: #1565c0; 
            transform: translateY(-1px); 
        }

        .btn-update {
            width: 100%;
            padding: 15px;
            margin-top: 30px;
            font-size: 16px;
        }

        /* PHP Alert Message Styling */
        .alert-box {
            width: 100%;
            max-width: 600px;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
            box-sizing: border-box;
        }
        .success { background-color: #c8e6c9; color: #2e7d32; border: 1px solid #a5d6a7; }
        .error { background-color: #ffcdd2; color: #c62828; border: 1px solid #ef9a9a; }
        
        .input-row { display: flex; gap: 15px; }
        .input-row > div { flex: 1; }

        footer {
            margin-top: auto;
            padding: 20px;
            color: #78909c;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <div>
            <h1>Edit Patient Record</h1>
            <p style="margin:0; color: #78909c; font-size: 12px; font-weight: bold; text-transform: uppercase;">Medical Records Department</p>
        </div>
        <button class="btn-main" onclick="window.location.href='HOSPITAL-RECORDS.php'">← Back to Records</button>
    </div>

    <?php
    $user = 'root';
    $password = '';
    $database = 'Hospital';
    $servername = 'localhost:3306';

    $mysqli = new mysqli($servername, $user, $password, $database);

    if ($mysqli->connect_error) {
        die("<div class='alert-box error'>Connection failed: " . $mysqli->connect_error . "</div>");
    }

    // Processing Logic
    if (isset($_POST['insert'])) {
        $errors = [];
        if (empty($_POST['PATIENT_TYPE'])) $errors[] = "Patient type is required.";
        if (empty($_POST['PATIENT_LNAME'])) $errors[] = "Patient last name is required.";
        if (empty($_POST['PATIENT_FNAME'])) $errors[] = "Patient first name is required.";
        
        if (!empty($errors)) {
            echo "<div class='alert-box error'><ul>";
            foreach ($errors as $err) echo "<li>$err</li>";
            echo "</ul></div>";
        } else {
            $PATIENT_TYPE = $_POST['PATIENT_TYPE'];
            $PATIENT_LNAME = ucwords(strtolower($_POST['PATIENT_LNAME']));
            $PATIENT_FNAME = ucwords(strtolower($_POST['PATIENT_FNAME']));
            $PATIENT_MED_HISTORY = $_POST['PATIENT_MED_HISTORY'];
            $PATIENT_ADMI_TIME = $_POST['PATIENT_ADMI_TIME'];
            $PATIENT_ADMI_DATE = $_POST['PATIENT_ADMI_DATE'];

            $check_sql = "SELECT * FROM Hospital.PATIENT WHERE PATIENT_LNAME = '$PATIENT_LNAME' AND PATIENT_FNAME = '$PATIENT_FNAME'";
            $result = mysqli_query($mysqli, $check_sql);

            if (mysqli_num_rows($result) > 0) {
                echo "<div class='alert-box error'>Record already exists.</div>";
            } else {
                $sql = "INSERT INTO Hospital.PATIENT (PATIENT_TYPE, PATIENT_LNAME, PATIENT_FNAME, PATIENT_MED_HISTORY, PATIENT_ADMI_TIME, PATIENT_ADMI_DATE) 
                        VALUES ('$PATIENT_TYPE', '$PATIENT_LNAME', '$PATIENT_FNAME', '$PATIENT_MED_HISTORY', '$PATIENT_ADMI_TIME', '$PATIENT_ADMI_DATE')";
                if (mysqli_query($mysqli, $sql)) echo "<div class='alert-box success'>Data stored successfully!</div>";
                else echo "<div class='alert-box error'>Error: " . mysqli_error($mysqli) . "</div>";
            }
        }
    }

    if (isset($_POST['delete'])) {
        $PATIENT_ID = $_POST['PATIENT_ID'];
        $sql = "DELETE FROM Hospital.PATIENT WHERE PATIENT_ID = '$PATIENT_ID'";
        if (mysqli_query($mysqli, $sql)) echo "<div class='alert-box success'>Patient record deleted successfully.</div>";
        else echo "<div class='alert-box error'>Error deleting record: " . mysqli_error($mysqli) . "</div>";
    }

    if (isset($_POST['update'])) {
        $PATIENT_ID = $_POST['PATIENT_ID'];
        $PATIENT_TYPE = $_POST['PATIENT_TYPE'];
        $PATIENT_LNAME = ucwords(strtolower($_POST['PATIENT_LNAME']));
        $PATIENT_FNAME = ucwords(strtolower($_POST['PATIENT_FNAME']));
        $PATIENT_MED_HISTORY = $_POST['PATIENT_MED_HISTORY'];
        $PATIENT_ADMI_TIME = $_POST['PATIENT_ADMI_TIME'];
        $PATIENT_ADMI_DATE = $_POST['PATIENT_ADMI_DATE'];

        if (empty($PATIENT_LNAME) || empty($PATIENT_FNAME)) {
            echo "<div class='alert-box error'>Error: All fields are required.</div>";
        } else {
            $check_sql = "SELECT * FROM Hospital.PATIENT WHERE PATIENT_LNAME = '$PATIENT_LNAME' AND PATIENT_FNAME = '$PATIENT_FNAME' AND PATIENT_ID != '$PATIENT_ID'";
            $result = mysqli_query($mysqli, $check_sql);
            if (mysqli_num_rows($result) > 0) {
                echo "<div class='alert-box error'>A patient with this name already exists.</div>";
            } else {
                $update_sql = "UPDATE Hospital.PATIENT SET PATIENT_TYPE = '$PATIENT_TYPE', PATIENT_LNAME = '$PATIENT_LNAME', PATIENT_FNAME = '$PATIENT_FNAME', PATIENT_MED_HISTORY = '$PATIENT_MED_HISTORY', PATIENT_ADMI_TIME = '$PATIENT_ADMI_TIME', PATIENT_ADMI_DATE = '$PATIENT_ADMI_DATE' WHERE PATIENT_ID = '$PATIENT_ID'";
                if (mysqli_query($mysqli, $update_sql)) echo "<div class='alert-box success'>Record updated successfully!</div>";
                else echo "<div class='alert-box error'>Error: " . mysqli_error($mysqli) . "</div>";
            }
        }
    }

    // Form Display Logic
    if (isset($_POST['edit'])) {
        $PATIENT_ID = $_POST['PATIENT_ID'];
        $sql = "SELECT * FROM Hospital.PATIENT WHERE PATIENT_ID = '$PATIENT_ID'";
        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            ?>
            <div class="form-container">
                <form action="Admission.php" method="post">
                    <input type="hidden" name="PATIENT_ID" value="<?php echo $row['PATIENT_ID']; ?>">

                    <label>Patient Priority Level</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="PATIENT_TYPE" value="Urgent" <?php if ($row['PATIENT_TYPE'] == 'Urgent') echo 'checked'; ?>> Urgent
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="PATIENT_TYPE" value="Non-urgent" <?php if ($row['PATIENT_TYPE'] == 'Non-urgent') echo 'checked'; ?>> Non-urgent
                        </label>
                    </div>

                    <div class="input-row">
                        <div>
                            <label for="PATIENT_FNAME">First Name</label>
                            <input type="text" name="PATIENT_FNAME" value="<?php echo $row['PATIENT_FNAME']; ?>" required>
                        </div>
                        <div>
                            <label for="PATIENT_LNAME">Last Name</label>
                            <input type="text" name="PATIENT_LNAME" value="<?php echo $row['PATIENT_LNAME']; ?>" required>
                        </div>
                    </div>

                    <label for="PATIENT_MED_HISTORY">Medical History / Diagnosis</label>
                    <input type="text" name="PATIENT_MED_HISTORY" value="<?php echo $row['PATIENT_MED_HISTORY']; ?>" required>

                    <div class="input-row">
                        <div>
                            <label for="PATIENT_ADMI_DATE">Admission Date</label>
                            <input type="date" name="PATIENT_ADMI_DATE" value="<?php echo $row['PATIENT_ADMI_DATE']; ?>" required>
                        </div>
                        <div>
                            <label for="PATIENT_ADMI_TIME">Admission Time</label>
                            <input type="time" name="PATIENT_ADMI_TIME" value="<?php echo $row['PATIENT_ADMI_TIME']; ?>" required>
                        </div>
                    </div>

                    <input type="submit" name="update" value="Save Changes" class="btn-main btn-update">
                </form>
            </div>
            <?php
        } else {
            echo "<div class='alert-box error'>No record found with ID: $PATIENT_ID</div>";
        }
    }

    $mysqli->close();
    ?>

    <footer>
        &copy; 2024 Hospital Management System | Secure Administrative Portal
    </footer>

</body>
</html>