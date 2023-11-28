<!-- This is code for sign up page -->
<?php
// Establish a database connection (replace with your database credentials)
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "v_rides";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables to store form data
$vit_registration_number = $name = $batch = $email = $password = $confirm_password = $birthday = "";
$password_match_error = "";
$registration_result = "";

// Handle user registration when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vit_registration_number = $_POST["vit_registration_number"];
    $name = $_POST["name"];
    $batch = $_POST["batch"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $birthday = $_POST["birthday"];

    // Check if passwords match
    if ($password === $confirm_password) {
        // Hash the password for security (you should use a stronger hashing method)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the user already exists
        $check_user_sql = "SELECT * FROM user_info WHERE vit_registration_number = ?";
        $stmt_check_user = $conn->prepare($check_user_sql);
        $stmt_check_user->bind_param("s", $vit_registration_number);
        $stmt_check_user->execute();
        $result_check_user = $stmt_check_user->get_result();

        if ($result_check_user->num_rows > 0) {
            // User already exists, display an appropriate message
            $registration_result = "User with VIT registration number $vit_registration_number already exists.";
        } else {
            // User does not exist, proceed with the registration

            // SQL query to insert user data into the database using prepared statement
            $sql = "INSERT INTO user_info (vit_registration_number, name, batch, email_id, passcode, birthday)
                    VALUES (?, ?, ?, ?, ?, ?)";

            // Use prepared statement to prevent SQL injection
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $vit_registration_number, $name, $batch, $email, $hashed_password, $birthday);

            if ($stmt->execute()) {
                // Insert initial wallet balance (50 tokens) for the user
                $initial_balance = 50;
                $wallet_sql = "INSERT INTO wallet (vit_registration_number, wallet_balance) 
                               VALUES (?, ?)";

                // Use prepared statement to prevent SQL injection
                $wallet_stmt = $conn->prepare($wallet_sql);
                $wallet_stmt->bind_param("si", $vit_registration_number, $initial_balance);

                if ($wallet_stmt->execute()) {
                    $registration_result = "Registration successful!";
                } else {
                    $registration_result = "Error: " . $wallet_stmt->error;
                }

                $wallet_stmt->close();
            } else {
                $registration_result = "Error: " . $stmt->error;
            }

            // Close the prepared statement
            $stmt->close();
        }
    } else {
        $password_match_error = "Passwords do not match. Please try again.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/signup.css">
    <title>Signup Page</title>
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
                <a href="login.php" class="button">Login</a>
            </div>
        </div>
    </nav>
    
<div class="login-container">
    <h2>Sign Up</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        
        <div class="form-group">
            
            <!-- <label class="input_head" for="name">Name:</label> -->
            <input type="text" name="name" placeholder="Enter your Name" required>
            
        </div>
        
        <div class="form-group">

            <!-- <label class="input_head" for="password">Email:</label> -->
            <input type="email" name="email" placeholder="Enter your Email" required>
            
            
        </div>
        
        <div class="form-group">
            
            <!-- <label class="input_head" for="username">Vit Registration Number :</label> -->
            <input type="text" name="vit_registration_number" placeholder="Enter your Registration Number" required>
            
        </div>

        
        <div class="form-group">
            
            <!-- <label class="input_head" for="batch">Batch:</label> -->
            <input type="number" name="batch" placeholder="Enter your Batch" required>
            
        </div>

        
        <div class="form-group">
            
            <!-- <label class="input_head" for="password">Password:</label> -->
            <input type="password" name="password" placeholder="Enter your password" required>
            
        </div>
        
        <div class="form-group">
            
            <!-- <label class="input_head" for="password">Confirm Password:</label> -->
            <input type="password" name="confirm_password" placeholder="Enter your password again" required>
            
        </div>
        
        <span style="color: red;"><?php echo $password_match_error; ?></span>
        
        <div class="form-group">
            
            <!-- <label for="birthday" class="input_head">Birthday:</label> -->
            <input type="date" name="birthday" required>

        </div>
        <div class="lg_button_cont">

            <input type="submit" value="Sign Up" class="lg_button">

        </div>
    </form>
    <p><?php echo $registration_result; ?></p>
</div>
</div>
</body>
</html>
