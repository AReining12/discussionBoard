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
            $username = $_POST['username'];
            $password = $_POST['password'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $email = $_POST['email'];

            // student is set to be student unless 'stuff_type' is defined
            if (isset($_POST['stuff_type'])) {
                // staff
                $groupid = $_POST['stuff_type'];
            } else {
                // student
                $groupid = 3;
            }

            // instance of UserController
            $userController = new userController();

            // Call registerUser()
            
            $userController->registerUser($username, $password, $firstname, $lastname, $email, $groupid);
        }
        ?>

    </body>
</html>