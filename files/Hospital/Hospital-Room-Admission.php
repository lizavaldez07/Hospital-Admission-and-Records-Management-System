# Discard 

<?php
$user = 'root';
$password = '';
$database = 'Hospital';
$servername = 'localhost:3306';

$mysqli = new mysqli($servername, $user, $password, $database);

$sql = "SELECT * FROM Hospital.H_ROOM";
$result = $mysqli->query($sql);
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Room Records</title>
    <style>
        body {
            background-image: url('REGISTRATION.png'); /* Replace with your image file */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Arial', sans-serif;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
        }
        table {
            width: 80%;
			height: 10%;
            border-collapse: collapse;
            margin-right: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .form-container {
            width: 15%;
            background-color: #fff;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        .form-container input[type="text"],
        .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .form-container input[type="submit"], 
        .form-container button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-container input[type="submit"]:hover, 
        .form-container button:hover {
            background-color: #0056b3;
        }
        .form-container .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <th>Room Number</th>
            <th>Room Type</th>
            <th>Room Availability</th>
        </tr>
        <?php
        while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
            <td><?php echo $row['ROOM_NUM']; ?></td>
            <td><?php echo $row['ROOM_TYPE']; ?></td>
            <td><?php echo $row['ROOM_AVAILABILITY']; ?></td>
        </tr>
        <?php
        }
        ?>
    </table>

    <div class="form-container">
        <form action="Hospital-Room.php" method="post" onsubmit="return validateForm()">
            <label for="ROOM_NUM">Room Number</label>
            <input type="text" name="ROOM_NUM" id="ROOM_NUM" required>
            <span class="error" id="error_room_num"></span>

            <label for="ROOM_TYPE">Room Type</label>
            <input type="text" name="ROOM_TYPE" id="ROOM_TYPE" required>
            <span class="error" id="error_room_type"></span>

            <label for="ROOM_AVAILABILITY">Room Availability</label>
            <select name="ROOM_AVAILABILITY" id="ROOM_AVAILABILITY" required>
                <option value="">Select</option>
                <option value="Available">Available</option>
                <option value="Not Available">Not Available</option>
            </select>
            <span class="error" id="error_room_availability"></span>

            <input type="submit" name="insert" value="Insert">

            <button type="button" onclick="window.open('HOSPITAL-RECORDS.php', '_blank')">Back</button>
        </form>
    </div>

    <script>
        function validateForm() {
            let valid = true;

            // Reset error messages
            document.getElementById("error_room_num").innerText = "";
            document.getElementById("error_room_type").innerText = "";
            document.getElementById("error_room_availability").innerText = "";

            // Validate Room Number
            const roomNum = document.getElementById("ROOM_NUM").value.trim();
            if (roomNum === "") {
                document.getElementById("error_room_num").innerText = "Room Number is required.";
                valid = false;
            }

            // Validate Room Type
            const roomType = document.getElementById("ROOM_TYPE").value.trim();
            if (roomType === "") {
                document.getElementById("error_room_type").innerText = "Room Type is required.";
                valid = false;
            }

            // Validate Room Availability
            const roomAvailability = document.getElementById("ROOM_AVAILABILITY").value;
            if (roomAvailability === "") {
                document.getElementById("error_room_availability").innerText = "Room Availability is required.";
                valid = false;
            }

            return valid;
        }
    </script>
</body>
</html>
