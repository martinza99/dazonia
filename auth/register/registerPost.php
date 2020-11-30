<?php
require_once(__DIR__ . "/../../include/functions.php");

if (!(isset($_POST["token"]) && isset($_POST["username"]) && isset($_POST["password"]))) {
    http_response_code(400);
    header("Location: /auth/register?status=missing");
    die('No registration token, username or password! <a href="/auth/register">Back to Form!</a>');
}

$token = $_POST["token"];
$sql = $conn->prepare("SELECT tokenID FROM register WHERE token = :token");
$sql->bindValue(":token", $token, PDO::PARAM_STR);
$sql->execute();

if ($sql->rowCount() == 0) {
    http_response_code(401);
    header("Location: /auth/register?status=invalid");
    die('invalid registration token! <a href="/auth/register">Back to Form!</a>');
}

$username = htmlspecialchars($_POST["username"]);
$password = password_hash(htmlspecialchars($_POST["password"]), PASSWORD_DEFAULT);

$sql = $conn->prepare("SELECT userID FROM user WHERE LOWER(username) = LOWER(:username)");
$sql->bindValue(":username", $username);
$sql->execute();

if ($sql->rowCount() > 0) {
    http_response_code(400);
    header("Location: /auth/register?status=taken");
    die('Name already in use! <a href="/auth/register">Back to Form!</a>');
} else {
    $apiKey = generateRandomString(64);
    $sql = $conn->prepare("INSERT INTO user (username, password, apiKey) VALUES (:username, :password, :apiKey)");
    $sql->bindValue(":username", $username, PDO::PARAM_STR);
    $sql->bindValue(":password", $password, PDO::PARAM_STR);
    $sql->bindValue(":apiKey", $apiKey, PDO::PARAM_STR);

    if (!$sql->execute()) {
        http_response_code(500);
        die("500 Internal Server Error<br>SQL error! - Please contact an admin.");
    }
    $_SESSION["userID"] = $conn->lastInsertId();
    $sql = $conn->prepare("DELETE FROM register WHERE token = :token");
    $sql->bindValue(":token", $token, PDO::PARAM_STR);
    $sql->execute();
    header("Location: /");
    die("successfully created account");
}
