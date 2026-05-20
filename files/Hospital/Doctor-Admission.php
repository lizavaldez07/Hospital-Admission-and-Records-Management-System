<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration Form</title>
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
            max-width: 500px;
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
        select {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
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

        /* PHP Message Styling */
        .message {
            background-color: #c8e6c9;
            color: #2e7d32;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }

        .error {
            background-color: #ffcdd2;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 5px;
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

        /* Submit Button - Primary Blue */
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
    <?php
    $user = 'root';
    $password = '';
    $database = 'Hospital';
    $servername = 'localhost:3306';

    $mysqli = new mysqli($servername, $user, $password, $database);
    $message = "";
    $error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert'])) {
        $dr_lname = trim($_POST['DR_LNAME']);
        $dr_fname = trim($_POST['DR_FNAME']);
        $dr_cnum = trim($_POST['DR_CNUM']);
        $dr_specialization = trim($_POST['DR_SPECIALIZATION']);
        $dr_num_station = trim($_POST['DR_NUM_STATION']);
        
        if (empty($dr_lname) || empty($dr_fname) || empty($dr_cnum) || empty($dr_specialization) || empty($dr_num_station)) {
            $error = "All fields must be filled out.";
        } elseif (!ctype_digit($dr_cnum)) {
            $error = "Contact Number must be numeric.";
        } else {
            $stmt = $mysqli->prepare("INSERT INTO DOCTOR (DR_LNAME, DR_FNAME, DR_CNUM, DR_SPECIALIZATION, DR_NUM_STATION) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $dr_lname, $dr_fname, $dr_cnum, $dr_specialization, $dr_num_station);

            if ($stmt->execute()) {
                echo "<script>alert('Doctor registered successfully!');</script>";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    $mysqli->close();
    ?>

    <div class="form-container">
        <h1>Doctor Registration</h1>
        
        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <?php if (!empty($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <form action="" method="post">
            <label for="DR_FNAME">First Name</label>
            <input type="text" name="DR_FNAME" id="DR_FNAME" required placeholder="Enter first name">

            <label for="DR_LNAME">Last Name</label>
            <input type="text" name="DR_LNAME" id="DR_LNAME" required placeholder="Enter last name">

            <label for="DR_CNUM">Contact Number</label>
            <input type="text" name="DR_CNUM" id="DR_CNUM" required
                pattern="\d{11}" title="Contact Number must be exactly 11 digits"
                placeholder="09XXXXXXXXX (11 digits)">

            <label for="DR_SPECIALIZATION">Specialization</label>
            <select name="DR_SPECIALIZATION" id="DR_SPECIALIZATION" required>
                <option value="">Select Specialization</option>
                <option value="Cardiology">Cardiology</option>
                <option value="Neurology">Neurology</option>
                <option value="Oncology">Oncology</option>
                <option value="Pediatrics">Pediatrics</option>
                <option value="Orthopedics">Orthopedics</option>
                <option value="Endocrinology">Endocrinology</option>
                <option value="Gastroenterology">Gastroenterology</option>
                <option value="Nephrology">Nephrology</option>
                <option value="Urology">Urology</option>
                <option value="Pulmonology">Pulmonology</option>
                <option value="Rheumatology">Rheumatology</option>
                <option value="Emergency Medicine">Emergency Medicine</option>
                <option value="Anesthesiology">Anesthesiology</option>
                <option value="Ophthalmology">Ophthalmology</option>
            </select>

            <label for="DR_NUM_STATION">Station Number</label>
            <input type="text" name="DR_NUM_STATION" id="DR_NUM_STATION" required
                pattern="\d{6}" title="Station Number must be exactly 6 digits"
                placeholder="Enter 6-digit station ID">

            <div class="button-group">
                <input type="submit" name="insert" value="Register Doctor">
                <input type="button" value="Back to Dashboard" onclick="window.location.href='End-User.php'" />
            </div>
        </form>
    </div>

    <footer>
        &copy; 2024 Hospital Management System
    </footer>
</body>
</html>