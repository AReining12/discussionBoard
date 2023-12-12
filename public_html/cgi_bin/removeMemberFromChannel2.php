<!DOCTYPE html>
<html>
    <head>
    </head>

    <body>
    <!-- Form to add or remove channels -->
        <form name="removeMembersForm2" action="boardController.php" method="post" autocomplete="on">
            <!--Hidden field to determine whether form is register or login-->
            <input type="hidden" name="action" value="remove_member_from_channel">
            <input type="hidden" name="user_name" value="<?php echo isset($_POST["name"]) ? $_POST["name"] : ''; ?>">
            <!-- Aligned text boxes-->
            <fieldset>
                <b id="name">Which channel would you like to remove this user from?</b>
                <!-- Dropdown box for channel names -->
                <select name="channel_name">
                    <option value="">Select a Channel</option>
                    <?php
                    error_reporting(E_ALL);
                    include('session_start.php');
                    include('userModel.php');
                    include('boardModel.php');
                    include('boardController.php');

                    $userRemove = $_POST['name'];

                    $userModel = new userModel();
                    $removeUserID = $userModel->getUserID($userRemove);

                    // get variables
                    $userID = $_SESSION['user_id'];
                    $boardname = $_SESSION['boardname'];

                    //get boardID
                    $boardModel = new boardModel();
                    $boardID = $boardModel->getBoardID($boardname);

                    // get user channels
                    $userChannels = $boardModel->getUserChannels($userRemove, $boardID);
                    
                    if (!empty($userChannels)) {
                        foreach ($userChannels as $userChannel) {
                            $channelID = $userChannel['channel_id'];
                            $channelName = $boardModel->getChannelNameFromID($channelID);
                            echo "<option value=\"$channelName\">{$channelName}</option>";
                        }
                    } else {
                        // Handle the case when $userChannels is empty (e.g., display a message)
                        echo "<option value=''>No Channels Found</option>";
                    }
                    
                    ?>
                </select>
                <br />
            </fieldset>

            <!-- Submit section -->
            <input type="submit" name="remove_member_from_channel" value="Remove">
        </form>

    </body>
</html>