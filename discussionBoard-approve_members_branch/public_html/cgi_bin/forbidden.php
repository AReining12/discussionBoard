<?php

if (!isset($_SESSION['username'])) {
    echo "<html><head><title>Forbidden</title></head><body><h1>Error 403 - Forbidden</h1><p>You do not have permission to access this page</p><a href='../landingpage.html'>Back to landing page</a></body></html>";
    exit();
}

?>