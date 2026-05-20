<?php
$user = 'root';
$password = '';
$database = 'Hospital';
$servername = 'localhost:3306';

$mysqli = new mysqli($servername, $user, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fixed: Corrected the filename in the logic to match the current page context
$current_page = basename(__FILE__); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function sanitize_input($mysqli, $data) {
        return mysqli_real_escape_string($mysqli, trim($data));
    }

    if (isset($_POST['delete'])) {
        $dr_id = sanitize_input($mysqli, $_POST['DR_ID']);
        $sql = "DELETE FROM DOCTOR WHERE DR_ID = '$dr_id'";
        $mysqli->query($sql) or die($mysqli->error);
        echo "<script>alert('Doctor record deleted successfully.'); window.location.href='$current_page';</script>";
    }

    if (isset($_POST['update'])) {
        $dr_id = sanitize_input($mysqli, $_POST['DR_ID']);
        $dr_lname = ucwords(strtolower(sanitize_input($mysqli, $_POST['DR_LNAME'])));
        $dr_fname = ucwords(strtolower(sanitize_input($mysqli, $_POST['DR_FNAME'])));
        $dr_cnum = sanitize_input($mysqli, $_POST['DR_CNUM']);
        $dr_specialization = sanitize_input($mysqli, $_POST['DR_SPECIALIZATION']);
        $dr_num_station = sanitize_input($mysqli, $_POST['DR_NUM_STATION']);

        $sql = "UPDATE DOCTOR SET 
                DR_LNAME = '$dr_lname',
                DR_FNAME = '$dr_fname',
                DR_CNUM = '$dr_cnum',
                DR_SPECIALIZATION = '$dr_specialization',
                DR_NUM_STATION = '$dr_num_station'
                WHERE DR_ID = '$dr_id'";
        $mysqli->query($sql) or die($mysqli->error);
        echo "<script>alert('Doctor record updated successfully.'); window.location.href='$current_page';</script>";
    }
}

$sql = "SELECT * FROM DOCTOR";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Management System</title>
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
            max-width: 1200px;
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
            max-width: 1200px;
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
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px;
            border-bottom: 2px solid #e3f2fd;
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f4f9;
            vertical-align: middle;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px 10px;
            border: 2px solid #e3f2fd;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.3s;
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
            <h1>Doctor Registry</h1>
            <p style="margin:0; color: #78909c; font-size: 11px; font-weight: bold; text-transform: uppercase;">Medical Staff Management</p>
        </div>
        <button class="btn-main" onclick="window.location.href='End-User.php'">Logout</button>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Contact No.</th>
                    <th>Specialization</th>
                    <th>Station</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <form action="<?php echo $current_page; ?>" method="post">
                        <td>
                            <input type="hidden" name="DR_ID" value="<?php echo htmlspecialchars($row['DR_ID']); ?>">
                            <span class="id-badge"><?php echo htmlspecialchars($row['DR_ID']); ?></span>
                        </td>
                        <td><input type="text" name="DR_FNAME" value="<?php echo htmlspecialchars($row['DR_FNAME']); ?>" required></td>
                        <td><input type="text" name="DR_LNAME" value="<?php echo htmlspecialchars($row['DR_LNAME']); ?>" required></td>
                        <td>
                            <input type="text" name="DR_CNUM" value="<?php echo htmlspecialchars($row['DR_CNUM']); ?>" pattern="\d+" title="Only numbers allowed" required>
                        </td>
                        <td><input type="text" name="DR_SPECIALIZATION" value="<?php echo htmlspecialchars($row['DR_SPECIALIZATION']); ?>" required></td>
                        <td>
                            <input type="text" name="DR_NUM_STATION" value="<?php echo htmlspecialchars($row['DR_NUM_STATION']); ?>" required>
                        </td>
                        <td>
                            <input type="submit" name="update" value="Update" class="btn-update">
                            <input type="submit" name="delete" value="Delete" class="btn-delete" onclick="return confirm('Are you sure you want to delete Dr. <?php echo $row['DR_LNAME']; ?>?');">
                        </td>
                    </form>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <footer>
        &copy; 2024 Hospital Management System | Staff Portal
    </footer>

</body>
</html>

<?php
$mysqli->close();
?>