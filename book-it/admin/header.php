<!DOCTYPE html>
<html>
    <head>
        <title>book-it Admin</title>
        <link rel="stylesheet" href="../style.css">
    </head>

    <body>
        <div class="navbar">
            <span class="logo">book-it ADMIN</span>
            <a href="index.php">Books</a>
            <a href="users.php">Users</a>
            <a href="orders.php">Orders</a>
            <a href="../logout.php">Logout (<?php echo h($_SESSION['name']); ?>)</a>
        </div>
    <div class="container">
