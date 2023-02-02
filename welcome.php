<?php
//start a session
session_start();

//chech if user is logged in...if not redirect them to the login page
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header('Location: login.php');
    exit();
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2 class="my-5"> Hi, <b> <?php echo htmlspecialchars($_SESSION['username']);?></b>. Welcome to Joes Limited</h2>

    <p>
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger ml-3">Logout</a>
    </p>
    
</body>
</html>