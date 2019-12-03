<?php
    //get username
    $userId = $_SESSION["userId"];
    $sql = $conn->prepare("SELECT `name` FROM `users` WHERE `id` = ?");
    $sql->bind_param("i",$userId);
    $sql->execute();
    $result = $sql->get_result();
    $rows = $result->fetch_object();
    $username = $rows->name;

    //get picCount
    $sql = $conn->prepare("SELECT COUNT(files.id) AS picCount FROM files");
    $sql->execute();
    $result = $sql->get_result();
    $rows = $result->fetch_object();
    $picCount = $rows->picCount;

echo '<div class="bottom">';
if($userId<2){
    echo '<a href="'.$domain.'/login/token.php" target="_top">create register token</a><br>';
    echo '<a href="'.$domain.'/login/remote.php" target="_top">remote SQL query</a><br>';
    echo '<a href="'.$domain.'/login/users.php" target="_top">User List</a><br>';
    echo '<a href="'.$domain.'/tags/editor.php" target="_top">Tag Editor</a><br>';
}
echo '
    <a href="'.$domain.'/login/settings.php" target="_top">Settings</a><br>
    <a href="'.$domain.'/login/api.php" target="_top">API Info</a><br>
    <a href="'.$domain.'/login/resetPassword.php" target="_top">Change Password</a><br>
    <a href="/fixMasking.user.js">Userscript</a><br>
    <a href="'.$domain.'/login/logout.php" target="_top"><button>Logout</button></a> <span><a href="'.$domain.'/list?q=u%3A'.$userId.'" target="_top">'.$username.'</a></span>
</div>';

unset($output);
exec('git show -s --format=%H & git show -s --format=%cr', $output);
echo "<div class=\"right bottom\">";

echo "
<div class=\"right bottom\">";
$temp = "";
if($_SERVER["SCRIPT_NAME"]=="/upload/index.php")
    $temp = "../login/";
    if($userId<2)//update server button
        echo "
            <form action=\"".$temp."remote.php\" method=\"POST\" autocomplete=\"off\">
                <input type=\"hidden\" name=\"action\" value=\"u\">
                <input type=\"submit\" value=\"Update\">
            </form>";

echo "
    <span><b>$picCount</b> pictures</span><br>
    <span data-toggle=\"tooltip\" data-placement=\"top\" title=\"$output[1]\"><b>".substr($output[0],0,6)."</b>".substr($output[0],6)."</span>
    </div>
</div>";
?>