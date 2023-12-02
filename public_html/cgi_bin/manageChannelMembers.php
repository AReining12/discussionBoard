<html>
    <head>
    </head>
    <body>
    
        <!--Form to add or remove channels-->
        <form name="input" action="boardController.php" method="post" autocomplete="on">
            <!--Hidden field to determine whether form is register or login-->
            <input type="hidden" name="action" value="manage_channel_members">
            <!-- Aligned text boxes-->
            <fieldset>
                <b id="user_name">Who would you like to add or remove from a channel?</b> <input type="text" name="user_name"> <br />
                <b id="channel_name">What channel would you like to add or remove this user from?</b> <input type="text" name="channel_name"> <br />
                <!--Get board id from $_POST variable boardname-->
            </fieldset>
            <b>Add or Remove?</b>
            <!--Radio Buttons-->
            <input type="radio" name="add_or_remove" value="add">Add
            <input type="radio" name="add_or_remove" value="remove">Remove
            <!--Submit section-->
            <input type="submit" name="manage_channel_members" value="Manage channel members">
        <?php
        // Anna Reining, 260885420
        // User login form
        ?>

    </body>
</html>
