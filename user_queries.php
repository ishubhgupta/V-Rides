<!-- this contains all the query  -->
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

$vit_registration_number = $_SESSION["vit_registration_number"];

// Fetch user information
$user_info_sql = "SELECT * FROM user_info WHERE vit_registration_number = '$vit_registration_number'";
$user_info_result = $conn->query($user_info_sql);

if ($user_info_result->num_rows == 1) {
    $user_info = $user_info_result->fetch_assoc();
} else {
    // Handle the case where user information is not found
    echo "User information not found.";
    exit;
}

// Fetch feedback and queries along with solutions using a JOIN operation
$user_queries_sql = "SELECT feedback.query_id, feedback.query, feedback.solved, solutions.solution
                    FROM feedback
                    LEFT JOIN solutions ON feedback.query_id = solutions.query_id
                    WHERE feedback.vit_registration_number = '$vit_registration_number'";

$user_queries_result = $conn->query($user_queries_sql);
?> 

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Queries</title>
    <link rel="stylesheet" href="css/user_queries.css">
</head>

<nav class="navbar">
    <div class="navbar-container">
      <div class="logo">
        <img src="assets/logo_index.png" alt="Logo">
      </div>
      <h1>User Queries</h1>
    </div>
  </nav>

<body>
    <main>

        
        <div class="container">

            <div class="flex-items height">
                <img src="assets/circle.png" alt="">
            </div>

            <div class="flex-items height">

                <h3>User Information</h3>
                <p>Name: <?php echo $user_info["name"]; ?></p>
                <p>Reg. no.: <?php echo $user_info["vit_registration_number"]; ?></p>
            </div>
    
            <div class="flex-items">

                <h3>Previous Queries</h3>
        
                <?php if ($user_queries_result->num_rows > 0) { ?>
                    <table border="1" class="user-queries-table">
                        <tr>
                            <th>Query ID</th>
                            <th>Query</th>
                            <th>Status</th>
                            <th>Response</th>
                        </tr>
                        <?php while ($row = $user_queries_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row["query_id"]; ?></td>
                                <td><?php echo $row["query"]; ?></td>
                                <td><?php echo $row["solved"] ? 'Answered' : 'Pending'; ?></td>
                                <td><?php echo $row["solution"] ?? ''; ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } else { ?>
                    <p>No previous queries found.</p>
                <?php } ?>
            </div>
    
            <br>
    
        </div>
        
    </main>
    <footer>
        <a href="user_dashboard.php">Back to Dashboard</a>
    </footer>
</body>

</html>
