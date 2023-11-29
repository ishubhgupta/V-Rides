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

// Fetch available cycles
$cycles_sql = "SELECT * FROM cycle_info WHERE status = 'available'";
$cycles_result = $conn->query($cycles_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Cycle</title>
    <link rel="stylesheet" type="text/css" href="css/select_cycle.css">
</head>
<body>
    <header>
        <img src="assets/logo.png" alt="Logo">
        <h1>V-Rides</h1>
    </header>

    <div class="content">
        <h2>Select a Cycle</h2>

        <?php if ($cycles_result->num_rows > 0) { ?>
            <ul>
                <?php while ($cycle = $cycles_result->fetch_assoc()) { ?>
                    <li>
                        <!-- Display the cycle link with an identifier -->
                        <strong>Cycle Link for Cycle ID <?php echo $cycle['cycle_id']; ?>:</strong>
                        <a href="user_authentication.php?cycle_id=<?php echo $cycle['cycle_id']; ?>" >
                            <?php echo $cycle['cycle_link']; ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p>No cycles available at the moment.</p>
        <?php } ?>

        <br>
        <a href="user_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>

