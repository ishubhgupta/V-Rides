<!-- admin dashboard page -->
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

// Move all database queries here at the top
// Cycles queries
$totalCyclesSql = "SELECT COUNT(*) as total_cycles FROM cycle_info";
$totalCyclesResult = $conn->query($totalCyclesSql);
$totalCycles = ($totalCyclesResult->num_rows == 1) ? $totalCyclesResult->fetch_assoc()["total_cycles"] : 0;

$cyclesRequiringMaintenanceSql = "SELECT COUNT(*) as cycles_requiring_maintenance FROM cycle_info WHERE health < 90";
$cyclesRequiringMaintenanceResult = $conn->query($cyclesRequiringMaintenanceSql);
$cyclesRequiringMaintenance = ($cyclesRequiringMaintenanceResult->num_rows == 1) ? $cyclesRequiringMaintenanceResult->fetch_assoc()["cycles_requiring_maintenance"] : 0;

// Users and wallet queries
$totalUsersSql = "SELECT COUNT(*) as total_users FROM user_info";
$totalUsersResult = $conn->query($totalUsersSql);
$totalUsers = ($totalUsersResult->num_rows == 1) ? $totalUsersResult->fetch_assoc()["total_users"] : 0;

$totalAmountSql = "SELECT SUM(wallet_balance) as total_amount FROM wallet";
$totalAmountResult = $conn->query($totalAmountSql);
$totalAmount = ($totalAmountResult->num_rows == 1) ? $totalAmountResult->fetch_assoc()["total_amount"] : 0;

// Rides queries
$totalRidesSql = "SELECT COUNT(*) as total_rides FROM rides";
$totalRidesResult = $conn->query($totalRidesSql);
$totalRides = ($totalRidesResult->num_rows == 1) ? $totalRidesResult->fetch_assoc()["total_rides"] : 0;

$activeRidesSql = "SELECT COUNT(*) as active_rides FROM rides WHERE status = 'ongoing'";
$activeRidesResult = $conn->query($activeRidesSql);
$activeRides = ($activeRidesResult->num_rows == 1) ? $activeRidesResult->fetch_assoc()["active_rides"] : 0;

$averageRideDurationSql = "SELECT AVG(duration) as avg_duration FROM rides WHERE status = 'completed'";
$averageRideDurationResult = $conn->query($averageRideDurationSql);
$avgDuration = round(($averageRideDurationResult->num_rows == 1) ? $averageRideDurationResult->fetch_assoc()["avg_duration"] : 0);

// Feedback queries
$pendingQueriesSql = "SELECT COUNT(*) as total_pending_queries FROM feedback WHERE solved = 0";
$pendingQueriesResult = $conn->query($pendingQueriesSql);
$totalPendingQueries = ($pendingQueriesResult->num_rows == 1) ? $pendingQueriesResult->fetch_assoc()["total_pending_queries"] : 0;

$answeredQueriesSql = "SELECT COUNT(*) as total_answered_queries FROM feedback WHERE solved = 1";
$answeredQueriesResult = $conn->query($answeredQueriesSql);
$totalAnsweredQueries = ($answeredQueriesResult->num_rows == 1) ? $answeredQueriesResult->fetch_assoc()["total_answered_queries"] : 0;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Developer Dashboard</title>
    <!-- Add your CSS link here -->
    <link rel="stylesheet" href="css/dev_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        <h2><i class="fas fa-chart-line"></i> Developer Dashboard</h2>

        <div class="dashboard">
            <div class="dashboard-box" onclick="location.href='cycle_info.php';">
                <h3><i class="fas fa-bicycle"></i> Cycle Statistics</h3>
                <?php
                echo "<p>$totalCycles</p>";
                ?>
                <div class="subtitle">Total Cycles</div>
                <div class="stat-row">
                    <span class="stat-label">Require Maintenance</span>
                    <span class="stat-value"><?php echo $cyclesRequiringMaintenance; ?></span>
                </div>
            </div>

            <div class="dashboard-box" onclick="location.href='user_info.php';">
                <h3><i class="fas fa-users"></i> User Analytics</h3>
                <?php
                echo "<p>$totalUsers</p>";
                ?>
                <div class="subtitle">Registered Users</div>
                <div class="stat-row">
                    <span class="stat-label">Total Balance</span>
                    <span class="stat-value">₹<?php echo number_format($totalAmount, 2); ?></span>
                </div>
            </div>

            <div class="dashboard-box" onclick="location.href='#';">
                <h3><i class="fas fa-route"></i> Ride Analytics</h3>
                <?php
                echo "<p>$totalAmount</p>";
                ?>
                <div class="subtitle">Total Rides</div>
                <div class="stat-row">
                    <span class="stat-label">Active Rides</span>
                    <span class="stat-value"><?php echo $activeRides; ?></span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Avg. Duration</span>
                    <span class="stat-value"><?php echo $avgDuration; ?> min</span>
                </div>
            </div>

            <div class="dashboard-box" onclick="location.href='handle_query.php';">
                <h3><i class="fas fa-question-circle"></i> Support Queries</h3>
                <p><?php echo $totalPendingQueries; ?></p>
                <div class="subtitle">Pending Queries</div>
                <div class="stat-row">
                    <span class="stat-label">Resolved</span>
                    <span class="stat-value"><?php echo $totalAnsweredQueries; ?></span>
                </div>
            </div>
            <div class="dashboard-box" onclick="location.href='ride_info.php';">
                <h3>Total Rides</h3>
                <p><?php echo $totalRides; ?></p>
            </div>

            <div class="dashboard-box" onclick="location.href='cycle_maintenance.php';">
                <h3>Cycles Requiring Maintenance</h3>
                <?php
                echo "<p>$cyclesRequiringMaintenance</p>";
                ?>
            </div>



        </div>
    </div>
</body>
</html>
