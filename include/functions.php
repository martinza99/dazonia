<?php
session_start();
$config = json_decode(file_get_contents(__DIR__ . "/config.json"));
$conn = new PDO($config->db->dsn, $config->db->username, $config->db->password);
$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
$webhookURL = $config->webhookURL;
$UPLOADS = $config->UPLOADS;
$CDN = $config->CDN;

$user;
if (isset($_SESSION["userID"])) {
	$stmt = $conn->prepare("SELECT * FROM user WHERE userID = :userID");
	$stmt->bindValue(":userID", $_SESSION["userID"], PDO::PARAM_INT);
	$stmt->execute();
	$user = $stmt->fetch();
	$stmt = null;
	if ($user === null) {
		session_destroy();
		return false;
	}
}

function prePrint(...$args)
{
	echo "<details open><summary>";
	echo sizeof($args);
	echo " Variables</summary><hr><pre>";
	foreach ($args as $arg) {
		print_r($arg);
		echo "<hr>";
	}
	echo "</pre></details>";
}

function checkLogin()
{
	if (!isset($GLOBALS["user"])) {
		session_destroy();
		http_response_code(401);
		die('401 Unauthorized<br>Not logged in!<br><a href="/auth/login?fw=' . $_SERVER["REQUEST_URI"] . '">Go to Login</a>');
	}
}

function checkAdmin()
{
	checkLogin();
	if ($GLOBALS["user"]->isAdmin) {
		http_response_code(401);
		die('401 Unauthorized<br>Admin only page!<br><a href="/auth/login?fw=' . $_SERVER["REQUEST_URI"] . '">Go to Login</a>');
	}
}

function printDatalistTags($conn)
{
	// $conn = $GLOBALS["conn"];
	$stmt = $conn->prepare("SELECT tag.tagname AS 'tagname', COUNT(tag.tagname) AS count FROM tag LEFT JOIN tagfile ON tag.tagID = tagfile.tagID GROUP BY tag.tagID ORDER BY COUNT DESC");
	$stmt->execute();
	echo '<datalist id="tagList">';
	while ($rows = $stmt->fetch()) {
		echo "<option value=\"$rows->tagName\">";
	}
	echo '</datalist>';
}

function generateRandomString(int $length)
{ //generates random strings
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
	$charLength = strlen($characters) - 1;
	$randomString = "";

	for ($j = 0; $j < $length; $j++) {
		$index = random_int(0, $charLength);
		$randomString .= $characters[$index];
	}
	return $randomString;
}
