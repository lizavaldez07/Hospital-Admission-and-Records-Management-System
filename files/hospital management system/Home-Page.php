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

// Default password
$default_password = 'password';

// Function to validate REG_ID
function is_valid_id($mysqli, $table, $column, $id) {
    // Prepare the SQL query to check if the ID exists in the specified table and column
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
    
    // Bind the ID to the query (assuming the ID is a string, hence "s")
    $stmt->bind_param("s", $id);
    
    // Execute the query
    $stmt->execute();
    
    // Bind the result to the $count variable
    $stmt->bind_result($count);
    
    // Fetch the result
    $stmt->fetch();
    
    // Close the prepared statement
    $stmt->close();
    
    // Return true if count is greater than 0 (meaning the ID exists), otherwise false
    return isset($count) && $count > 0; // Ensure $count is set before checking
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login_password'])) {
        // Password validation
        if ($_POST['login_password'] === $default_password) {
            header("Location: HOSPITAL-RECORDS.php");
            exit;
        } else {
            echo "Incorrect password. Please try again.";
        }
    } elseif (isset($_POST['reg_id']) && isset($_POST['new_password'])) {
        // Change password
        $reg_id = $_POST['reg_id'];
        $new_password = $_POST['new_password'];

        // Specify your table and column names here (e.g., 'users' table and 'REG_ID' column)
        if (is_valid_id($mysqli, 'REGISTRAR', 'REG_ID', $reg_id)) {
            $default_password = $new_password;
            echo "Password changed successfully.";
        } else {
            echo "Invalid REG_ID. Please try again.";
        }
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

        input[type="text"], input[type="password"] {
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

    <h1>Hospital Admission System</h1>

    <form method="POST">
        <h2>Enter Password</h2>
        <input type="password" name="login_password" placeholder="Enter Password" required>
        <button type="submit">Login</button>
    </form>

    <form method="POST">
        <h2>Change Password</h2>
        <input type="text" name="reg_id" placeholder="Enter REG_ID" required>
        <input type="password" name="new_password" placeholder="Enter New Password" required>
        <button type="submit">Change Password</button>
    </form>

    <footer>
        <p>&copy; 2024 Hospital Management System</p>
    </footer>

</body>
</html>
