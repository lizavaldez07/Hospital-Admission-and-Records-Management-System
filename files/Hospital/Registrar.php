<?php
$user = 'root';
$password = '';
$database = 'Hospital';
$servername = 'localhost:3306';

$mysqli = new mysqli($servername, $user, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Initialize variables to prevent "undefined index" notices
$REG_LNAME = isset($_POST['REG_LNAME']) ? $_POST['REG_LNAME'] : null;
$REG_FNAME = isset($_POST['REG_FNAME']) ? $_POST['REG_FNAME'] : null;
$REG_NUM_STATION = isset($_POST['REG_NUM_STATION']) ? $_POST['REG_NUM_STATION'] : null;
$REG_MOP = isset($_POST['REG_MOP']) ? $_POST['REG_MOP'] : null;
$REG_SHIFT = isset($_POST['REG_SHIFT']) ? $_POST['REG_SHIFT'] : null;
$PATIENT_ID = isset($_POST['PATIENT_ID']) ? $_POST['PATIENT_ID'] : null;
$NURSE_ID = isset($_POST['NURSE_ID']) ? $_POST['NURSE_ID'] : null;
$ROOM_NUM = isset($_POST['ROOM_NUM']) ? $_POST['ROOM_NUM'] : null;
$REG_ID = isset($_POST['REG_ID']) ? $_POST['REG_ID'] : null;

$status_message = "";
$status_type = "";

// --- DELETE LOGIC ---
if (isset($_POST['delete'])) {
    $query = "SELECT PATIENT_ID, NURSE_ID FROM Hospital.REGISTRAR WHERE REG_ID = '$REG_ID'";
    $result = mysqli_query($mysqli, $query);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $p_id = $row['PATIENT_ID'];
        $n_id = $row['NURSE_ID'];
        mysqli_query($mysqli, "DELETE FROM Hospital.PATIENT WHERE PATIENT_ID = '$p_id'");
        mysqli_query($mysqli, "DELETE FROM Hospital.NURSE WHERE NURSE_ID = '$n_id'");
        if (mysqli_query($mysqli, "DELETE FROM Hospital.REGISTRAR WHERE REG_ID = '$REG_ID'")) {
            $status_message = "Record and associated data deleted successfully.";
            $status_type = "success-msg";
        }
    }
}

// --- UPDATE LOGIC ---
elseif (isset($_POST['update'])) {
    // 1. Check for empty fields
    if (empty($REG_LNAME) || empty($REG_FNAME) || empty($REG_NUM_STATION)) {
        $status_message = "Error: Name and Station Number are required.";
        $status_type = "error-msg";
    } 
    // 2. NEW VALIDATION: Exactly 6 digits only
    elseif (!preg_match('/^\d{6}$/', $REG_NUM_STATION)) {
        $status_message = "Error: Station Number must be exactly 6 digits (numbers only).";
        $status_type = "error-msg";
    }
    else {
        // Formatting names
        $REG_LNAME = ucwords(strtolower($REG_LNAME));
        $REG_FNAME = ucwords(strtolower($REG_FNAME));

        // Existence Checks
        $p_check = mysqli_query($mysqli, "SELECT * FROM Hospital.PATIENT WHERE PATIENT_ID = '$PATIENT_ID'");
        $n_check = mysqli_query($mysqli, "SELECT * FROM Hospital.NURSE WHERE NURSE_ID = '$NURSE_ID'");
        $r_check = mysqli_query($mysqli, "SELECT * FROM Hospital.H_ROOM WHERE ROOM_NUM = '$ROOM_NUM'");
        
        // Assignment conflict check (excluding current record)
        $assign_check = mysqli_query($mysqli, "SELECT * FROM Hospital.REGISTRAR WHERE (PATIENT_ID = '$PATIENT_ID' OR NURSE_ID = '$NURSE_ID' OR ROOM_NUM = '$ROOM_NUM') AND REG_ID != '$REG_ID'");

        if (mysqli_num_rows($p_check) == 0) {
            $status_message = "Error: Patient ID does not exist.";
            $status_type = "error-msg";
        } elseif (mysqli_num_rows($n_check) == 0) {
            $status_message = "Error: Nurse ID does not exist.";
            $status_type = "error-msg";
        } elseif (mysqli_num_rows($r_check) == 0) {
            $status_message = "Error: Room Number does not exist.";
            $status_type = "error-msg";
        } elseif (mysqli_num_rows($assign_check) > 0) {
            $status_message = "Error: Patient, Nurse, or Room is already assigned to another registrar.";
            $status_type = "error-msg";
        } else {
            $update_sql = "UPDATE Hospital.REGISTRAR SET 
                            REG_LNAME = '$REG_LNAME', REG_FNAME = '$REG_FNAME', REG_NUM_STATION = '$REG_NUM_STATION',
                            REG_MOP = '$REG_MOP', REG_SHIFT = '$REG_SHIFT', PATIENT_ID = '$PATIENT_ID',
                            NURSE_ID = '$NURSE_ID', ROOM_NUM = '$ROOM_NUM' WHERE REG_ID = '$REG_ID'";
            
            if (mysqli_query($mysqli, $update_sql)) {
                $status_message = "Record updated successfully!";
                $status_type = "success-msg";
            } else {
                $status_message = "Database Error: " . mysqli_error($mysqli);
                $status_type = "error-msg";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Registrar Record</title>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #e3f2fd; margin: 0; padding: 20px; display: flex; flex-direction: column; align-items: center; min-height: 100vh; }
        .header-container { width: 95%; max-width: 700px; background: #ffffff; padding: 20px 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-bottom: 4px solid #1976d2; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; box-sizing: border-box; }
        .form-container { background-color: #ffffff; padding: 30px 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); width: 100%; max-width: 700px; box-sizing: border-box; margin-bottom: 30px; }
        h1 { font-size: 24px; color: #1565c0; margin: 0; }
        label { display: block; font-size: 13px; font-weight: bold; color: #546e7a; margin-bottom: 8px; margin-top: 20px; text-transform: uppercase; }
        input[type="text"], select { width: 100%; padding: 12px; border: 2px solid #bbdefb; border-radius: 8px; font-size: 15px; box-sizing: border-box; outline: none; transition: border-color 0.3s; }
        input:focus, select:focus { border-color: #1976d2; }
        .btn-main { padding: 10px 20px; font-size: 14px; font-weight: bold; cursor: pointer; border: none; border-radius: 8px; background-color: #1976d2; color: white; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-main:hover { background-color: #1565c0; transform: translateY(-1px); }
        .btn-update { width: 100%; padding: 15px; margin-top: 30px; font-size: 16px; }
        .alert-box { width: 100%; max-width: 700px; padding: 15px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; font-weight: bold; text-align: center; border: 1px solid transparent; }
        .error-msg { background-color: #ffcdd2; color: #c62828; border-color: #ef9a9a; }
        .success-msg { background-color: #c8e6c9; color: #2e7d32; border-color: #a5d6a7; }
        .input-row { display: flex; gap: 15px; }
        .input-row > div { flex: 1; }
        footer { margin-top: auto; padding: 20px; color: #78909c; font-size: 13px; }
    </style>
</head>
<body>

<div class="header-container">
    <div>
        <h1>Edit Registrar Record</h1>
        <p style="margin:0; color: #78909c; font-size: 12px; font-weight: bold;">ADMINISTRATIVE MANAGEMENT</p>
    </div>
    <button class="btn-main" onclick="window.location.href='HOSPITAL-RECORDS.php'">← Back to Records</button>
</div>

<?php if ($status_message): ?>
    <div class="alert-box <?php echo $status_type; ?>">
        <?php echo $status_message; ?>
    </div>
<?php endif; ?>

<?php
// Display form if 'edit' was clicked OR if an 'update' attempt was made (failed or successful)
if (isset($_POST['edit']) || isset($_POST['update'])) {
    
    if (isset($_POST['edit'])) {
        $sql = "SELECT * FROM Hospital.REGISTRAR WHERE REG_ID = '$REG_ID'";
        $result = $mysqli->query($sql);
        $data = $result->fetch_assoc();
    } else {
        // If update was clicked, keep the data the user just typed (useful if there was an error)
        $data = $_POST; 
    }

    if ($data) {
        ?>
        <div class="form-container">
            <form action="" method="post">
                <input type="hidden" name="REG_ID" value="<?php echo $data['REG_ID']; ?>">

                <div class="input-row">
                    <div>
                        <label>First Name</label>
                        <input type="text" name="REG_FNAME" value="<?php echo $data['REG_FNAME']; ?>" required>
                    </div>
                    <div>
                        <label>Last Name</label>
                        <input type="text" name="REG_LNAME" value="<?php echo $data['REG_LNAME']; ?>" required>
                    </div>
                </div>

                <label>Registrar Station Number (6 Digits Only)</label>
                <input type="text" 
                       name="REG_NUM_STATION" 
                       value="<?php echo $data['REG_NUM_STATION']; ?>" 
                       required 
                       pattern="\d{6}" 
                       title="Must be exactly 6 digits (numbers only)"
                       placeholder="e.g. 123456">

                <div class="input-row">
                    <div>
                        <label>Mode of Payment</label>
                        <select name="REG_MOP">
                            <option value="Cash" <?php if ($data['REG_MOP'] == 'Cash') echo 'selected'; ?>>Cash</option>
                            <option value="Credit Card" <?php if ($data['REG_MOP'] == 'Credit Card') echo 'selected'; ?>>Credit Card</option>
                            <option value="Debit Card" <?php if ($data['REG_MOP'] == 'Debit Card') echo 'selected'; ?>>Debit Card</option>
                            <option value="Digital Wallet" <?php if ($data['REG_MOP'] == 'Digital Wallet') echo 'selected'; ?>>Digital Wallet</option>
                        </select>
                    </div>
                    <div>
                        <label>Shift</label>
                        <select name="REG_SHIFT">
                            <option value="Day" <?php if ($data['REG_SHIFT'] == 'Day') echo 'selected'; ?>>Day</option>
                            <option value="Swing" <?php if ($data['REG_SHIFT'] == 'Swing') echo 'selected'; ?>>Swing</option>
                            <option value="Graveyard" <?php if ($data['REG_SHIFT'] == 'Graveyard') echo 'selected'; ?>>Graveyard</option>
                        </select>
                    </div>
                </div>

                <div class="input-row">
                    <div>
                        <label>Patient ID</label>
                        <input type="text" name="PATIENT_ID" value="<?php echo $data['PATIENT_ID']; ?>" required>
                    </div>
                    <div>
                        <label>Nurse ID</label>
                        <input type="text" name="NURSE_ID" value="<?php echo $data['NURSE_ID']; ?>" required>
                    </div>
                </div>

                <label>Room Number</label>
                <input type="text" name="ROOM_NUM" value="<?php echo $data['ROOM_NUM']; ?>" required>

                <input type="submit" name="update" value="Save Changes" class="btn-main btn-update">
            </form>
        </div>
        <?php
    }
}
$mysqli->close();
?>

<footer>
    &copy; 2026 Hospital Management System
</footer>

</body>
</html>