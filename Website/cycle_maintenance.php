<!-- This page control Cycle maintenance -->
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

// Fetch cycles that require maintenance (health less than 70)
$fetch_cycles_sql = "SELECT * FROM cycle_info WHERE health < 90 ORDER BY health ASC";
$result = $conn->query($fetch_cycles_sql);

// Fetch cycles that don't require maintenance (health 70 or above)
$healthy_cycles_sql = "SELECT * FROM cycle_info WHERE health >= 90 ORDER BY health DESC";
$healthy_result = $conn->query($healthy_cycles_sql);
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cycle Maintenance</title>
    <link rel="stylesheet" href="css/cycle_info.css">
    <style>
        
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

<div class="container">
    <h2>Cycle Maintenance</h2>

    <!-- Display cycles requiring maintenance -->
    <div class="maintenance-list">
        <h3>Cycles Requiring Maintenance</h3>
        <table class="cycle-table">
            <thead>
                <tr>
                    <th>Cycle ID</th>
                    <th>Health</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["cycle_id"] . "</td>";
                        echo "<td>" . $row["health"] . "</td>";
                        echo "<td><button class='send-to-maintenance' onclick='sendToMaintenance(" . $row["cycle_id"] . ")'>Send to Maintenance</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No cycles require maintenance.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Display healthy cycles -->
    <div class="healthy-list">
        <h3>Healthy Cycles</h3>
        <table class="cycle-table">
            <thead>
                <tr>
                    <th>Cycle ID</th>
                    <th>Health</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($healthy_result && $healthy_result->num_rows > 0) {
                    while ($row = $healthy_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["cycle_id"] . "</td>";
                        echo "<td>" . $row["health"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No healthy cycles available</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<a href="dev_dashboard.php" class="back-link"><h3>Back to Dashboard</h3></a>
<br>
<script>
    function sendToMaintenance(cycleId) {
        // Implement the logic to send the cycle with the given ID to maintenance
        // You can use AJAX to send an asynchronous request to the server
        // and update the database accordingly.
        alert("Sending Cycle ID " + cycleId + " to maintenance.");
    }
</script>

</body>
</html>
