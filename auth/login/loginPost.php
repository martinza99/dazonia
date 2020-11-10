<?php
require_once(__DIR__ . "/../../include/functions.php");

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    http_response_code(400);
    header("Location: /auth/login?status=missing");
    die('No username or password given<br><a href="/auth/login">Back to Login!</a>');
}

$username = htmlspecialchars($_POST['username']);
$password = htmlspecialchars($_POST['password']);


if (!checkUser($username, $password, $conn)) {
    http_response_code(400);
    header("Location: /auth/login?status=incorrect");
    die('Wrong Username or Password <a href="/auth/login/">Back to Login!</a>');
}

$domain = "{$_SERVER["REQUEST_SCHEME"]}://{$_SERVER["SERVER_NAME"]}";
header("Location: $domain" . $_POST["forward"]);
echo '<a href="' . $domain . $_POST["forward"] . '">Click here if you don\'t get forwarded</a>';
die();

function checkUser(String $username, String $password, PDO $conn)
{
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = :username");
    $stmt->bindValue(":username", $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetchObject();
    if ($stmt->rowCount() == 0)
        return false;
    if (password_verify($password, $user->password)) {
        $_SESSION["userID"] = $user->userID;
        $stmt = $conn->prepare("UPDATE user SET lastLogin = NOW() WHERE `userID` = :userID");
        $stmt->bindValue(":userID", $user->userID, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = null;
        return true;
    }
}
