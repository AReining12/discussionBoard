<?php
// Anna Reining, 260885420
// User login listener
include('userController.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ajax = file_get_contents("php://input");
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
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
    } else if ($ajax) {
        $request = json_decode($ajax);
        if ($request->action == "authenicate" && isset($_SESSION['loggedin']) && isset($_SESSION['username'])) {
            echo json_encode(['success'=>$_SESSION['loggedin'], 'username'=>$_SESSION['username']]);
        } else {
            echo json_encode(['success'=>false]);
        }
        exit();
    } else {
        echo "Error: No action specified";
        exit();
    }
}
?>