<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
   
   <form action="test.php" method="post" enctype="multipart/form-data">
            <input type="file" name="files[]" multiple="multiple"><br><br>
            <input type="submit" value="Upload" name="upload"> &nbsp; &nbsp;
            <input type="reset">
    </form>
       
        
    <?php
        require('db_cred.php');
    
    
        $dir_path = array($main_storage. $user);
        print_r($dir_path);
    
        $connection = new MySQLi("localhost", "root", "", "file_storage");
    

        if(isset($_POST['upload']))
        {
                        // Count total files
            $countfiles = count($_FILES['files']['name']);

             // Looping all files
            for($i=0;$i<$countfiles;$i++)
            {
                $filename = $_FILES['files']['name'][$i];

               // Upload file
                $msg = move_uploaded_file($_FILES['files']['tmp_name'][$i], $main_storage. "admin\\". $filename);
                
                if($msg == 1)
                {
                    echo("<p>". $filename. " uploaded successfully". "</p>");
                }
                else
                {
                    echo("<p>". $filename. " not uploaded". "</p>");
                }
            }
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
</body>
</html>