<?php
    require "config.php";
    need_login();

    $uid = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $order_id = (int)$_POST['order_id'];

        // cancel order
        if (isset($_POST['cancel_order'])) {
            mysqli_query($conn, "UPDATE orders SET status='Cancelled' WHERE id=$order_id AND user_id=$uid AND status='Processing'");

            // balikin stok
            if (mysqli_affected_rows($conn) == 1) {
                $r = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id=$order_id");
                $items = mysqli_fetch_all($r, MYSQLI_ASSOC);

                foreach ($items as $item) {
                    $book_id = (int)$item['book_id'];
                    $qty = (int)$item['qty'];
                    mysqli_query($conn, "UPDATE books SET stock = stock + $qty WHERE id=$book_id");
                }
            }
        }
    }

    $q = "";
    if (isset($_GET['q'])) {
        $q = trim($_GET['q']);
    }
    $qs = mysqli_real_escape_string($conn, $q);

    $sql = "SELECT * FROM orders
            WHERE user_id=$uid
            AND (id LIKE '%$qs%' OR status LIKE '%$qs%' OR payment_method LIKE '%$qs%' OR address LIKE '%$qs%')
            ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

    include "header.php";
?>
    
<html>
    <h1>My Orders</h1>

    <form method="get">
        <input type="text" name="q" placeholder="Search by id, status, or payment method" value="<?php echo h($q); ?>">
        <button class="btn" type="submit">Search</button>
    </form><br>

    <?php if (count($orders) == 0) { ?>
        <p>You have no orders yet.</p>
    <?php } ?>

    <?php foreach ($orders as $order) {
        $r = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id=" . $order['id']);
        $order_items = mysqli_fetch_all($r, MYSQLI_ASSOC);
    ?>

    <div class="card">
        <b>Order #<?php echo $order['id']; ?></b> - <?php echo $order['created_at']; ?><br>
        Payment: <?php echo h($order['payment_method']); ?><br>
        Address: <?php echo h($order['address']); ?><br>
        Status: <b><?php echo h($order['status']); ?></b>

        <table>
            <tr>
                <th>Book</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>

            <?php foreach ($order_items as $oi) { ?>
            <tr>
                <td><?php echo h($oi['title']); ?></td>
                <td><?php echo $oi['qty']; ?></td>
                <td><?php echo rupiah($oi['price'] * $oi['qty']); ?></td>
            </tr>
            <?php } ?>

        </table>
        <p class="total">Total: <?php echo rupiah($order['total']); ?></p>

        <?php if ($order['status'] == 'Processing') { ?>
        <form method="post">
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
            <button class="btn-evil" type="submit" name="cancel_order">Cancel Order</button>
        </form>
        <?php } ?>
    </div>
    <?php } ?>

    <?php include "footer.php"; ?>
</html>