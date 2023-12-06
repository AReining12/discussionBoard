<!DOCTYPE html>
<html>
<head>
    <title>Create Discussion Board</title>
    <style>
        #logoutButton {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        /* Additional styles for the form and the page */
    </style>
</head>
<body>
    <h1>Create a New Discussion Board</h1>

    <?php
    session_start();
    include 'db_connect.php'; // Include the database connection

    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id']; // Assuming the user's ID is stored in the session

        // Check if the user is a staff member
        $staffCheck = $conn->prepare("SELECT is_staff FROM groups JOIN users ON groups.group_id = users.group_id WHERE users.user_id = ?");
        $staffCheck->bind_param("i", $userId);
        $staffCheck->execute();
        $staffResult = $staffCheck->get_result();
        $isStaff = false;
        if ($row = $staffResult->fetch_assoc()) {
            $isStaff = $row['is_staff'];
        }
        $staffCheck->close();

        if ($isStaff) {
            // Staff member logic
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $boardName = $_POST['boardName'];

                // Begin transaction
                $conn->begin_transaction();

                try {
                    // Insert new board
                    $stmt = $conn->prepare("INSERT INTO boards (board_name) VALUES (?)");
                    $stmt->bind_param("s", $boardName);
                    $stmt->execute();
                    $boardId = $conn->insert_id; // Get the id of the newly created board

                    // Set the current user as the admin of the new board
                    $isAdmin = true;
                    $adminStmt = $conn->prepare("INSERT INTO board_users (user_id, board_id, is_board_admin) VALUES (?, ?, ?)");
                    $adminStmt->bind_param("iii", $userId, $boardId, $isAdmin);
                    $adminStmt->execute();

                    // Commit transaction
                    $conn->commit();

                    echo "Board created successfully. You are the admin of this board.";
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $conn->rollback();
                    echo "Error creating board: " . $e->getMessage();
                }
                
                $stmt->close();
                $adminStmt->close();
            } else {
                echo '<form action="" method="post">
                        Board Name: <input type="text" name="boardName"><br>
                        <input type="submit" value="Create Board">
                      </form>';
            }
        } else {
            echo "You do not have permission to create a board.";
        }
    } else {
        echo "Please log in to create a board.";
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
