<?php
require_once(__DIR__ . "/../include/functions.php");
require_once(__DIR__ . "/../classes/file.php");

if (isset($_POST["key"])) {
	$apiKey = $_POST["key"];

	if (!checkApiKey($conn, $apiKey)) {
		http_response_code(401);
		die("401 Unauthorized\nInvalid API-Key");
	}
} else {
	checkLogin();
}

//re-arrange file objects
/** @var File[] */
$files = [];
$f = $_FILES["file"];
$file_count = count($f["name"]);

for ($i = 0; $i < $file_count; $i++) {
	$file = new File($f["name"][$i], $f["type"][$i], $f["tmp_name"][$i], $f["size"][$i]);
	array_push($files, $file);
}


$errors = [];
if ($file_count === 0) {
	array_push($errors, "No files uploaded!");
} else {
	$dupStmt = $conn->prepare("SELECT * FROM file WHERE hash = :hash");
	$nameStmt = $conn->prepare("SELECT * FROM file WHERE LOWER(filename) = LOWER(:filename)");
	$insertStmt = $conn->prepare("INSERT INTO file (filename, ogName, hash, userID) VALUES (:filename, :ogName, :hash, :userID)");
}
$n = 0;
foreach ($files as $file) {
	#region check duplicate
	$dupStmt->bindValue(":hash", $file->md5, PDO::PARAM_STR);
	$dupStmt->execute();
	if ($dupStmt->rowCount() > 0) {
		$name = $dupStmt->fetchObject()->fileName;
		array_push($errors, [$file, "File already exists: [{$file->md5}] <a href=/view/$name\">$name</a>"]);
		continue;
	}
	#endregion check duplicate

	#region make name for database
	$longName = $file->hashname;
	$shortName = "";
	do {
		$shortName = $shortName . substr($longName, 0, 1);
		$longName = substr($longName, 1);
		$finalname = $shortName . "." . $file->ext();
	} while (checkName($finalname, $nameStmt) == false);
	#endregion make name for database

	#region create thumbnails and move files
	move_uploaded_file($file->tmp_name, $UPLOADS . "/files/$finalname");
	exec("ffmpeg -i $UPLOADS/files/$finalname -vf scale=w=180:h=180:force_original_aspect_ratio=decrease -frames:v 1 thumbnail.png");
	rename("thumbnail.png", $UPLOADS . "/thumbnails/$finalname");
	#endregion create thumbnails and move files

	#region insert entry into database
	$insertStmt->bindValue(":filename", $finalname, PDO::PARAM_STR);
	$insertStmt->bindValue(":ogName", $file->filename(), PDO::PARAM_STR);
	$insertStmt->bindValue(":hash", $file->md5, PDO::PARAM_STR);
	$insertStmt->bindValue(":userID", $user->userID, PDO::PARAM_STR);
	$insertStmt->execute();
	#endregion insert file into database

	sendWebhook($finalname);
}

#region error messages
foreach ($errors as $error) {
	$name = $error[0]->name ?? "(unknown)";
	echo "<p>$name: {$error[1]}</p>";
}
#endregion error messages

if (sizeof($errors) == 0) {
	if (isset($_POST["json"])) {
		$domain = "{$_SERVER["REQUEST_SCHEME"]}://{$_SERVER["SERVER_NAME"]}";
		$response = new stdClass();
		$response->url = $domain . "/view/$finalname";
		$response->thumbnail = $domain . "/thumbnail/$finalname";
		echo json_encode($response);
		die();
	} else {
		header("Location: /view/$finalname");
		die("<a href=\"/view/$finalname\">$finalname</a>");
	}
}



/**
 * checks db if name is available 
 */
function checkName(String $filename, PDOStatement $stmt)
{
	//reserved windows names
	if (preg_match("/^(CON|PRN|AUX|NUL|COM\d|LPT\d)\..*$/i", $filename))
		return false;
	$stmt->bindValue(":filename", $filename, PDO::PARAM_STR);
	$stmt->execute();
	if ($stmt->rowCount() > 0) //return false if name is taken
		return false;       //else insert into database
	return true;
}

function sendWebhook(String $filename)
{
	if (isset($_ENV["webhookURL"])) {
		$webhookURL = $_ENV["webhookURL"];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $webhookURL);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt(
			$ch,
			CURLOPT_POSTFIELDS,
			"content={$_SERVER["REQUEST_SCHEME"]}://{$_SERVER["SERVER_NAME"]}"
		);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		curl_close($ch);
	}
}

function checkApiKey(PDO $conn, String $apiKey)
{
	$sql = $conn->prepare("SELECT * FROM `users` WHERE `apiKey` = :apiKey");
	$sql->bindValue(":apiKey", $apiKey, PDO::PARAM_STR);
	$sql->execute();
	$user = $sql->fetchObject();
	if ($sql->rowCount() > 0) {
		$GLOBALS['user'] = $user;
		return true;
	}
}
