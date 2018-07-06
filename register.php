<!doctype html>
<html>

<head>

    <meta charset="utf-8">

    <title>Register</title>

    <link rel="stylesheet" href="css/register.css" media="screen" type="text/css" />

</head>

<body>

   <div class="login-card">
    <h1>Registration Form</h1><br>
    
    <div class="login-error">
       <p id="errorMessage">
           <?php
           
                require('db_cred.php');
                
                if(isset($_POST['submitButton']))
                {
                    $connection = new MySQLi($host, $db_user, $db_pass, $db_name);
                    
                    if($connection->connect_error)
                        die($connection->connect_error);
                    
                    $name = sanitizeMySQL($connection, $_POST['name']);
                    $user = sanitizeMySQL($connection, $_POST['user']);
                    $pass = sanitizeMySQL($connection, $_POST['password']);
                    $cpass = sanitizeMySQL($connection, $_POST['cpassword']);
                    
                    if($pass == $cpass)
                    {
                        $pass = password_hash($pass, PASSWORD_DEFAULT);
                        
                        $query = "SELECT * FROM users WHERE username='$user';";
                        $result = $connection->query($query);
                        
                        if(!$result)
                        {
                            die($connection->connect_error);
                        }
                        elseif($result->num_rows)
                        {
                            //$result->close();
                            echo("Username already taken");
                        }
                        else
                        {
                            $query = "INSERT INTO users VALUES('$name', '$user', '$pass', DEFAULT, DEFAULT);";
                            $result = $connection->query($query);
                            
                            mkdir($main_storage. $user, null, true);

                            if(!$result)
                            {
                                die($connection->connect_error);
                            }
                            else
                            {
                                header('Location: register_success.html');
                            }
                        }
                    }
                    else
                    {
                        echo("Passwords do not match");
                    }
                    
                    $connection->close();
                }
           
                function sanitizeString($var)
                {
                    $var = stripslashes($var);
                    $var = htmlentities($var);
                    $var = strip_tags($var);

                    return $var;
                }

                function sanitizeMySQL($connection, $var)
                {
                    $var = $connection->real_escape_string($var);
                    $var = sanitizeString($var);

                    return $var;
                }
           ?>
       </p>
        
    </div>
    
    <form name="registrationForm" method="post" action="register.php">
       <input type="text" name="name" placeholder="Name" onclick="document.getElementById('errorMessage').innerHTML='';" required maxlength="30">
        <input type="text" name="user" placeholder="Username" maxlength="30" required>
        <input type="password" name="password" placeholder="Password" maxlength="20" required>
        <input type="password" name="cpassword" placeholder="Confirm Password" maxlength="20" required>
        <input type="submit" name="submitButton" class="login login-submit" value="submit">
    </form>

       
   </div>


</body>

</html>