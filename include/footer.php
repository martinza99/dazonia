<?php
//get picCount
$sql = $conn->prepare("SELECT COUNT(file.fileID) AS picCount FROM file");
$sql->execute();
$row = $sql->fetchObject();
$sql = null;
$picCount = $row->picCount;
?>

<footer>
    <div class="left">
        <?php if (isset($user) && $user->isAdmin) : ?>
            <a href="/login/token.php">create register token</a><br>
            <a href="/login/update.php">Update Dazonia</a><br>
            <a href="/users/">User List</a><br>
            <a href="/tags/editor.php">Tag Editor</a><br>
        <?php endif; ?>
        <a href="/misc/settings.php">Settings</a><br>
        <a href="/misc/hotkeys.php">Hotkey List</a><br>
        <a href="/auth/api.php">API Info</a><br>
        <a href="/auth/reset">Change Password</a><br>
        <a href="/auth/logout.php">
            <button>Logout</button>
        </a>
        <span><?= $user->username ?? "" ?></a></span>
    </div>
    <div class="right">
        <span><b><?= $picCount ?></b> pictures</span><br>
        <span>Version $version</span>
    </div>
</footer>