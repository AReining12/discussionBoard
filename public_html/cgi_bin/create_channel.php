<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
    <?php
        // Anna Reining, 260885420
        // User login form
        ?>
    <!--Form to add or remove channels-->
        <form name="createChannelsForm" action="boardController.php" method="post" autocomplete="on">
            <!--Hidden field to determine whether form is register or login-->
            <input type="hidden" name="action" value="create_channels">
            <!-- Aligned text boxes-->
            <fieldset>
                <b id="name">New Channel Name</b> <input type="text" name="name"> <br />

            </fieldset>

            <!--Submit section-->
            <input type="submit" name="create_channels" value="Create">
        </form>

    </body>
</html>
