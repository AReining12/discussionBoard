<!DOCTYPE html>
<html>
<head>
    <title>Delete Discussion Board</title>
    <style>
        #logoutButton {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        /* Additional styles for the page */
    </style>
</head>
<body>
    <h1>Delete a Discussion Board</h1>

    <?php
    session_start();

    // Assuming your database connection variables are set up correctly
    $servername = "localhost";
    $username = "username"; // Your database username
    $password = "password"; // Your database password
    $dbname = "your_database_name"; // Your database name

    // Create a database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check if the user is an admin of any board
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get board ID from form submission
            $boardId = $_POST['boardId'];

            // Check if the user is the admin of the board
            $stmt = $conn->prepare("SELECT admin_id FROM discussion_boards WHERE board_id = ?");
            $stmt->bind_param("i", $boardId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row && $row['admin_id'] == $userId) {
                // User is the admin, delete the board
                $deleteStmt = $conn->prepare("DELETE FROM discussion_boards WHERE board_id = ?");
                $deleteStmt->bind_param("i", $boardId);
                $deleteStmt->execute();

                if ($deleteStmt->affected_rows > 0) {
                    echo "Board deleted successfully.";
                } else {
                    echo "Error deleting board.";
                }
                $deleteStmt->close();
            } else {
                echo "You do not have permission to delete this board.";
            }
            $stmt->close();
        } else {
            // Display the form to delete a board
            echo '<form action="" method="post">
                    Board ID: <input type="number" name="boardId"><br>
                    <input type="submit" value="Delete Board">
                  </form>';
        }
    } else {
        echo "You need to log in to delete a board.";
    }

    $conn->close();
    ?>

    <button id="logoutButton" onclick="logout()">Logout</button>

    <script>
        function logout() {
            var confirmation = confirm("Are you sure to logout?");
            if (confirmation) {
                window.location.href = 'logout.php'; // Redirect to logout.php
            }
        }
    </script>
</body>
</html>
