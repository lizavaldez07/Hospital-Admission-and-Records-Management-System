<?php
// Database Configuration
$user = 'root';
$password = '';
$database = 'Hospital';
$servername = 'localhost:3306';

$mysqli = new mysqli($servername, $user, $password, $database);

// Check for connection errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fixed: Corrected the filename in the logic to match the current page context
$current_page = basename(__FILE__); 

// Handle Delete Operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $nurse_id = intval($_POST['NURSE_ID']);
    $stmt = $mysqli->prepare("DELETE FROM NURSE WHERE NURSE_ID = ?");
    $stmt->bind_param("i", $nurse_id);

    if ($stmt->execute()) {
        // Updated alert to match DoctorRecords style
        echo "<script>alert('Nurse record deleted successfully.'); window.location.href='$current_page';</script>";
    } else {
        echo "<script>alert('Error deleting record: " . addslashes($stmt->error) . "');</script>";
    }
    $stmt->close();
}

// Handle Update Operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $nurse_id = intval($_POST['NURSE_ID']);
    // Apply name formatting (ucwords) consistent with DoctorRecords
    $nurse_lname = ucwords(strtolower(trim($_POST['NURSE_LNAME'])));
    $nurse_fname = ucwords(strtolower(trim($_POST['NURSE_FNAME'])));
    $nurse_num_station = trim($_POST['NURSE_NUM_STATION']);
    $nurse_specialization = trim($_POST['NURSE_SPECIALIZATION']);
    $patient_id = trim($_POST['PATIENT_ID']);
    $dr_id = trim($_POST['DR_ID']);

    // Validate numeric fields
    if (!ctype_digit($nurse_num_station)) {
        echo "<script>alert('Station Number must be numeric.');</script>";
    } else {
        $stmt = $mysqli->prepare(
            "UPDATE NURSE 
            SET NURSE_LNAME = ?, NURSE_FNAME = ?, NURSE_NUM_STATION = ?, NURSE_SPECIALIZATION = ?, PATIENT_ID = ?, DR_ID = ? 
            WHERE NURSE_ID = ?"
        );
        
        // Using "ssssssi" to ensure all VARCHAR and INT fields are handled correctly
        $stmt->bind_param("ssssssi", $nurse_lname, $nurse_fname, $nurse_num_station, $nurse_specialization, $patient_id, $dr_id, $nurse_id);

        if ($stmt->execute()) {
            // Updated alert to match DoctorRecords style
            echo "<script>alert('Nurse record updated successfully.'); window.location.href='$current_page';</script>";
        } else {
            // Error alert with technical details if the update fails
            echo "<script>alert('Error updating record: " . addslashes($stmt->error) . "');</script>";
        }
        $stmt->close();
    }
}

// Fetch Nurse Records
$sql = "SELECT * FROM NURSE";
$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Management System</title>
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
            font-size: 14px;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px 10px;
            border: 2px solid #e3f2fd;
            border-radius: 6px;
            font-size: 14px;
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
            font-size: 13px;
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
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 5px;
            width: 85px;
        }

        .btn-delete {
            background-color: #ef5350;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            width: 85px;
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
            <h1>Nurse Records</h1>
            <p style="margin:0; color: #78909c; font-size: 11px; font-weight: bold; text-transform: uppercase;">Medical Staff Management</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="btn-main" onclick="window.location.href='End-User.php'">Logout</button>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 80px;">Nurse ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Station No.</th>
                    <th>Specialization</th>
                    <th>Patient ID</th>
                    <th>Doctor ID</th>
                    <th style="width: 110px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <form action="<?php echo htmlspecialchars($current_page); ?>" method="post">
                                <td>
                                    <input type="hidden" name="NURSE_ID" value="<?php echo htmlspecialchars($row['NURSE_ID']); ?>">
                                    <span class="id-badge"><?php echo htmlspecialchars($row['NURSE_ID']); ?></span>
                                </td>
                                <td><input type="text" name="NURSE_FNAME" value="<?php echo htmlspecialchars($row['NURSE_FNAME']); ?>" required></td>
                                <td><input type="text" name="NURSE_LNAME" value="<?php echo htmlspecialchars($row['NURSE_LNAME']); ?>" required></td>
                                <td>
                                    <input type="text" name="NURSE_NUM_STATION" value="<?php echo htmlspecialchars($row['NURSE_NUM_STATION']); ?>" pattern="\d+" title="Only numbers are allowed" required>
                                </td>
                                <td><input type="text" name="NURSE_SPECIALIZATION" value="<?php echo htmlspecialchars($row['NURSE_SPECIALIZATION']); ?>" required></td>
                                <td>
                                    <input type="text" name="PATIENT_ID" value="<?php echo htmlspecialchars($row['PATIENT_ID']); ?>" required>
                                </td>
                                <td>
                                    <input type="text" name="DR_ID" value="<?php echo htmlspecialchars($row['DR_ID']); ?>" required>
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
                        <td colspan="8" style="text-align: center; padding: 30px; color: #78909c;">No nursing records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>
        &copy; 2024 Hospital Management System | Nursing Division
    </footer>

</body>
</html>
<?php
$mysqli->close();
?>