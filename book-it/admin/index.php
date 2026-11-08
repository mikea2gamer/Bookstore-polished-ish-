<?php
    require "../config.php";
    need_admin();

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
    <h1>Manage Books</h1>

    <form method="get">
        <input type="text" name="q" placeholder="Search by book name, author, or genre" value="<?php echo h($q); ?>">
        <button class="btn" type="submit">Search</button>
    </form><br>
    
    <a class="btn" href="book_form.php">+ Add Book</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Cover</th>
            <th>Title</th>
            <th>Author</th>
            <th>Genre</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>
        
        <?php foreach ($books as $b) { ?>
        <tr>
            <td><?php echo $b['id']; ?></td>
            <td>
                <?php if ($b['image'] != "") { ?>
                    <img class="book-thumb" src="../uploads/<?php echo h($b['image']); ?>">
                <?php } else { ?>
                    <span class="no-image">No image</span>
                <?php } ?>
            </td>
            <td><?php echo h($b['title']); ?></td>
            <td><?php echo h($b['author']); ?></td>
            <td><?php echo h($b['genre']); ?></td>
            <td><?php echo rupiah($b['price']); ?></td>
            <td><?php echo $b['stock']; ?></td>
            <td>
                <a class="btn" href="book_form.php?id=<?php echo $b['id']; ?>">Edit</a>
                <a class="btn btn-red" href="book_delete.php?id=<?php echo $b['id']; ?>" onclick="return confirm('Delete this book?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
    <?php include "footer.php"; ?>
</html>