<?php
    require "config.php";
    need_login();

    $id = 0;
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    }

    $result = mysqli_query($conn, "SELECT * FROM books WHERE id=$id");
    $book = mysqli_fetch_assoc($result);

    if (!$book) {
        header("Location: products.php");
        exit;
    }

    $error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $book['stock'] > 0) {
        $uid = $_SESSION['user_id'];
        $qty = (int)$_POST['qty'];
        if ($qty < 1) {
            $qty = 1;
        }

        $check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$uid AND book_id=$id");
        $row = mysqli_fetch_assoc($check);

        $already = 0;
        if ($row) {
            $already = $row['qty'];
        }

        if ($already + $qty > $book['stock']) {
            $error = "Not enough stock. You already have $already in your cart.";
        } else {
            if ($row) {
                mysqli_query($conn, "UPDATE cart SET qty = qty + $qty WHERE id=" . $row['id']);
            } else {
                mysqli_query($conn, "INSERT INTO cart (user_id, book_id, qty) VALUES ($uid, $id, $qty)");
            }
            header("Location: cart.php");
            exit;
        }
    }

    include "header.php";
?>

<html>
    <a href="products.php">&laquo; Back to products</a>
    <?php if ($book['image'] != "") { ?>
        <img class="book-big" src="uploads/<?php echo h($book['image']); ?>">
    <?php } ?>

    <h1><?php echo h($book['title']); ?></h1>
    <p><b>Author:</b> <?php echo h($book['author']); ?></p>
    <p><b>Genre:</b> <?php echo h($book['genre']); ?></p>
    <p><b>Price:</b> <?php echo rupiah($book['price']); ?></p>
    <p><b>Description:</b><br><?php echo nl2br(h($book['description'])); ?></p>

    <?php if ($error != "") { ?>
        <div class="error"><?php echo h($error); ?></div>
    <?php } ?>

    <?php if ($book['stock'] > 0) { ?>
        <p><b>Stock:</b> <span class="stock-ok"><?php echo $book['stock']; ?></span></p>
        <form method="post">
            Quantity
            <input type="number" name="qty" value="1" min="1" max="<?php echo $book['stock']; ?>">
            <button class="btn" type="submit">Add to Cart</button>
        </form>
    <?php } else { ?>
        <p><b>Stock:</b> <span class="stock-out">Out of stock</span></p>
        <button class="btn" disabled>Add to Cart</button>
    <?php } ?>

    <?php include "footer.php"; ?>
</html>