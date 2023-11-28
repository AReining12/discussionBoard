<html>
    <head>
    </head>
    <body>
    <!--Form to add or remove channels-->
    <form name="input" action="messageController.php" method="post" autocomplete="on">
            <!--Hidden field to determine whether form is register or login-->
            <input type="hidden" name="action" value="add_message">
            <!-- Aligned text boxes-->
            <fieldset>
                <b id="message">Message Content</b> <input type="text" name="message"> <br />
                <b id="channel">Channel</b> <input type="text" name="channel"> <br />
                <!--Get board id from $_POST variable boardname-->

            </fieldset>

            <!--Submit section-->
            <input type="submit" name="add_message" value="Post message">
        <?php
        // Anna Reining, 260885420
        // User login form
        ?>

    </body>
</html>