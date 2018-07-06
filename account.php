<!doctype html>

<?php
    session_start();
    unset($_SESSION['createVisited']);
    unset($_SESSION['uploadVisited']);

    if(isset($_SESSION['dir_path_create']))
    {
        unset($_SESSION['dir_path_create']);
    }

    if(isset($_SESSION['dir_path_upload']))
    {
        unset($_SESSION['dir_path_upload']);
    }
?>


<html>

<head>

    <meta charset="utf-8">

    <title>Account Settings</title>

</head>

<body>
   
   <?php
    
        require('db_cred.php');    
    
        $connection = new MySQLi($host, $db_user, $db_pass, $db_name);
        $user = $_SESSION['username'];
        $msg1 = "";
        $msg2 = "";
    
        if($connection->connect_error)
            die($connection->connect_error);
    
        $query = "SELECT COUNT(*) FROM users WHERE username='$user';";
        $result = $connection->query($query);
    
        if(!$result)
        {
            die($connection->connect_error);
        }
        else
        {
            $row = $result->fetch_array(MYSQLI_NUM);
            $result->close();
            
            if($row[0] == 0)
            {
                echo("<h3>Invalid Login</h3>");
                echo("Please <a href='login.php' target='_top' onClick='self.close()'>login</a> again");
            }
            else
            {
                if($_SESSION['ip'] != $_SERVER['REMOTE_ADDR'] && $_SESSION['ua'] != $_SERVER['HTTP_USER_AGENT'])
                {
                    echo("<h3>Invalid Login</h3>");
                    echo("Please <a href='login.php' target='_top' onClick='self.close()'>login</a> again");
                }
                else
                {
                    if(isset($_POST['change_password']))
                    {
                        $old_pass = sanitizeMySQL($connection, $_POST['old_password']);
                        $new_pass = sanitizeMySQL($connection, $_POST['new_password']);
                        $confirm_pass = sanitizeMySQL($connection, $_POST['re_password']);
                        
                        $query = "SELECT * FROM users WHERE username='$user';";
                        $result = $connection->query($query);
                        
                        if(!$result)
                        {
                            die($connection->connect_error);
                        }
                        else
                        {
                            $row = $result->fetch_array(MYSQLI_NUM);
                            $result->close();
                            
                            if(password_verify($old_pass, $row[2]))
                            {
                                if($new_pass == $confirm_pass)
                                {
                                    $new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                                    
                                    $query = "UPDATE users SET password='$new_pass' WHERE username='$user';";
                                    $result = $connection->query($query);
                                    
                                    if(!$result)
                                    {
                                        die($connection->connect_error);
                                    }
                                    else
                                    {
                                        $msg1 = "Password updated successfully";
                                    }
                                }
                                else
                                {
                                    $msg1 = "Passwords do not match";
                                }
                            }
                            else
                            {
                                $msg1 = "Invalid password entered";
                            }
                        }
                    }
                    elseif(isset($_POST['delete_account']))
                    {
                        $dpass = sanitizeMySQL($connection, $_POST['password']);
                        
                        $query = "SELECT * FROM users WHERE username='$user';";
                        $result = $connection->query($query);
                        
                        if(!$result)
                        {
                            die($connection->connect_error);
                        }
                        else
                        {
                            $row = $result->fetch_array(MYSQLI_NUM);
                            $result->close();
                            
                            if(password_verify($dpass, $row[2]))
                            {
                                $query = "DELETE FROM users WHERE username='$user';";
                                $result = $connection->query($query);
                                
                                if(!$result)
                                {
                                    die($connection->connect_error);
                                }
                                else
                                {
                                    deleteAll($main_storage. $user);
                                
                                    echo('<script type="text/javascript">window.open("login.php"); window.close(); </script>');
                                }
                            }
                            else
                            {
                                $msg2 = "Invalid password entered";
                            }
                        }
                    }
                }
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
    
        function deleteAll($str)
        {
            //It it's a file.
            if (is_file($str)) {
                //Attempt to delete it.
                return unlink($str);
            }
            //If it's a directory.
            elseif (is_dir($str)) {
                //Get a list of the files in this directory.
                $scan = glob(rtrim($str,'/').'/*');
                //Loop through the list of files.
                foreach($scan as $index=>$path) {
                    //Call our recursive function.
                    deleteAll($path);
                }
                //Remove the directory itself.
                return @rmdir($str);
            }
        }

    ?>
    
    <div>
        <h2>
            Change Password
        </h2>
        
        <form name="changePassword" action="account.php" method="post">
            Old Password &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<input type="password" name="old_password" placeholder="Old Password" maxlength="20" onClick="document.getElementById('error1').innerHTML='';" required><br><br>
            New Password &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<input type="password" name="new_password" placeholder="New Password" maxlength="20" required><br><br>
            Confirm Password &nbsp; &nbsp;<input type="password" name="re_password" placeholder="Confirm Password" maxlength="20" required><br><br>
            &nbsp;<input type="submit" name="change_password" value="Submit">
            &nbsp; &nbsp;<input type="reset" value="Reset" onClick="document.getElementById('error1').innerHTML='';">&nbsp;
          <span id="error1">
                <font style="color: #E70003; font-size: 0.9em;">
                    <?php
                        echo($msg1);  
                    ?>
                </font>
          </span>
        </form>
        
        <br>
        
        <hr>
    </div>
    
    <div>
        <font size="+2"><strong>Delete Account</strong></font> (All files will also be deleted) <br><br>
        
        <form name="deleteAccount" action="account.php" method="post">
            Enter Password &nbsp; &nbsp; <input type="password" name="password" placeholder="Password" maxlength="20" onClick="document.getElementById('error2').innerHTML='';" required><br><br>
            &nbsp;<input type="submit" name="delete_account" value="Submit">&nbsp;
            <span id="error2">
                <font style="color: #E70003; font-size: 0.9em;">
                    <?php
                        echo($msg2);  
                    ?>
                </font>
            </span>
        </form>
    </div>

</body>

</html>