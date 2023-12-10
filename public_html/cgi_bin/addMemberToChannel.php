<!DOCTYPE html>
<html>
    <head>
    </head>

    <body>

        <!-- Form to add or remove channels -->
        <form name="removeMemberFromChannelForm" action="boardController.php" method="post" autocomplete="on">
            <!--Hidden field to determine whether form is register or login-->
            <input type="hidden" name="action" value="add_member_to_channel">
            <!-- Aligned text boxes-->
            <fieldset>
                <b id="name">Username</b> <input type="text" name="name"> <br />
                <b id="channel_name">Which channel would you like to add this user to?</b>
                <!-- Dropdown box for channel names -->
                <select name="channel_name">
                    <option value="">Select a Channel</option>
                    <?php
                    error_reporting(E_ALL);
                    include('session_start.php');
                    include('userModel.php');
                    include('boardModel.php');
                    include('boardController.php');

                    // get variables
                    $userID = $_SESSION['user_id'];
                    $boardname = $_SESSION['boardname'];

                    //get boardID
                    $boardModel = new boardModel();
                    $boardID = $boardModel->getBoardID($boardname);

                    // verify user is admin
                    $userModel = new userModel();
                    $isAdmin = $userModel->verifyAdmin($boardID);
                    var_dump($isAdmin);

                    // if user is admin, show them list of channels
                    if ($isAdmin){
                        $boardController = new boardController();
                        $channels = $boardController->getChannels($boardID);
                        foreach ($channels as $channel) {
                            echo "<option value=\"{$channel['channel_name']}\">{$channel['channel_name']}</option>";
                        }
                    } 
                    ?>
                </select>
                <br />
            </fieldset>

            <!-- Submit section -->
            <input type="submit" name="add_member_to_channel" value="Add">
        </form>

    </body>
</html>
