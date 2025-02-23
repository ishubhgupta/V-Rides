<!-- this page is used for login -->
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "123456";
    $dbname = "v_rides";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $vit_registration_number = $_POST["vit_registration_number"];
    $passcode = $_POST["password"]; // Update to match the input field name in your HTML form

    $sql = "SELECT * FROM user_info WHERE vit_registration_number = '$vit_registration_number'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($passcode, $row["passcode"])) { // Update to match the column name in your database
            $_SESSION["vit_registration_number"] = $vit_registration_number;
            $_SESSION["name"] = $row["name"];
            header("Location: user_dashboard.php");
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Login - V-Rides</title>
</head>
<body>
    <div class="main">
        <div class="overlay">
            <div class="param">
                <h1>
                    <span class="highlight">Welcome Back!</span>
                    <br>Your Next Ride Awaits<br>
                    <span class="subtitle">Connect with fellow travelers</span>
                </h1>
            </div>
        </div>

        <nav class="navbar">
            <div class="navbar-container">
                <div class="logo">
                    <img src="assets/logo_index.png" alt="Logo">
                </div>
                <div class="buttons">
                    <a href="signup.php" class="button">Signup</a>
                </div>
            </div>
        </nav>
        
        <div class="login-container">
            <div class="login-header">
                <h2>Sign In</h2>
                <p class="login-subtitle">Please enter your credentials to continue</p>
            </div>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="form-group">
                    <label class="input_head" for="vit_registration_number">Registration Number</label>
                    <input type="text" id="vit_registration_number" name="vit_registration_number" placeholder="Enter your VIT registration number" required>
                </div>
                <div class="form-group">
                    <label class="input_head" for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <?php if (isset($login_error)): ?>
                    <p class="error-message"><?php echo $login_error; ?></p>
                <?php endif; ?>

                <div class="lg_button_cont">
                    <button class="lg_button">Sign In</button>
                </div>
                <p class="signup-link">Don't have an account? <a href="signup.php">Sign Up</a></p>
            </form>
        </div>
    </div>
</body>
</html>
