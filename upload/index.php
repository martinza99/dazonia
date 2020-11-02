<?php
if($_SERVER["REQUEST_METHOD"] === "GET")
    require_once("uploadGet.php");
else
require_once("uploadPost.php");
