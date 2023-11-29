<?php
session_start(); // Start a new session or resume the existing session

// Handle login when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish a database connection (replace with your database credentials)
    $servername = "localhost";
    $username = "root";
    $password = "123456";
    $dbname = "v_rides";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $vit_registration_number = $_POST["vit_registration_number"];
    $passcode = $_POST["passcode"];

    // Query to fetch user data by VIT registration number
    $sql = "SELECT * FROM user_info WHERE vit_registration_number = '$vit_registration_number'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Verify passcode using password_verify for securely hashed passcodes
        if (password_verify($passcode, $row["passcode"])) {
            // Authentication successful, set session variables
            $_SESSION["vit_registration_number"] = $vit_registration_number;
            $_SESSION["name"] = $row["name"];
            header("Location: cycle_ride.php"); // Redirect to the cycle ride
            exit;
        } else {
            $login_error = "Invalid passcode. Please try again.";
        }
    } else {
        $login_error = "User not found. Please check your VIT registration number.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label>VIT Registration Number:</label>
        <input type="text" name="vit_registration_number" required><br>

        <label>Passcode:</label>
        <input type="password" name="passcode" required><br>

        <?php
        if (isset($login_error)) {
            echo '<p style="color: red;">' . $login_error . '</p>';
        }
        ?>

        <input type="submit" value="Login">
    </form>
</body>
</html>
