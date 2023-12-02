<html>
    <head>
    </head>
    <body>
            <!--Form to add or remove members-->
            <form name="input" action="boardController.php" method="post" autocomplete="on">
            <!--Hidden field to determine whether form is register or login-->
            <input type="hidden" name="action" value="manage_members">
            <!-- Aligned text boxes-->
            <fieldset>
                <b id="name">Who would you like to add or remove from this discussion board?</b> <input type="text" name="name"> <br />
                <!--Get board id from $_POST variable boardname-->

            </fieldset>
            <b>Add or Remove?</b>
            <!--Radio Buttons-->
            <input type="radio" name="add_or_remove" value="addMember">Add
            <input type="radio" name="add_or_remove" value="removeMember">Remove

            <!--Submit section-->
            <input type="submit" name="manage_members" value="Manage members">
        <?php
        // Anna Reining, 260885420
        // User login form
        ?>

    </body>
</html>
