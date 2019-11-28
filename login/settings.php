<?php
session_start();
require_once "sql.php";
require_once 'functions.php';
if (!isset($_SESSION["userId"]) || !checkLogin($_SESSION["userId"])) {
	header('Location: ../login/');
	die();
}
$userId = $_SESSION["userId"];

require_once "../header.php";
?>

<title>Site Settings</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>

<body>
	<label for="noMasking">noMasking</label>
	<input type="checkbox" id="noMasking" class="cookieSettings"><br>
	<label for="showTaglist">showTaglist</label>
	<input type="checkbox" id="showTaglist" class="cookieSettings"><br>

	<button onclick="saveCookies();">Save</button>
	<script>
		function saveCookies() {
			document.querySelectorAll(".cookieSettings").forEach(setting => {
				document.cookie = setting.id + "=" + setting.checked + " ; path=/";
			});
			alert("saved");
		}
		let settings = document.cookie.split("; ").map(setting => {
			setting = setting.split("=");
			return object = {
				name: setting[0],
				value: setting[1]
			};
		}).filter(setting => setting.name != "PHPSESSID");
		settings.forEach(setting => {
			document.getElementById(setting.name).checked = setting.value == "true";
		});
	</script>
	<?php

	require_once "../footer.php";
	?>
</body>

</html>