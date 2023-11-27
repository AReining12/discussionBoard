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

    // Assuming your database connection variables are set up correctly
    $servername = "localhost";
    $username = "username"; // Your database username
    $password = "password"; // Your database password
    $dbname = "your_database_name"; // Your database name

    // Create a database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check if the user is staff (professor, lecturer, teaching assistant, or team mentor)
    if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['professor', 'lecturer', 'teaching_assistant', 'team_mentor'])) {
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $boardName = $_POST['boardName'];
            $description = $_POST['description'];
            $adminId = $_SESSION['user_id']; // Assuming the user's ID is stored in the session

            // Prepare and execute SQL statement
            $stmt = $conn->prepare("INSERT INTO discussion_boards (board_name, description, admin_id) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $boardName, $description, $adminId);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "Board created successfully. You are the admin of this board.";
            } else {
                echo "Error creating board.";
            }

            $stmt->close();
        } else {
            // Display the form to create a board
            echo '<form action="" method="post">
                    Board Name: <input type="text" name="boardName"><br>
                    Description: <textarea name="description"></textarea><br>
                    <input type="submit" value="Create Board">
                  </form>';
        }
    } else {
        echo "You do not have permission to create a board.";
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
