<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("../include/head.php"); ?>
    <title>User List</title>
    <link rel="stylesheet" href="/static/users/users.css">
</head>

<body>
    <?php include("../include/nav.php"); ?>

    <table>
        <?php if ($user->isAdmin) : ?>
            <tr>
                <th><a href="/login/token.php" style="color:#2196F3;"><button>#</a></th>
                <th>Name</th>
                <th>LastLogin</th>
                <th>ResetPW</th>
                <th><button>X</button></th>
            </tr>
        <?php else : ?>
            <tr>
                <th>#</th>
                <th>Name</th>
            </tr>
        <?php endif; ?>
        <?php while ($row = $sql->fetch()) : ?>
            <tr id="<?= $row->userID ?>">
                <td><?= $row->userID ?></td>
                <td><a href="/list/?q=u%3A<?= $row->userID ?>"><?= $row->username ?></a>
                    <?php if ($user->isAdmin) : ?>
                <td title="<?= $row->lastLogin ?>"><?= timeElapsed($row->lastLogin) ?></td>
                <td><a href="/login/resetPassword.php?resetKey=<?= $row->apiKey ?>">Link</a>

                <td><button class="deleteButtonUser">X</button></td>
            <?php endif; ?>
            </tr>
        <?php endwhile; ?>
    </table>
    <?php include("../include/footer.php"); ?>
</body>

</html>