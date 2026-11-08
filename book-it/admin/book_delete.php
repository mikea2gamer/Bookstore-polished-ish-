<?php
    require "../config.php";
    need_admin();

    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];

        // delete the cover image file
        $result = mysqli_query($conn, "SELECT image FROM books WHERE id=$id");
        $book = mysqli_fetch_assoc($result);
        if ($book && $book['image'] != "" && file_exists("../uploads/" . $book['image'])) {
            unlink("../uploads/" . $book['image']);
        }

        mysqli_query($conn, "DELETE FROM books WHERE id=$id");
        mysqli_query($conn, "DELETE FROM cart WHERE book_id=$id");
    }

    header("Location: index.php");
    exit;
?>
