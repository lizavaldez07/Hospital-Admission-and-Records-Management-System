<!DOCTYPE html>
<html>
<head>
    <title>Doctor Registration Form</title>
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
            width: 500px;
            text-align: center;
        }
        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
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
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .message {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        h1 {
            margin-bottom: 20px;
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
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    $mysqli->close();
    ?>

    <form action="" method="post" onsubmit="return validateForm()">
        <h1>Doctor Registration Form</h1>
        
        <label for="DR_LNAME">Doctor Last Name:</label>
        <input type="text" name="DR_LNAME" id="DR_LNAME" required><br>

        <label for="DR_FNAME">Doctor First Name:</label>
        <input type="text" name="DR_FNAME" id="DR_FNAME" required><br>

        <label for="DR_CNUM">Doctor Contact Number:</label>
        <input type="text" name="DR_CNUM" id="DR_CNUM" required pattern="\d+" title="Only numbers are allowed"><br>

        <label for="DR_SPECIALIZATION">Doctor Specialization:</label>
        <select name="DR_SPECIALIZATION" id="DR_SPECIALIZATION" required>
            <option value="">Select</option>
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
        </select><br>


        <label for="DR_NUM_STATION">Doctor Station Number:</label>
        <input type="text" name="DR_NUM_STATION" id="DR_NUM_STATION" required><br>

        <input type="submit" name="insert" value="Submit">
        <input type="button" value="Back" onclick="window.location.href='End-User.php'" />
        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <?php if (!empty($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>
    </form>

    <script>
        function validateForm() {
            const contactNumber = document.getElementById('DR_CNUM').value;
            if (!/^\d+$/.test(contactNumber)) {
                alert("Contact Number must contain only numbers.");
                return false;
            }


            return true;
        }
    </script>
</body>
</html>
