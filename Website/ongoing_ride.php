<!-- this code is for ongoing ride, having current ride details -->
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

// Check if the cycle_id is set in the session; if not, redirect to the cycle_info page
if (!isset($_SESSION["cycle_id"])) {
    header("Location: cycle_info.php");
    exit;
}

$vit_registration_number = $_SESSION["vit_registration_number"];
$cycle_id = $_SESSION["cycle_id"];

// Fetch user information
$user_info_sql = "SELECT * FROM user_info WHERE vit_registration_number = '$vit_registration_number'";
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

// Check if the ride is ongoing
if (isset($_SESSION["ride_start_time"]) && isset($_SESSION["ride_duration"])) {
    $start_time = $_SESSION["ride_start_time"];
    $duration = $_SESSION["ride_duration"];

    // Calculate elapsed time
    $current_time = time();
    $elapsed_time = $current_time - $start_time;

    // Check if the ride duration has passed
    if ($elapsed_time >= $duration) {
        // Calculate the base cost
        $base_cost = 10; // You can adjust this value

        // Calculate the overtime cost
        $overtime_minutes = ceil(($elapsed_time - $duration) / 60);
        $overtime_cost = $overtime_minutes * 0.5; // Adjust the overtime rate as needed

        // Calculate the total cost
        $total_cost = $base_cost + $overtime_cost;

        // Deduct the cost from the user's wallet (assuming you have a wallet system)
        $wallet_sql = "UPDATE wallet SET balance = balance - $total_cost WHERE vit_registration_number = '$vit_registration_number'";
        $conn->query($wallet_sql);

        // Update the ride completion details in the database (you may need to adjust this based on your database schema)
        $ride_id = $_SESSION["ride_id"];
        $completion_time = date("Y-m-d H:i:s");
        $update_ride_sql = "UPDATE rides SET status = 'completed', completion_time = '$completion_time', total_cost = $total_cost WHERE ride_id = $ride_id";
        $conn->query($update_ride_sql);

        // Clear the ride-related session variables
        unset($_SESSION["ride_id"]);
        unset($_SESSION["ride_start_time"]);
        unset($_SESSION["ride_duration"]);

        // Redirect to a page indicating ride completion
        header("Location: ride_completed.php");
        exit;
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ongoing Ride Details</title>
    <link rel="stylesheet" type="text/css" href="css/ongoing_ride.css">
    <script>
            var initialDuration = 90; // Initial ride duration in seconds
            var extraTimeRate = 30; // Cost per minute for overtime in tokens

            function updateTimer() {
                // var startTime = <?php echo $_SESSION["ride_start_time"]; ?>;
                var elapsedTime = Math.max(0, time() - startTime);
                var remainingTime = Math.max(initialDuration - elapsedTime, 0);

                if (remainingTime <= 0) {
                    // Stop updating timer if initial time is up
                    document.getElementById("timer").innerHTML = "Initial Time Expired";
                    startExtraTime(startTime);
                    return;
                }

                var minutes = Math.floor(remainingTime / 60);
                var seconds = remainingTime % 60;

                var formattedMinutes = (minutes < 10) ? '0' + minutes : minutes;
                var formattedSeconds = (seconds < 10) ? '0' + seconds : seconds;

                var timerDisplay = "Timer: " + formattedMinutes + "m " + formattedSeconds + "s";
                document.getElementById("timer").innerHTML = timerDisplay;

                // Schedule the next update in 1 second
                setTimeout(updateTimer, 1000);
            }

            function startExtraTime(startTime) {
                var extraTime = 0;

                // Start counting extra time
                var extraTimeInterval = setInterval(function () {
                    extraTime++;

                    // Display the extra time
                    displayExtraTime(extraTime);

                    // Calculate and display extra cost
                    var extraCost = Math.ceil(extraTime / 60) * extraTimeRate;
                    displayExtraCost(extraCost);
                }, 1000);

                // Store the extra time interval ID in a session variable for later use
                sessionStorage.setItem('extraTimeInterval', extraTimeInterval);
            }

            function displayExtraTime(extraTime) {
                var extraMinutes = Math.floor(extraTime / 60);
                var extraSeconds = extraTime % 60;
                var formattedExtraMinutes = (extraMinutes < 10) ? '0' + extraMinutes : extraMinutes;
                var formattedExtraSeconds = (extraSeconds < 10) ? '0' + extraSeconds : extraSeconds;

                var extraTimeDisplay = "Extra Time: " + formattedExtraMinutes + "m " + formattedExtraSeconds + "s";
                document.getElementById("extraTime").innerHTML = extraTimeDisplay;
            }

            function displayExtraCost(extraCost) {
                var extraCostDisplay = "Extra Cost: " + extraCost + " tokens";
                document.getElementById("extraCost").innerHTML = extraCostDisplay;
            }

            function time() {
                return Math.floor(new Date().getTime() / 1000);
            }

            // Initial call to start the timer
            updateTimer();

    </script>
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
            <h1>Ongoing Ride</h1>
        </div>
        <div class="flex-item flex-col">

            <div class ="review">
                <img src="assets/circle.png" alt="photo of the reviewer">
            </div>

            <div>
                <p class="text_">Name: <?php echo $user_info["name"]; ?></p>
            </div>

        </div>
        <div class="flex-item flex-col">
            <p class="text_">Cycle ID: <?php echo $cycle_info["cycle_id"]; ?></p>
        </div>


        <div class="flex-item flex-head">
            <h1>Actions</h1>
        </div>

        <form method="post" action="ride_completed.php">
            <input type="submit" name="complete_ride" value="Complete Ride">
        </form>

    </section>