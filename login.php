<html>
    <head>
    </head>
    <body>
    <form name="input" action="userController.php" method="post" autocomplete="on">
            <!--Hidden field to determine whether form is register or login-->
            <input type="hidden" name="action" value="login">
            <!-- Aligned text boxes-->
            <fieldset>
                <b id="username">Username:</b> <input type="text" name="username" required> <br />
                <b id="password">Password:</b> <input type="text" name="password" required> <br />

            </fieldset>

            <!--Submit section-->
            <input type="submit" name="login" value="Login">
        <?php
        // Anna Reining, 260885420
        // User login form
        ?>

    </body>
</html>
