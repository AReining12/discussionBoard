<html>
    <head>
    </head>
    <body>
    <!--Form to add or remove channels-->
    <form name="input" action="messageController.php" method="post" autocomplete="on">
            <!--Hidden field to determine whether form is register or login-->
            <input type="hidden" name="action" value="view_messages">
            <!-- Aligned text boxes-->
            <fieldset>
                <b id="name">Channel name</b> <input type="text" name="name"> <br />
                <!--Get board id from $_POST variable boardname-->

            </fieldset>

            <!--Submit section-->
            <input type="submit" name="view_messages" value="View messages">

        <a href="discussion_board.php">Discussion Board</a>
        <a href="addMessage.php">New Message</a>
        <a href="search.php">Search Messages</a>
        <?php
        // Anna Reining, 260885420
        // User login form

        ?>

    </body>
</html>
