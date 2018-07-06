<!doctype html>

<?php
    session_start();
?>


<html>

<head>

    <meta charset="utf-8">

    <title>Logout</title>
    
    <script type="text/javascript">
        
        function Redirect()
        {
            window.location = "login.php";
        }
        
        setTimeout('Redirect()', 5000);
        
    </script>

</head>

<body>

    <div style="margin-top: 9%;">
    
            <h1 align="center">
                You have successfully been logged out.
            </h1>
            
        <p align="center">You will be redirected to login page in 5 seconds... else click <a href="login.php">here</a></p>
        
    </div>
    
    <?php
        
        if(session_status() == PHP_SESSION_ACTIVE)
        {
            destroy_session_data();
        }
    
        function destroy_session_data()
        {
            session_unset();
            setcookie(session_name(), '', time() - 2592000, '/');
            session_destroy();
        }
    
    ?>

</body>

</html>