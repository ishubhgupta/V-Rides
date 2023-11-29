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
// Fetch and display the total number of pending queries
$pendingQueriesSql = "SELECT COUNT(*) as total_pending_queries FROM feedback WHERE solved = 0";
$pendingQueriesResult = $conn->query($pendingQueriesSql);
$totalPendingQueries = ($pendingQueriesResult->num_rows == 1) ? $pendingQueriesResult->fetch_assoc()["total_pending_queries"] : 0;

// Fetch and display the total number of answered queries
$answeredQueriesSql = "SELECT COUNT(*) as total_answered_queries FROM feedback WHERE solved = 1";
$answeredQueriesResult = $conn->query($answeredQueriesSql);
$totalAnsweredQueries = ($answeredQueriesResult->num_rows == 1) ? $answeredQueriesResult->fetch_assoc()["total_answered_queries"] : 0;

// Fetch and display the total number of rides
$totalRidesSql = "SELECT COUNT(*) as total_rides FROM rides";
$totalRidesResult = $conn->query($totalRidesSql);
$totalRides = ($totalRidesResult->num_rows == 1) ? $totalRidesResult->fetch_assoc()["total_rides"] : 0;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Developer Dashboard</title>
    <!-- Add your CSS link here -->
    <link rel="stylesheet" href="css/dev_dashboard.css">
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
        <h2>Developer Dashboard</h2>

        <div class="dashboard">
            <div class="dashboard-box" onclick="location.href='cycle_info.php';">
                <h3>Total Cycles</h3>
                <?php
                // Database connection code here (replace this with your actual database connection code)
                $servername = "localhost";
                $username = "root";
                $password = "123456";
                $dbname = "v_rides";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch and display the total number of cycles from your database
                $totalCyclesSql = "SELECT COUNT(*) as total_cycles FROM cycle_info";
                $totalCyclesResult = $conn->query($totalCyclesSql);
                $totalCycles = ($totalCyclesResult->num_rows == 1) ? $totalCyclesResult->fetch_assoc()["total_cycles"] : 0;
                echo "<p>$totalCycles</p>";
                ?>
            </div>

            <div class="dashboard-box" onclick="location.href='user_info.php';">
                <h3>Total Users</h3>
                <?php
                // Fetch and display the total number of users from your database
                $totalUsersSql = "SELECT COUNT(*) as total_users FROM user_info";
                $totalUsersResult = $conn->query($totalUsersSql);
                $totalUsers = ($totalUsersResult->num_rows == 1) ? $totalUsersResult->fetch_assoc()["total_users"] : 0;
                echo "<p>$totalUsers</p>";
                ?>
            </div>

            <div class="dashboard-box" onclick="location.href='#';">
                <h3>Total Amount</h3>
                <?php
                // Fetch and display the sum of the total amount from the wallet
                $totalAmountSql = "SELECT SUM(wallet_balance) as total_amount FROM wallet";
                $totalAmountResult = $conn->query($totalAmountSql);
                $totalAmount = ($totalAmountResult->num_rows == 1) ? $totalAmountResult->fetch_assoc()["total_amount"] : 0;
                echo "<p>$totalAmount</p>";
                ?>
            </div>

            <div class="dashboard-box" onclick="location.href='handle_query.php';">
                <h3>Query Handling</h3>
                <p>Total Pending: <?php echo $totalPendingQueries; ?></p>
                <p>Total Answered: <?php echo $totalAnsweredQueries; ?></p>
            </div>
            <div class="dashboard-box" onclick="location.href='ride_info.php';">
                <h3>Total Rides</h3>
                <p><?php echo $totalRides; ?></p>
            </div>

            <div class="dashboard-box" onclick="location.href='cycle_maintenance.php';">
                <h3>Cycles Requiring Maintenance</h3>
                <?php
                // Fetch and display the total number of cycles requiring maintenance (health less than 70)
                $cyclesRequiringMaintenanceSql = "SELECT COUNT(*) as cycles_requiring_maintenance FROM cycle_info WHERE health < 90";
                $cyclesRequiringMaintenanceResult = $conn->query($cyclesRequiringMaintenanceSql);
                $cyclesRequiringMaintenance = ($cyclesRequiringMaintenanceResult->num_rows == 1) ? $cyclesRequiringMaintenanceResult->fetch_assoc()["cycles_requiring_maintenance"] : 0;
                echo "<p>$cyclesRequiringMaintenance</p>";
                ?>
            </div>



        </div>
    </div>
</body>
</html>
