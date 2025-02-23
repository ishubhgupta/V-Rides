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
    <link rel="stylesheet" href="css/previous_rides.css">
</head>
<body>
    <div class="container">
        <h2>Previous Rides</h2>
        
        <?php if ($past_rides_result->num_rows > 0) { ?>
            <div class="table-wrapper">
                <table class="past-rides-table">
                    <thead>
                        <tr>
                            <th>Ride ID</th>
                            <th>Cycle ID</th>
                            <th>Ride Date</th>
                            <th>Distance</th>
                            <th>Duration</th>
                            <th>Ride Cost</th>
                        </tr>
                    </thead>
                    <tbody>
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
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p class="no-rides">No past rides found.</p>
        <?php } ?>

        <a href="user_dashboard.php" class="back-button">Back to User Dashboard</a>
    </div>
</body>
</html>
