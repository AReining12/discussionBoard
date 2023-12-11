<?php
    session_start();
//Mingchen Ju 260864282
    include 'db_connect.php'; // Include the database connection

    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id']; // Assuming the user's ID is stored in the session

        // Check if the user is a staff member
        $staffCheck = $conn->prepare("SELECT groups.is_staff FROM groups JOIN users ON groups.group_id = users.group_id WHERE users.user_id = ?");
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
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['courseName'])) {
                $courseName = $_POST['courseName'];

                // Ensure that the course name is not empty
                if (!empty($courseName)) {
                    // Begin transaction
                    $conn->begin_transaction();

                    try {
                        // Insert new board
                        $stmt = $conn->prepare("INSERT INTO boards (board_name) VALUES (?)");
                        $stmt->bind_param("s", $courseName);
                        $stmt->execute();
                        $boardId = $conn->insert_id; // Get the id of the newly created board

                        // Set the current user as the admin of the new board
                        $isAdmin = 1; // Assuming 'is_board_admin' is a boolean represented as an integer
                        $adminStmt = $conn->prepare("INSERT INTO board_users (user_id, board_id, is_board_admin) VALUES (?, ?, ?)");
                        $adminStmt->bind_param("iii", $userId, $boardId, $isAdmin);
                        $adminStmt->execute();

                        // Commit transaction
                        $conn->commit();

                        // Using JavaScript to show an alert and redirect
                        echo "<script>
                                alert('Board created successfully. You are the admin of this board.');
                                window.location.href = '../pages/SelectBoard.html#myCourses'; // Replace with the path to your SelectBoard.html page
                              </script>";
                    } catch (Exception $e) {
                        // Rollback transaction on error
                        $conn->rollback();
                        echo "Error creating board: " . $e->getMessage();
                    }

                    // Close the prepared statements if they have been set
                    if (isset($stmt)) {
                        $stmt->close();
                    }
                    if (isset($adminStmt)) {
                        $adminStmt->close();
                    }
                } else {
                    echo "Board name is required.";
                }
            } else {
                // Form display logic
                echo '<form action="" method="post">
                        Board Name: <input type="text" name="courseName"><br>
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
