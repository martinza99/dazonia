<!DOCTYPE html>
<html>

<head>
    <?php include(__DIR__ . "/../../include/head.php"); ?>
    <title>Logout</title>
    <link rel="stylesheet" href="/static/auth/form.css">
</head>

<body>
    <?php include(__DIR__ . "/../../include/nav.php"); ?>
    <main class="center">
        <p>Click here to logout</p>
        <form action="." method="POST">
            <input type="submit" value="Logout">
        </form>
    </main>
</body>

</html>