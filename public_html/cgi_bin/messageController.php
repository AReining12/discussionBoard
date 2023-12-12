<?php
// Anna Reining, 260885420
// Handles message-related actions and interacts with the MessageModel
// contains methods:
/*
    addMessage($boardID, $channelID, $userID, $message):
    - processes the addition of a new message
    getMessages($channelId):
    - retrieves messages from a specific channel
*/
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once('userModel.php');
require_once('messageModel.php');
require_once('session_start.php');
require_once('boardModel.php');

class messageController {
    public function addMessage($boardID, $channelID, $userID, $message, $message_title){
        // verify user can send message (is member)
        // verify that user is a member of channel
//        $userID = $_SESSION['user_id'];

//        $userModel = new userModel();
//        $isMember = $userModel->verifyMember($userID, $channelID);
        // echo "$isMember";
        
        //user verification done in database
//        if ($isMember){
            // call messageModel->addMessage
        $messageModel = new MessageModel();
        $code = $messageModel->addMessage($boardID, $channelID, $userID, $message, $message_title);
        return $code;
//        } 
//        header('Location: viewMessages.php');
//        exit;   
        
        // return user to viewMessages.php
    }

    public function getMessages($channelID){
        // verify that user is a member of channel
        $userID = $_SESSION['user_id'];

        $userModel = new userModel();
        $isMember = $userModel->verifyMember($userID, $channelID);
        // call and return getMessages in messageModel
        if ($isMember){
            $messageModel = new MessageModel();
            $messages = $messageModel->getMessages($channelID);
            // row form
            return $messages;
        } else {
            header('Location: viewMessages.php');
            exit;
        }
    }

    public function searchMessages($search_query, $channelID){
        // verify that user is a member of channel
        $userID = $_SESSION['user_id'];

        $userModel = new userModel();
        $isMember = $userModel->verifyMember($userID, $channelID);
        if ($isMember){
            // call searchMessage($search_query, $channel_id)
            $messageModel = new messageModel();
            $messageModel->searchMessage($search_query, $channelID);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ajax = file_get_contents("php://input");

    // check if field is set to determine login vs register switch
    if (isset($_POST['action'])){
        $action = $_POST['action'];

        switch($action) {
            case 'view_messages':
                $channelname = $_POST['name'];

                // get channel id
                $boardModel = new boardModel();
                $channelID = $boardModel->getChannelIDFromName($channelname);

                // get messages
                $messageController = new messageController();
                $messageController->getMessages($channelID);
                // $message = $messages['message_text'];
                
                // display messages
                break;

            case 'add_message':
                require_once('boardModel.php');

                // get board ID
                $boardname = $_SESSION['boardname'];
                $boardModel = new boardModel();
                $boardID = $boardModel->getBoardID($boardname);

                // get channel id
                $channelname = $_POST['channel'];
                $boardModel = new boardModel();
                $channelID = $boardModel->getChannelIDFromName($channelname);

                // addMessage($boardID, $channelID, $userID, $message)
                $message = $_POST['message'];
                $userID = $_SESSION['user_id'];

                // call message controller -> addMessage($boardID, $channelID, $userID, $message)
                $messageController = new messageController();
                $messageController->addMessage($boardID, $channelID, $userID, $message);
                break;

            case 'search_messages':
                $channelname = $_POST['channel'];
                $search_query = $_POST['search'];

                // get channel id
                $boardModel = new boardModel();
                $channelID = $boardModel->getChannelIDFromName($channelname);

                // call searchMessage
                $messageController = new messageController();
                $messageController->searchMessages($search_query, $channelID);

                break;
        }
    } else if ($ajax) {
        $request = json_decode($ajax);
        if (!isset($_SESSION['username'])) {
            echo json_encode(['success'=>false]);
            exit();
        }
        switch ($request->action) {
            case 'get_messages':
                if (isset($_SESSION['boardID'])) {
                    $messageModel = new messageModel();
                    $rows = $messageModel->getBoardMessages($_SESSION['username'], $_SESSION['boardID']);
                    echo json_encode(['success' => true, 'message' => 'Success', 'data' => $rows]);           
                } else {
                    echo json_encode(['success' => false, 'message' => 'No active board found', 'data' => []]);
                }
                break;

            case 'send_message':
                if (isset($_SESSION['boardID']) && isset($request->title) && isset($request->content) && isset($request->channel)) {
                    $messageController = new messageController();
                    $status = $messageController->addMessage($_SESSION['boardID'], $request->channel, $_SESSION['user_id'], $request->content, $request->title);
                    echo json_encode(['success' => true, 'status' => $status]);           
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

