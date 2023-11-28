<!-- this page authenticate user after scanning QR -->
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

// Check if vit_registration_number and cycle_id are provided in the GET request
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['cycle_id'])) {
    $cycle_id = $_GET['cycle_id'];

    // You might want to add more authentication logic here
    // For now, we'll assume a simple check

    // Check if the cycle exists
    $cycle_check_sql = "SELECT * FROM cycle_info WHERE cycle_id = $cycle_id";
    $cycle_check_result = $conn->query($cycle_check_sql);

    if ($cycle_check_result->num_rows == 1) {
        // Cycle exists, set session variable

        // Check if the user's account is suspended
        $user_registration_number = $_SESSION['vit_registration_number'] ?? null;
        $user_suspended_sql = "SELECT suspended FROM user_info WHERE vit_registration_number = '$user_registration_number'";
        $user_suspended_result = $conn->query($user_suspended_sql);

        if ($user_suspended_result->num_rows == 1) {
            $user_suspended = $user_suspended_result->fetch_assoc()["suspended"];

            if ($user_suspended == 1) {
                // User's account is suspended, display an error message
                $error_message = "Your account is suspended. You can't proceed with the ride.";
            } else {
                // User's account is not suspended, redirect to the cycle ride page
                $_SESSION['cycle_id'] = $cycle_id;
                header("Location: cycle_ride.php");
                exit;
            }
        } else {
            // Unable to check account status, display an error message
            $error_message = "Unable to check your account status. Please try again.";
        }
    } else {
        // Cycle not found, display an error message
        $error_message = "Invalid Cycle ID. Please try again.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Authentication</title>
</head>
<body>
    <h2>User Authentication</h2>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <!-- Form not needed since we are using GET parameters -->
</body>
</html>
