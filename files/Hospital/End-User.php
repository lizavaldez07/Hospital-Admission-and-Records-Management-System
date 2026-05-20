<?php
session_start();

$user = 'root';
$password = ''; // Ensure this password is stored securely
$database = 'Hospital';
$servername = 'localhost:3306';
$mysqli = new mysqli($servername, $user, $password, $database);

if ($mysqli->connect_error) {
    die('Connect Error(' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// Function to validate user ID
function is_valid_id($mysqli, $table, $column, $id) {
    // Prepare SQL query to check if the ID exists
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    
    // Return true if the ID exists in the table
    return isset($count) && $count > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];

    // Mapping roles to respective tables, columns, and pages
    $roles = [
        "Doctor" => ["table" => "DOCTOR", "column" => "DR_ID", "loginPage" => "DoctorRecords.php", "signupPage" => "Doctor-Admission.php"],
        "Nurse" => ["table" => "NURSE", "column" => "NURSE_ID", "loginPage" => "NurseRecords.php", "signupPage" => "Nurse-Admission.php"],
        "Patient" => ["table" => "PATIENT", "column" => "PATIENT_ID", "loginPage" => "PatientRecords.php", "signupPage" => "AdmissionForm.php"],
        "Registrar" => ["table" => "REGISTRAR", "column" => "REG_ID", "loginPage" => "RegistrarRecords.php", "signupPage" => "Registrar-Admission.php"]
    ];

    if (isset($roles[$role])) {
        $table = $roles[$role]['table'];
        $column = $roles[$role]['column'];
        $loginPage = $roles[$role]['loginPage'];
        $signupPage = $roles[$role]['signupPage'];

        // Handling the login action
        if (isset($_POST['login'])) {
            $id = $_POST['id'];
            if (is_valid_id($mysqli, $table, $column, $id)) {
                // Store the user ID and role in the session
                $_SESSION['USER_ID'] = $id;
                $_SESSION['ROLE'] = $role;

                // Redirect to the respective role login page
                header("Location: $loginPage");
                exit;
            } else {
                echo "<script>alert('Invalid ID for $role. Please try again.');</script>";
            }
        } 
        // Handling the signup action
        elseif (isset($_POST['signup'])) {
            // Redirect to the signup page for the selected role
            header("Location: $signupPage");
            exit;
        }
    } else {
        echo "<script>alert('Invalid role selected. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System</title>
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
            height: 100vh;
            flex-direction: column;
        }

        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        h1 {
            font-size: 28px;
            color: #1565c0; /* Deep blue for the title */
            margin-bottom: 10px;
        }

        h2 {
            font-size: 18px;
            color: #546e7a;
            margin-bottom: 25px;
            font-weight: normal;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        select, input[type="text"] {
            padding: 12px 15px;
            font-size: 16px;
            width: 100%;
            border: 2px solid #bbdefb;
            border-radius: 8px;
            box-sizing: border-box; /* Ensures padding doesn't affect width */
            outline: none;
            transition: border-color 0.3s;
        }

        select:focus, input[type="text"]:focus {
            border-color: #1976d2;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        button {
            flex: 1;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        /* Login Button - Primary Blue */
        button[name="login"] {
            background-color: #1976d2;
            color: white;
        }

        button[name="login"]:hover {
            background-color: #1565c0;
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
        }

        /* Signup Button - Subtle Outline/Secondary */
        button[name="signup"] {
            background-color: #f5f5f5;
            color: #1976d2;
            border: 1px solid #1976d2;
        }

        button[name="signup"]:hover {
            background-color: #e3f2fd;
        }

        footer {
            margin-top: 20px;
            color: #78909c;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h1>HMS Portal</h1>
        <h2>Hospital Management System</h2>

        <form method="POST">
            <select name="role" required>
                <option value="" disabled selected>Select your role</option>
                <option value="Doctor">Doctor</option>
                <option value="Nurse">Nurse</option>
                <option value="Patient">Patient</option>
                <option value="Registrar">Registrar</option>
            </select>

            <div id="id-input-container">
                <input type="text" name="id" placeholder="Enter ID Number">
            </div>

            <div class="button-group">
                <button type="submit" name="login">Login</button>
                <button type="submit" name="signup">Sign-up</button>
            </div>
        </form>
    </div>

    <footer>
        <p>&copy; 2024 Hospital Management System</p>
    </footer>

</body>
</html>