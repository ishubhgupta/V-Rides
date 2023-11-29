<!-- this page track all ride history -->
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

// Fetch ride information
$rideInfoSql = "SELECT * FROM rides";
$rideInfoResult = $conn->query($rideInfoSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ride Information</title>
    <!-- Add your CSS link here -->
    <link rel="stylesheet" href="css/ride_info.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <img src="assets/logo_index.png" alt="Logo">
            </div>
        </div>
    </nav>
    <div class="container">
        <h1>Ride Information</h1>
        <?php if ($rideInfoResult->num_rows > 0) { ?>
        <table border='1'>
            <thead>
                <tr>
                    <th>Ride ID</th>
                    <th>User ID</th>
                    <th>Cycle ID</th>
                    <th>Ride Date</th>
                    <th>Distance (in m)</th>
                    <th>Duration</th>
                    <th>Ride Cost</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $rideInfoResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row["ride_id"]; ?></td>
                        <td><?php echo $row["vit_registration_number"]; ?></td>
                        <td><?php echo $row["cycle_id"]; ?></td>
                        <td><?php echo $row["ride_date"]; ?></td>
                        <td><?php echo $row["distance"]; ?></td>
                        <td><?php echo $row["duration"]; ?></td>
                        <td><?php echo $row["ride_cost"]; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
            <p>No past rides found.</p>
        <?php } ?>

        <br>

        <a class="back-to-dashboard" href="dev_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
