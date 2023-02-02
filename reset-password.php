<?php

//initialize the session
session_start();

//check if user is logged in...otherwise redirect to login page
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header('Location: login.php');
    exit();
}
//include config file
require_once("config.php");

//define and initialize variables
$new_password = $confirm_password = '';
$new_password_err = $confirm_password_err = '';

//process form data when data is submitted
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    //validate new password
    if(empty(trim($_POST['new_password']))){
        $new_password_err = "Please enter a password.";
    } elseif(strlen(trim($_POST['new_password'])) < 6){
        $new_password_err = "Password must have atleast 6 characters.";
    } else{
        $new_password = trim($_POST['new_password']);
    }

    //validate confirm password
    if(empty(trim($_POST['confirm_password']))){
        $new_password_err = "Please enter confirm password.";
    } else{
        $confirm_password = trim($_POST['confirm_password']);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    //check errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){

        //prepare an update statement
        $sql = " UPDATE users SET password = ? WHERE id = ? ";

        if($stmt = mysqli_prepare($conn, $sql)){
            //Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);


            //set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION['id'];



            //Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                //password updated successfully...Destroy session and redirect to login page
                session_destroy();
                header('Location: login.php');
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);

        }
    }
    mysqli_close($conn);

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
<div class="wrapper">
        <h2>Reset Password</h2>
        <p>Please fill this form to reset your password</p>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
           
            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password;?>">
                <span class="invalid-feedback"> <?php echo $new_password_err;?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password;?>">
                <span class="invalid-feedback"> <?php echo $confirm_password_err;?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
                <a href="welcome.php" class="btn btn-link ml-2">Cancel</a>
            </div>
            <p>Already have an account? <a href="login.php">Login</a></p>

        </form>
    </div>
    
</body>
</html>