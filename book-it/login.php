<?php
    require "config.php";

    $error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        $password = $_POST['password'];

        $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: products.php");
            }
            exit;
        } else {
            $error = "Wrong email or password.";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Login - book-it</title>
        <link rel="stylesheet" href="style.css">
    </head>
    
    <body>
    <div class="box-small">
        <h1>book-it</h1>
        <h2>Login</h2>
        <?php if ($error != "") { ?>
            <div class="error"><?php echo h($error); ?></div>
        <?php } ?>
        <form method="post">
            Email
            <input type="email" name="email" required>
            Password
            <input type="password" name="password" required>
            <button class="btn" type="submit">Login</button>
        </form>
        <p>No account yet? <a href="register.php">Register here</a></p>
    </div>
    </body>
</html>
