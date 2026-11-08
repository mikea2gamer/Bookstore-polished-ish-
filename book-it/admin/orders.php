<?php
    require "../config.php";
    need_admin();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $order_id = (int)$_POST['order_id'];

        // authorize to on the way
        if (isset($_POST['authorize'])) {
            mysqli_query($conn, "UPDATE orders SET status='On the way' WHERE id=$order_id AND status='Processing'");
        }

        // arrived to complete
        if (isset($_POST['arrived'])) {
            mysqli_query($conn, "UPDATE orders SET status='Complete' WHERE id=$order_id AND status='On the way'");
        }

        // domt authorize to cancel
        if (isset($_POST['dont_authorize'])) {
            mysqli_query($conn, "UPDATE orders SET status='Cancelled' WHERE id=$order_id AND status='Processing'");
            if (mysqli_affected_rows($conn) == 1) {
                restock($conn, $order_id);
            }
        }

        // cancel order
        if (isset($_POST['cancel'])) {
            mysqli_query($conn, "UPDATE orders SET status='Cancelled' WHERE id=$order_id AND status='On the way'");
            if (mysqli_affected_rows($conn) == 1) {
                restock($conn, $order_id);
            }
        }

        // not a single soul will ever know about how hard it was to make the part above this comment, and it sickens me

        header("Location: orders.php");
        exit;
    }

    // give the stock back (used by both cancel buttons)
    function restock($conn, $order_id) {
        $r = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id=$order_id");
        $items = mysqli_fetch_all($r, MYSQLI_ASSOC);

        foreach ($items as $item) {
            $book_id = (int)$item['book_id'];
            $qty = (int)$item['qty'];
            mysqli_query($conn, "UPDATE books SET stock = stock + $qty WHERE id=$book_id");
        }
    }

    $q = "";
    if (isset($_GET['q'])) {
        $q = trim($_GET['q']);
    }
    $qs = mysqli_real_escape_string($conn, $q);

    $sql = "SELECT orders.*, users.name, users.email
            FROM orders JOIN users ON orders.user_id = users.id
            WHERE orders.id LIKE '%$qs%'
               OR orders.status LIKE '%$qs%'
               OR orders.payment_method LIKE '%$qs%'
               OR users.name LIKE '%$qs%'
               OR users.email LIKE '%$qs%'
               OR orders.id IN (SELECT order_id FROM order_items WHERE title LIKE '%$qs%'
               OR address LIKE '%$qs%')
            ORDER BY orders.id DESC";
    $result = mysqli_query($conn, $sql);
    $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

    include "header.php";
?>

<html>
    <h1>Orders</h1>

    <form method="get">
        <input type="text" name="q" placeholder="Search by id, status, or payment method" value="<?php echo h($q); ?>">
        <button class="btn" type="submit">Search</button>
    </form><br>

    <?php if (count($orders) == 0) { ?>
        <p>No orders yet.</p>
    <?php } ?>

    <?php foreach ($orders as $order) {
        $r = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id=" . $order['id']);
        $order_items = mysqli_fetch_all($r, MYSQLI_ASSOC);
    ?>
    <div class="card">
        <b>Order #<?php echo $order['id']; ?></b> - <?php echo $order['created_at']; ?><br>
        Customer: <?php echo h($order['name']); ?> (<?php echo h($order['email']); ?>)<br>
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

        <?php if ($order['proof'] != "") { ?>
            <p>Proof of payment:<br>
            <a href="../uploads/<?php echo h($order['proof']); ?>" target="_blank">
                <img src="../uploads/<?php echo h($order['proof']); ?>" width="150">
            </a></p>
        <?php } else { ?>
            <p><i>No proof (pay at location)</i></p>
        <?php } ?>

        <form method="post">
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
            <?php if ($order['status'] == 'Processing') { ?>
                <button class="btn" type="submit" name="authorize">Authorize</button>
                <button class="btn-evil" type="submit" name="dont_authorize">Don't Authorize</button>
            <?php } elseif ($order['status'] == 'On the way') { ?>
                <button class="btn" type="submit" name="arrived">Confirm Arrived</button>
                <button class="btn-evil" type="submit" name="cancel">Cancel Order</button>
            <?php } elseif ($order['status'] == 'Cancelled') { ?>
                <i>Order cancelled.</i>
            <?php } else { ?>
                <i>Order complete.</i>
            <?php } ?>
        </form>
    </div>
    <?php } ?>
    <?php include "footer.php"; ?>
</html>