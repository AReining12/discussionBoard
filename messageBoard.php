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
        echo "Username: {$user['username']}, Password: {$user['password']}<br>";
    }
    ?>

    </body>
</html>