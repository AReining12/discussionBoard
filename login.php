<html>
    <body>
        <?php
        // Database connection details
        $host = 'localhost'; // or your database server address
        $dbname = 'messageBoard';
        $username = 'root'; // your database username
        $password = ''; // your database password

        // Establish a database connection using PDO
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            // Set the PDO error mode to exception
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }

        // Query the database for user data
        $query = $pdo->query('SELECT * FROM users');
        $users = $query->fetchAll(PDO::FETCH_ASSOC);

        // Display the data
        foreach ($users as $user) {
            if ($_POST['username'] == $user['username'] && $_POST['password'] == $user['password']) {
                
                
                if(isset($_POST['login'])) {
                    
                    header("Location: selectDiscussionBoard.html");
                    exit();
                }

            }
            // possibly some sort of message about the password not being recognized here
            header("Location: login.html");
            exit();

        }
        ?>

    </body>
</html>