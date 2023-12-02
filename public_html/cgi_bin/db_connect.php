<html>
    <head>
    </head>
    <body>
        <?php
        // Anna Reining, 260885420
        // Establishes a connection to the mySQL database
        // Included in files that requre a database connection

        $host = 'localhost';
        $dbname = 'comp307';
        $dbusername = 'root'; 
        $dbpassword = ''; 
        
        // Create connection
        $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
        // Check connection
        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        }
        ?>

    </body>
</html>
