<?php
    require "config.php";
    need_login();

    $q = "";
    if (isset($_GET['q'])) {
        $q = trim($_GET['q']);
    }
    $qs = mysqli_real_escape_string($conn, $q);

    $sql = "SELECT * FROM books
            WHERE title LIKE '%$qs%' OR author LIKE '%$qs%' OR genre LIKE '%$qs%'
            ORDER BY title";
    $result = mysqli_query($conn, $sql);
    $books = mysqli_fetch_all($result, MYSQLI_ASSOC);

    include "header.php";
?>

<html>
    <h1>Products</h1>

    <form method="get">
        <input type="text" name="q" placeholder="Search by book name, author, or genre" value="<?php echo h($q); ?>">
        <button class="btn" type="submit">Search</button>
    </form>

    <table>
        <tr>
            <th>Cover</th>
            <th>Title</th>
            <th>Author</th>
            <th>Genre</th>
            <th>Price</th>
            <th>Stock</th>
            <th></th>
        </tr>
        <?php foreach ($books as $b) { ?>
        <tr>
            <td>
                <?php if ($b['image'] != "") { ?>
                    <img class="book-thumb" src="uploads/<?php echo h($b['image']); ?>">
                <?php } else { ?>
                    <span class="no-image">No image</span>
                <?php } ?>
            </td>
            <td><?php echo h($b['title']); ?></td>
            <td><?php echo h($b['author']); ?></td>
            <td><?php echo h($b['genre']); ?></td>
            <td><?php echo rupiah($b['price']); ?></td>
            <td>
                <?php if ($b['stock'] > 0) { ?>
                    <span class="stock-ok"><?php echo $b['stock']; ?></span>
                <?php } else { ?>
                    <span class="stock-out">Out of stock</span>
                <?php } ?>
            </td>
            <td><a class="btn" href="book.php?id=<?php echo $b['id']; ?>">View</a></td>
        </tr>
        <?php } ?>
    </table>

    <?php if (count($books) == 0) { ?>
        <p>No books found.</p>
    <?php } ?>

    <?php include "footer.php"; ?>
</html>