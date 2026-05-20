<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Nurse Record</title>
    <style>
        /* Modernized Dashboard UI */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e3f2fd; 
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

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
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #bbdefb;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.3s;
        }

        input:focus, select:focus { border-color: #1976d2; }

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
            <h1>Edit Nurse Record</h1>
            <p style="margin:0; color: #78909c; font-size: 12px; font-weight: bold; text-transform: uppercase;">Nursing Staff Management</p>
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

    $NURSE_ID = isset($_POST['NURSE_ID']) ? $_POST['NURSE_ID'] : null;

    // --- UPDATE LOGIC ---
    if (isset($_POST['update'])) {
        $NURSE_LNAME = ucwords(strtolower($_POST['NURSE_LNAME']));
        $NURSE_FNAME = ucwords(strtolower($_POST['NURSE_FNAME']));
        $NURSE_NUM_STATION = $_POST['NURSE_NUM_STATION'];
        $NURSE_SPECIALIZATION = $_POST['NURSE_SPECIALIZATION'];
        $PATIENT_ID = $_POST['PATIENT_ID'];
        $DR_ID = $_POST['DR_ID'];

        // Updated Regex: Numbers only, 1 to 6 digits
        if (empty($NURSE_LNAME) || empty($NURSE_FNAME) || empty($NURSE_NUM_STATION) || empty($PATIENT_ID) || empty($DR_ID)) {
            echo "<div class='alert-box error'>Error: All fields are required.</div>";
        } elseif (!preg_match('/^\d{1,6}$/', $NURSE_NUM_STATION)) {
            echo "<div class='alert-box error'>Error: Station number must be numbers only (max 6 digits).</div>";
        } else {
            // Check patient existence
            $p_check = mysqli_query($mysqli, "SELECT * FROM Hospital.PATIENT WHERE PATIENT_ID = '$PATIENT_ID'");
            // Check doctor existence
            $d_check = mysqli_query($mysqli, "SELECT * FROM Hospital.DOCTOR WHERE DR_ID = '$DR_ID'");

            if (mysqli_num_rows($p_check) == 0) {
                echo "<div class='alert-box error'>Error: Patient ID does not exist.</div>";
            } elseif (mysqli_num_rows($d_check) == 0) {
                echo "<div class='alert-box error'>Error: Doctor ID does not exist.</div>";
            } else {
                $update_sql = "UPDATE Hospital.NURSE SET 
                                NURSE_LNAME = '$NURSE_LNAME', NURSE_FNAME = '$NURSE_FNAME',
                                NURSE_NUM_STATION = '$NURSE_NUM_STATION', NURSE_SPECIALIZATION = '$NURSE_SPECIALIZATION',
                                PATIENT_ID = '$PATIENT_ID', DR_ID = '$DR_ID'
                                WHERE NURSE_ID = '$NURSE_ID'";

                if (mysqli_query($mysqli, $update_sql)) {
                    echo "<div class='alert-box success'>Record updated successfully!</div>";
                } else {
                    echo "<div class='alert-box error'>Error: " . mysqli_error($mysqli) . "</div>";
                }
            }
        }
    }

    // --- DELETE LOGIC ---
    if (isset($_POST['delete'])) {
        $NURSE_ID = mysqli_real_escape_string($mysqli, $_POST['NURSE_ID']);
        
        $delete_sql = "DELETE FROM Hospital.NURSE WHERE NURSE_ID = '$NURSE_ID'";

        if (mysqli_query($mysqli, $delete_sql)) {
            // Success: Alert the user and redirect back to the main records page
            echo "<script>
                    alert('Nurse record deleted successfully!');
                    window.location.href='HOSPITAL-RECORDS.php';
                  </script>";
            exit();
        } else {
            // If there's a foreign key constraint (e.g., this nurse is linked to a registrar log)
            echo "<div class='alert-box error'>Error deleting record: This nurse may be linked to other records. " . mysqli_error($mysqli) . "</div>";
        }
    }

    // --- EDIT FORM DISPLAY ---
    if (isset($_POST['edit'])) {
        $sql = "SELECT * FROM Hospital.NURSE WHERE NURSE_ID = '$NURSE_ID'";
        $result = $mysqli->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            ?>
            <div class="form-container">
                <form action="Nurse.php" method="post">
                    <input type="hidden" name="NURSE_ID" value="<?php echo $row['NURSE_ID']; ?>">
                    
                    <div class="input-row">
                        <div>
                            <label>First Name</label>
                            <input type="text" name="NURSE_FNAME" value="<?php echo $row['NURSE_FNAME']; ?>" required>
                        </div>
                        <div>
                            <label>Last Name</label>
                            <input type="text" name="NURSE_LNAME" value="<?php echo $row['NURSE_LNAME']; ?>" required>
                        </div>
                    </div>

                    <label>Nurse Station Number</label>
                    <input type="text" 
                           name="NURSE_NUM_STATION" 
                           value="<?php echo $row['NURSE_NUM_STATION']; ?>" 
                           required 
                           pattern="\d{1,6}" 
                           title="Must be numbers only, up to 6 digits"
                           placeholder="e.g. 123456">

                    <label>Nurse Specialization</label>
                    <select name="NURSE_SPECIALIZATION" required>
                        <option value="Casualty" <?php if ($row['NURSE_SPECIALIZATION'] == 'Casualty') echo 'selected'; ?>>Casualty</option>
                        <option value="Medical" <?php if ($row['NURSE_SPECIALIZATION'] == 'Medical') echo 'selected'; ?>>Medical</option>
                        <option value="Surgery" <?php if ($row['NURSE_SPECIALIZATION'] == 'Surgery') echo 'selected'; ?>>Surgery</option>
                        <option value="Maternity" <?php if ($row['NURSE_SPECIALIZATION'] == 'Maternity') echo 'selected'; ?>>Maternity</option>
                        <option value="Medicine" <?php if ($row['NURSE_SPECIALIZATION'] == 'Medicine') echo 'selected'; ?>>Medicine</option>
                    </select>

                    <div class="input-row">
                        <div>
                            <label>Patient ID</label>
                            <input type="text" name="PATIENT_ID" value="<?php echo $row['PATIENT_ID']; ?>" required>
                        </div>
                        <div>
                            <label>Doctor ID</label>
                            <input type="text" name="DR_ID" value="<?php echo $row['DR_ID']; ?>" required>
                        </div>
                    </div>

                    <input type="submit" name="update" value="Save Changes" class="btn-main btn-update">
                </form>
            </div>
            <?php
        }
    }
    $mysqli->close();
    ?>

    <footer>
        &copy; 2024 Hospital Management System
    </footer>
</body>
</html>