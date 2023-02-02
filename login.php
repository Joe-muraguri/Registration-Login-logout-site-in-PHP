<?php
//initialize session
session_start();

//check if user is loggedin..if so redirect them to welcome page.
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
    header('Location: welcome.php');
    exit();
}

// include config file
require_once("config.php");

//define variables and initialize them with empties
$username = $password = '';
$username_err = $password_err = $login_err = '';

//processing form data when data is submitted

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    //check if username is empty
    if(empty(trim($_POST['username']))){
        $username_err = "Please enter a username";
    } else{
        $username = trim($_POST['username']);
    }

    //check if password is empty
    if(empty(trim($_POST['password']))){
        $password_err = "Please enter your.";
    } else{
        $password = trim($_POST['password']);
    }


    //validate credentials
    if(empty($username_err) && empty($password_err)){
        //prepare a select satement
        $sql = " SELECT id, username, password FROM users WHERE username = ? ";

        if($stmt = mysqli_prepare($conn, $sql)){
            //Bind variables to the prepared statement using parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            $param_username = $username;

            //attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                //store result
                mysqli_stmt_store_result($stmt);

                //check if username exist, if yes verify password
                if(mysqli_stmt_num_rows($stmt) == 1){
                    //Bind result variable
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);

                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // password is correct, so start a new session
                            session_start();

                            //store data in session variables
                            $_SESSION['loggedin'] = true;
                            $_SESSION['id'] = $id;
                            $_SESSION['username'] = $username;

                            //redirect user to login page
                            header('Location: welcome.php');
                        } else{
                            // password not valid,,generate a generic error message
                            $login_err = "Incorrect username or password.";
                        }
                    }
                } else{
                    // username does not exist ,, display a generic error
                    $login_err = "Incorrect username or password.";
                }
            } else{
                echo "Something went wrong. Please try again later.";
            }

            //close statement
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
        <h2>Login</h2>
        <p>Please fill this form to login into your account</p>


        <?php

            if(!empty($login_err)){
                echo '<div class="alert alert-danger">' .$login_err . '</div>';
            }

        ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" name="username" value="<?php echo $username;?>">
                <span class="invalid-feedback"> <?php echo $username_err;?></span>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password)) ? 'is-invalid' : ''; ?>" value="<?php echo $password;?>">
                <span class="invalid-feedback"> <?php echo $password_err;?></span>
            </div>
            
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p>Don't have an account? <a href="register.php">Sign up here</a></p>

        </form>
    </div>
    
</body>
</html>