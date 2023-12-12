

<?php
error_reporting(E_ALL);
include('session_start.php');
include('userModel.php');
include('boardModel.php');
include('boardController.php');
?>
<!-- Name: Junji Duan   e-mail: junji.duan@mail.mcgill.ca -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>McChat Discussion System</title>

    <link href="../cgi_bin/bootstrap/bootstrap.min.css" rel="stylesheet">
    
    <style>
        
        /* Sidebar styling */
        .sidebar {
            flex: 0 0 200px; 
            min-height: 100vh; 
            background-color: #f8f9fa; 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
            box-shadow: 2px 0px 5px rgba(0,0,0,0.1);
        }
        
        /* Style for each link in the sidebar */
        .sidebar-link {
            cursor: pointer;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        /* Hover effect for sidebar links */
        .sidebar-link:hover {
            background-color: #f5f5f5;
        }
        
        /* Sidebar header styling */
        .sidebar-header {
            display: flex;
            align-items: center;
            padding: 10px 0;
            justify-content: center; 
        }
        
        /* Logo within the sidebar */
        .sidebar-header .logo {
            /* flex: 1; */
            height: 35px;
            width: 150px;
        }
        
        /* Main content area styling */
        .content-area {
            padding: 20px; 
            min-height: 100vh; 
        }

        /* Ensure the main container is a flex container */
        .container-fluid {
            display: flex;
            min-height: 100vh; /* Full viewport height */
        }

         /* Adjust row to work inside a flex container */
        .row {
            display: flex;
            flex-grow: 1; /* Allow row to grow and fill space */
        }

        .welcome {
            min-height: 95vh; /* Full viewport height */
            background: 
                linear-gradient(
                to top,
                rgba(200, 200, 255, 0),
                rgba(0, 0, 100, 0.4)
                ),
                url('../asserts/images/welcome.jpg');
            background-size: cover; 
            position: relative; /* 使绝对定位的子元素（如 .logout）相对于此元素定位 */
        }

        .welcome-text{
            margin-top: 1rem;
            font-family: Tahoma, Arial, sans-serif;
            font-weight: bold; 
            text-align: center; 
            color: white; 
            position: absolute;
            display: flex;       /* 启用 flexbox */
            justify-content: space-between; /* 在项目之间平均分配可用空间 */
            align-items: center; /* 垂直居中对齐 */
            top: 10%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .logout {
            position: absolute;
            top: 0;
            right: 0;
            margin: 10px; /* 根据需要调整边距 */
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            background-color: white; /* 可选：添加背景色以增加可见度 */
            z-index: 100; /* 确保按钮位于其他元素之上 */
        }

        
        /* Course card styling */
        .course-card {
            border: 1px solid #ddd;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .search {
            position: absolute;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .create {
            position: absolute;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        /* Search container styling */
        .search-container {
            text-align: center;
            padding: 20px;
        }

        /* Search header with flexbox for alignment */
        .search-header {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .search-header h2 {
            margin-right: 10px;
        }

        /* Search box styling */
        .search-box {
            width: 50%;
            padding: 10px;
            margin: 10px auto; 
        }

        /* Search button styling */
        .search-button {
            margin-top: 10px;
        }

    </style>

</head>

<body>
    <!-- Sidebar with only the 'Create a Course' tab -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <div class="sidebar-header">
                    <!-- Click mcgill logo to show welcome page and log out-->
                    <button id="logoButton" style="background:none;border:none;padding:0;">
                        <div class="logo">
                            <img src="../asserts/images/logo.png" alt="Logo" class="img-fluid">
                        </div>
                    </button>
                </div>
                <!-- Only the 'Create a Course' tab is visible -->
                
            </div>

            <!-- Right Content Area -->
            <div class="col-md-10">
                <div class="content-area" id="contentArea">
                    <!-- Content for the 'Create a Course' section -->
                    <div class="create" id="createCourseSection">
                        <div class="create-course-container" style="text-align: center;">
                            <!-- Course Creation Form -->
                           <!-- Form to add or remove channels -->
                           <a class="btn btn-primary" href="create_channel.php">Create Channel</a><br>
                           <a href="remove_channel.php">Delete Channel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts for functionality -->
    <script src="../cgi_bin/bootstrap/jquery.min.js"></script>
    <script src="../cgi_bin/bootstrap/popper.min.js"></script>
    <script src="../cgi_bin/bootstrap/bootstrap.min.js"></script>
    <script src="../cgi_bin/connection.js"></script>

    <script>
        let connection = null;

        async function init() {
            try {
                connection = await Connection.connect();
            } catch (error) {
                if (error instanceof TypeError) {
                    throw error;
                }
                window.location.replace("../landingpage.html");
            }
        }

        init();
        
    </script>
</body>
</html>


