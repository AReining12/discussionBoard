<?php
// Anna Reining, 260885420
// Manages discussion boards, members, channels, and messages
// contains methods:
/*
    createBoard($username, $boardName):  // not my job
    - Adds new discussion board to the database
    - Makes board creator the admin of the board
    - calls SQL function written by Mike

    deleteBoard($username, $boardID): // not my job
    - removes discussion board from database
    - only board admins are permitted to delete boards -> verified in controller

    addMember($boardID, $userID):
    - adds a user as a member to a discussion board
    - user may only be added by admin -> verified in controller

    removeMember($boardID, $userID):
    - removes user from discussion board
    - user may only be removed by admin (or themselves?) -> verified in controller

    addChannel($boardID, $channelName):
    - adds channel to discussion board
    - calls SQL function written by mike
    
    removeChannel($channelID):
    - removes channel from discussion board
    - can only be removed by admin

    searchMessages($boardID, $searchTerm):
    - retrieves messages by search criteria

*/
require_once('session_start.php');
require_once('userModel.php');

class boardModel {
    public function createBoard($username, $boardName) {
        include('db_connect.php');
        $sql = $conn->prepare("SELECT createBoard(?, ?) AS code");
        $sql->bind_param("ss", $username, $boardName);
        $sql->execute();
        $code = $sql->get_result()->fetch_all(MYSQLI_ASSOC)[0]["code"];
        
        $sql->close();
        $conn->close();
        return $code;
    }

    public function deleteBoard($username, $boardID) {
        include('db_connect.php');
        $sql = $conn->prepare("SELECT deleteBoard(?, ?) AS code");
        $sql->bind_param("ss", $username, $boardID);
        $sql->execute();
        $code = $sql->get_result()->fetch_all(MYSQLI_ASSOC)[0]["code"];
        
        $sql->close();
        $conn->close();
        return $code;
    }

    public function addMember($boardID, $userID){
        include('db_connect.php');
        // updates board users with new user on given board id
        // default is_admin is 0
        $sql = $conn->prepare("INSERT INTO `board_users` (`user_id`, `board_id`, `is_board_admin`) VALUES ('{$userID}', '{$boardID}', 0)");
        $sql->execute();
    }

    public function removeMember($boardID, $userID){
        include('db_connect.php');
        // SQL statement
        $stmt = $conn->prepare("SELECT * FROM board_users WHERE user_id = ? AND board_id = ?");

        // Bind parameters
        $stmt->bind_param("ii", $userID, $boardID);

        // Execute the query
        $stmt->execute();

        // Store the result
        $result = $stmt->get_result();

        // if true -> remove, if false -> user is already not a member
        if($result) {
            $stmt = $conn->prepare("DELETE FROM board_users WHERE user_id = ? AND board_id = ?");
            $stmt->bind_param("ii", $userID, $boardID);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            }
    }

    public function getBoardID($boardname) {
        include('db_connect.php');

        $stmt = $conn->prepare("SELECT * FROM boards WHERE board_name = ?");

        // Bind parameters
        $stmt->bind_param("s", $boardname);

        // Execute the query
        $stmt->execute();

        // Store the result
        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        $boardID = $row['board_id'];

        return $boardID;

    }

    public function getChannelIDFromName($channelname){
        include('db_connect.php');

        $stmt = $conn->prepare("SELECT * FROM channels WHERE channel_name = ?");

        $stmt->bind_param("s", $channelname);

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        $channelID = $row['channel_id'];

        return $channelID;
    }

    public function getChannelNameFromID($channelID){
        include('db_connect.php');

        $stmt = $conn->prepare("SELECT * FROM channels WHERE channel_id = ?");

        $stmt->bind_param("i", $channelID);

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        $channelname = $row['channel_name'];

        return $channelname;
    }

    public function addChannel($boardID, $channelName){
        // adds channel to board
        include('db_connect.php');

        $stmt = $conn->prepare("SELECT createChannel(?, ?, ?)");

        // Get user from session var
        $username = $_SESSION['username'];

        // Bind parameters
        $stmt->bind_param("sis", $username, $boardID, $channelName);

        if (!$stmt->execute()) {
            // Log or handle the error
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }

    public function removeChannel($boardID, $channelName){

        // verify that user is admin
        $userModel = new userModel();

        $result = $userModel->verifyAdmin($boardID);

        if ($result){
            // verify that channel exists
            include('db_connect.php');

            // SQL statement
            $stmt = $conn->prepare("SELECT * FROM channels WHERE board_id = ? AND channel_name = ?");

            // Bind parameters
            $stmt->bind_param("is", $boardID, $channelName);

            // Execute the query
            $stmt->execute();

            // Store the result
            $result = $stmt->get_result();

            // if true -> remove, if false -> channel does not exist
            if($result->num_rows > 0) {
                $channelID = $this->getChannelIDFromName($channelName);

                // delete channel from channel_users and from channels

                $stmt = $conn->prepare("DELETE FROM channel_users WHERE channel_id = ?");
                $stmt->bind_param("i", $channelID);
                $stmt->execute();
                $stmt->close();

                $stmt = $conn->prepare("DELETE FROM channels WHERE board_id = ? AND channel_name = ?");
                $stmt->bind_param("is", $boardID, $channelName);
                $stmt->execute();
                $stmt->close();

                $conn->close();
                }
            
        }

        
    }
    public function deleteChannel($boardID, $channelName){
        // verify that user is admin
        $userModel = new userModel();

        $result = $userModel->verifyAdmin($boardID);

        if ($result){
            // verify that channel exists
            include('db_connect.php');

            $channelID = $this->getChannelIDFromName($channelName);

            $stmt = $conn->prepare("SELECT deleteChannel(?)");

            // Bind parameters
            $stmt->bind_param("i", $channelID);

            if (!$stmt->execute()) {
                // Log or handle the error
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
            $conn->close();


        }


    }

    public function addChannelUser($boardID, $channelName, $userID){
        include('db_connect.php');
        // verify that user is admin
        $userModel = new userModel();
        $isAdmin = $userModel->verifyAdmin($boardID);
        
        if ($isAdmin){
            // get channel id
            $channelID = $this->getChannelIDFromName($channelName);

            // check if in channel_users alread
            
            // SQL statement
            $stmt = $conn->prepare("SELECT * FROM channel_users WHERE user_id = ? AND channel_id = ?");

            // Bind parameters
            $stmt->bind_param("ii", $userID, $channelID);

            // Execute the query
            $stmt->execute();

            // Store the result
            $result = $stmt->get_result();

            if ($result->num_rows == 0){
                $sql = $conn->prepare("INSERT INTO `channel_users` (`user_id`, `channel_id`) VALUES ('{$userID}', '{$channelID}')");
                $sql->execute();
                $stmt->close();
                $sql->close();
                $conn->close();
            }

        }

    }

    public function removeChannelUser($boardID, $channelName, $userID){
        include('db_connect.php');
        // verify that user is admin
        $userModel = new userModel();
        $isAdmin = $userModel->verifyAdmin($boardID);

        
        if ($isAdmin){
            // get channel id
            $channelID = $this->getChannelIDFromName($channelName);

            // check if in channel_users alread
            
            // SQL statement
            $stmt = $conn->prepare("SELECT * FROM channel_users WHERE user_id = ? AND channel_id = ?");

            // Bind parameters
            $stmt->bind_param("ii", $userID, $channelID);

            // Execute the query
            $stmt->execute();

            // Store the result
            $result = $stmt->get_result();

            if ($result->num_rows > 0){
                $sql = $conn->prepare("DELETE FROM channel_users WHERE user_id = ? AND channel_id = ?");
                $sql->bind_param("ii", $userID, $channelID);
                $sql->execute();
                $stmt->close();
                $sql->close();
                $conn->close();
            }

        }
    }

    public function getUserBoards($username) {
        include('db_connect.php');
        // SQL statement
        $stmt = $conn->prepare("SELECT DISTINCT board_id, board_name FROM user_boards WHERE user = ?");

        // Bind parameters
        $stmt->bind_param("s", $username);

        // Execute the query
        $stmt->execute();

        // Store the result
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $rows;
    }

    public function searchBoards($query) {
        include('db_connect.php');
        // SQL statement
        $stmt = $conn->prepare("SELECT * FROM boards WHERE boards.board_name LIKE ?");

        $query = "%$query%";
        // Bind parameters
        $stmt->bind_param("s", $query);

        // Execute the query
        $stmt->execute();

        // Store the result
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $rows;
    }

    public function getChannels($boardID){
        include('db_connect.php');
        // SQL statement
        $stmt = $conn->prepare("SELECT * FROM channels WHERE board_id = ?");

        // Bind parameters
        $stmt->bind_param("i", $boardID);

        // Execute the query
        $stmt->execute();

        // Store the result
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $rows;
    }

    public function getUserChannels($boardID, $userID){
        include('db_connect.php');
        // SQL statement
        $stmt = $conn->prepare("SELECT * FROM channel_users WHERE user_id = ?");

        // Bind parameters
        $stmt->bind_param("i", $userID);

        // Execute the query
        $stmt->execute();

        // Store the result
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $rows;
    }
}
?>