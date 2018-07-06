<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">

  <title>Log-in</title>


  <link rel="stylesheet" href="css/login.css" media="screen" type="text/css" />
</head>

<body>

   <div class="login-card">
    <h1>Log-in</h1><br>
    
    <div class="login-error">
       <p id="errorMessage">
           <?php
           
                require('db_cred.php');
           
                if(isset($_POST['submitButton']))
                {
                    $connection = new MySQLi($host, $db_user, $db_pass, $db_name);

                    if($connection->connect_error)
                        die($connection->connect_error);

                    $user = sanitizeMySQL($connection, $_POST['user']);
                    $password = sanitizeMySQL($connection, $_POST['password']);

                    $query = "SELECT * FROM users WHERE username='$user';";
                    $result = $connection->query($query);

                    if(!$result)
                    {
                        die($connection->connect_error);
                    }
                    elseif($result->num_rows)
                    {
                        $row = $result->fetch_array(MYSQLI_NUM);
                        $result->close();
                        
                        session_start();
                        $_SESSION['username'] = $user;
                        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
                        $_SESSION['ua'] = $_SERVER['HTTP_USER_AGENT'];
                        

                        if(password_verify($password, $row[2]))
                        {
                            header('Location: main.html');
                        }
                        else
                        {
                            echo("Invalid username/password combination");
                        }
                    }
                    else
                    {
                        echo("Invalid username/password combination");
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
    
    <form name="loginForm" method="post" action="login.php">
        <input type="text" name="user" placeholder="Username" onclick="document.getElementById('errorMessage').innerHTML='';" required maxlength="30"> 
        <input type="password" name="password" placeholder="Password" required maxlength="20">
        <input type="submit" name="submitButton" class="login login-submit" value="login">
    </form>
    
    <div align="right">
       
        <a href="register.php"><u><font color="#000AD1" size="2em">Register here</font></u></a>
          
    </div>
       
   </div>

<!-- <div id="error"><img src="https://dl.dropboxusercontent.com/u/23299152/Delete-icon.png" /> Your caps-lock is on.</div> -->



</body>

</html>