<?php
    require "../config.php";
    need_admin();

    $q = "";
    if (isset($_GET['q'])) {
        $q = trim($_GET['q']);
    }
    $qs = mysqli_real_escape_string($conn, $q);

    $sql = "SELECT * FROM users
            WHERE id LIKE '%$qs%' OR name LIKE '%$qs%' OR email LIKE '%$qs%' OR role LIKE '%$qs%'
            ORDER BY role DESC";
    $result = mysqli_query($conn, $sql);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);

    include "header.php";
?>

<html>
    <h1>Users and Admins</h1>

    <form method="get">
        <input type="text" name="q" placeholder="Search by id, name, or role" value="<?php echo h($q); ?>">
        <button class="btn" type="submit">Search</button>
    </form><br>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
        </tr>

        <?php foreach ($users as $u) { ?>
        <tr>
            <td><?php echo $u['id']; ?></td>
            <td><?php echo h($u['name']); ?></td>
            <td><?php echo h($u['email']); ?></td>
            <td><?php echo h($u['role']); ?></td>
        </tr>
        <?php } ?>
    </table>
    
    <?php include "footer.php"; ?>
</html>