<html>
    <head>
    </head>
    <body>
        <form name="input" action="userController.php" method="post" autocomplete="on">
            <!--Hidden field to determine whether form is register or login-->
            <input type="hidden" name="action" value="register">
            <!-- Aligned text boxes-->
            <fieldset>
                <b id="username">Username:</b> <input type="text" name="username" required> <br />
                <b id="password">Password:</b> <input type="text" name="password" required> <br />
                <b id="firstname">First Name:</b> <input type="text" name="firstname" required> <br />
                <b id="lastname">Last name:</b> <input type="text" name="lastname" required> <br />
                <b id="email">Email:</b> <input type="text" name="email" pattern=".*@.*" required> <br />

            </fieldset>

            <b>Member type</b>
            <!--Radio Buttons-->
            <input type="radio" name="membertype" value="3">Student
            <input type="radio" name="membertype" value="1">Professor
            <input type="radio" name="membertype" value="2">TA
            <!--YOU CANNOT REGISTER AS ADMIN: MUST CHANGE THIS VALUE IN SQL-->
            <input type="radio" name="membertype" value="4">Other<br><br>

            <!--Submit section-->
            <input type="submit" name="register" value="Register">
        <?php
        // Anna Reining, 260885420
        // User registration form
        // gets $username, $password, $firstname, $lastname, $email, $groupid (Prof 1, TA  2, Student 3, Admin 4) from user
        ?>

    </body>
</html>
