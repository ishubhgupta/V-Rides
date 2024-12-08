<!-- this page contains all user informations -->
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

// Handle user removal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remove_user"])) {
    $removeRegistrationNumber = $_POST["registration_number_to_remove"];
    $removeUserSql = "DELETE FROM user_info WHERE vit_registration_number = '$removeRegistrationNumber'";

    if ($conn->query($removeUserSql) === TRUE) {
        // Redirect to refresh the page after removing the user
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error removing user: " . $conn->error;
    }
}

// Handle adding money to wallet
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_money"])) {
    $amount_to_add = $_POST["amount"];
    $vit_registration_number = $_POST["vit_registration_number"];

    // Update wallet balance
    $update_wallet_sql = "UPDATE wallet SET wallet_balance = wallet_balance + '$amount_to_add' WHERE vit_registration_number = '$vit_registration_number'";
    $conn->query($update_wallet_sql);

    // Redirect to user_info.php to refresh the page
    header("Location: user_info.php");
    exit;
}

// Fetch user count
$userCountSql = "SELECT COUNT(*) as total_users FROM user_info";
$userCountResult = $conn->query($userCountSql);
$total_users = ($userCountResult->num_rows == 1) ? $userCountResult->fetch_assoc()["total_users"] : 0;

// Fetch user information
$userInfoSql = "SELECT id, vit_registration_number, name, warnings, suspended FROM user_info";
$userInfoResult = $conn->query($userInfoSql);

// Fetch additional details like wallet balance, total rides, warnings, and suspension status
$userDetails = array();
while ($row = $userInfoResult->fetch_assoc()) {
    $registrationNumber = $row["vit_registration_number"];

    $walletSql = "SELECT wallet_balance FROM wallet WHERE vit_registration_number = '$registrationNumber'";
    $walletResult = $conn->query($walletSql);
    $walletBalance = ($walletResult->num_rows == 1) ? $walletResult->fetch_assoc()["wallet_balance"] : 0;

    $totalRidesSql = "SELECT COUNT(*) AS total_rides FROM rides WHERE vit_registration_number = '$registrationNumber'";
    $totalRidesResult = $conn->query($totalRidesSql);
    $totalRides = ($totalRidesResult->num_rows == 1) ? $totalRidesResult->fetch_assoc()["total_rides"] : 0;

    $userDetails[] = array(
        "id" => $row["id"],
        "registration_number" => $registrationNumber,
        "name" => $row["name"],
        "batch" => isset($row["batch"]) ? $row["batch"] : "Not Available",
        "email_id" => isset($row["email_id"]) ? $row["email_id"] : "Not Available",
        "birthday" => isset($row["birthday"]) ? $row["birthday"] : "Not Available",
        "wallet_balance" => $walletBalance,
        "total_rides" => $totalRides,
        "warnings" => $row["warnings"],
        "suspended" => $row["suspended"]
    );
}


// Handle suspending user account
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["suspend_user"])) {
    $suspendRegistrationNumber = $_POST["registration_number_to_suspend"];
    $suspendUserSql = "UPDATE user_info SET suspended = 1 WHERE vit_registration_number = '$suspendRegistrationNumber'";

    if ($conn->query($suspendUserSql) === TRUE) {
        // Redirect to refresh the page after suspending the user
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error suspending user: " . $conn->error;
    }
}

// Handle unsuspending user account
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["unsuspend_user"])) {
    $unsuspendRegistrationNumber = $_POST["registration_number_to_unsuspend"];
    $unsuspendUserSql = "UPDATE user_info SET suspended = 0 WHERE vit_registration_number = '$unsuspendRegistrationNumber'";

    if ($conn->query($unsuspendUserSql) === TRUE) {
        // Redirect to refresh the page after unsuspending the user
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error unsuspending user: " . $conn->error;
    }
}

// Handle giving a warning to a user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["give_warning"])) {
    $warningRegistrationNumber = $_POST["registration_number_to_warn"];
    $giveWarningSql = "UPDATE user_info SET warnings = warnings + 1 WHERE vit_registration_number = '$warningRegistrationNumber'";

    if ($conn->query($giveWarningSql) === TRUE) {
        // Redirect to refresh the page after giving a warning
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Error giving a warning to the user: " . $conn->error;
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Information</title>
    <link rel="stylesheet" href="css/user_info.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Include your CSS file or add styles here -->
</head>

<body>
    <div class="dashboard-container">
        <!-- Analytics Overview -->
        <div class="analytics-grid">
            <div class="analytics-card">
                <h3>Total Users</h3>
                <div class="stat-value"><?php echo $total_users; ?></div>
            </div>
            <div class="analytics-card">
                <h3>Active Users</h3>
                <div class="stat-value"><?php echo $total_users - count(array_filter($userDetails, function($user) { return $user['suspended'] == 1; })); ?></div>
            </div>
            <div class="analytics-card">
                <h3>Suspended Users</h3>
                <div class="stat-value"><?php echo count(array_filter($userDetails, function($user) { return $user['suspended'] == 1; })); ?></div>
            </div>
            <div class="analytics-card">
                <h3>Users with Warnings</h3>
                <div class="stat-value"><?php echo count(array_filter($userDetails, function($user) { return $user['warnings'] > 0; })); ?></div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <div class="chart-container">
                <canvas id="userStatusChart"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="ridesDistributionChart"></canvas>
            </div>
        </div>

        <!-- Enhanced User Table -->
        <div class="table-container">
            <h2>User Information</h2>

            <!-- Add Search and Filter Controls -->
            <div class="controls-container">
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Search by name or registration number...">
                </div>
                <div class="filter-container">
                    <select id="filterCriteria">
                        <option value="">Filter By</option>
                        <option value="wallet">Wallet Balance</option>
                        <option value="rides">Total Rides</option>
                        <option value="warnings">Warnings</option>
                    </select>
                    <select id="filterCondition">
                        <option value="greater">Greater Than</option>
                        <option value="less">Less Than</option>
                        <option value="equal">Equal To</option>
                    </select>
                    <input type="number" id="filterValue" placeholder="Value">
                    <button onclick="applyFilter()" class="filter-button">Apply Filter</button>
                    <button onclick="resetFilter()" class="reset-button">Reset</button>
                </div>
            </div>

            <?php
            if (isset($error_message)) {
                echo '<p style="color: red;">' . $error_message . '</p>';
            }
            ?>

            <p>Total Number of Users: <?php echo $total_users; ?></p>

            <table border="1" class="cycle-table">
                <tr>
                    <th>Name</th>
                    <th>Registration Number</th>
                    <th>Wallet Balance</th>
                    <th>Total Rides</th>
                    <th>Warnings</th>
                    
                    <th>Action</th>
        
                </tr>
                <?php foreach ($userDetails as $user) { ?>
                    <tr <?php echo ($user['suspended'] == 1) ? 'class="suspended"' : (($user['warnings'] >= 1) ? 'class="warned"' : ''); ?>>
                        <td>
                            <span class="status-indicator <?php 
                                echo $user['suspended'] == 1 ? 'status-suspended' : 
                                    ($user['warnings'] >= 1 ? 'status-warned' : 'status-active'); 
                            ?>"></span>
                            <?php echo $user["name"]; ?>
                        </td>
                        <td><?php echo $user["registration_number"]; ?></td>
                        <td><?php echo $user["wallet_balance"]; ?></td>
                        <td><?php echo $user["total_rides"]; ?></td>
                        <td>
                            <?php if ($user["warnings"] > 0) { ?>
                                <span class="warning-count"><?php echo $user["warnings"]; ?></span>
                            <?php } else { 
                                echo "0";
                            } ?>
                        </td>
                        
                        <td>
                            <div class="button-container">
                                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                    <input type="hidden" name="registration_number_to_remove" value="<?php echo $user['registration_number']; ?>">
                                    <input type="submit" name="remove_user" value="Remove" class='delete-button' onclick="return confirmUserRemoval();">
                                </form>

                                <?php if ($user['suspended'] == 1) { ?>
                                    <!-- If user is suspended, show Unsuspend button -->
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <input type="hidden" name="registration_number_to_unsuspend" value="<?php echo $user['registration_number']; ?>">
                                        <input type="submit" name="unsuspend_user" value="Unsuspend" class='unsuspend-button' onclick="return confirm('Are you sure you want to unsuspend this user?');">
                                    </form>
                                <?php } else { ?>
                                    <!-- If user is not suspended, show Suspend and Give Warning buttons -->
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <input type="hidden" name="registration_number_to_suspend" value="<?php echo $user['registration_number']; ?>">
                                        <input type="submit" name="suspend_user" value="Suspend" class='suspend-button' onclick="return confirm('Are you sure you want to suspend this user?');">
                                    </form>
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <input type="hidden" name="registration_number_to_warn" value="<?php echo $user['registration_number']; ?>">
                                        <input type="submit" name="give_warning" value="Give Warning" class='warning-button' onclick="return confirm('Are you sure you want to give a warning to this user?');">
                                    </form>
                                    <button onclick="showAddMoneyOverlay('<?php echo $user['registration_number']; ?>')" class="add-money-button">Add Money</button>

                                    
                                <?php } ?>
                            </div>
                        </td>




                    </tr>
                <?php } ?>
            </table>

            <br>
            <a href="dev_dashboard.php" class="back-link">Back to Dashboard</a>

            <div id="overlay">
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <h3>Add Money to Wallet</h3>
                    <label>Enter Amount:</label>
                    <input type="number" name="amount" min="1" placeholder="Enter tokens to add" required>
                    <input type="hidden" name="vit_registration_number" id="overlay-vit-registration-number" value="">
                    <div class="overlay-buttons">
                        <input type="submit" name="add_money" value="Add Money">
                        <button type="button" onclick="hideAddMoneyOverlay()">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Script for confirmation on user removal -->
            <script>
                function confirmUserRemoval() {
                    return confirm('Are you sure you want to remove this user?');
                }

                // Add overlay functions
                function showAddMoneyOverlay(registrationNumber) {
                    document.getElementById('overlay-vit-registration-number').value = registrationNumber;
                    document.getElementById('overlay').style.display = 'flex';
                }

                function hideAddMoneyOverlay() {
                    document.getElementById('overlay').style.display = 'none';
                }
            </script>

            <!-- Add JavaScript for search and filter -->
            <script>
                const searchInput = document.getElementById('searchInput');
                const filterCriteria = document.getElementById('filterCriteria');
                const filterCondition = document.getElementById('filterCondition');
                const filterValue = document.getElementById('filterValue');
                const tableRows = document.querySelectorAll('.cycle-table tr:not(:first-child)');

                // Search functionality
                searchInput.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    
                    tableRows.forEach(row => {
                        const name = row.cells[0].textContent.toLowerCase();
                        const regNo = row.cells[1].textContent.toLowerCase();
                        
                        if (name.includes(searchTerm) || regNo.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });

                // Filter functionality
                function applyFilter() {
                    const criteria = filterCriteria.value;
                    const condition = filterCondition.value;
                    const value = parseFloat(filterValue.value);

                    if (!criteria || isNaN(value)) return;

                    tableRows.forEach(row => {
                        let compareValue;
                        switch(criteria) {
                            case 'wallet':
                                compareValue = parseFloat(row.cells[2].textContent);
                                break;
                            case 'rides':
                                compareValue = parseFloat(row.cells[3].textContent);
                                break;
                            case 'warnings':
                                compareValue = parseFloat(row.cells[4].textContent);
                                break;
                        }

                        let show = false;
                        switch(condition) {
                            case 'greater':
                                show = compareValue > value;
                                break;
                            case 'less':
                                show = compareValue < value;
                                break;
                            case 'equal':
                                show = compareValue === value;
                                break;
                        }

                        row.style.display = show ? '' : 'none';
                    });
                }

                function resetFilter() {
                    filterCriteria.value = '';
                    filterCondition.value = 'greater';
                    filterValue.value = '';
                    tableRows.forEach(row => row.style.display = '');
                    searchInput.value = '';
                }
            </script>

        </div>

        <script>
            // User Status Chart
            new Chart(document.getElementById('userStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Active', 'Suspended', 'Warned'],
                    datasets: [{
                        data: [
                            <?php echo $total_users - count(array_filter($userDetails, function($user) { return $user['suspended'] == 1; })); ?>,
                            <?php echo count(array_filter($userDetails, function($user) { return $user['suspended'] == 1; })); ?>,
                            <?php echo count(array_filter($userDetails, function($user) { return $user['warnings'] > 0; })); ?>
                        ],
                        backgroundColor: ['#2ecc71', '#e74c3c', '#f39c12']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'User Status Distribution',
                            color: '#ffffff'
                        },
                        legend: {
                            labels: {
                                color: '#ffffff'
                            }
                        }
                    }
                }
            });

            // Rides Distribution Chart
            new Chart(document.getElementById('ridesDistributionChart'), {
                type: 'bar',
                data: {
                    labels: ['0-5', '6-10', '11-20', '20+'],
                    datasets: [{
                        label: 'Users by Ride Count',
                        data: [
                            <?php echo count(array_filter($userDetails, function($user) { return $user['total_rides'] <= 5; })); ?>,
                            <?php echo count(array_filter($userDetails, function($user) { return $user['total_rides'] > 5 && $user['total_rides'] <= 10; })); ?>,
                            <?php echo count(array_filter($userDetails, function($user) { return $user['total_rides'] > 10 && $user['total_rides'] <= 20; })); ?>,
                            <?php echo count(array_filter($userDetails, function($user) { return $user['total_rides'] > 20; })); ?>
                        ],
                        backgroundColor: '#e67e22'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Rides Distribution',
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
            });
        </script>
    </div>
</body>

</html>
