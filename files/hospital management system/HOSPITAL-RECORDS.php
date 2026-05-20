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
        body {
            background-image: url('REGISTRATION.png'); /* Replace with your image file */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Arial', sans-serif;
            background-color: #f1f4f7;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            font-size: 28px;
            color: #2f4f4f;
            margin-top: 20px;
        }

        table {
            width: 90%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }

        table, th, td {
            border: 1px solid #f2f2f2;
        }

        th, td {
            padding: 12px;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .button-container button {
            margin: 5px;
        }

        input[type="submit"], button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            transition: all 0.3s ease;
        }

        input[type="submit"]:hover,
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h2>Hospital Records</h2>
<div class="button-container">
    <button onclick="window.location.href='Home-Page.php'">Back</button>
</div>

<!-- Patients Table -->
<h2>Patients</h2>
<table>
    <tr>
        <th>Patient ID</th>
        <th>Patient Type</th>
        <th>Patient Last Name</th>
        <th>Patient First Name</th>
        <th>Medical History</th>
        <th>Admission Time</th>
        <th>Admission Date</th>
        <th>Actions</th>
    </tr>
    <?php
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . $row['PATIENT_ID'] . '</td>
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
</table>

<!-- Doctors Table -->
<h2>Doctors</h2>
<table>
    <tr>
        <th>Doctor ID</th>
        <th>Doctor Last Name</th>
        <th>Doctor First Name</th>
        <th>Contact Number</th>
        <th>Specialization</th>
        <th>Station Number</th>
        <th>Actions</th>
    </tr>
    <?php
    while ($row = $result_doctors->fetch_assoc()) {
        echo '<tr>
                <td>' . $row['DR_ID'] . '</td>
                <td>' . $row['DR_LNAME'] . '</td>
                <td>' . $row['DR_FNAME'] . '</td>
                <td>' . $row['DR_CNUM'] . '</td>
                <td>' . $row['DR_SPECIALIZATION'] . '</td>
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
</table>

<!-- Room Table -->
<h2>Rooms</h2>
<div class="button-container">
    <button onclick="window.location.href='Hospital Room Admission.php'">Add New Room</button>
</div>
<table>
    <tr>
        <th>Room Number</th>
        <th>Room Type</th>
        <th>Room Availability</th>
        <th>Actions</th>
    </tr>
    <?php
    while ($row = $result_room->fetch_assoc()) {
        echo '<tr>
                <td>' . $row['ROOM_NUM'] . '</td>
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
</table>


<!-- Nurse Table -->
<h2>Nurses</h2>
<table>
    <tr>
        <th>Nurse ID</th>
        <th>Nurse Last Name</th>
        <th>Nurse First Name</th>
        <th>Station Number</th>
        <th>Specialization</th>
        <th>Patient ID</th>
        <th>Doctor ID</th>
        <th>Actions</th>
    </tr>
    <?php
    while ($row = $result_nurses->fetch_assoc()) {
        echo '<tr>
                <td>' . $row['NURSE_ID'] . '</td>
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
</table>

<!-- Registrar Table -->
<h2>Registrars</h2>
<table>
    <tr>
        <th>Registrar ID</th>
        <th>Registrar Last Name</th>
        <th>Registrar First Name</th>
        <th>Station Number</th>
        <th>Mode of Payment</th>
        <th>Shift</th>
        <th>Patient ID</th>
        <th>Nurse ID</th>
        <th>Room Number</th>
        <th>Actions</th>
    </tr>
    <?php
    while ($row = $result_registrar->fetch_assoc()) {
        echo '<tr>
                <td>' . $row['REG_ID'] . '</td>
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
</table>

</body>
</html>

