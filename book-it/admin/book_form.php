<?php
    require "../config.php";
    need_admin();

    $id = 0;
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    }

    // empty values for "add", filled values for "edit"
    $book = array('title' => '', 'author' => '', 'genre' => '', 'description' => '', 'price' => '', 'stock' => '', 'image' => '');

    if ($id > 0) {
        $result = mysqli_query($conn, "SELECT * FROM books WHERE id=$id");
        $book = mysqli_fetch_assoc($result);
        if (!$book) {
            header("Location: index.php");
            exit;
        }
    }

    $error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = mysqli_real_escape_string($conn, trim($_POST['title']));
        $author = mysqli_real_escape_string($conn, trim($_POST['author']));
        $genre = mysqli_real_escape_string($conn, trim($_POST['genre']));
        $description = mysqli_real_escape_string($conn, trim($_POST['description']));
        $price = (int)$_POST['price'];
        $stock = (int)$_POST['stock'];

        // keep the old image unless a new one is uploaded
        $image_name = $book['image'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if ($ext != "jpg" && $ext != "jpeg" && $ext != "png") {
                $error = "Book image must be a jpg or png.";
            } else {
                $new_name = "book_" . time() . "." . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $new_name);

                // delete the old image file
                if ($image_name != "" && file_exists("../uploads/" . $image_name)) {
                    unlink("../uploads/" . $image_name);
                }
                $image_name = $new_name;
            }
        }

        if ($error == "") {
            if ($id > 0) {
                mysqli_query($conn, "UPDATE books SET title='$title', author='$author', genre='$genre',
                                    description='$description', price=$price, stock=$stock, image='$image_name'
                                    WHERE id=$id");
            } else {
                mysqli_query($conn, "INSERT INTO books (title, author, genre, description, price, stock, image)
                                    VALUES ('$title', '$author', '$genre', '$description', $price, $stock, '$image_name')");
            }
            header("Location: index.php");
            exit;
        }
    }

    include "header.php";
?>

<html>
    <h1><?php echo ($id > 0) ? "Edit Book" : "Add Book"; ?></h1>

    <?php if ($error != "") { ?>
        <div class="error"><?php echo h($error); ?></div>
    <?php } ?>

    <form method="post" enctype="multipart/form-data">
        Title
        <input type="text" name="title" value="<?php echo h($book['title']); ?>" required>
        Author
        <input type="text" name="author" value="<?php echo h($book['author']); ?>" required>
        Genre
        <input type="text" name="genre" value="<?php echo h($book['genre']); ?>" required>
        Description
        <textarea name="description" rows="5"><?php echo h($book['description']); ?></textarea>
        Price (Rp)
        <input type="number" name="price" value="<?php echo h($book['price']); ?>" min="0" required>
        Stock
        <input type="number" name="stock" value="<?php echo h($book['stock']); ?>" min="0" required>

        Book cover image (jpg / png)
        <?php if ($book['image'] != "") { ?>
            <br><img class="book-thumb" src="../uploads/<?php echo h($book['image']); ?>">
            <br><small>Current image. Upload a new one only if you want to replace it.</small>
        <?php } ?>
        <input type="file" name="image">

        <button class="btn" type="submit">Save</button>
        <a class="btn btn-red" href="index.php">Cancel</a>
    </form>
    <?php include "footer.php"; ?>
</html>