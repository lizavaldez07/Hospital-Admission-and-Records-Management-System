<?php
// Database connection
$user = 'root';
$password = '';
$database = 'Hospital';
$servername = 'localhost:3306';

$mysqli = new mysqli($servername, $user, $password, $database);

// Check for connection error
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// SQL query to fetch data
$sql = "SELECT * FROM Hospital.PATIENT";
$result = $mysqli->query($sql);

$sql_doctors = "SELECT * FROM Hospital.DOCTOR";  
$result_doctors = $mysqli->query($sql_doctors);

$sql_room = "SELECT * FROM Hospital.H_ROOM";  
$result_room = $mysqli->query($sql_room);

$sql_nurses = "SELECT * FROM Hospital.NURSE";  
$result_nurses = $mysqli->query($sql_nurses);

$sql_registrar = "SELECT * FROM Hospital.REGISTRAR";  
$result_registrar = $mysqli->query($sql_registrar);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Records</title>
    <style>
        /* Modernized Dashboard UI */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e3f2fd; /* Light blue background */
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #333;
        }

        h2 {
            font-size: 24px;
            color: #1565c0;
            margin-top: 30px;
            margin-bottom: 15px;
            align-self: flex-start;
            width: 95%;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            border-left: 5px solid #1976d2;
            padding-left: 15px;
        }

        /* Top Action Bar */
        .header-container {
            width: 95%;
            max-width: 1200px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        /* Table Container for Responsiveness */
        .table-responsive {
            width: 95%;
            max-width: 1200px;
            background-color: #ffffff;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            min-width: 800px;
        }

        th {
            background-color: #f8fbff;
            color: #1565c0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 15px;
            border-bottom: 2px solid #e3f2fd;
            text-align: left;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f4f7;
            color: #546e7a;
        }

        tr:hover {
            background-color: #f1f8ff;
        }

        /* Button Styling */
        button, .btn-submit {
            padding: 8px 16px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        /* Primary Back/Add Button */
        .btn-main {
            background-color: #1976d2;
            color: white;
        }

        .btn-main:hover {
            background-color: #1565c0;
            transform: translateY(-1px);
        }

        /* Action Buttons in Table */
        input[name="edit"] {
            background-color: #e3f2fd;
            color: #1976d2;
            border: 1px solid #1976d2;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        input[name="delete"] {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-left: 5px;
        }

        input[name="edit"]:hover { background-color: #bbdefb; }
        input[name="delete"]:hover { background-color: #ffcdd2; }

        .status-pill {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            background-color: #e8f5e9;
            color: #2e7d32;
        }
    </style>
</head>
<body>

<div class="header-container" style="
    background: #ffffff; 
    padding: 20px 30px; 
    border-radius: 12px; 
    box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
    border-bottom: 4px solid #1976d2;
    margin-bottom: 30px;
    margin-top: 10px;
">
    <div style="display: flex; align-items: center; gap: 15px;">
        <div style="background: #e3f2fd; padding: 10px; border-radius: 8px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1565c0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
        </div>
        <div>
            <h1 style="color: #1565c0; margin: 0; font-size: 28px; letter-spacing: -0.5px;">Hospital Records</h1>
            <p style="margin: 0; color: #78909c; font-size: 13px; font-weight: bold; text-transform: uppercase;">Central Database Management</p>
        </div>
    </div>
    <button class="btn-main" onclick="window.location.href='Home-Page.php'" style="white-space: nowrap;">← Back to Dashboard</button>
</div>

<h2>Patient Registry</h2>
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Med History</th>
                <th>Time</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo '<tr>
                        <td><strong>' . $row['PATIENT_ID'] . '</strong></td>
                        <td>' . $row['PATIENT_TYPE'] . '</td>
                        <td>' . $row['PATIENT_LNAME'] . '</td>
                        <td>' . $row['PATIENT_FNAME'] . '</td>
                        <td>' . $row['PATIENT_MED_HISTORY'] . '</td>
                        <td>' . $row['PATIENT_ADMI_TIME'] . '</td>
                        <td>' . $row['PATIENT_ADMI_DATE'] . '</td>
                        <td>
                            <form action="Admission.php" method="post" style="display:inline;">
                                <input type="hidden" name="PATIENT_ID" value="' . $row['PATIENT_ID'] . '">
                                <input type="submit" name="edit" value="Edit">
                            </form>
                            <form action="Admission.php" method="post" style="display:inline;">
                                <input type="hidden" name="PATIENT_ID" value="' . $row['PATIENT_ID'] . '">
                                <input type="submit" name="delete" value="Delete">
                            </form>
                        </td>
                      </tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<h2>Doctor Directory</h2>
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Dr. ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Contact</th>
                <th>Specialization</th>
                <th>Station</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result_doctors->fetch_assoc()) {
                echo '<tr>
                        <td><strong>' . $row['DR_ID'] . '</strong></td>
                        <td>' . $row['DR_LNAME'] . '</td>
                        <td>' . $row['DR_FNAME'] . '</td>
                        <td>' . $row['DR_CNUM'] . '</td>
                        <td><span class="status-pill">' . $row['DR_SPECIALIZATION'] . '</span></td>
                        <td>' . $row['DR_NUM_STATION'] . '</td>
                        <td>
                            <form action="Doctor.php" method="post" style="display:inline;">
                                <input type="hidden" name="DR_ID" value="' . $row['DR_ID'] . '">
                                <input type="submit" name="edit" value="Edit">
                            </form>
                            <form action="Doctor.php" method="post" style="display:inline;">
                                <input type="hidden" name="DR_ID" value="' . $row['DR_ID'] . '">
                                <input type="submit" name="delete" value="Delete">
                            </form>
                        </td>
                      </tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<div class="header-container" style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center; width: 95%; max-width: 1200px; margin-left: auto; margin-right: auto;">
    <h2 style="margin: 0; flex-grow: 1;">Facility Rooms</h2>
    <button class="btn-main" onclick="window.location.href='Hospital-Room.php'">+ Add New Room</button>
</div>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Room #</th>
                <th>Type</th>
                <th>Availability</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result_room->fetch_assoc()) {

                echo '<tr>
                        <td><strong>' . $row['ROOM_NUM'] . '</strong></td>
                        <td>' . $row['ROOM_TYPE'] . '</td>
                        <td>' . $row['ROOM_AVAILABILITY'] . '</td>
                        <td>
                            <form action="Hospital-Room.php" method="post" style="display:inline;">
                                <input type="hidden" name="ROOM_NUM" value="' . $row['ROOM_NUM'] . '">
                                <input type="submit" name="edit" value="Edit">
                            </form>
                            <form action="Hospital-Room.php" method="post" style="display:inline;">
                                <input type="hidden" name="ROOM_NUM" value="' . $row['ROOM_NUM'] . '">
                                <input type="submit" name="delete" value="Delete">
                            </form>
                        </td>
                    </tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<h2>Nurse Assignments</h2>
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Nurse ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Station</th>
                <th>Specialization</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result_nurses->fetch_assoc()) {
                echo '<tr>
                        <td><strong>' . $row['NURSE_ID'] . '</strong></td>
                        <td>' . $row['NURSE_LNAME'] . '</td>
                        <td>' . $row['NURSE_FNAME'] . '</td>
                        <td>' . $row['NURSE_NUM_STATION'] . '</td>
                        <td>' . $row['NURSE_SPECIALIZATION'] . '</td>
                        <td>' . $row['PATIENT_ID'] . '</td>
                        <td>' . $row['DR_ID'] . '</td>
                        <td>
                            <form action="Nurse.php" method="post" style="display:inline;">
                                <input type="hidden" name="NURSE_ID" value="' . $row['NURSE_ID'] . '">
                                <input type="submit" name="edit" value="Edit">
                            </form>
                            <form action="Nurse.php" method="post" style="display:inline;">
                                <input type="hidden" name="NURSE_ID" value="' . $row['NURSE_ID'] . '">
                                <input type="submit" name="delete" value="Delete">
                            </form>
                        </td>
                      </tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<h2>Registrar Logs</h2>
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Reg. ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Station</th>
                <th>Payment</th>
                <th>Shift</th>
                <th>Patient</th>
                <th>Nurse</th>
                <th>Room</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result_registrar->fetch_assoc()) {
                echo '<tr>
                        <td><strong>' . $row['REG_ID'] . '</strong></td>
                        <td>' . $row['REG_LNAME'] . '</td>
                        <td>' . $row['REG_FNAME'] . '</td>
                        <td>' . $row['REG_NUM_STATION'] . '</td>
                        <td>' . $row['REG_MOP'] . '</td>
                        <td>' . $row['REG_SHIFT'] . '</td>
                        <td>' . $row['PATIENT_ID'] . '</td>
                        <td>' . $row['NURSE_ID'] . '</td>
                        <td>' . $row['ROOM_NUM'] . '</td>
                        <td>
                            <form action="Registrar.php" method="post" style="display:inline;">
                                <input type="hidden" name="REG_ID" value="' . $row['REG_ID'] . '">
                                <input type="submit" name="edit" value="Edit">
                            </form>
                            <form action="Registrar.php" method="post" style="display:inline;">
                                <input type="hidden" name="REG_ID" value="' . $row['REG_ID'] . '">
                                <input type="submit" name="delete" value="Delete">
                            </form>
                        </td>
                      </tr>';
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
