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
    include 'db_connect.php'; // Include the database connection

    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get board ID from form submission
            $boardId = $_POST['boardId'];

            // Check if the user is the admin of the board
            $checkAdminStmt = $conn->prepare("SELECT * FROM board_users WHERE board_id = ? AND user_id = ? AND is_board_admin = 1");
            $checkAdminStmt->bind_param("ii", $boardId, $userId);
            $checkAdminStmt->execute();
            $result = $checkAdminStmt->get_result();

            if ($result->num_rows > 0) {
                // User is the admin, delete the board
                $deleteStmt = $conn->prepare("DELETE FROM boards WHERE board_id = ?");
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
            $checkAdminStmt->close();
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
