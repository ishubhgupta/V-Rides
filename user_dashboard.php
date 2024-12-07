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
    // Add these lines to define the missing variables
    $warningCount = isset($user_info["warnings"]) ? $user_info["warnings"] : 0;
    $isSuspended = isset($user_info["suspended"]) ? $user_info["suspended"] : 0;
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
    <div class="dashboard-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="assets/logo_index.png" alt="Logo" class="logo">
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="assets/circle.png" alt="User Avatar">
                    </div>
                    <h2>Welcome back,</h2>
                    <p class="user-name"><?php echo htmlspecialchars($user_info["name"]); ?></p>
                </div>
            </div>
            <div class="sidebar-menu">
                <?php if (!$isSuspended) { ?>
                    <form method="post" action="wallet.php">
                        <button type="submit" class="menu-btn">
                            <i class="fa fa-plus-circle"></i> Add Token
                        </button>
                    </form>
                <?php } ?>
                <a href="previous_rides.php" class="menu-btn">
                    <i class="fa fa-history"></i> Previous Rides
                </a>
                <a href="user_queries.php" class="menu-btn">
                    <i class="fa fa-question-circle"></i> Previous Queries
                </a>
                <form method="post" action="logout.php" class="logout-form">
                    <button type="submit" name="logout" class="menu-btn logout">
                        <i class="fa fa-sign-out"></i> Logout
                    </button>
                </form>
            </div>
        </nav>

        <main class="main-content">
            <div class="content-grid">
                <div class="dashboard-card status-card <?php echo ($isSuspended) ? 'suspended' : ($warningCount > 0 ? 'warning' : ''); ?>">
                    <h3><i class="fa fa-id-card"></i> Account Status</h3>
                    <p>Reg. no.: <?php echo htmlspecialchars($user_info["vit_registration_number"]); ?></p>
                    <?php if ($warningCount > 0) { ?>
                        <div class="warning-badge">
                            <i class="fa fa-exclamation-triangle"></i>
                            <span>Warnings: <?php echo $warningCount; ?></span>
                        </div>
                    <?php } ?>
                    <?php if ($isSuspended) { ?>
                        <div class="suspended-badge">
                            <i class="fa fa-ban"></i>
                            <span>Account Suspended</span>
                        </div>
                    <?php } ?>
                </div>

                <div class="dashboard-card wallet-card">
                    <h3><i class="fa fa-wallet"></i> Wallet</h3>
                    <div class="wallet-balance">
                        <span class="balance-amount"><?php echo htmlspecialchars($wallet_info["wallet_balance"]); ?></span>
                        <span class="balance-label">tokens</span>
                    </div>
                </div>

                <div class="dashboard-card feedback-card">
                    <h3><i class="fa fa-comments"></i> Quick Feedback</h3>
                    <form method="post" class="feedback-form">
                        <textarea name="feedback" placeholder="Share your thoughts or concerns..." required></textarea>
                        <button type="submit" name="submit_feedback" class="submit-btn">
                            <i class="fa fa-paper-plane"></i> Submit
                        </button>
                    </form>
                </div>

                <div class="dashboard-card rides-card">
                    <h3><i class="fa fa-bicycle"></i> Recent Rides</h3>
                    <?php if (!empty($displayedRides)) { ?>
                        <div class="rides-table-wrapper">
                            <table class="rides-table">
                                <thead>
                                    <tr>
                                        <th>Ride ID</th>
                                        <th>Date</th>
                                        <th>Distance</th>
                                        <th>Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($displayedRides as $ride) { ?>
                                        <tr>
                                            <td>#<?php echo htmlspecialchars($ride["ride_id"]); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($ride["ride_date"])); ?></td>
                                            <td><?php echo htmlspecialchars($ride["distance"]); ?>m</td>
                                            <td><?php echo htmlspecialchars($ride["ride_cost"]); ?> tokens</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="no-rides">
                            <i class="fa fa-info-circle"></i>
                            <p>No rides yet</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
<?php


?>
