<!DOCTYPE html>

<?php

    session_start();

?>

<html>

<head>

    <meta charset="utf-8" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Download</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>

<body>

    <?php

        require_once('db_cred.php');

        $user = $_SESSION['username'];

        $filename = basename($_GET['file']);
        // Specify file path.
        $path = ""; // '/uplods/'

        foreach ($_SESSION['dir_path_home'] as $i)
        {
            $path = $path. $i. "\\";
        }

        $download_file =  $path. $filename;

        if(!empty($filename))
        {
            // Check file is exists on given path.
            if(file_exists($download_file))
            {
                header('Content-Disposition: attachment; filename=' . $filename);
                readfile($download_file);
                exit;
            }
            else
            {
                echo 'File does not exists on given path';
            }
        }
    ?>

</body>

</html>