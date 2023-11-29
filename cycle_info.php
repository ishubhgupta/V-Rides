

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

// Handle the form submission for adding a new cycle or deleting a cycle
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_new_cycle"])) {
        // Generate a random link for the new cycle
        $cycle_link = bin2hex(random_bytes(6)); // Adjust length as needed

        // Insert a new cycle with default values and the generated link
        $insertSql = "INSERT INTO cycle_info (health, distance_travelled, total_time, cycle_link) 
                      VALUES (100, 0, 0, '$cycle_link')";

        if ($conn->query($insertSql) === TRUE) {
            // Redirect to refresh the page after adding a new cycle
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error_message = "Error adding a new cycle: " . $conn->error;
        }
    } elseif (isset($_POST["delete_cycle"])) {
        // Handle the form submission for deleting a cycle
        $cycle_id_to_delete = $_POST["cycle_id_to_delete"];

        // Delete the selected cycle
        $deleteSql = "DELETE FROM cycle_info WHERE cycle_id = $cycle_id_to_delete";
        if ($conn->query($deleteSql) === TRUE) {
            // Redirect to refresh the page after deleting the cycle
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error_message = "Error deleting the cycle: " . $conn->error;
        }
    }
}

// Query to fetch all cycle information including total rides
$sql = "SELECT *, (SELECT COUNT(*) FROM rides WHERE cycle_id = cycle_info.cycle_id) AS total_rides
        FROM cycle_info";
$result = $conn->query($sql);

// Get the total number of cycles
$total_cycles = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Cycle Information</title>
    <link rel="stylesheet" href="css/cycle_info.css">
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
        <h2>All Cycle Information</h2>

        <p id="total-cycles">Total Number of Cycles: <?php echo $total_cycles; ?></p>

        <?php
        if (isset($error_message)) {
            echo '<p class="error-message">' . $error_message . '</p>';
        }
        ?>

        <div class="button-container">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="submit" name="add_new_cycle" value="Add New Cycle" class="button">
                <a href="cycle_maintenance.php" class="maintenance-button">Maintenance</a>
            </form>
        </div>

        <table border="1" class="cycle-table">
            <tr>
                <th>Cycle ID</th>
                <th>Health</th>
                <th>Distance Traveled</th>
                <th>Total Time</th>
                <th>Cycle Link</th>
                <th>Total Rides</th>
                <th>Action</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["cycle_id"] . "</td>";
                    echo "<td>" . $row["health"] . "</td>";
                    echo "<td>" . $row["distance_travelled"] . "</td>";
                    echo "<td>" . $row["total_time"] . "</td>";
                    echo "<td><a href='user_authentication.php?cycle_id=" . $row["cycle_id"] . "' target='_blank'>" . $row["cycle_link"] . "</a></td>";
                    echo "<td>" . $row["total_rides"] . "</td>";
                    echo "<td>";
                    echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>";
                    echo "<input type='hidden' name='cycle_id_to_delete' value='" . $row['cycle_id'] . "'>";
                    echo "<input type='submit' name='delete_cycle' value='Delete' class='delete-button'>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No cycle information available</td></tr>";
            }
            ?>
        </table>

        <br>
        <a href="dev_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
