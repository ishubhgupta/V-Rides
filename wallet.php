<!-- this page controlls money related transactions -->
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

// Fetch user information along with wallet balance
$vit_registration_number = $_SESSION["vit_registration_number"];
$user_info_sql = "SELECT *, w.wallet_balance FROM user_info u
                  LEFT JOIN wallet w ON u.vit_registration_number = w.vit_registration_number
                  WHERE u.vit_registration_number = '$vit_registration_number'";
$user_info_result = $conn->query($user_info_sql);

if ($user_info_result->num_rows == 1) {
    $user_info = $user_info_result->fetch_assoc();
} else {
    echo "User information not found.";
    exit;
}

// Fetch user's wallet balance
$wallet_balance_sql = "SELECT wallet_balance FROM wallet WHERE vit_registration_number = '$vit_registration_number'";
$wallet_balance_result = $conn->query($wallet_balance_sql);

if ($wallet_balance_result->num_rows == 1) {
    $wallet_balance_row = $wallet_balance_result->fetch_assoc();
    $wallet_balance = $wallet_balance_row["wallet_balance"];
} else {
    // Set default wallet balance if not found and insert a new wallet entry
    $wallet_balance = 50;

    $insert_wallet_sql = "INSERT INTO wallet (vit_registration_number, wallet_balance) VALUES ('$vit_registration_number', '$wallet_balance')";
    if ($conn->query($insert_wallet_sql) !== TRUE) {
        echo "Error inserting wallet entry: " . $conn->error;
        exit;
    }
}

// Handle adding money to wallet
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount_to_add = $_POST["amount"];

    // Update wallet balance
    $new_balance = $wallet_balance + $amount_to_add;

    // Update the wallet_balance in the wallet table
    $update_wallet_sql = "UPDATE wallet SET wallet_balance = '$new_balance' WHERE vit_registration_number = '$vit_registration_number'";
    if ($conn->query($update_wallet_sql) === TRUE) {
        // Redirect to wallet page with updated balance
        header("Location: wallet.php");
        exit;
    } else {
        echo "Error updating wallet balance: " . $conn->error;
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wallet</title>
    <link rel="stylesheet" type="text/css" href="css/wallet.css">
    <!-- Additional styles or external CSS can be linked here -->
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
            <h1>Your Wallet</h1>
            <img src="assets/circle.png" alt="dp">
            <div class="user-info flex">
                <p>Name: <?php echo $user_info["name"]; ?></p>
            </div>
    
            <div class="wallet-balance flex">
                <p>Total Balance: <span><?php echo $wallet_balance; ?></span> tokens</p>
            </div>
    
            <div class="add-money-section flex">
                <h3>Add Money to Your Wallet:</h3>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <label>Amount (in tokens):</label>
                    <input type="number" name="amount" required>
                    <input type="submit" value="Add Money">
                </form>
            </div>
    
            <div class="back-to-dashboard flex">
                <a href="user_dashboard.php">Back to Dashboard</a>
            </div>
        </div>
    </main>
</body>
</html>
