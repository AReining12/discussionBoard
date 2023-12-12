<html>
    <head>
    </head>
    <body>

        <?php
        // Anna Reining, 260885420
        // Displays discussions, members, channels, and messages
        // not my job -> assuming A will verify and pass inputs
        // within selected discussion board
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        require_once('session_start.php');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['boardname'] = $_POST['boardname'];
            echo "{$_POST['boardname']}";

            // for now: 
            // get board id from board name

            // get user_id from session info

            //insert into SQL
            // $sql = "INSERT INTO `board_users` (`user_id`, `board_id`, `is_admin`) VALUES (NULL, '{$_POST['username']}', '0')";
        }
        ?>
        <a href="manageChannels.php">Manage channels</a>
        <a href="manageMembers.php">Manage members</a>
        <a href="manageChannelMembers.php">Manage channel members</a>
        <a href="viewMessages.php">View Messages</a>


    </body>
</html>
