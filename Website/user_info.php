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

    <!-- Include your CSS file or add styles here -->
</head>

<body>
    <div class="container">

        <h2>User Information</h2>

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
                    <td><?php echo $user["name"]; ?></td>
                    <td><?php echo $user["registration_number"]; ?></td>
                    <td><?php echo $user["wallet_balance"]; ?></td>
                    <td><?php echo $user["total_rides"]; ?></td>
                    <td><?php echo $user["warnings"]; ?></td>
                    
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
                <label>Amount (in tokens):</label>
                <input type="number" name="amount" required>
                <input type="hidden" name="vit_registration_number" id="overlay-vit-registration-number" value="">
                <input type="submit" name="add_money" value="Add Money">
                <button type="button" onclick="hideAddMoneyOverlay()">Cancel</button>
            </form>
        </div>

        <!-- Script for confirmation on user removal -->
        <script>
            function confirmUserRemoval() {
                return confirm('Are you sure you want to remove this user?');
            }
        </script>

    </div>

    <script>
        function showAddMoneyOverlay(registrationNumber) {
            document.getElementById('overlay-vit-registration-number').value = registrationNumber;
            document.getElementById('overlay').style.display = 'block';
        }

        function hideAddMoneyOverlay() {
            document.getElementById('overlay').style.display = 'none';
        }
    </script>

</body>

</html>
