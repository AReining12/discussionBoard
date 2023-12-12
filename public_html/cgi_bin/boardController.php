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

    createChannel($channelName):
    - creates a new channel

    manageChannelUsers($boardID, $action, $username, $channelName):
    - manages users of a specific channel

    searchMessages($boardID, $searchTerm):
    - processes message search

    getChannels($boardID):
    - returns a list of channels in the board

    
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
        header("Location: ../pages/DiscussionBoard.html");
        exit();
    }

    public function addMember($boardID, $username){
        $userModel = new userModel();


        // Get userID from username
        $userID = $userModel->getUserID($username);

        $boardModel = new boardModel();
        $boardModel->addMember($boardID, $userID);

        $redirectUrl = "../pages/DiscussionBoard.html?course=" . urlencode($boardID);

        // Redirect
        header("Location: " . $redirectUrl);
        exit;
    }

    public function approveMember($username, $boardID){
        $curBoardName = $_SESSION['boardname'];

        $boardModel = new boardModel();
        $curBoardID = $boardModel->getBoardID($curBoardName);

        $boardModel->approveMember($username, $boardID);

        $redirectUrl = "../pages/DiscussionBoard.html?course=" . urlencode($curBoardID);

        // Redirect
        header("Location: " . $redirectUrl);
        exit;
    }

    public function rejectMember($username, $boardID){
        $curBoardName = $_SESSION['boardname'];

        $boardModel = new boardModel();
        $curBoardID = $boardModel->getBoardID($curBoardName);

        $boardModel->rejectMember($username, $boardID);

        $redirectUrl = "../pages/DiscussionBoard.html?course=" . urlencode($curBoardID);

        // Redirect
        header("Location: " . $redirectUrl);
        exit;
    }

    public function removeMember($boardID, $username){
        $userModel = new userModel();


        // Get userID from username
        $userID = $userModel->getUserID($username);

        $boardModel = new boardModel();
        $boardModel->removeMember($boardID, $userID);

        $redirectUrl = "../pages/DiscussionBoard.html?course=" . urlencode($boardID);

        // Redirect
        header("Location: " . $redirectUrl);
        exit;
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

    public function addChannelMember($boardID, $username, $channelName){
        // get user id
        $userModel = new userModel();
        $userID = $userModel->getUserID($username);

        // call addChannelUser($boardID, $channelName, $userID)
        $boardModel = new boardModel();
        $boardModel->addChannelUser($boardID, $channelName, $userID);

        $redirectUrl = "../pages/DiscussionBoard.html?course=" . urlencode($boardID);

        // Redirect
        header("Location: " . $redirectUrl);
        exit;
    }

    public function removeChannelMember($boardID, $username, $channelName){
         // get user id
         $userModel = new userModel();
         $userID = $userModel->getUserID($username);
 
         // call removeChannelUser($boardID, $channelName, $userID)
         $boardModel = new boardModel();
         $boardModel->removeChannelUser($boardID, $channelName, $userID);
 
         $redirectUrl = "../pages/DiscussionBoard.html?course=" . urlencode($boardID);
 
         // Redirect
         header("Location: " . $redirectUrl);
         exit;
    }


    public function getChannels($boardID){
        $boardModel = new boardModel();
        $channels = $boardModel->getChannels($boardID);

        return $channels;
        // return true;
    }

    public function createChannel($boardID, $channelName){
        $boardModel = new boardModel();
        $boardModel->addChannel($boardID, $channelName);

        $redirectUrl = "../pages/DiscussionBoard.html?course=" . urlencode($boardID);

        // Redirect
        header("Location: " . $redirectUrl);
        exit; 
    }

    public function deleteChannel($boardID, $channelName){
        $boardModel = new boardModel();
        $boardModel->deleteChannel($boardID, $channelName);

        $redirectUrl = "../pages/DiscussionBoard.html?course=" . urlencode($boardID);

        // Redirect
        header("Location: " . $redirectUrl);
        exit; 
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
            case 'add_members':
                $boardName = $_SESSION['boardname'];

                //TODO: determine what inputs I will get (board name vs board id)
                // get board id (session variable boardname)
                $boardModel = new boardModel();
                $boardID = $boardModel->getBoardID($boardName);

                // get username
                $username = $_POST['name'];

                $boardController = new boardController();
                $boardController->addMember($boardID, $username);

                break;

            case 'remove_members':
                $boardName = $_SESSION['boardname'];

                //TODO: determine what inputs I will get (board name vs board id)
                // get board id (session variable boardname)
                $boardModel = new boardModel();
                $boardID = $boardModel->getBoardID($boardName);

                // get username
                $username = $_POST['name'];

                $boardController = new boardController();
                $boardController->removeMember($boardID, $username);
                
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

            case 'add_member_to_channel':
                $boardName = $_SESSION['boardname'];

                $boardModel = new boardModel();
                $boardID = $boardModel->getBoardID($boardName);

                // get username
                $username = $_POST['name'];

                // get channelName
                $channelName = $_POST['channel_name'];

                $boardController = new boardController();
                $boardController->addChannelMember($boardID, $username, $channelName);
                break;


            case 'remove_member_from_channel':
                // removeChannelMember($boardID, $username, $channelName)

                // get board id
                $boardName = $_SESSION['boardname'];

                $boardModel = new boardModel();
                $boardID = $boardModel->getBoardID($boardName);

                // get channelName
                $channelName = $_POST['channel_name'];

                // get user name
                $username = $_POST['user_name'];

                $boardController = new boardController();
                $boardController->removeChannelMember($boardID, $username, $channelName);
                break;

            case 'create_channels':
                // get board id
                $boardName = $_SESSION['boardname'];

                $boardModel = new boardModel();
                $boardID = $boardModel->getBoardID($boardName);


                // get channelName
                $channelName = $_POST['name'];

                $boardController = new boardController();
                $boardController->createChannel($boardID, $channelName);
                break;

            case 'remove_channels':
                // get board id
                $boardName = $_SESSION['boardname'];

                $boardModel = new boardModel();
                $boardID = $boardModel->getBoardID($boardName);


                // get channelName
                $channelName = $_POST['name'];

                $boardController = new boardController();
                $boardController->deleteChannel($boardID, $channelName);
                break;

            case 'approve_members':
                // get board id, username
                $boardName = $_SESSION['boardname'];

                $boardModel = new boardModel();
                $boardID = $boardModel->getBoardID($boardName);

                // get user name
                $username = $_POST['name'];

                $boardModel->approveMember($username, $boardID);
                break;


            case 'accept_request':
                // get board id, username
                $boardName = $_SESSION['boardname'];

                $boardModel = new boardModel();
                $boardID = $boardModel->getBoardID($boardName);

                // get user name
                $username = $_POST['name'];

                $boardController = new boardController();
                $boardController->approveMember($username, $boardID);
                break;

            case 'reject_request':
                // get board id, username
                $boardName = $_SESSION['boardname'];

                $boardModel = new boardModel();
                $boardID = $boardModel->getBoardID($boardName);

                // get user name
                $username = $_POST['name'];

                $boardController = new boardController();
                $boardController->rejectMember($username, $boardID);
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

            case 'set_board':
                if (isset($request->board_id) && isset($_SESSION['username'])) {
                    $boardModel = new boardModel();
                    $boards = $boardModel->getUserBoards($_SESSION['username']);
                    $found = false;
                    foreach ($boards as $board) {
                        if ($board['board_id'] === $request->board_id) {
                            $boardID = $board['board_id'];
                            $_SESSION['boardname'] = $board['board_name'];
                            $_SESSION['boardID'] = $board['board_id'];
                            $found = true;
                            break;
                        }
                    }
                    if ($found) {
                        echo json_encode(['success' => true, 'status' => 0, 'message' => 'Board set successfully']);
                    } else {
                        echo json_encode(['success' => false, 'status' => 1, 'message' => 'User is in no such board']);
                    }
                } else {
                    echo json_encode(['success' => false, 'status' => 2, 'message' => 'Illegal arguments']);
                }
                break;

            case 'get_channels':
                if (isset($_SESSION['boardID'])) {
                    $boardModel = new boardModel();
                    $channels = $boardModel->getUserChannels($_SESSION['username'], $_SESSION['boardID']);
                    echo json_encode(['success' => true, 'status' => 0, 'data' => $channels]);
                } else {
                    echo json_encode(['success' => false, 'status' => 1]);
                }
                break;

            case 'add_to_waiting_list':
                if (isset($_SESSION['user_id']) && isset($request->boardID)) {
                    $boardModel = new boardModel();
                    // $boardID = $boardModel->getBoardID($request->boardName);
                    $status = $boardModel->addToWaitingList($_SESSION['username'], $request->boardID);
                    echo json_encode(['success' => true, 'status' => $status]);           
                } else {
                    echo json_encode(['success' => false, 'status' => 1]);
                }
                break;

            case 'get_courses_not_joined':
                if (isset($_SESSION['username'])) {
                    $username = $_SESSION['username'];
                    $boardModel = new boardModel();
                    $courses = $boardModel->getCoursesNotJoined($username);
                    echo json_encode(['success' => true, 'status' => 0, 'data' => $courses]);
                } else {
                    echo json_encode(['success' => false, 'status' => 1]);
                }
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