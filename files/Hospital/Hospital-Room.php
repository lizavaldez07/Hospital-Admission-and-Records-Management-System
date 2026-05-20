<?php
session_start();

$user = 'root';
$password = '';
$database = 'Hospital';
$servername = 'localhost:3306';

$mysqli = new mysqli($servername, $user, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$message = "";
$message_type = "";

// Insert Room
if (isset($_POST['insert'])) {
    $ROOM_NUM = trim($_POST['ROOM_NUM']);
    $ROOM_TYPE = trim($_POST['ROOM_TYPE']);
    $ROOM_AVAILABILITY = trim($_POST['ROOM_AVAILABILITY']);

    $errors = [];
    if (empty($ROOM_NUM) || !preg_match('/^\d{1,8}$/', $ROOM_NUM)) {
        $errors[] = "Room number must be 1-8 digits.";
    }
    if (empty($ROOM_TYPE)) $errors[] = "Room type is required.";
    if (empty($ROOM_AVAILABILITY)) $errors[] = "Room availability is required.";

    if (empty($errors)) {
        $stmt = $mysqli->prepare("SELECT ROOM_NUM FROM H_ROOM WHERE ROOM_NUM = ?");
        $stmt->bind_param("s", $ROOM_NUM);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Room number already exists.";
            $message_type = "error";
        } else {
            $insert_stmt = $mysqli->prepare("INSERT INTO H_ROOM (ROOM_NUM, ROOM_TYPE, ROOM_AVAILABILITY) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $ROOM_NUM, $ROOM_TYPE, $ROOM_AVAILABILITY);

            if ($insert_stmt->execute()) {
                $message = "Room added successfully!";
                $message_type = "success";
            } else {
                $message = "Error adding room: " . $mysqli->error;
                $message_type = "error";
            }
            $insert_stmt->close();
        }
        $stmt->close();
    } else {
        $message = implode(" ", $errors);
        $message_type = "error";
    }
}

// Update Room
if (isset($_POST['update'])) {
    $ROOM_NUM = trim($_POST['ROOM_NUM']);
    $ROOM_TYPE = trim($_POST['ROOM_TYPE']);
    $ROOM_AVAILABILITY = trim($_POST['ROOM_AVAILABILITY']);

    $update_stmt = $mysqli->prepare("UPDATE H_ROOM SET ROOM_TYPE = ?, ROOM_AVAILABILITY = ? WHERE ROOM_NUM = ?");
    $update_stmt->bind_param("sss", $ROOM_TYPE, $ROOM_AVAILABILITY, $ROOM_NUM);

    if ($update_stmt->execute()) {
        $message = "Room updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating room: " . $mysqli->error;
        $message_type = "error";
    }
    $update_stmt->close();
}

// Delete Room
if (isset($_POST['delete'])) {
    $ROOM_NUM = trim($_POST['ROOM_NUM']);

    $check_stmt = $mysqli->prepare("SELECT * FROM REGISTRAR WHERE ROOM_NUM = ?");
    $check_stmt->bind_param("s", $ROOM_NUM);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $message = "This room cannot be deleted because it is associated with a registrar.";
        $message_type = "error";
    } else {
        $del_stmt = $mysqli->prepare("DELETE FROM H_ROOM WHERE ROOM_NUM = ?");
        $del_stmt->bind_param("s", $ROOM_NUM);

        if ($del_stmt->execute()) {
            $message = "Room deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Error deleting room: " . $mysqli->error;
            $message_type = "error";
        }
        $del_stmt->close();
    }
    $check_stmt->close();
}

// Fetch all rooms for the table
$result = $mysqli->query("SELECT * FROM H_ROOM");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Room Records</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Header Card Section */
        .header-container {
            width: 95%;
            max-width: 1000px;
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

        .header-container h1 { font-size: 24px; color: #1565c0; margin: 0; }

        /* Table Styling */
        .table-responsive {
            width: 95%;
            max-width: 1000px;
            background-color: #ffffff;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }

        th {
            background-color: #f8fbff;
            color: #1565c0;
            padding: 15px;
            border-bottom: 2px solid #e3f2fd;
            text-align: left;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f4f7;
            color: #546e7a;
        }

        /* Status Pills */
        .status-pill {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .Available { background-color: #e8f5e9; color: #2e7d32; }
        .Not.Available { background-color: #ffebee; color: #c62828; }

        /* Form Section */
        .form-container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 95%;
            max-width: 1000px;
            box-sizing: border-box;
            margin-bottom: 40px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        label { display: block; font-size: 14px; font-weight: bold; color: #546e7a; margin-bottom: 8px; }

        input[type="text"], select {
            width: 100%;
            padding: 12px;
            border: 2px solid #bbdefb;
            border-radius: 8px;
            box-sizing: border-box;
            outline: none;
        }

        /* Buttons */
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
        }

        input[type="submit"], .back-btn {
            padding: 12px 25px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            border-radius: 8px;
            transition: 0.3s;
            color: white;
            text-decoration: none;
        }

        .btn-add { background-color: #1976d2; }
        .btn-update { background-color: #43a047; }
        .btn-delete { background-color: #e53935; }
        .back-btn { background-color: #78909c; }

        input[type="submit"]:hover { opacity: 0.9; transform: translateY(-1px); }

        /* Alerts */
        .alert {
            width: 95%;
            max-width: 1000px;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        .success { background-color: #c8e6c9; color: #2e7d32; border: 1px solid #a5d6a7; }
        .error { background-color: #ffcdd2; color: #c62828; border: 1px solid #ef9a9a; }
    </style>
</head>
<body>

<div class="header-container">
    <div>
        <h1>Hospital Room Records</h1>
        <p style="margin:0; color: #78909c; font-size: 12px; font-weight: bold;">Facility Management System</p>
    </div>
    <a href="HOSPITAL-RECORDS.php" class="back-btn">← Back to Records</a>
</div>

<?php if(!empty($message)): ?>
    <div class="alert <?php echo $message_type; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Availability Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($row['ROOM_NUM']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['ROOM_TYPE']); ?></td>
                <td>
                    <span class="status-pill <?php echo str_replace(' ', '.', $row['ROOM_AVAILABILITY']); ?>">
                        <?php echo htmlspecialchars($row['ROOM_AVAILABILITY']); ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="form-container">
    <h2 style="color: #1565c0; margin-top: 0; margin-bottom: 20px; font-size: 20px;">Add / Update Facility Room</h2>
    <form method="post" action="">
        <div class="form-grid">
            <div>
                <label>Room Number</label>
                <input type="text" name="ROOM_NUM" required pattern="\d{1,8}" title="Enter 1-8 digits" placeholder="e.g. 101">
            </div>

            <div>
                <label>Room Type</label>
                <select name="ROOM_TYPE" required>
                    <option value="">Select Type</option>
                    <option value="Emergency">Emergency</option>
                    <option value="Non-emergency">Non-emergency</option>
                </select>
            </div>

            <div>
                <label>Availability Status</label>
                <select name="ROOM_AVAILABILITY" required>
                    <option value="">Select Status</option>
                    <option value="Available">Available</option>
                    <option value="Not Available">Not Available</option>
                </select>
            </div>
        </div>

        <div class="btn-group">
            <input type="submit" name="insert" value="Add New Room" class="btn-add">
            <input type="submit" name="update" value="Update Existing" class="btn-update">
            <input type="submit" name="delete" value="Delete Room" class="btn-delete">
        </div>
    </form>
</div>

<footer style="margin-bottom: 20px; color: #78909c; font-size: 13px;">
    &copy; 2024 Hospital Management System | Infrastructure Division
</footer>

<?php $mysqli->close(); ?>
</body>
</html>