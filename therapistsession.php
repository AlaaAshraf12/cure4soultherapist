<!DOCTYPE html>
<html>
<head>
    <title>Booked Sessions</title>
    <style>
        body{background-color:#f0f7f8}
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            color:#164277
        }
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }
        .button:disabled {
            background-color: gray;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;color:#164277">Booked Sessions</h2>
    <table>
        <tr>
            <th>Day</th>
            <th>Date</th>
            <th>Time</th>
            <th>Action</th>
        </tr>
        <?php
       
        require_once "connection.php";
        require_once "index.php"; 
        $conn = OpenConnection();
        // Check if the therapist is logged in
        if (isset($_SESSION['name'])) {
            $therapistEmail = $_SESSION['name'];

            // Retrieve the therapist's ID based on their email
            $therapistQuery = "SELECT tid FROM therapist WHERE email = '$therapistEmail'";
            $therapistResult = sqlsrv_query($conn, $therapistQuery);
            $therapistRow = sqlsrv_fetch_array($therapistResult,SQLSRV_FETCH_ASSOC);
            $tid = $therapistRow['tid'];

            // Retrieve the booked sessions with pending attendstatus for the therapist
            $sessionQuery = "SELECT sid,date, dayy, Time1 FROM sessions WHERE tid = '$tid' AND status = 'booked' AND attendstatus = 'pending'";
            $sessionResult = sqlsrv_query($conn, $sessionQuery);

            while ($sessionRow =sqlsrv_fetch_array($sessionResult,SQLSRV_FETCH_ASSOC)) {
                $sid=$sessionRow['sid'];
                $date = $sessionRow['date'];
                $day = $sessionRow['dayy'];
                $time = $sessionRow['Time1'];

                echo "<tr>";
                echo "<td>$day</td>";
                echo "<td>$date</td>";
                echo "<td>$time</td>";
                echo "<td><button class='button' onclick='updateAttendStatus($sid, this)'>Go Live</button></td>";
                echo "</tr>";
            }
        }

        // Update attendstatus when Go Live button is clicked
        if (isset($_POST['sid'])) {
            $sid = $_POST['sid'];

            // Update the attendstatus column in the sessions table
            $updateQuery = "UPDATE sessions SET attendstatus = 'attended' WHERE sid = '$sid'";
            $updateResult =sqlsrv_query($conn, $updateQuery);

            if ($updateResult) {
                echo "success";
            } else {
                echo "error";
            }

            exit; // Stop further execution of the script
        }
        ?>
    </table>

    <script>
        function updateAttendStatus(sid, button) {
            // Make an AJAX request to update the attendstatus in the sessions table
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status == 200) {
                    button.textContent = 'Attended';
                    button.disabled = true;
                    window.location.href = 'video.html';
                } else {
                    alert('Request failed. Status: ' + xhr.status);
                }
            };
            xhr.send('sid=' + sid);
        }
    </script>
    
</body>
</html>
