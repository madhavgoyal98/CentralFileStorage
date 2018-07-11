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

<body topmargin="10%" leftmargin="15%">

    <?php

        require('db_cred.php');

        $connection = new MySQLi($host, $db_user, $db_pass, $db_name);
        $user = $_SESSION['username'];
        $msg1 = "";

        if(!isset($_SESSION['dir_path_home']))
        {
            $_SESSION['dir_path_home'] = array($main_storage. $user);
        }

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

        function showFiles($d)
        {
            $dir = "";

            foreach($d as $i)
            {
                $dir = $dir. $i. "\\";
            }

            $allFiles = scandir($dir);
            $files = array_diff($allFiles, array('.', '..')); // To remove . and ..

            foreach($files as $file)
            {
                echo("<a style='text-decoration: none;' href='download.php?file=". $file. "'>". $file. "</a><br><br>");
            }
        }

        function getDirectoryList($dir_array)
        {
            $dir = $dir_array[0];

            if(count($dir_array) > 1)
            {
                for($i = 1; $i<count($dir_array); $i=$i+1)
                {
                    $dir = $dir. "\\". $dir_array[$i];
                }
            }

            $d = new DirectoryIterator($dir);
            foreach ($d as $fileinfo)
            {
                if ($fileinfo->isDir() && !$fileinfo->isDot())
                {
                    echo("<option value='". $fileinfo->getFilename(). "'>". $fileinfo->getFilename(). "</option>");
                }
            }
        }

    ?>

    <div>

        <font style="color: #E70003; font-size: 1em;">
            Current Directory: root >
        </font>

        <span id="dir-path" style="color: #E70003; font-size: 1em;">
            <?php
                for($i = 1; $i<count($_SESSION['dir_path_home']); $i++)
                {
                    $msg1 = $msg1. $_SESSION['dir_path_home'][$i]. " >";
                }

                echo($msg1);
            ?>
        </span>

        <br><br>

        <form name="change-directory" action="home.php" method="post">
            <select size="1" name="dirSelect" id="dirSelect">
                <?php

                    if(!isset($_SESSION['homeVisited']))
                    {
                        //first visit
                        $_SESSION['homeVisited'] = true;

                        getDirectoryList($_SESSION['dir_path_home']);
                    }

                    if(isset($_POST['changeSubmit']))
                    {
                        array_push($_SESSION['dir_path_home'], $_POST['dirSelect']);
                        getDirectoryList($_SESSION['dir_path_home']);
                    }
                ?>
            </select>
            &nbsp;
            <input type="submit" value="Change" name="changeSubmit">
            <br><br>
        </form>
        <?php

            print_r($_SESSION['dir_path_home']);

            echo("<br><br>");

            showFiles($_SESSION['dir_path_home']);
        ?>
    </div>

</body>

</html>