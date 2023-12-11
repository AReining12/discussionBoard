<?php
// Anna Reining, 260885420
// Establishes a connection to the mySQL database
// Included in files that requre a database connection

// localhost
// $host = 'localhost';
// $dbname = 'comp307';
// $dbusername = 'root'; 
// $dbpassword = ''; 

$host = 'mysql.cs.mcgill.ca';
$dbname = '2023fall-comp307-mlavre1';
$dbusername = 'mlavre1'; 
$dbpassword = '4zWW$f#5XR@FFdJgwX6h3ihd'; 

// Create connection
$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>