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
        "Doctor" => ["table" => "DOCTOR", "column" => "DR_ID", "loginPage" => "DoctorRecords.php", "signupPage" => "Doctor Admission.php"],
        "Nurse" => ["table" => "NURSE", "column" => "NURSE_ID", "loginPage" => "NurseRecords.php", "signupPage" => "Nurse Admission.php"],
        "Patient" => ["table" => "PATIENT", "column" => "PATIENT_ID", "loginPage" => "PatientRecords.php", "signupPage" => "AdmissionForm.php"],
        "Registrar" => ["table" => "REGISTRAR", "column" => "REG_ID", "loginPage" => "RegistrarRecords.php", "signupPage" => "Registrar Admission.php"]
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
        
        body {
            background-image: url('HOMEPAGE.png'); /* Replace with your image file */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Arial', sans-serif;
            background-color: #f1f4f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        h1 {
            font-size: 32px;
            color: #2f4f4f;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        select, input[type="text"] {
            padding: 10px;
            font-size: 16px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 5px;
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

    <h1>Hospital Management System</h1>

    <form method="POST">
        <h2>Select Role</h2>
        <select name="role" required>
            <option value="" disabled selected>Select your role</option>
            <option value="Doctor">Doctor</option>
            <option value="Nurse">Nurse</option>
            <option value="Patient">Patient</option>
            <option value="Registrar">Registrar</option>
        </select>

        <div id="id-input-container">
            <!-- The ID field will only be shown when login is selected -->
            <input type="text" name="id" placeholder="Enter ID">
        </div>

        <button type="submit" name="login">Login</button>
        <button type="submit" name="signup">Sign-up</button>
    </form>

    <footer>
        <p>&copy; 2024 Hospital Management System</p>
    </footer>

</body>
</html>
