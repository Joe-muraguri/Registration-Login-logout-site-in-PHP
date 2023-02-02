<?php
//include config file
require_once("config.php");

//define variables and initialize with empty values
$username = $password = $confirm_password = '';
$username_err = $password_err = $confirm_password_err = '';

//processing form data when form is submitted
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    //validate username
    if(empty(trim($_POST['username']))){
        $username_err = 'Please a username';
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST['username']))){
        $username_err = "Username can only contain letters,numbers and underscores.";
    } else{
        //prepare select statement
        $sql = "SELECT id FROM users WHERE username = ? ";

        if($stmt = mysqli_prepare($conn, $sql)){
            //Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            //set parameters
            $param_username = trim($_POST['username']);

            //attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /*store result*/
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST['username']);
                }
            } else{
                echo "Something went wrong. Please try again later.";
            }

            //close statement
            mysqli_stmt_close($stmt);
        }
    }

    //validate password
    if(empty(trim($_POST['password']))){
        $password_err = "Please enter a password.";
    } elseif(strlen(trim($_POST['password'])) < 6){
        $password_err = "Password must be atleast 6 characters long";
    } else{
        $password = trim($_POST['password']);
    }


    //validate confirm password
    if(empty(trim($_POST['confirm_password']))){
        $confirm_password_err = "Please enter confirm password";
    } else{
        $confirm_password = trim($_POST['confirm_password']);
        if(empty($password_err) && ($password!=$confirm_password)){
            $confirm_password_err = "Password did not match";
        }
    }


    //check errors before inserting into the database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

        // prepare an insert statement
        $sql = " INSERT INTO users (username, password) VALUES(?, ?)";

        if($stmt = mysqli_prepare($conn, $sql)){
            //Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

            //set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);


            //attempt to execute prepared statement
            if(mysqli_stmt_execute($stmt)){
                header('Location: login.php');
            } else{
                echo "Something went wrong. Please try again later";
            }

            // close statement
            mysqli_stmt_close($stmt);
        }
    }

    //close the connection
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
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account</p>
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
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password;?>">
                <span class="invalid-feedback"> <?php echo $confirm_password_err;?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login</a></p>

        </form>
    </div>
    
</body>
</html>