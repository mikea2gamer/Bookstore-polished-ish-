<?php
    require "config.php";
    need_login();

    $uid = $_SESSION['user_id'];

    $sql = "SELECT cart.qty, books.id AS book_id, books.title, books.price, books.stock
            FROM cart JOIN books ON cart.book_id = books.id
            WHERE cart.user_id = $uid";
    $result = mysqli_query($conn, $sql);
    $items = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if (count($items) == 0) {
        header("Location: cart.php");
        exit;
    }

    $total = 0;
    foreach ($items as $item) {
        $total = $total + ($item['price'] * $item['qty']);
    }

    $error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $method = $_POST['method'];
        $proof = "";
        $address = trim($_POST['address']);

        // check address
        if ($address == "") {
            $error = "Please enter your delivery address.";
        }

        // check stock
        foreach ($items as $item) {
            if ($error == "" && $item['qty'] > $item['stock']) {
                $error = "Sorry, not enough stock for: " . $item['title'];
            }
        }

        // check proof
        if ($error == "" && $method == "transfer") {
            if (!isset($_FILES['proof']) || $_FILES['proof']['error'] != 0) {
                $error = "Please upload your proof of payment.";
            } else {
                $ext = strtolower(pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION));
                if ($ext != "jpg" && $ext != "jpeg" && $ext != "png") {
                    $error = "Proof must be a jpg or png image.";
                } else {
                    $proof = time() . "_" . $uid . "." . $ext;
                    move_uploaded_file($_FILES['proof']['tmp_name'], "uploads/" . $proof);
                }
            }
        }

        // save order
        if ($error == "") {
            $payment = "Pay at location";
            if ($method == "transfer") {
                $payment = "Transfer";
            }

            $addr = mysqli_real_escape_string($conn, $address);

            mysqli_query($conn, "INSERT INTO orders (user_id, total, payment_method, proof, status, address)
                                VALUES ($uid, $total, '$payment', '$proof', 'Processing', '$addr')");
            $order_id = mysqli_insert_id($conn);

            foreach ($items as $item) {
                $title = mysqli_real_escape_string($conn, $item['title']);
                $book_id = $item['book_id'];
                $qty = $item['qty'];
                $price = $item['price'];

                mysqli_query($conn, "INSERT INTO order_items (order_id, book_id, title, qty, price)
                                    VALUES ($order_id, $book_id, '$title', $qty, $price)");
                mysqli_query($conn, "UPDATE books SET stock = stock - $qty WHERE id=$book_id");
            }

            mysqli_query($conn, "DELETE FROM cart WHERE user_id=$uid");
            header("Location: orders.php");
            exit;
        }
    }

    include "header.php";
?>

<html>
    <h1>Checkout</h1>

    <?php if ($error != "") { ?>
        <div class="error"><?php echo h($error); ?></div>
    <?php } ?>

    <table>
        <tr>
            <th>Book</th>
            <th>Qty</th>
            <th>Subtotal</th>
        </tr>
        <?php foreach ($items as $item) { ?>
        <tr>
            <td><?php echo h($item['title']); ?></td>
            <td><?php echo $item['qty']; ?></td>
            <td><?php echo rupiah($item['price'] * $item['qty']); ?></td>
        </tr>
        <?php } ?>
    </table>
    <p class="total">Total: <?php echo rupiah($total); ?></p>

    <h2>Payment Method</h2>
    <form method="post" enctype="multipart/form-data">
        <p>
            <input type="radio" name="method" value="transfer" checked> Pay through other methods (upload proof of payment)<br>
            <input type="radio" name="method" value="location"> Pay at location (no proof needed)
        </p>

        <p>
            Delivery address<br>
            <textarea name="address" maxlength="255" required><?php echo h($_POST['address'] ?? ''); ?></textarea>
        </p>

        bukti of payment (jpg / png)
        <input type="file" name="proof">

        <button class="btn" type="submit">Place Order</button>
        <a class="btn btn-red" href="cart.php">Cancel</a>
    </form>
    <?php include "footer.php"; ?>
</html>