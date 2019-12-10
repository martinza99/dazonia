<?php
session_start();
require_once "sql.php";
require_once 'functions.php';
checkLogin();
$userId = $_SESSION["userId"];

if (isset($_POST["action"])) {
	switch ($_POST["action"]) {
		case "d":
			$sql = $conn->prepare("DELETE FROM users WHERE id = ?");
			$sql->bind_param("i", $_POST["id"]);
			$sql->execute();
			die("User deleted");
			break;
	}
}
require_once "../header.php";
?>

	<title>User List</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="login.js<?php echo "?$hash" ?>"></script>
	</head>

	<body>
		<?php
		$sql = $conn->prepare("SELECT * FROM users ORDER BY id");
		$sql->execute();
		$result = $sql->get_result();
		echo '<table border="1">';
		if ($user->isAdmin)
			echo '<tr><th><a href="' . $domain . '/login/token.php" target="_top" style="color:#2196F3;"><button>#</a></th><th>Name</th><th>LastLogin</th><th>ResetPW</th><th><button>X</button></th></tr>';
		else
			echo '<tr><th>#</th><th>Name</th></tr>';
		while ($rows = $result->fetch_object()) {
			echo "<tr id=\"$rows->id\">";
			echo "<td>$rows->id</td>";
			echo "<td><a href=\"$domain/list/?q=u%3A$rows->id\" target=\"_top\">$rows->name</a>"; //print name
			if ($user->isAdmin) {
				$timeStamp = timeElapsed($rows->lastLogin);
				echo "<td title=\"$rows->lastLogin\">$timeStamp</td>"; //print last login
				echo "<td><a href=\"$domain/login/resetPassword.php?resetKey=$rows->apiKey\" target=\"_top\">Link</a>"; //print password reset link 
				echo "<td><button class=\"deleteButtonUser\">X</button></td>";
			}
			echo "</tr>";
		}
		echo "</table>";
		require_once "../footer.php";
		?>
	</body>

	</html>

<?php
function generateRandomString($length){ //generates random strings
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function timeElapsed($dateTime) {
	$now = new DateTime;
	$ago = new DateTime($dateTime);
	$diff = $now->diff($ago);

	if($diff->y > 2000)
		return "-";
	$diff->w = floor($diff->d / 7);
	$diff->d -= $diff->w * 7;

	$string = array(
		'y' => 'year',
		'm' => 'month',
		'w' => 'week',
		'd' => 'day',
		'h' => 'hour',
		'i' => 'minute',
		's' => 'second',
	);
	foreach ($string as $k => &$v) {
		if ($diff->$k) {
			$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
		} else {
			unset($string[$k]);
		}
	}

	$string = array_slice($string, 0, 1);
	return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>