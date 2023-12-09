<!DOCTYPE html>
<html>
    <head>
    </head>

    <body>

        <!-- Form to add or remove channels -->
        <form name="removeChannelsForm" action="boardController.php" method="post" autocomplete="on">
            <!--Hidden field to determine whether form is register or login-->
            <input type="hidden" name="action" value="remove_channels">
            <!-- Aligned text boxes-->
            <fieldset>
                <b id="name">What channel would you like to add or remove from this discussion board?</b>
                <!-- Dropdown box for channel names -->
                <select name="name">
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
            <input type="submit" name="remove_channels" value="Remove">
        </form>

    </body>
</html>
