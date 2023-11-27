<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Board</title>
    <link rel="stylesheet" type="text/css" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>

<div class="navbar">
    <a href="#">Dashboard</a>
    <a href="#" class="logout" onclick="logout()">Logout</a>
</div>

<div class="container">
    <?php
    session_start();

    // Assuming your database connection variables are set up correctly
    $servername = "localhost";
    $username = "username"; // Your database username
    $password = "password"; // Your database password
    $dbname = "your_database_name"; // Your database name

    // Create a database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to select all boards
    $sql = "SELECT board_id, board_name FROM discussion_boards";
    $result = $conn->query($sql);

    // Check and display boards
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div class='board' onclick='selectBoard(" . $row["board_id"] . ")'>";
            echo "<h3>" . htmlspecialchars($row["board_name"]) . "</h3>";
            echo "</div>";
        }
    } else {
        echo "No boards available.";
    }

    $conn->close();
    ?>
</div>

<script>
    function logout() {
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = 'logout.php';
        }
    }

    function selectBoard(boardId) {
        window.location.href = 'boardDetails.php?id=' + boardId;
    }
</script>

</body>
</html>
