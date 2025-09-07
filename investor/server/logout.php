<?php 
    session_start();
    unset($_SESSION['investorID']);
    header('Location: /access');
?>