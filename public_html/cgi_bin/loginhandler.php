<html>
    <head>
    </head>
    <body>
        <?php
        // Anna Reining, 260885420
        // User login listener
        include('userController.php');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // get data
            $username = $_POST['login_username'];
            $password = $_POST['login_password'];
            echo "$username";
            //echo "password";
            echo "$password";

            // instance of UserController
            $userController = new userController();

            // call loginUser()
            $userController->loginUser($username, $password);
        }
        ?>

    </body>
</html>