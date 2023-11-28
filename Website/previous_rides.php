<!-- this page stores history of previous rides -->
<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "v_rides";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION["vit_registration_number"])) {
    header("Location: login.php");
    exit;
}

$vit_registration_number = $_SESSION["vit_registration_number"];

// fetching past rides
$past_rides_sql = "SELECT * FROM rides WHERE vit_registration_number = '$vit_registration_number'";
$past_rides_result = $conn->query($past_rides_sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Previous Rides</title>
    <link rel="stylesheet" href="css/user_dashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        h2 {
            text-align: center;
            color: #ccc;
        }

        .past-rides-table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            
        }

        .past-rides-table th,
        .past-rides-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        
        }
        .past-rides-table td{
            color: #ccc;
        }

        .past-rides-table th {
            background-color: #f0f0f0;
        }

        .button {
            background-color: orange;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .button:hover {
            background-color: darkorange;
        }
    </style>
</head>

<body>
    <h2>Previous Rides</h2>

    <?php if ($past_rides_result->num_rows > 0) { ?>
        <table border="1" class="past-rides-table">
            <tr>
                <th>Ride ID</th>
                <th>Cycle ID</th>
                <th>Ride Date</th>
                <th>Distance</th>
                <th>Duration</th>
                <th>Ride Cost</th>
            </tr>
            <?php while ($row = $past_rides_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row["ride_id"]; ?></td>
                    <td><?php echo $row["cycle_id"]; ?></td>
                    <td><?php echo $row["ride_date"]; ?></td>
                    <td><?php echo $row["distance"]; ?></td>
                    <td><?php echo $row["duration"]; ?></td>
                    <td><?php echo $row["ride_cost"]; ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No past rides found.</p>
    <?php } ?>

    <a href="user_dashboard.php" class="button">Back to User Dashboard</a>
</body>

</html>
