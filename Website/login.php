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
    <link rel="stylesheet" href="css/signup.css">
    <title>Login Page</title>
</head>
<body>
    <div class="main">

        <div class="overlay">
            <div class="param">
                <h1>
                    ___
                    <br>Discover, Connect, Ride.<br> ___
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
    <h2>Login</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="form-group">
                    <label class="input_head" for="vit_registration_number">VIT Registration Number:</label>
                    <input type="text" id="vit_registration_number" name="vit_registration_number" placeholder="Enter your VIT registration number" required>
                </div>
                <div class="form-group">
                    <label class="input_head" for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <?php
                if (isset($login_error)) {
                    echo '<p style="color: red;">' . $login_error . '</p>';
                }
                ?>

                <div class="lg_button_cont">
                    <button class="lg_button">Login</button>
                </div>
            </form>

</div>
</div>
</body>
</html>
