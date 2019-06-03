<?php
    //get username
    $userId = $_SESSION["userId"];
    $sql = $conn->prepare("SELECT `name` FROM `users` WHERE `id` = ?");
    $sql->bind_param("i",$userId);
    $sql->execute();
    $result = $sql->get_result();
    $rows = $result->fetch_assoc();
    $username = $rows['name'];

    //get picCount
    $sql = $conn->prepare("SELECT COUNT(files.id) AS picCount FROM files");
    $sql->execute();
    $result = $sql->get_result();
    $rows = $result->fetch_assoc();
    $picCount = $rows["picCount"];

echo '<div class="bottom">';
if($userId<2){
    echo '<a href="'.$domain.'/login/token.php" target="_top">create register token</a><br>';
    echo '<a href="'.$domain.'/login/remote.php" target="_top">remote SQL query</a><br>';
    echo '<a href="'.$domain.'/login/users.php" target="_top">User List</a><br>';
}
echo '
    <a href="'.$domain.'/login/api.php" target="_top">API Info</a><br>
    <a href="'.$domain.'/login/logout.php" target="_top"><button>Logout</button></a><span> '.$username.'</span>
</div>';

exec('git rev-parse --verify HEAD', $output);
echo "<div class=\"right bottom\">";

echo "
<div class=\"right bottom\">";

    if($userId<2)//update server button
        echo "
            <form action=\"$domain/login/remote.php\" method=\"POST\" autocomplete=\"off\">
                <input type=\"hidden\" name=\"action\" value=\"u\">
                <input type=\"submit\" value=\"Update\">
            </form>";

echo "
    <span><b>$picCount</b> pictures</span><br>
    <span><a href=\"https://github.com/martinza99/dazonia/commit/$output[0]\" target=\"_top\"><b>".substr($output[0],0,6)."</b>".substr($output[0],6)."</a></span>
</div>";
?>