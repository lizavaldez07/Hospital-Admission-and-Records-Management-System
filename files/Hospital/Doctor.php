<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor Record</title>
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
            <h1>Edit Doctor Record</h1>
            <p style="margin:0; color: #78909c; font-size: 12px; font-weight: bold; text-transform: uppercase;">Medical Staff Management</p>
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

    $DR_ID = isset($_POST['DR_ID']) ? $_POST['DR_ID'] : null;

    // --- UPDATE LOGIC ---
    if (isset($_POST['update'])) {
        $DR_CNUM = $_POST['DR_CNUM'];
        $DR_NUM_STATION = $_POST['DR_NUM_STATION'];
        $DR_LNAME = ucwords(strtolower($_POST['DR_LNAME']));
        $DR_FNAME = ucwords(strtolower($_POST['DR_FNAME']));
        $DR_SPECIALIZATION = $_POST['DR_SPECIALIZATION'];

        // Updated Regex for Station Number: 6 digits only, no letters
        if (!preg_match('/^09\d{9}$/', $DR_CNUM)) {
            echo "<div class='alert-box error'>Error: Contact number must be 11 digits starting with 09.</div>";
        } elseif (!preg_match('/^\d{6}$/', $DR_NUM_STATION)) {
            echo "<div class='alert-box error'>Error: Station Number must be exactly 6 digits (numbers only).</div>";
        } elseif (empty($DR_LNAME) || empty($DR_FNAME)) {
            echo "<div class='alert-box error'>Error: Name fields are required.</div>";
        } else {
            $check_sql = "SELECT * FROM Hospital.DOCTOR WHERE DR_LNAME = '$DR_LNAME' AND DR_FNAME = '$DR_FNAME' AND DR_ID != '$DR_ID'";
            $result = mysqli_query($mysqli, $check_sql);

            if (mysqli_num_rows($result) > 0) {
                echo "<div class='alert-box error'>A doctor with this name already exists.</div>";
            } else {
                $update_sql = "UPDATE Hospital.DOCTOR SET 
                                DR_LNAME = '$DR_LNAME', DR_FNAME = '$DR_FNAME', 
                                DR_CNUM = '$DR_CNUM', DR_SPECIALIZATION = '$DR_SPECIALIZATION', 
                                DR_NUM_STATION = '$DR_NUM_STATION' WHERE DR_ID = '$DR_ID'";

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
        $DR_ID = mysqli_real_escape_string($mysqli, $_POST['DR_ID']);
        
        $delete_sql = "DELETE FROM Hospital.DOCTOR WHERE DR_ID = '$DR_ID'";

        if (mysqli_query($mysqli, $delete_sql)) {
            // Success: Alert the user and redirect back to the records page
            echo "<script>
                    alert('Doctor record deleted successfully!');
                    window.location.href='HOSPITAL-RECORDS.php';
                  </script>";
            exit();
        } else {
            echo "<div class='alert-box error'>Error deleting record: " . mysqli_error($mysqli) . "</div>";
        }
    }

    // --- EDIT FORM DISPLAY ---
    if (isset($_POST['edit'])) {
        $sql = "SELECT * FROM Hospital.DOCTOR WHERE DR_ID = '$DR_ID'";
        $result = $mysqli->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            ?>
            <div class="form-container">
                <form action="Doctor.php" method="post">
                    <input type="hidden" name="DR_ID" value="<?php echo $row['DR_ID']; ?>">
                    
                    <div class="input-row">
                        <div>
                            <label>First Name</label>
                            <input type="text" name="DR_FNAME" value="<?php echo $row['DR_FNAME']; ?>" required>
                        </div>
                        <div>
                            <label>Last Name</label>
                            <input type="text" name="DR_LNAME" value="<?php echo $row['DR_LNAME']; ?>" required>
                        </div>
                    </div>

                    <label>Contact Number (09XXXXXXXXX)</label>
                    <input type="text" name="DR_CNUM" value="<?php echo $row['DR_CNUM']; ?>" required pattern="09\d{9}" title="Must be 11 digits starting with 09">

                    <label>Specialization</label>
                    <select name="DR_SPECIALIZATION" required>
                        <option value="Cardiology" <?php if ($row['DR_SPECIALIZATION'] == 'Cardiology') echo 'selected'; ?>>Cardiology</option>
                        <option value="Neurology" <?php if ($row['DR_SPECIALIZATION'] == 'Neurology') echo 'selected'; ?>>Neurology</option>
                        <option value="Pediatrics" <?php if ($row['DR_SPECIALIZATION'] == 'Pediatrics') echo 'selected'; ?>>Pediatrics</option>
                        <option value="Surgery" <?php if ($row['DR_SPECIALIZATION'] == 'Surgery') echo 'selected'; ?>>Surgery</option>
                    </select>

                    <label>Station Number (6 Digits)</label>
                    <input type="text" 
                           name="DR_NUM_STATION" 
                           value="<?php echo $row['DR_NUM_STATION']; ?>" 
                           required 
                           pattern="\d{6}" 
                           title="Station Number must be exactly 6 digits (numbers only)" 
                           placeholder="e.g. 123456">

                    <input type="submit" name="update" value="Save Doctor Profile" class="btn-main btn-update">
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