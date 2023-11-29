<!-- this page page show information of particular cycle to start ride -->
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
    header("Location: ride_login.php");
    exit;
}


// Check if the cycle_id is set in the session; if not, redirect to the cycle_info page
if (!isset($_SESSION["cycle_id"])) {
    header("Location: cycle_info.php");
    exit;
}

$vit_registration_number = $_SESSION["vit_registration_number"];
$cycle_id = $_SESSION["cycle_id"];

// Fetch user information
$user_info_sql = "
    SELECT u.name, w.wallet_balance
    FROM user_info u
    JOIN wallet w ON u.vit_registration_number = w.vit_registration_number
    WHERE u.vit_registration_number = '$vit_registration_number'
";

$user_info_result = $conn->query($user_info_sql);

if ($user_info_result->num_rows == 1) {
    $user_info = $user_info_result->fetch_assoc();
}

// Fetch cycle information
$cycle_info_sql = "SELECT * FROM cycle_info WHERE cycle_id = $cycle_id";
$cycle_info_result = $conn->query($cycle_info_sql);

if ($cycle_info_result->num_rows == 1) {
    $cycle_info = $cycle_info_result->fetch_assoc();
} else {
    // Handle the case where cycle information is not found
    echo "Cycle information not found.";
    exit;
}



// Check if the user is starting the ride and has enough money
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["start_ride"])) {
    $requiredBalance = 10; // Adjust this value based on your ride cost

    // Check if the user has enough money in the wallet
    if ($user_info["wallet_balance"] >= $requiredBalance) {
        // Set the start time and duration in the session
        $_SESSION["ride_start_time"] = time();
        $_SESSION["ride_duration"] = 15 * 60; // 15 minutes in seconds

        // Redirect to the ongoing ride page
        header("Location: ongoing_ride.php");
        exit;
    } else {
        $insufficientBalanceMessage = "Not enough money. <a href='wallet.php'>Recharge your wallet now.</a>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cycle Ride Details</title>
    <link rel="stylesheet" type="text/css" href="css/cycle_ride.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
          <div class="logo">
            <img src="assets/logo_index.png" alt="Logo">
          </div>
        </div>
    </nav>

    <section class="main-content">
        <div class="flex-item flex-head">
            <h1>Cycle Ride Information</h1>
        </div>
        <div class="flex-item flex-col">

            <div class ="review">
                <img src="assets/circle.png" alt="photo of the reviewer">
            </div>

            <div>
                <p class="text_">Name: <?php echo $user_info["name"]; ?></p>
            </div>

        </div>
        <div class="flex-item">
            <h3>Cycle Info</h3>
            <p>Cycle ID: <?php echo $cycle_info["cycle_id"]; ?></p>
            <p>Health: <?php echo $cycle_info["health"]; ?></p>
            <p>Distance Traveled: <?php echo $cycle_info["distance_travelled"]; ?></p>
            <p>Total Time: <?php echo $cycle_info["total_time"]; ?></p>
        </div>

        <div class="start-ride-button">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="submit" name="start_ride" value="Start Ride">
            </form>
        </div>
 
        <!-- <?php
        if (isset($insufficientBalanceMessage)) {
            echo '<p style="color: red;">' . $insufficientBalanceMessage . '</p>';
        }
        ?> -->

    </section>

</body>
</html>