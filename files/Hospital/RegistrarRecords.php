<?php
// Database Configuration
$user = 'root';
$password = '';
$database = 'Hospital';
$servername = 'localhost:3306';

$mysqli = new mysqli($servername, $user, $password, $database);

// Check for connection error
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Keep the dynamic filename variable for redirects
$current_page = basename(__FILE__); 

// Handle Delete Operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $reg_id = intval($_POST['REG_ID']);
    $stmt = $mysqli->prepare("DELETE FROM REGISTRAR WHERE REG_ID = ?");
    $stmt->bind_param("i", $reg_id);

    if ($stmt->execute()) {
        echo "<script>alert('Registrar record deleted successfully!'); window.location.href='$current_page';</script>";
    } else {
        echo "<script>alert('Error deleting record.');</script>";
    }
    $stmt->close();
}

// Handle Update Operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $reg_id = intval($_POST['REG_ID']);
    $reg_lname = trim($_POST['REG_LNAME']);
    $reg_fname = trim($_POST['REG_FNAME']);
    $reg_num_station = trim($_POST['REG_NUM_STATION']);
    $reg_mop = trim($_POST['REG_MOP']);
    $reg_shift = trim($_POST['REG_SHIFT']);
    $patient_id = trim($_POST['PATIENT_ID']);
    $nurse_id = trim($_POST['NURSE_ID']);
    $room_num = trim($_POST['ROOM_NUM']);

    // Validate numeric fields
    if (!ctype_digit($reg_num_station) || !ctype_digit($room_num)) {
        echo "<script>alert('Station Number and Room Number must be numeric.');</script>";
    } else {
        $stmt = $mysqli->prepare(
            "UPDATE REGISTRAR 
            SET REG_LNAME = ?, REG_FNAME = ?, REG_NUM_STATION = ?, REG_MOP = ?, REG_SHIFT = ?, PATIENT_ID = ?, NURSE_ID = ?, ROOM_NUM = ? 
            WHERE REG_ID = ?"
        );
        $stmt->bind_param("sssssiisi", $reg_lname, $reg_fname, $reg_num_station, $reg_mop, $reg_shift, $patient_id, $nurse_id, $room_num, $reg_id);

        if ($stmt->execute()) {
            echo "<script>alert('Registrar record updated successfully!'); window.location.href='$current_page';</script>";
        } else {
            echo "<script>alert('Error updating record.');</script>";
        }
        $stmt->close();
    }
}

// Fetch Registrar Records
$sql = "SELECT * FROM REGISTRAR";
$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Management System</title>
    <style>
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
            width: 98%;
            max-width: 1300px;
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

        .table-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 98%;
            max-width: 1300px;
            box-sizing: border-box;
            overflow-x: auto;
        }

        h1 { font-size: 24px; color: #1565c0; margin: 0; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #f8fbff;
            color: #546e7a;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px;
            border-bottom: 2px solid #e3f2fd;
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f4f9;
            vertical-align: middle;
            font-size: 13px;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px 10px;
            border: 2px solid #e3f2fd;
            border-radius: 6px;
            font-size: 13px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus {
            border-color: #1976d2;
            outline: none;
            background-color: #fff;
        }

        .id-badge {
            background-color: #f1f8ff;
            color: #1976d2;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
        }

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
        }

        .btn-main:hover { background-color: #1565c0; transform: translateY(-1px); }

        .btn-update {
            background-color: #1976d2;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 5px;
            width: 80px;
        }

        .btn-delete {
            background-color: #ef5350;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            width: 80px;
        }

        .btn-update:hover { background-color: #1565c0; }
        .btn-delete:hover { background-color: #d32f2f; }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: center;
        }

        footer {
            margin-top: 30px;
            padding: 20px;
            color: #78909c;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <div>
            <h1>Registrar Records</h1>
            <p style="margin:0; color: #78909c; font-size: 11px; font-weight: bold; text-transform: uppercase;">Administrative Logs</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="btn-main" onclick="window.location.href='End-User.php'">Logout</button>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 70px;">Reg ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Station</th>
                    <th>Payment</th>
                    <th>Shift</th>
                    <th>Patient</th>
                    <th>Nurse</th>
                    <th>Room</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <form action="<?php echo htmlspecialchars($current_page); ?>" method="post">
                                <td>
                                    <input type="hidden" name="REG_ID" value="<?php echo htmlspecialchars($row['REG_ID']); ?>">
                                    <span class="id-badge"><?php echo htmlspecialchars($row['REG_ID']); ?></span>
                                </td>
                                <td><input type="text" name="REG_FNAME" value="<?php echo htmlspecialchars($row['REG_FNAME']); ?>" required></td>
                                <td><input type="text" name="REG_LNAME" value="<?php echo htmlspecialchars($row['REG_LNAME']); ?>" required></td>
                                <td>
                                    <input type="text" name="REG_NUM_STATION" value="<?php echo htmlspecialchars($row['REG_NUM_STATION']); ?>" pattern="\d+" title="Only numbers" required>
                                </td>
                                <td><input type="text" name="REG_MOP" value="<?php echo htmlspecialchars($row['REG_MOP']); ?>" required></td>
                                <td><input type="text" name="REG_SHIFT" value="<?php echo htmlspecialchars($row['REG_SHIFT']); ?>" required></td>
                                <td>
                                    <input type="text" name="PATIENT_ID" value="<?php echo htmlspecialchars($row['PATIENT_ID']); ?>" required>
                                </td>
                                <td>
                                    <input type="text" name="NURSE_ID" value="<?php echo htmlspecialchars($row['NURSE_ID']); ?>" required>
                                </td>
                                <td>
                                    <input type="text" name="ROOM_NUM" value="<?php echo htmlspecialchars($row['ROOM_NUM']); ?>" pattern="\d+" title="Only numbers" required>
                                </td>
                                <td class="action-buttons">
                                    <input type="submit" name="update" value="Update" class="btn-update">
                                    <input type="submit" name="delete" value="Delete" class="btn-delete" onclick="return confirm('Are you sure you want to delete this record?');">
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 30px; color: #78909c;">No registrar logs found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>
        &copy; 2026 Hospital Management System | Administrative Portal
    </footer>

</body>
</html>
<?php
$mysqli->close();
?>