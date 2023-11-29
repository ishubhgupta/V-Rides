<!-- this page make all the updation of ride in database -->
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

// Check if the cycle_id and ride_start_time are set in the session; if not, redirect to the ongoing ride page
if (!isset($_SESSION["cycle_id"]) || !isset($_SESSION["ride_start_time"])) {
    header("Location: ongoing_ride.php");
    exit;
}

// Check if the cycle_id is set in the session; if not, redirect to the cycle_info page
if (!isset($_SESSION["cycle_id"])) {
    header("Location: cycle_info.php");
    exit;
}

$vit_registration_number = $_SESSION["vit_registration_number"];
$cycle_id = $_SESSION["cycle_id"];
$ride_start_time = $_SESSION["ride_start_time"];

// Fetch user wallet information using prepared statements
$wallet_info_sql = "SELECT * FROM wallet WHERE vit_registration_number = ?";
$stmt = $conn->prepare($wallet_info_sql);
$stmt->bind_param("s", $vit_registration_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $wallet_info = $result->fetch_assoc();

    // Check if the wallet information is retrieved successfully
    if ($wallet_info) {
        // Check if the "wallet_balance" key exists in the array
        if (isset($wallet_info["wallet_balance"])) {
            // Calculate the ride duration
            $current_time = time();
            $ride_duration = $current_time - $ride_start_time;

            // Calculate tokens to be deducted (base price + overtime)
            $base_price = 10;
            $overtime_price = max(0, ceil($ride_duration / 60) - 15) * 0.5;
            $total_price = $base_price + $overtime_price;

            // Check if the user has enough tokens in the wallet
            if ($wallet_info["wallet_balance"] >= $total_price) {
                // Deduct tokens from the wallet using prepared statements
                $new_balance = $wallet_info["wallet_balance"] - $total_price;
                $update_wallet_sql = "UPDATE wallet SET wallet_balance = ? WHERE vit_registration_number = ?";
                $stmt = $conn->prepare($update_wallet_sql);
                $stmt->bind_param("ds", $new_balance, $vit_registration_number);
                $stmt->execute();

                // Update the ride duration in the database
                $update_duration_sql = "UPDATE cycle_info SET total_time = total_time + ? WHERE cycle_id = ?";
                $stmt = $conn->prepare($update_duration_sql);
                $stmt->bind_param("ii", $ride_duration, $cycle_id);
                $stmt->execute();

                // Update total_rides in cycle_info
                $update_cycle_sql = "UPDATE cycle_info SET total_rides = total_rides + 1 WHERE cycle_id = ?";
                $stmt = $conn->prepare($update_cycle_sql);
                $stmt->bind_param("i", $cycle_id);
                $stmt->execute();

                // Reduce the health of the cycle by 2
                $update_health_sql = "UPDATE cycle_info SET health = GREATEST(health - 2, 0) WHERE cycle_id = ?";
                $stmt = $conn->prepare($update_health_sql);
                $stmt->bind_param("i", $cycle_id);
                $stmt->execute();

                // Clear the ride-related session variables
                unset($_SESSION["cycle_id"]);
                unset($_SESSION["ride_start_time"]);

                // Calculate distance based on average speed (7 meters per second)
                $average_speed = 7;
                $calculated_distance = $average_speed * $ride_duration;

                // Update the total distance traveled in the database
                $update_distance_sql = "UPDATE cycle_info SET distance_travelled = distance_travelled + ? WHERE cycle_id = ?";
                $stmt = $conn->prepare($update_distance_sql);
                $stmt->bind_param("di", $calculated_distance, $cycle_id);
                $stmt->execute();

                // Insert ride details into rides table
                $insert_ride_sql = "INSERT INTO rides (cycle_id, ride_date, distance, duration, start_time, status, vit_registration_number, ride_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $status = 'completed'; // Assuming 'completed' is the status for a completed ride

                $current_time = time(); // Store the result of the time() function in a variable
                $ride_date = date('Y-m-d H:i:s'); // Store the result of the date() function in a variable

                $stmt = $conn->prepare($insert_ride_sql);
                $stmt->bind_param("ssddissd", $cycle_id, $ride_date, $calculated_distance, $ride_duration, $ride_start_time, $status, $vit_registration_number, $total_price);
                $stmt->execute();

                // Additional information for displaying
                $total_distance = $average_speed * $ride_duration;
                $overtime_cost = $overtime_price;
                $total_amount = $total_price;

                // Show the HTML content
                include('ride_completed_content.php');
                exit;
            } else {
                echo "Not enough tokens in the wallet.";
            }
        } else {
            echo "Error: Missing 'wallet_balance' key in wallet information.";
        }
    } else {
        echo "Error: Wallet information not found.";
    }
} else {
    echo "Error fetching wallet information: " . $conn->error;
}
?>
