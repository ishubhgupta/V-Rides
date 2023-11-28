<!-- this page will handle queries send by users -->
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

// Fetch all queries from the feedback table initially
$fetchAllQueriesSql = "SELECT * FROM feedback";
$allQueriesResult = $conn->query($fetchAllQueriesSql);

$unsolvedQueries = [];
$solvedQueries = [];

// Separate queries into unsolved and solved
while ($row = $allQueriesResult->fetch_assoc()) {
    if ($row["solved"] == 0) {
        $unsolvedQueries[] = $row;
    } else {
        $solvedQueries[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_answer"])) {
    $queryId = $_POST["query_id"];
    $answer = mysqli_real_escape_string($conn, $_POST["answer"]);

    // Update the feedback table to mark the query as solved
    $updateQuerySql = "UPDATE feedback SET solved = 1 WHERE query_id = $queryId";

    if ($conn->query($updateQuerySql) === TRUE) {
        // Insert the solution into the solutions table
        $insertSolutionSql = "INSERT INTO solutions (query_id, solution) VALUES ($queryId, '$answer')";

        if ($conn->query($insertSolutionSql) === FALSE) {
            echo '<p style="color: red;">Error submitting answer: ' . $conn->error . '</p>';
        }
    } else {
        echo '<p style="color: red;">Error updating query status: ' . $conn->error . '</p>';
    }
}

// Fetch all queries again after the modification
$allQueriesResult = $conn->query($fetchAllQueriesSql);
$unsolvedQueries = [];
$solvedQueries = [];

// Separate queries into unsolved and solved
while ($row = $allQueriesResult->fetch_assoc()) {
    if ($row["solved"] == 0) {
        $unsolvedQueries[] = $row;
    } else {
        $solvedQueries[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Answer Query</title>
    <link rel="stylesheet" href="css/handle_query.css">
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
            <div class="flex-container">
                <!-- Unsolved Queries on Left -->
                <div class="flex-item">
                    <h3>Unsolved Queries</h3>
                    <?php if (!empty($unsolvedQueries)) { ?>
                        <?php foreach ($unsolvedQueries as $row) { ?>
                            <div class="query-box">
                                <p><strong>Query ID:</strong> <?php echo $row["query_id"]; ?></p>
                                <p><strong>Name:</strong> <?php echo $row["name"]; ?></p>
                                <p><strong>Query:</strong> <?php echo $row["query"]; ?></p>
                                
                                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                    <input type="hidden" name="query_id" value="<?php echo $row["query_id"]; ?>">
                                    <label for="answer"><strong>Your Answer:</strong></label>
                                    <textarea id="answer" name="answer" rows="4" required></textarea>
                                    <input type="submit" name="submit_answer" value="Submit Answer">
                                </form>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <p>No unsolved queries left.</p>
                    <?php } ?>
                </div>

                <!-- Solved Queries on Right with Scroll -->
                <div class="flex-item scroll-right">
                    <h3>Solved Queries</h3>
                    <?php if (!empty($solvedQueries)) { ?>
                        <?php foreach ($solvedQueries as $row) { ?>
                            <div class="query-box">
                                <p><strong>Query ID:</strong> <?php echo $row["query_id"]; ?></p>
                                <p><strong>Name:</strong> <?php echo $row["name"]; ?></p>
                                <p><strong>Query:</strong> <?php echo $row["query"]; ?></p>
                                <p class="solved-message">This query has already been solved.</p>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </main>

    <a class="back-to-dashboard" href="dev_dashboard.php">Back to Dashboard</a>
    <br>
</body>
</html>
