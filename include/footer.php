<?php
require_once(__DIR__ . "/functions.php");
//get picCount
$sql = $conn->prepare("SELECT COUNT(file.fileID) AS picCount FROM file");
$sql->execute();
$row = $sql->fetch();
$sql = null;
$picCount = $row->picCount;
?>

<footer>
    <div class="left">
        <ul>
            <?php if (isset($user) && $user->isAdmin) : ?>
                <li><a href="/login/token.php">create register token</a></li>
                <li><a href="/users/">User List</a></li>
                <li><a href="/tags/editor.php">Tag Editor</a></li>
            <?php endif; ?>
            <li><a href="/misc/settings.php">Settings</a></li>
            <li><a href="/misc/hotkeys.php">Hotkey List</a></li>
            <li><a href="/auth/api.php">API Info</a></li>
            <li><a href="/auth/reset">Change Password</a></li>
        </ul>
        <form action="/auth/logout" method="POST">
            <button type="submit">Logout</button>
        </form>
        <span><?= $user->username ?? "" ?></span>
    </div>
    <div class="right">
        <span><b><?= $picCount ?></b> pictures</span><br>
        <span>Version $version</span>
    </div>
</footer>