<?php
//get picCount
$sql = $conn->prepare("SELECT COUNT(files.id) AS picCount FROM files");
$sql->execute();
$result = $sql->get_result();
$rows = $result->fetch_object();
$picCount = $rows->picCount;

echo '<div class="bottom">';
if ($user->isAdmin) {
    echo '<a href="/login/token.php">create register token</a><br>';
    echo '<a href="/login/update.php">Update Dazonia</a><br>';
    echo '<a href="/login/users.php">User List</a><br>';
    echo '<a href="/tags/editor.php">Tag Editor</a><br>';
}
echo '
    <a href="/login/settings.php">Settings</a><br>
    <a href="/login/api.php">API Info</a><br>
    <a href="/login/resetPassword.php">Change Password</a><br>
    <a href="/login/hotkeys.php">Hotkey List</a><br>
    <a href="/login/logout.php"><button>Logout</button></a> <span><a href="/list?q=u%3A' . $user->id . '">' . $user->name . '</a></span>
</div>';

echo "<div class=\"right bottom\">";

echo "
<div class=\"right bottom\">";
echo "
    <span><b>$picCount</b> pictures</span><br>
    <span>Version $version</span>
    </div>
</div>";
