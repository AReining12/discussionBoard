<html>
    <head>
    </head>
    <body>
        <?php
        // Anna Reining, 260885420
        // Handles user actions and interactions with userModel
        // contains methods:
        /*
            registerUser($username, $password, $firstname, $lastname, $email, $groupid):
            - processes user registration: serves as interface between user 
            interface and UserModel
            - redirects user to landing page when registration is complete

            loginUser($username, $password):
            - processes user login

            logoutUser():
            - logs out user and destroys the session
        */
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
            include('../cgi_bin/session_start.php');
            require_once('../cgi_bin/userModel.php');
            
            class userController {
                public function registerUser($username, $password, $firstname, $lastname, $email, $groupid){
                    // Call the addUser method of the UserModel
                    $userModel = new UserModel();
                    $result = $userModel->addUser($username, $password, $firstname, $lastname, $email, $groupid);
                    // user model returns 1 or 2 if $username or email are repeated

                    if ($result == 0){
                        // otherwise adds user to database and returns 0
                        header("Location: ../landingpage.html");
                        exit();
                    } else {
                        // header ("Location: ../landingpage.html");
                        echo "fail";
                    }
                    

                }

                public function loginUser($username, $password){
                    $userModel = new UserModel();
                    $result = $userModel->verifyUser($username, $password);
                    // will return error if user does not exist in system
                    if ($result) { 
                        // user exists in system
                        
                        $_SESSION['loggedin'] = true;
                        header("Location: ../pages/SelectBoard.html");
                        exit();
                    } else {
                        // if user does not exist in system
                        header("Location: ../landingpage.html");
                        exit();
                    }
                    

                }

                public function logoutUser(){
                    session_start();
                    session_destroy();
                    header('Location: login.php');
                    exit;
                }
            }

            // if the form is submitted
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                //echo "here";
                var_dump($_POST); 

                // check if field is set to determine login vs register switch
                if (isset($_POST['action'])){
                    echo "here1";
                    $action = $_POST['action'];

                    switch($action) {
                        case 'register':
                            // get data
                            echo "here2";
                            $username = $_POST['register_username'];
                            $password = $_POST['register_password'];
                            $firstname = $_POST['register_firstname'];
                            $lastname = $_POST['register_lastname'];
                            $email = $_POST['register_email'];
                            //$groupid = $_POST['membertype'];

                            // instance of UserController
                            $userController = new UserController();

                            // Call registerUser()
                            // 
                            $userController->registerUser($username, $password, $firstname, $lastname, $email, $groupid);
                            break;
                            
                        case 'login':
                            //echo "username";
                            // get data
                            $username = $_POST['login_username'];
                            $password = $_POST['login_password'];
                            //echo "$username";
                            //echo "password";
                            //echo "$password";
                            echo "here";

                            // instance of UserController
                            $userController = new UserController();

                            // call loginUser()
                            //$userController->loginUser($username, $password);

                        }
                }
                
            }
        ?>

    </body>
</html>