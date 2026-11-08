<?php
    require "config.php";
    
    if (isset($_SESSION['user_id'])) {
        if ($_SESSION['role'] == 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: products.php");
        }
    } else {
        header("Location: login.php");
    }
    exit;
?>
