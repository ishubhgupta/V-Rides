<!-- this is code for user dashboard where user will control all activites -->
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

$vit_registration_number = $conn->real_escape_string($_SESSION["vit_registration_number"]);

// Fetch user information
$user_info_sql = "SELECT * FROM user_info WHERE vit_registration_number = '$vit_registration_number'";
$user_info_result = $conn->query($user_info_sql);

if ($user_info_result->num_rows == 1) {
    $user_info = $user_info_result->fetch_assoc();
} else {
    // Handle the case where user information is not found
    echo "User information not found.";
    exit;
}

// Fetch past rides
$past_rides_sql = "SELECT * FROM rides WHERE vit_registration_number = '$vit_registration_number'";
$past_rides_result = $conn->query($past_rides_sql);

// Check if there are past rides
if ($past_rides_result->num_rows > 0) {
    $rides = $past_rides_result->fetch_all(MYSQLI_ASSOC);
    $displayedRides = array_slice($rides, 0, 3);
} else {
    $displayedRides = array(); // Set $displayedRides to an empty array if there are no past rides
}

// Fetch wallet details
$wallet_sql = "SELECT * FROM wallet WHERE vit_registration_number = '$vit_registration_number'";
$wallet_result = $conn->query($wallet_sql);

if ($wallet_result->num_rows == 1) {
    $wallet_info = $wallet_result->fetch_assoc();
} else {
    // Handle the case where wallet information is not found
    echo "Wallet information not found.";
    exit;
}

// Submit feedback
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_feedback"])) {
    $user_email = $user_info["email_id"]; // Assuming 'email' is a field in the 'user_info' table

    // Sanitize the input to prevent SQL injection
    $feedback = $conn->real_escape_string($_POST["feedback"]);

    // Use prepared statement to prevent SQL injection
    $insertFeedbackSql = $conn->prepare("INSERT INTO feedback (name, vit_registration_number, email, query, solved) VALUES (?, ?, ?, ?, 0)");
    $insertFeedbackSql->bind_param("ssss", $user_info["name"], $user_info["vit_registration_number"], $user_email, $feedback);

    if ($insertFeedbackSql->execute()) {
        echo '<p style="color: green;">Feedback submitted successfully!</p>';
    } else {
        echo '<p style="color: red;">Error submitting feedback: ' . $conn->error . '</p>';
    }

    $insertFeedbackSql->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="css/user_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* Additional CSS for improved styling */
        .feedback-form {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <img src="assets/logo_index.png" alt="Logo">
            </div>
        </div>
    </nav>

    <section class="flex">
        <div class="left-nav">
            <div class="review">
                <img src="assets/circle.png" alt="photo of the reviewer">
            </div>
            <h1>Welcome back,</h1>
            <p><?php echo htmlspecialchars($user_info["name"]); ?></p>
            <?php
            // Assuming $user_info is the user information array
            $isSuspended = $user_info["suspended"];

            // Check if the account is not suspended before showing the "Add Token" button
            if (!$isSuspended) {
                echo '<form method="post" action="wallet.php">';
                echo '<button type="submit" class="active">Add Token</button>';
                echo '</form>';
            }
            ?>

            <button class="active">
                <a href="previous_rides.php" class="button">View Previous Rides</a>
            </button>            
            <button class="active">
                <a href="user_queries.php" class="button">View Previous Queries</a>
            </button>
            <form method="post" action="logout.php"> <!-- Add a form for logout -->
                <button class="active" type="submit" name="logout">Logout</button>
            </form>
        </div>

        <div class="main-content">
            <div class="flex-item <?php echo ($isSuspended) ? 'account-suspended' : 'text-warnings'; ?>">
                <h3>Registration number:</h3>
                <p class="text_">Reg. no. : <?php echo htmlspecialchars($user_info["vit_registration_number"]); ?></p>

                <?php
                // Check if there are warnings
                $warningCount = $user_info["warnings"]; // Assuming warnings field in the table
                if ($warningCount > 0) {
                    echo '<h3>Warning</h3>';
                    echo '<p>Number of warnings given: ' . $warningCount . '</p>';
                }

                // Check if the account is suspended
                if ($isSuspended) {
                    echo '<h3>Your account is suspended</h3>';
                }
                ?>
            </div>

            <div class="flex-item">
                <h3>Wallet Details</h3>
                <div class="wallet">
                    <img src="assets/wallet.png" alt="wallet_icon">
                </div>
                <p>Wallet Balance: <?php echo htmlspecialchars($wallet_info["wallet_balance"]); ?> tokens</p>
            </div>

            <div class="flex-item feedback">
                <h3>Feedback and Queries</h3>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="feedback-form">
                    <label for="feedback">Your Feedback or Query:</label>
                    <textarea name="feedback" id="feedback" rows="4" cols="50" required></textarea>
                    <button type="submit" class="button" name="submit_feedback">Submit</button>
                </form>
              
            </div>

            <div class="flex-item">
                <h3>Past Rides</h3>
                <?php if (!empty($displayedRides)) { ?>
                    <table border="1" class="past-rides-table">
                        <tr>
                            <th>Ride ID</th>
                            <th>Cycle ID</th>
                            <th>Ride Date</th>
                            <th>Distance (in m)</th>
                            <th>Duration</th>
                            <th>Ride Cost</th>
                        </tr>
                        <?php foreach ($displayedRides as $row) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["ride_id"]); ?></td>
                                <td><?php echo htmlspecialchars($row["cycle_id"]); ?></td>
                                <td><?php echo htmlspecialchars($row["ride_date"]); ?></td>
                                <td><?php echo htmlspecialchars($row["distance"]); ?></td>
                                <td><?php echo htmlspecialchars($row["duration"]); ?></td>
                                <td><?php echo htmlspecialchars($row["ride_cost"]); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                    
                <?php } else { ?>
                    <p>No past rides found.</p>
                <?php } ?>
            </div>
        </div>
    </section>

</body>

</html>
<?php


?>
