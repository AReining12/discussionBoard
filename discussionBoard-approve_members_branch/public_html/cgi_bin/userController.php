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
    include('session_start.php');
    require_once('../cgi_bin/userModel.php');
    
    class userController {
        public function registerUser($username, $password, $firstname, $lastname, $email, $groupid){
            // Call the addUser method of the UserModel
            //echo "$groupid";
            //echo "$username";
            
            $userModel = new UserModel();
            $status = $userModel->addUser($username, $password, $firstname, $lastname, $email, $groupid);
            // user model returns 1 or 2 if $username or email are repeated
            // otherwise adds user to database and returns 0
            if ($status !== 0) {
                header("Location: ../landingpage.html");
            } else {
                header("Location: ../landingpage.html");
            }
            exit();

        }

        public function loginUser($username, $password){
            $userModel = new UserModel();
            echo "$username";
            echo "$password";
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
            header('Location: ../landingpage.html');
            exit;
        }
    }

?>
