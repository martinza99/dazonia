<?php
if ($_SERVER["REQUEST_METHOD"] === "GET")
    require_once(__DIR__ . "/registerGet.php");
else if ($_SERVER["REQUEST_METHOD"] === "POST")
    require_once(__DIR__ . "/registerPost.php");
else {
    http_response_code(405);
    die("Unsupported Request Method");
}
