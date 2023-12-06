<html>
    <head>
    </head>
    <body>
        <?php
        // Anna Reining, 260885420
        // User login listener
        include('userController.php');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $groupid = $_POST['stuff_type']; // used to determine if user is student or staff
            //echo "$groupid";

            // student is set to be student unless 'stuff_type' is defined
            if ($groupid != 3) {
                // staff
                
                // echo "$groupid";
                // get data
                $username = $_POST['register_staff_username'];
                $password = $_POST['register_staff_password'];
                $firstname = $_POST['register_staff_firstname'];
                $lastname = $_POST['register_staff_lastname'];
                $email = $_POST['register_staff_email'];


            } else {
                // student
                // get data
                $username = $_POST['register_username'];
                $password = $_POST['register_password'];
                $firstname = $_POST['register_firstname'];
                $lastname = $_POST['register_lastname'];
                $email = $_POST['register_email'];

            }

            //echo "$email";
            //echo "$username";

            // instance of UserController
            $userController = new userController();

            // Call registerUser()
            
            $userController->registerUser($username, $password, $firstname, $lastname, $email, $groupid);
        }
        ?>

    </body>
</html>