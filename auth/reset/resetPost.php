<?php
require_once(__DIR__ . "/../../include/functions.php");

if (!$user && !isset($_GET["resetKey"]) && !isset($_POST["resetKey"])) {
    http_response_code(401);
    header("Location: /auth/reset?status=missing");
    die('Not logged in and no key given! <a href="/auth/reset">Back to Form!</a>');
}
if (isset($_POST["newPassword"])) {
    $changePass = false;
    if (isset($user)) {
        $currentPassword = $_POST["currentPassword"];
        $changePass = password_verify($currentPassword, $user->password);
    } else if (isset($_POST["resetKey"])) {
        $sql = $conn->prepare("SELECT userID FROM user WHERE apiKey = :apiKey");
        $sql->bindValue(":apiKey", $_POST["resetKey"], PDO::PARAM_STR);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $user = $sql->fetch();
            $changePass = true;
            $_SESSION["userID"] = $user->userID;
        } else {
            http_response_code(401);
            header("Location: /auth/reset?status=invalid");
            die('Invalid reset key! <a href="/auth/reset">Back to Form!</a>');
        }
    }

    if ($changePass) {
        $apiKey = generateRandomString(64);
        $newPasswordHash = password_hash(htmlspecialchars($_POST["newPassword"]), PASSWORD_DEFAULT);
        $sql = $conn->prepare("UPDATE user SET password = :password, apiKey = :apiKey WHERE userID = :userID");
        $sql->bindValue(":password", $newPasswordHash, PDO::PARAM_STR);
        $sql->bindValue(":apiKey", $apiKey, PDO::PARAM_STR);
        $sql->bindValue(":userID", $user->userID, PDO::PARAM_INT);
        if (!$sql->execute()) {
            http_response_code(500);
            die("500 Internal Server Error<br>SQL error! - Please contact an admin.");
        }
        header("Location: /auth/reset?status=success");
        die('Successfully changed password! <a href="/auth/reset">Back to Form!</a>');
    } else {
        http_response_code(401);
        header("Location: /auth/reset?status=wrong");
        die('Wrong password! <a href="/auth/reset">Back to Form!</a>');
    }
} else {
    http_response_code(401);
    header("Location: /auth/reset?status=missing");
    die('No password set! <a href="/auth/reset">Back to Form!</a>');
}
