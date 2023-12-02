<html>
    <head>
    </head>
    <body>
        <?php
        // Anna Reining, 260885420
        // Handles user operations related to messages (should search messages be here?)
        // contains methods:
        /*
            addMessage($boardID, $channelID, $userID):
            - adds new message to discussion board and channel
            getMessages($channelID):
            - retrieves messages from a specific channel
            - search?
            
        */
        class messageModel{
            public function addMessage($boardID, $channelID, $userID, $message_text){
                include('db_connect.php');
                require_once('session_start.php');
                $username = $_SESSION['username'];
                // adds user to database using stored function written by Mike
                $stmt = $conn->prepare("SELECT sendMessage(?, ?, ?)");
                $stmt->bind_param("sis", $username, $channelID, $message_text);
                echo "$username";
                echo "$channelID";
                echo "$message_text";

                $stmt->execute();

                $stmt->close();
            }
            public function getMessages($channelID){
                include('db_connect.php');
                
                $sql = "SELECT message_text FROM messages WHERE channel_id = ?";
                
                $stmt = mysqli_prepare($conn, $sql);

                // Bind the parameter
                mysqli_stmt_bind_param($stmt, "i", $channelID);

                // Execute the query
                mysqli_stmt_execute($stmt);

                // Bind the result variable
                mysqli_stmt_bind_result($stmt, $messageText);

                // Fetch and display data
                while (mysqli_stmt_fetch($stmt)) {
                    echo $messageText . "<br>";
                }
     

            }
            public function searchMessage($search_query, $channel_id){
                include('db_connect.php');  // Ensure the database connection is included

                // use LIKE to select messages
                $sql = "SELECT * FROM messages WHERE message_text LIKE ? AND channel_id = ?";
                
                // wildcards
                $search_query = "%" . $search_query . "%";  
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $search_query, $channel_id);

                $stmt->execute();
                $result = $stmt->get_result();

                echo "Similar Messages: <br>";

                while ($row = $result->fetch_assoc()) {
                    echo $row['message_text'] . '<br>';
                }

                $stmt->close();
                $conn->close();
            }
        }
        ?>

    </body>
</html>