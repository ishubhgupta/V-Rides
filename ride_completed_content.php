 <!-- this page shows information of a completed ride -->
 <?php
// session_start();

// Initialize variables
$thankYouMessage = "";

// Database connection
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "v_rides";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the cycle_id is set in the session
// if (!isset($_SESSION['cycle_id'])) {
//     // Redirect to select_cycle.php if cycle_id is not set
//     header("Location: select_cycle.php");
//     exit;
// }

// $cycleId = $_SESSION["cycle_id"];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitReview"])) {
    // Reduce cycle health by 2 points
    $updateCycleHealthSql = "UPDATE cycle_info SET health = health - 2 WHERE cycle_id = $cycleId";

    if ($conn->query($updateCycleHealthSql) === TRUE) {
        $thankYouMessage = '<p style="color: green;">Cycle health reduced by 2 points. Thank you for submitting the form!</p>';
    } else {
        $thankYouMessage = '<p style="color: red;">Error updating health: ' . $conn->error . '</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ride Completed</title>
    <link rel="stylesheet" href="css/ride_completed.css">
</head>
<body>

    <nav class="navbar">
        <div class="navbar-container">
          <div class="logo">
            <img src="assets/logo_index.png" alt="Logo">
          </div>
        </div>
    </nav>

    <main>
        <div class="container">
            <h1>Ride Completed</h1>
            
            <img src="assets/circle.png" alt="">
            <!-- Your ride completion details -->
            <p>Total Distance: <?php echo $total_distance; ?> m</p>
            <p>Duration: <?php echo $ride_duration; ?> seconds</p>
            <p>Base Price: <?php echo $base_price; ?> tokens</p>
            <p>Extratime Cost: <?php echo $overtime_cost; ?> tokens</p>
            <p style="color: red;">Total Amount: <?php echo $total_amount; ?> tokens</p>
        
            <!-- Display the thank you message -->
            <div id="thankYouMessage">
                <?php echo $thankYouMessage; ?>
            </div>
        
            <div>
                
        
                <!-- Display the review form -->
                <form id="reviewForm" method="post" action="logout.php">
                    <input type="submit" name="submitReview" value="Log Out">
                </form>
            </div>
        </div>
    </main>

</body>
</html>
