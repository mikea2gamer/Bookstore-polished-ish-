<?php
    require "config.php";
    need_login();

    $uid = $_SESSION['user_id'];

    // remove item from cart
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove'])) {
        $cart_id = (int)$_POST['cart_id'];
        mysqli_query($conn, "DELETE FROM cart WHERE id=$cart_id AND user_id=$uid");
        header("Location: cart.php");
        exit;
    }

    $sql = "SELECT cart.id, cart.qty, books.title, books.price
            FROM cart JOIN books ON cart.book_id = books.id
            WHERE cart.user_id = $uid";
    $result = mysqli_query($conn, $sql);
    $items = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $total = 0;

    include "header.php";
?>

<html>
    <h1>My Cart</h1>

    <?php if (count($items) == 0) { ?>
        <p>Your cart is empty. <a href="products.php">Browse books</a></p>
    <?php } else { ?>
        <table>
            <tr>
                <th>Book</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
            <?php foreach ($items as $item) {
                $subtotal = $item['price'] * $item['qty'];
                $total = $total + $subtotal;
            ?>
            <tr>
                <td><?php echo h($item['title']); ?></td>
                <td><?php echo rupiah($item['price']); ?></td>
                <td><?php echo $item['qty']; ?></td>
                <td><?php echo rupiah($subtotal); ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                        <button class="btn btn-red" type="submit" name="remove">Remove</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>

        <p class="total">Total: <?php echo rupiah($total); ?></p>
        <p style="text-align:right;"><a class="btn" href="checkout.php">Purchase</a></p>
    <?php } ?>
    <!-- g guna ini wok -->
    <?php include "footer.php"; ?>
</html>
