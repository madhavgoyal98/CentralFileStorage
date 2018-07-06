<!doctype html>

<?php
    session_start();
?>

<html>

<head>

    <meta charset="utf-8">

    <title>Upload files</title>

</head>

<body topmargin="10%" leftmargin="15%">
    
    <?php
    
        require('db_cred.php');
    
        $connection = new MySQLi($host, $db_user, $db_pass, $db_name);
        $user = $_SESSION['username'];
        $used_storage = "";
        $max_storage = "";
        $msg1 = "";
        $msg2 = "";
    
        if(!isset($_SESSION['dir_path_create']))
        {
            $_SESSION['dir_path_create'] = array($main_storage. $user);
        }
    
        if(!isset($_SESSION['dir_path_upload']))
        {
            $_SESSION['dir_path_upload'] = array($main_storage. $user);
        }
    
//        for($i = 1; $i<count($_SESSION['dir_path_create']); $i++)
//        {
//            $msg2 = $msg2. $_SESSION['dir_path_create'][$i]. " >";
//        }
    
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
                    $used_storage = storageUsed($main_storage. $user);
                    
                    $query = "SELECT max_storage FROM users WHERE username='$user';";
                    $result = $connection->query($query);
                    
                    if(!$result)
                    {
                        die($connection->connect_error);
                    }
                    else
                    {
                        $row = $result->fetch_array(MYSQLI_NUM);
                        $result->close();
                        
                        $max_storage = $row[0];
                    }
                    
                    
//                    if(isset($_POST['upload']))
//                    {
//                        $msg1 = uploadFiles($main_storage. $user. "\\");
//                    }
                    
                    
                    if(isset($_POST['createFolder']))
                    {
                        $dir = "";
                        
                        foreach($_SESSION['dir_path_create'] as $d)
                        {
                            $dir = $dir. $d. "\\";
                        }
                        
                        mkdir($dir. $_POST['folder_name'], null, true);
                    }
                }
                    
            }
            
//            $connection->close();
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
    
        function storageUsed($directory) 
        {
            $size = 0;
            
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) 
            {
                $size += $file->getSize();
            }
            
            return $size;
        }
    
          //without session
//        function uploadFiles($dir)
//        {
//                        // Count total files
//            $countfiles = count($_FILES['files']['name']);
//            $status = "";
//            
//            global $connection, $main_storage;
//            $user = $_SESSION['username'];
//
//             // Looping all files
//            for($i=0;$i<$countfiles;$i++)
//            {
//                $query = "SELECT max_storage, storage_used FROM users WHERE username='$user';";
//                $result = $connection->query($query);
//                
//                if(!$result)
//                {
//                    die($connection->connect_error);
//                }
//                else
//                {
//                    $row = $result->fetch_array(MYSQLI_NUM);
//                    $result->close();
//                }
//                
//                $filename = $_FILES['files']['name'][$i];
//                
//                if($_FILES['files']['size'][$i]+$row[1] > $row[0])
//                {
//                    $status = $status. "<p>". $filename. " not uploaded". "</p>";
//                    continue;
//                }
//                else
//                {
//                    // Upload file
//                    $flag = move_uploaded_file($_FILES['files']['tmp_name'][$i], $dir. $filename); 
//
//                    if($flag == 1)
//                    {
//                        $storage = storageUsed($main_storage. $user);
//                        $query = "UPDATE users SET storage_used='$storage' WHERE username='$user';";
//                        $result = $connection->query($query);
//                        
//                        if(!$result)
//                        {
//                            die($connection->connect_error);
//                        }
//                        
//                        $status = $status. "<p>". $filename. " uploaded successfully". "</p>";
//                    }
//                    else
//                    {
//                        $status = $status. "<p>". $filename. " not uploaded". "</p>";
//                    }
//                }
//            }
//            
//            return $status;
//        }
    
        //with session
        function uploadFiles($dir)
        {
                        // Count total files
            $countfiles = count($_FILES['files']['name']);
            $status = "";
            
            global $connection, $main_storage;
            $user = $_SESSION['username'];

             // Looping all files
            for($i=0;$i<$countfiles;$i++)
            {
                $query = "SELECT max_storage, storage_used FROM users WHERE username='$user';";
                $result = $connection->query($query);
                
                if(!$result)
                {
                    die($connection->connect_error);
                }
                else
                {
                    $row = $result->fetch_array(MYSQLI_NUM);
                    $result->close();
                }
                
                $filename = $_FILES['files']['name'][$i];
                
                if($_FILES['files']['size'][$i]+$row[1] > $row[0])
                {
                    $status = $status. "<p>". $filename. " not uploaded". "</p>";
                    continue;
                }
                else
                {
                    $d = "";
                    
                    for($x = 0; $x<count($_SESSION['dir_path_upload']); $x++)
                    {
                        $d = $d. $_SESSION['dir_path_upload'][$x]. "\\";
                    }
                    // Upload file
                    $flag = move_uploaded_file($_FILES['files']['tmp_name'][$i], $d. $filename); 

                    if($flag == 1)
                    {
                        $storage = storageUsed($main_storage. $user);
                        $query = "UPDATE users SET storage_used='$storage' WHERE username='$user';";
                        $result = $connection->query($query);
                        
                        if(!$result)
                        {
                            die($connection->connect_error);
                        }
                        
                        $status = $status. "<p>". $filename. " uploaded successfully". "</p>";
                    }
                    else
                    {
                        $status = $status. "<p>". $filename. " not uploaded". "</p>";
                    }
                }
            }
            
            return $status;
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
        <font size="+2"><strong>Storage used</strong></font><br><br>
         <?php echo(round($used_storage/(1024*1024))); ?>MB out of <?php echo($max_storage/(1024*1024)); ?>MB
         
        <br><br>
        <hr>
    </div>
    
    
    
    <div>
       <br>
        <font size="+2">
            <strong>Create Folder</strong>
        </font>
        
        <br><br>
        
        <font style="color: #E70003; font-size: 1em;">
            Current Directory: root > 
        </font>
        
        <span id="dir-path" style="color: #E70003; font-size: 1em;">
            <?php
                for($i = 1; $i<count($_SESSION['dir_path_create']); $i++)
                {
                    $msg2 = $msg2. $_SESSION['dir_path_create'][$i]. " >";
                }
            
                echo($msg2);
            ?>
        </span>
        
        <br><br>
        
        <form name="change-directory" action="upload.php" method="post">
            <select size="1" name="dirSelect" id="dirSelect">
                <?php
                
                    if(!isset($_SESSION['createVisited']))
                    {
                        //first visit
                        $_SESSION['createVisited'] = true;
                        
                        getDirectoryList($_SESSION['dir_path_create']);
                    }
                
                    if(isset($_POST['changeSubmit']))
                    {
                        array_push($_SESSION['dir_path_create'], $_POST['dirSelect']);
                        getDirectoryList($_SESSION['dir_path_create']);
                    }
                ?>
            </select>
            &nbsp;
            <input type="submit" value="Change" name="changeSubmit">
            <br><br>
            <input type="text" name="folder_name" placeholder="Folder name"> &nbsp;
            <input type="submit" value="Create Folder" name="createFolder">
        </form>
        <?php print_r($_SESSION['dir_path_create']); ?>
        <br><br>
        <hr>
    </div>
    
    <div>
        <br>
        <font size="+2">
            <strong>Upload Files</strong>
        </font>
        
        <br><br>
        
        <form action="upload.php" method="post" enctype="multipart/form-data">
           <select size="1" name="upload-dirSelect" id="upload-dirSelect">
                <?php
                
                    if(!isset($_SESSION['uploadVisited']))
                    {
                        //first visit
                        $_SESSION['uploadVisited'] = true;
                        
                        getDirectoryList($_SESSION['dir_path_upload']);
                    }
                
                    if(isset($_POST['upload-changeSubmit']))
                    {
                        array_push($_SESSION['dir_path_upload'], $_POST['upload-dirSelect']);
                        getDirectoryList($_SESSION['dir_path_upload']);
                    }
                ?>
            </select>
            &nbsp;
            <input type="submit" value="Change" name="upload-changeSubmit"><br><br>
            <input type="file" name="files[]" multiple><br><br>
            <input type="submit" value="Upload" name="upload" onClick="document.getElementById('error').innerHTML='';"> &nbsp; &nbsp;
            <input type="reset" onClick="document.getElementById('error').innerHTML='';">
        </form>
    
        <div id="error" style="color: #E70003; font-size: 1em;">
            <?php 
                if(isset($_POST['upload']))
                {
                    $msg1 = uploadFiles($_SESSION['dir_path_upload']);
//                    $msg1 = uploadFiles($main_storage. $user);
                }
            
                echo($msg1);
            ?>
        </div>
    </div>

</body>

</html>