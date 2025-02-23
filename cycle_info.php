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

// Add additional analytics queries
$totalRidesQuery = "SELECT SUM(total_rides) as total_rides FROM 
    (SELECT COUNT(*) as total_rides FROM rides GROUP BY cycle_id) as ride_counts";
$avgHealthQuery = "SELECT AVG(health) as avg_health FROM cycle_info";
$totalDistanceQuery = "SELECT SUM(distance_travelled) as total_distance FROM cycle_info";

$totalRidesResult = $conn->query($totalRidesQuery)->fetch_assoc();
$avgHealthResult = $conn->query($avgHealthQuery)->fetch_assoc();
$totalDistanceResult = $conn->query($totalDistanceQuery)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cycle Management Dashboard</title>
    <link rel="stylesheet" href="css/cycle_info.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
          <div class="logo">
            <img src="assets/logo_index.png" alt="Logo">
          </div>
        </div>
      </nav>
    <div class="dashboard-container">
        <div class="analytics-grid">
            <div class="analytics-card">
                <h3>Total Cycles</h3>
                <div class="stat-value"><?php echo $total_cycles; ?></div>
            </div>
            <div class="analytics-card">
                <h3>Total Rides</h3>
                <div class="stat-value"><?php echo $totalRidesResult['total_rides']; ?></div>
            </div>
            <div class="analytics-card">
                <h3>Average Health</h3>
                <div class="stat-value"><?php echo round($avgHealthResult['avg_health'], 1); ?>%</div>
            </div>
            <div class="analytics-card">
                <h3>Total Distance</h3>
                <div class="stat-value"><?php echo round($totalDistanceResult['total_distance'], 1); ?> km</div>
            </div>
        </div>

        <div class="charts-row">
            <div class="chart-container">
                <canvas id="healthDistribution"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="ridesPerCycle"></canvas>
            </div>
        </div>



        <div class="table-container">
            <div class="table-header">
                <h2>All Cycle Information</h2>
                <p id="total-cycles">Total Number of Cycles: <?php echo $total_cycles; ?></p>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="button-container">
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="action-buttons">
                    <input type="submit" name="add_new_cycle" value="Add New Cycle" class="action-button add-button">
                    <a href="cycle_maintenance.php" class="action-button maintenance-button">Maintenance</a>
                </form>
            </div>
            <div class="controls-container">
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Search cycles by ID, health, distance...">
                </div>
                <div class="filter-container">
                    <select id="filterCriteria">
                        <option value="all">All Fields</option>
                        <option value="health">Health</option>
                        <option value="distance">Distance</option>
                        <option value="rides">Rides</option>
                    </select>
                    <select id="filterCondition">
                        <option value="gt">Above</option>
                        <option value="lt">Below</option>
                        <option value="eq">Equal</option>
                    </select>
                    <input type="number" id="filterValue" placeholder="Value">
                    <button class="reset-button">Reset</button>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="cycle-table">
                    <thead>
                        <tr>
                            <th>Cycle ID</th>
                            <th>Health</th>
                            <th>Distance Traveled</th>
                            <th>Total Time</th>
                            <th>Cycle Link</th>
                            <th>Total Rides</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows > 0): 
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><span class="cycle-id"><?php echo $row["cycle_id"]; ?></span></td>
                                <td>
                                    <div class="health-indicator" style="background: <?php echo getHealthColor($row["health"]); ?>">
                                        <?php echo $row["health"]; ?>%
                                    </div>
                                </td>
                                <td><?php echo number_format($row["distance_travelled"] / 1000, 1); ?> km</td>
                                <td><?php echo formatTime($row["total_time"]); ?></td>
                                <td><a href="user_authentication.php?cycle_id=<?php echo $row["cycle_id"]; ?>" target="_blank" class="cycle-link"><?php echo $row["cycle_link"]; ?></a></td>
                                <td><span class="rides-count"><?php echo $row["total_rides"]; ?></span></td>
                                <td>
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="action-form">
                                        <input type="hidden" name="cycle_id_to_delete" value="<?php echo $row['cycle_id']; ?>">
                                        <input type="submit" name="delete_cycle" value="Delete" class="delete-button">
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr><td colspan="7" class="no-data">No cycle information available</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

<?php
function getHealthColor($health) {
    if ($health >= 90) return '#2ecc71';
    if ($health >= 70) return '#f1c40f';
    return '#e74c3c';
}

function formatTime($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    return sprintf("%dh %dm", $hours, $minutes);
}
?>

            <br>
            <a href="dev_dashboard.php" class="back-link">Back to Dashboard</a>
        </div>
    </div>

    <script>
        // Sample chart initialization
        const healthChart = new Chart(
            document.getElementById('healthDistribution'),
            {
                type: 'bar',
                data: {
                    labels: ['90-100%', '80-90%', '70-80%', '<70%'],
                    datasets: [{
                        label: 'Cycles by Health',
                        data: [
                            <?php
                            // Add PHP code to calculate health distribution
                            echo $conn->query("SELECT COUNT(*) FROM cycle_info WHERE health >= 90")->fetch_row()[0] . ",";
                            echo $conn->query("SELECT COUNT(*) FROM cycle_info WHERE health >= 80 AND health < 90")->fetch_row()[0] . ",";
                            echo $conn->query("SELECT COUNT(*) FROM cycle_info WHERE health >= 70 AND health < 80")->fetch_row()[0] . ",";
                            echo $conn->query("SELECT COUNT(*) FROM cycle_info WHERE health < 70")->fetch_row()[0];
                            ?>
                        ],
                        backgroundColor: '#e67e22'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Cycle Health Distribution',
                            color: '#ffffff'
                        },
                        legend: {
                            labels: {
                                color: '#ffffff'
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: '#ffffff' }
                        },
                        x: {
                            ticks: { color: '#ffffff' }
                        }
                    }
                }
            }
        );

        // Add Rides per Cycle chart
        const ridesChart = new Chart(
            document.getElementById('ridesPerCycle'),
            {
                type: 'line',
                data: {
                    labels: [
                        <?php
                        $cycleIds = [];
                        $rideData = [];
                        $result->data_seek(0);
                        while ($row = $result->fetch_assoc()) {
                            echo "'" . $row['cycle_id'] . "',";
                            $rideData[] = $row['total_rides'];
                        }
                        ?>
                    ],
                    datasets: [{
                        label: 'Rides per Cycle',
                        data: [<?php echo implode(',', $rideData); ?>],
                        borderColor: '#e67e22',
                        backgroundColor: 'rgba(230, 126, 34, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Rides Distribution per Cycle',
                            color: '#ffffff'
                        },
                        legend: {
                            labels: {
                                color: '#ffffff'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#ffffff' }
                        },
                        x: {
                            ticks: { color: '#ffffff' }
                        }
                    }
                }
            }
        );

        // Enhanced Filter and Search Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const filterCriteria = document.getElementById('filterCriteria');
            const filterCondition = document.getElementById('filterCondition');
            const filterValue = document.getElementById('filterValue');
            const resetButton = document.querySelector('.reset-button');
            const tableRows = document.querySelectorAll('.cycle-table tbody tr');

            // Combined search and filter function
            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const criteria = filterCriteria.value;
                const condition = filterCondition.value;
                const value = parseFloat(filterValue.value);

                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    let showRow = text.includes(searchTerm);

                    if (showRow && criteria !== 'all' && !isNaN(value)) {
                        let compareValue;
                        switch(criteria) {
                            case 'health':
                                compareValue = parseFloat(row.querySelector('.health-indicator').textContent);
                                break;
                            case 'distance':
                                compareValue = parseFloat(row.cells[2].textContent);
                                break;
                            case 'rides':
                                compareValue = parseInt(row.querySelector('.rides-count').textContent);
                                break;
                        }

                        switch(condition) {
                            case 'gt':
                                showRow = compareValue > value;
                                break;
                            case 'lt':
                                showRow = compareValue < value;
                                break;
                            case 'eq':
                                showRow = compareValue === value;
                                break;
                        }
                    }

                    row.style.display = showRow ? '' : 'none';
                });
            }

            // Event listeners for real-time filtering
            searchInput.addEventListener('input', filterTable);
            filterCriteria.addEventListener('change', filterTable);
            filterCondition.addEventListener('change', filterTable);
            filterValue.addEventListener('input', filterTable);

            // Reset functionality
            resetButton.addEventListener('click', function() {
                searchInput.value = '';
                filterValue.value = '';
                filterCriteria.value = 'all';
                filterCondition.value = 'gt';
                tableRows.forEach(row => row.style.display = '');
            });

            // Auto-focus search input
            searchInput.focus();
        });
    </script>
</body>
</html>
