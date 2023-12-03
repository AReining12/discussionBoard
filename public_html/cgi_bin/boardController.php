<?php
// Anna Reining, 260885420
// Handles discussion boards, members, channels, and messages
// Ineracts with boardModel
// contains methods:
/*
    createBoard($boardName): //not mine
    - processes creation of new board

    deleteBoard($boardID): // not mine
    - processes deletion of discussionBoard

    manageMembers($boardID, $action, $channelName):
    - manages members in a discussion board

    manageChannels($boardID, $action, $channelName):
    - manages channels in a discussion board

    searchMessages($boardID, $searchTerm):
    - processes message search
    
*/
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once('boardModel.php');
require_once('session_start.php');
require_once('userModel.php');

class boardController {
    public function createBoard($username, $boardName) {
        $boardModel = new boardModel();
        return $boardModel->createBoard($username, $boardName);
    }

    public function deleteBoard($username, $boardID) {
        $boardModel = new boardModel();
        return $boardModel->deleteBoard($username, $boardID);
    }

    public function manageMembers($boardID, $action, $username){
        $userModel = new userModel();

        // Get userID from username
        $userID = $userModel->getUserID($username);
        
        if ($action == 'addMember'){
            // call addMember
            $boardModel = new boardModel();
            $boardModel->addMember($boardID, $userID);
        } else {
            // call removeMember
            $boardModel = new boardModel();
            $boardModel->removeMember($boardID, $userID);
        }
        header("Location: discussion_board.php");
        exit();
    }

    public function manageChannels($boardID, $action, $channelName){
        if ($action == 'addChannel'){
            // call addChannel
            $boardModel = new boardModel();
            $boardModel->addChannel($boardID, $channelName);

        } else {
            // user verified as admin in model
            // if user is not admin of board, channel is not deleted
            $boardModel = new boardModel();
            $boardModel->removeChannel($boardID, $channelName);
        }
        header("Location: discussion_board.php");
        exit();
    }

    public function manageChannelUsers($boardID, $action, $username, $channelName){
        // get user id
        $userModel = new userModel();
        $userID = $userModel->getUserID($username);

        if ($action == 'add'){
            // call addChannelUser($boardID, $channelName, $userID)
            $boardModel = new boardModel();
            $boardModel->addChannelUser($boardID, $channelName, $userID);
        } else {
            // call removeChannelUser($boardID, $channelName, $userID)
            $boardModel = new boardModel();
            $boardModel->removeChannelUser($boardID, $channelName, $userID);
        }
        header("Location: discussion_board.php");
        exit();
    }

}

// if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $ajax = file_get_contents("php://input");

    // check if field is set to determine members vs channels switch
    
    if (isset($_POST['action'])){
        $action = $_POST['action'];

        switch($action) {
            case 'manage_members':

                $boardName = $_SESSION['boardname'];

                //TODO: determine what inputs I will get (board name vs board id)
                // get board id (session variable boardname)
                $boardModel = new boardModel();
                $boardID = $boardModel->getBoardID($boardName);

                // verify user is admin
                $userModel = new userModel();
                $isAdmin = $userModel->verifyAdmin($boardID);

                // if is admin, call manageMembers, otherwise return to page with no changes
                if ($isAdmin){
                    
                    // get variables from post
                    $action = $_POST['add_or_remove'];
                    $username = $_POST['name'];

                    $boardController = new boardController();
                    $boardController->manageMembers($boardID, $action, $username);

                } else { // do not make changes TODO: add message to user?
                    header("Location: discussion_board.php");
                    exit();
                }
                break;
            case 'manage_channels':
                // get board ID
                $boardName = $_SESSION['boardname'];

                // get board id (session variable boardname)
                $boardModel = new boardModel();
                $boardID = $boardModel->getBoardID($boardName);

                // get action
                $action = $_POST['add_or_remove'];

                // get channel name
                $channelName = $_POST['name'];

                // call manageChannels($boardID, $channelname)
                $boardController = new boardController();
                $boardController->manageChannels($boardID, $action, $channelName);
                break;

            case 'manage_channel_members':
                // manageChannelUsers($boardID, $action, $username, $channelName)
                // get board id
                $boardName = $_SESSION['boardname'];

                $boardModel = new boardModel();
                $boardID = $boardModel->getBoardID($boardName);

                // get action
                $action = $_POST['add_or_remove'];
                
                // get username
                $username = $_POST['user_name'];


                // get channelName
                $channelName = $_POST['channel_name'];

                // call manageChannels($boardID, $channelname)
                $boardController = new boardController();
                $boardController->manageChannelUsers($boardID, $action, $username, $channelName);
                break;
        }
    } else if ($ajax) {
        $request = json_decode($ajax);
        if (!isset($_SESSION['username'])) {
            echo json_encode(['success'=>false]);
            exit();
        }
        switch ($request->action) {
            case 'list_boards':
                $boardModel = new boardModel();
                echo json_encode(['success'=>true, 'data'=>$boardModel->getUserBoards($_SESSION['username'])]);
                break;

            case 'search_boards':
                if (!isset($request->query)) {
                    echo json_encode(['success'=>false]);
                    break;
                }
                $boardModel = new boardModel();
                echo json_encode(['success'=>true, 'data'=>$boardModel->searchBoards($request->query)]);
                break;

            case 'create_board':
                if (!isset($request->board_name)) {
                    echo json_encode(['success'=>false]);
                    break;
                }
                $boardController = new boardController();
                $status = $boardController->createBoard($_SESSION['username'], $request->board_name);
                echo json_encode(['success'=>true, 'status'=>$status]);
                break;

            case 'delete_board':
                if (!isset($request->board_id)) {
                    echo json_encode(['success'=>false]);
                    break;
                }
                $boardController = new boardController();
                $status = $boardController->deleteBoard($_SESSION['username'], $request->board_id);
                echo json_encode(['success'=>true, 'status'=>$status]);
                break;
            
            default:
                echo json_encode(['success'=>false]);
                break;
        }
        exit();
    } else {
        echo "Error: No action specified";
        exit();
    }


}
?>