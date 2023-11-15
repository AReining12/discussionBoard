<html>
    <body>
        <?php
        // Anabel Reining, 260885420
        // Database connection details
        $host = 'localhost'; // or your database server address
        $dbname = 'messageBoard';
        $username = 'root'; // your database username
        $password = ''; // your database password

        // Create connection
        $conn = new mysqli($host, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        }

        /*
        $addusername = mysql_real_escape_string($_POST['username']);
        $addpassword = mysql_real_escape_string($_POST['password']);
        $addemail = mysql_real_escape_string($_POST['email']);
        $addmembertype = mysql_real_escape_string($_POST['membertype']);
        */
        /*TODO: fix SQL vulnerability using mysql_real_escape_string*/

        $sql = "INSERT INTO `users` (`id`, `username`, `email`, `password`, `memberType`) VALUES (NULL, '{$_POST['username']}', '{$_POST['email']}', '{$_POST['password']}', '{$_POST['membertype']}')";

        if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
        } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
        ?>

    </body>
</html>