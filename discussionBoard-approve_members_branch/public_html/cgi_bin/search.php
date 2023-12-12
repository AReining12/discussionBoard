<html>
    <head>
    </head>
    <body>
    <!--Form to add or remove channels-->
    <form name="input" action="messageController.php" method="post" autocomplete="on">
            <!--Hidden field to determine whether form is register or login-->
            <input type="hidden" name="action" value="search_messages">
            <!-- Aligned text boxes-->
            <fieldset>
                <b id="search">Search</b> <input type="text" name="search"> <br />
                <b id="channel">Channel</b> <input type="text" name="channel"> <br />
                <!--Get board id from $_POST variable boardname-->

            </fieldset>

            <!--Submit section-->
            <input type="submit" name="search_messages" value="Search">
        <?php
        // Anna Reining, 260885420
        // User login form
        ?>

    </body>
</html>