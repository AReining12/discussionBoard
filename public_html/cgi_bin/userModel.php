<html>
    <head>
    </head>
    <body>
        <?php
        // Anna Reining, 260885420
        // Handles user operations in the database
        // contains methods:
        /*
            addUser ($username, $password, $firstname, $lastname, $email, $groupid):
            - adds new user to the database

            verifyUser ($username, $password):
            - verifies user credentials during login

            forgotPassword ($username, $recoveryAnswer):
            - implemented time allowing, allows user to change password
        */
            // database connection file
            // include('db_connect.php');

            class UserModel {
                public function addUser($username, $password, $firstname, $lastname, $email, $groupid) {
                    include('db_connect.php');
                    // adds user to database using function written by Mike
                    //$stmt = $conn->prepare("CALL registerUser('{$username}', '{$password}', '{$firstname}', '{$lastname}', '{$email}', '{$groupid}')");
                    //$stmt->bind_param("ss", $username, $password, $firstname, $lastname, $email, $groupid);

                    $stmt = $conn->prepare("SELECT registerUser(?, ?, ?, ?, ?, ?)");

                    // Bind parameters
                    $stmt->bind_param("sssssi", $username, $password, $firstname, $lastname, $email, $groupid);

                    if (!$stmt->execute()) {
                        // Log or handle the error
                        echo "Error: " . $stmt->error;
                    }

                    $stmt->close();
                    $conn->close();

                }

                public function verifyUser($username, $password){
                    // verifies user credentials against database
                    // is mike going to write a stored procedure for this?
                    require_once('session_start.php');
                    include('db_connect.php');
                    // SQL statement
                    $stmt = $conn->prepare("SELECT * FROM users WHERE user = ? AND pass = ?");

                    // Bind parameters
                    $stmt->bind_param("ss", $username, $password);

                    // Execute the query
                    $stmt->execute();

                    // Store the result
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        // User exists, set session vars
                        $row = $result->fetch_assoc();
                        $_SESSION['username'] = $row['user'];
                        $_SESSION['user_id'] = $row['user_id'];

                        // Close the statement and database connection
                        $stmt->close();
                        $conn->close();
                
                        return true; // User exists
                    } else {
                        // User doesn't exist
                        // Close the statement and database connection
                        $stmt->close();
                        $conn->close();
                
                        return false; // User doesn't exist
                    }

            }
            public function getUserID($username) {
                include('db_connect.php');

                $stmt = $conn->prepare("SELECT * FROM users WHERE user = ?");

                // Bind parameters
                $stmt->bind_param("s", $username);

                // Execute the query
                $stmt->execute();

                // Store the result
                $result = $stmt->get_result();

                $row = $result->fetch_assoc();

                $userID = $row['user_id'];

                return $userID;

            }

            public function verifyAdmin($boardID){
                include('db_connect.php');

                // verifies if user is an admin using $_SESSION
                $user = $_SESSION['username'];

                // tries to get row from board_members where 
                // user = userID and is_board_admin = 1
                // Using a prepared statement to avoid SQL injection
                $stmt = $conn->prepare("SELECT * FROM board_members WHERE user = ? AND board_id = ? AND is_board_admin = 1");
                $stmt->bind_param("si", $user, $boardID); // Assuming user is a string, change to "i" if it's an integer

                $stmt->execute();

                $result = $stmt->get_result();

                // Check if there is a row with is_board_admin = 1
                if ($result->num_rows > 0) {
                    echo "true";
                    $stmt->close();
                    $conn->close();

                    return true;
                } else {
                    echo "false";
                    $stmt->close();
                    $conn->close();

                    return false;
                }
            }

            public function verifyMember($userID, $channelID){
                require_once('session_start.php');
                include('db_connect.php');
                // SQL statement
                $stmt = $conn->prepare("SELECT * FROM channel_users WHERE user_id = ? AND channel_id = ?");

                // Bind parameters
                $stmt->bind_param("ii", $userID, $channelID);

                // Execute the query
                $stmt->execute();

                // Store the result
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {

                    // Close the statement and database connection
                    $stmt->close();
                    $conn->close();
            
                    return true; // User in channel
                } else {
                    // user not in channel
                    // Close the statement and database connection
                    $stmt->close();
                    $conn->close();
            
                    return false; // User doesn't exist
                }

            }
                
        }

            
        ?>

    </body>
</html>
