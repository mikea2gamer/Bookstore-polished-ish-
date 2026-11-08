<?php
    require "config.php";

    $error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = mysqli_real_escape_string($conn, trim($_POST['name']));
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        $password = $_POST['password'];

        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");

        if (mysqli_num_rows($check) > 0) {
            $error = "That email is already registered.";
        } elseif (strlen($password) < 4) {
            $error = "Password must be at least 4 characters.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hash', 'user')");
            header("Location: login.php");
            exit;
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Register - book-it</title>
        <link rel="stylesheet" href="style.css">
    </head>
    
    <body>
    <div class="box-small">
        <h1>book-it</h1>
        <h2>Register</h2>
        <?php if ($error != "") { ?>
            <div class="error"><?php echo h($error); ?></div>
        <?php } ?>
        <form method="post">
            Name
            <input type="text" name="name" required>
            Email
            <input type="email" name="email" required>
            Password
            <input type="password" name="password" required>
            <button class="btn" type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
    </body>
</html>
