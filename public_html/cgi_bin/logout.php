<?php
//Mingchen Ju 260864282
// Start the session
session_start();

// Destroy all session variables
session_destroy();

// Redirect to the landing page
header("Location: ../landingpage.html");
exit();
?>
