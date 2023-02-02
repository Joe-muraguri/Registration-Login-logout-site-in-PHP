<?php

//initialize session
session_start();

//unset all session variables
$_SESSION = array();

//destroy the session
session_destroy();

//redirect to login page
header('Location: login.php')

?>