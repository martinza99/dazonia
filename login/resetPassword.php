<?php
    session_start();
    require_once "sql.php";
    require_once 'functions.php';
    require_once "../header.php";

    // if(!isset($_GET["resetKey"])&&(!isset($_SESSION["userId"])||!checkLogin($_SESSION["userId"]))){
    //     header("Location: $domain/login");
    //     die();
    // }
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="login.js"></script>
	<title>Password Reset</title>
</head>
<body>
	<form id="login" action="resetPassword.php" method="post" class="center">
        <?php
        if(isset($_GET["resetKey"])){//warning icon or current password field
            echo "<span class=\"glyphicon glyphicon-exclamation-sign\" style=\"color:red;\"></span><span>Your API key will be reset</span><br>";
        }else{
            echo "<input type=\"password\" placeholder=\"current password\" name=\"currentPassword\" autocomplete=\"current-password\" required><br>";
        }
        ?>
		<input type="password" placeholder="new password" name="newPassword" id="password" oninput="equals(this, '#passwordConfirm');" autocomplete="new-password" required><br>
        <input type="password" placeholder="confirm password" autocomplete="new-password" id="passwordConfirm" oninput="equals(this, '#password');" autocomplete="new-password" required><br>
        <?php if(isset($_GET["resetKey"])) echo "<input type=\"hidden\" name=\"resetKey\" value=\"$_GET[resetKey]\">";?>
        <input type="submit" value="Change" id="submitButton" disabled>
	</form>

<?php
    if(isset($_POST["newPassword"])){
        $changePass = false;
        if(isset($_SESSION["userId"])){
            $userid = $_SESSION["userId"];
            $currentPassword = $_POST["currentPassword"];

            $sql = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $sql->bind_param("i", $userid);
            $sql->execute();

            $hash = $sql->get_result()->fetch_assoc()["password"];
            $changePass = password_verify($currentPassword,$hash);
        }
        else if(isset($_POST["resetKey"])){
            $sql = $conn->prepare("SELECT id FROM users WHERE apiKey = ?");
            $sql->bind_param("s", $_POST["resetKey"]);
            $sql->execute();
            $result = $sql->get_result();
            if($result->num_rows>0){
                $userid = $result->fetch_assoc()["id"];
                $changePass = true;
                $_SESSION["userId"] = $userid;

                //reset API key
                $apiKey = generateRandomString(64);
                $sql = $conn->prepare("UPDATE users SET apiKey = ? WHERE id = ?");
                $sql->bind_param("si",$apiKey,$userid);
                $sql->execute();
            }
        }

        if($changePass){
            $newPasswordHash = password_hash(htmlspecialchars($_POST["newPassword"]),PASSWORD_DEFAULT);
            $sql = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $sql->bind_param("si",$newPasswordHash, $userid);
            if(!$sql->execute()){
                die("SQL error! - Please contact an admin.");
            }

            echo "successfully changed password";
        }
        else{
            echo "Wrong password!";
        }
    }
    if(isset($_SESSION["userId"]))
        require "../footer.php";
?>

</body>
</html>

<?php
function generateRandomString($length){//generates random strings
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>