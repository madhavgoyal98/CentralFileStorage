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

<title>Home</title>

</head>

<body>

    <?php

        require('db_cred.php');

        $connection = new MySQLi($host, $db_user, $db_pass, $db_name);
        $user = $_SESSION['username'];

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

        function showFiles($dir)
        {
            $allFiles = scandir($dir);
            $files = array_diff($allFiles, array('.', '..')); // To remove . and ..

            foreach($files as $file)
            {
                echo("<a style='text-decoration: none;' href='download.php?file=". $file. "'>". $file. "</a><br>");
            }
        }

    ?>

</body>

</html>