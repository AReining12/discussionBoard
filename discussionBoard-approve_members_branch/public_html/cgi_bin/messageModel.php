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
    public function addMessage($boardID, $channelID, $userID, $message_text, $message_title){
        include('db_connect.php');
        require_once('session_start.php');
        $username = $_SESSION['username'];
        // adds user to database using stored function written by Mike
        $stmt = $conn->prepare("SELECT sendMessage(?, ?, ?, ?) AS code");
        $stmt->bind_param("siss", $username, $channelID, $message_text, $message_title);
        // echo "$username";
        // echo "$channelID";
        // echo "$message_text";

        $stmt->execute();

        $code = $stmt->get_result()->fetch_all(MYSQLI_ASSOC)[0]["code"];
        $stmt->close();
        $conn->close();
        return $code;
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

    public function getBoardMessages($username, $boardID) {
        include('db_connect.php');
        // SQL statement
        $stmt = $conn->prepare("SELECT * FROM visible_messages WHERE board_id=? AND user=?");

        // Bind parameters
        $stmt->bind_param("is", $boardID, $username);

        // Execute the query
        $stmt->execute();

        // Store the result
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $rows;
    }

    public function searchMessage($search_query, $channel_id){
        include('db_connect.php');  // Ensure the database connection is included

        // use LIKE to select messages
        $sql = "SELECT * FROM messages WHERE channel_id = ?";
        
        // wildcards
        $tokens = explode(" ", $search_query);
        if (($key = array_search("", $tokens)) !== false) {
            unset($tokens[$key]);
        }
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $channel_id);

        $stmt->execute();
        $result = $stmt->get_result();
        $fetch = $result->fetch_all(MYSQLI_ASSOC);
        $rows = array();
        foreach ($fetch as $row) {
            foreach ($tokens as $token) {
                if (str_contains($row['message_text'], $token)) {
                    array_push($rows, $row);
                    break;
                }
            }
        }

        echo "Similar Messages: <br>";

        foreach($rows as $row) {
            echo $row['message_text'] . '<br>';
        }
        $stmt->close();
        $conn->close();
    }
}
?>