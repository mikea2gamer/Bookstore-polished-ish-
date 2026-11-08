<!DOCTYPE html>
    <html>
            <head>
                <title>book-it</title>
                <link rel="stylesheet" href="style.css">
            </head>

            <body>
                <div class="navbar">
                    <span class="logo">book-it</span>
                    <a href="about.php">About Us</a>
                    <a href="products.php">Products</a>
                    <a href="cart.php">Cart</a>
                    <a href="orders.php">Orders</a>
                    <a href="logout.php">Logout (<?php echo h($_SESSION['name']); ?>)</a>
                </div>
        <div class="container">
            
