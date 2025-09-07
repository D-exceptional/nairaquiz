<?php 
    session_start();
    unset($_SESSION['userID']);
    header('Location: /login');
    //echo 'Logout approved';
    //exit();
?>