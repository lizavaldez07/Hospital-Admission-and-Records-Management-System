<?php
// Database Configuration
$user = 'root';
$password = '';
$database = 'Hospital';
$servername = 'localhost:3306';
$mysqli = new mysqli($servername, $user, $password, $database);

// Ensure the connection is successful
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$message = ""; 
$message_type = ""; // 'success' or 'error'

// Function to validate REG_ID exists
function is_valid_id($mysqli, $table, $column, $id) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return isset($count) && $count > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- LOGIN LOGIC (HARDCODED PASSWORD) ---
    if (isset($_POST['login_id']) && isset($_POST['login_password'])) {
        $login_id = $_POST['login_id'];
        $login_password = $_POST['login_password'];

        // Check if REG_ID exists
        if (is_valid_id($mysqli, 'REGISTRAR', 'REG_ID', $login_id)) {

            // 🔒 HARDCODED PASSWORD
            $fixed_password = "admin123";

            if ($login_password === $fixed_password) {
                header("Location: HOSPITAL-RECORDS.php");
                exit;
            } else {
                $message = "Invalid Password.";
                $message_type = "error";
            }

        } else {
            $message = "REG_ID not found.";
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System - Login</title>
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

        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }

        h1 {
            font-size: 28px;
            color: #1565c0; /* Deep blue for the title */
            margin-bottom: 10px;
            margin-top: 0;
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

        input[type="text"], 
        input[type="password"] {
            padding: 12px 15px;
            font-size: 16px;
            width: 100%;
            border: 2px solid #bbdefb;
            border-radius: 8px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.3s;
        }

        input:focus {
            border-color: #1976d2;
        }

        button {
            padding: 12px;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            border-radius: 8px;
            background-color: #1976d2;
            color: white;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            background-color: #1565c0;
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
        }

        /* Alert Message Styling */
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }

        .success {
            color: #2e7d32;
            background-color: #c8e6c9;
            border: 1px solid #a5d6a7;
        }

        .error {
            color: #c62828;
            background-color: #ffcdd2;
            border: 1px solid #ef9a9a;
        }

        footer {
            margin-top: 20px;
            color: #78909c;
            font-size: 14px;
        }

        hr {
            width: 100%;
            border: 0;
            border-top: 1px solid #e3f2fd;
            margin: 20px 0;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>HMS Portal</h1>

        <?php if (isset($message) && $message): ?>
            <div class="alert <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <h2>Staff Login</h2>
            <input type="text" name="login_id" placeholder="Registrar ID" required>
            <input type="password" name="login_password" placeholder="Password" required>
            <button type="submit">Sign In</button>
        </form>

        <hr>
        
        <p style="font-size: 13px; color: #90a4ae;">Authorized Personnel Only</p>
    </div>

    <footer>
        <p>&copy; 2024 Hospital Management System</p>
    </footer>

</body>
</html>