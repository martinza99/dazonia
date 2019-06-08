<?php
    session_start();
    require_once 'sql.php';
    require_once 'functions.php';
    if(!isset($_SESSION["userId"])||!checkLogin($_SESSION["userId"])){
        header('Location: login/');
        die();
    }

    $userId = $_SESSION["userId"];
    $sql = $conn->prepare("SELECT users.* FROM `users` WHERE `id` = ?");
    $sql->bind_param("i",$userId);
    $sql->execute();
    $result = $sql->get_result();
    $rows = $result->fetch_assoc();
    $username = $rows['name'];
    $apiKey = $rows['apiKey'];

    if(isset($_POST["action"])){
        switch ($_POST["action"]){
            case "reset": 
                $apiKey = generateRandomString(64);
                $sql = $conn->prepare("UPDATE users SET apiKey = ? WHERE id = ?");
                $sql->bind_param("si",$apiKey,$userId);
                $sql->execute();
                header('Location: api.php');
                die("Key reset");
                break;
        }
    }

    require_once "../header.php";
    echo '
        <title>API-Key</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="../main.js?'.$hash.'"></script>
    </head>
    <body>
        <div>
            <span>API-Token:</span>
            <code class="apiKey">'.$apiKey.'</code>
            <button onclick="copyKey(\'apiKey\');" style="height:22px;"><i class="glyphicon glyphicon-copy"></i></button>
            <br><br>
            <span>ShareX custom uploader config:</span>
            <button onclick="copyKey(\'shareX\');" style="height:22px;"><i class="glyphicon glyphicon-copy"></i></button>
            <button onclick="copyKey(\'shareX\');" style="height:22px;"  type="button" data-toggle="collapse" data-target="#collapseGuide" aria-expanded="false" aria-controls="collapseExample"><i class="glyphicon glyphicon-question-sign"></i></button>
            <div class="collapse" id="collapseGuide">
                <div class="card card-body shareXguide" id="text">
                    <ul>
                        <li>Get ShareX on <a href="https://getsharex.com/" target="_blank">https://getsharex.com/</a></li>
                        <li>Click the clipboard button</li>
                        <li>Open ShareX</li>
                        <li>Go to <kbd>Destinations</kbd> → <kbd>Image uploader</kbd></li>
                        <li>Click <kbd>Custom image uploader</kbd></li>
                        <li>Go to <kbd>Destinations</kbd> → <kbd>Custom uploader settings...</kbd></li>
                        <li>Click <kbd>Import</kbd></li>
                        <li>Click <kbd>From Clipboard...</kbd></li>
                        <li>Right below that choose <kbd>Dazonia</kbd> as <kbd>Image uploader</kbd></li>
                    </ul>
                </div>
            </div>
        <div>
<pre class="shareX"><code>{
    "Version": "12.4.1",
    "Name": "Dazonia",
    "DestinationType": "ImageUploader",
    "RequestMethod": "POST",
    "RequestURL": "'.$_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["HTTP_HOST"].'/upload.php",
    "Body": "MultipartFormData",
    "Arguments": {
        "key": "'.$apiKey.'"
    },
    "FileFormName": "file"
}</code></pre>
            <form action="api.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" value="reset" name="action">
                <input type="submit" value="Reset Key">
            </form>
        </div>
        <br><br><br><br><br>
        <div class="bottom">';
    require_once "../footer.php";
?>
    <script>
        function copyKey(_target){
            let node = document.querySelector("."+_target);
            if (document.body.createTextRange) {
                const range = document.body.createTextRange();
                range.moveToElementText(node);
                range.select();
            } else if (window.getSelection) {
                const selection = window.getSelection();
                const range = document.createRange();
                range.selectNodeContents(node);
                selection.removeAllRanges();
                selection.addRange(range);
                document.execCommand("copy");
                selection.removeAllRanges();
            } else
                console.warn("Could not select text in node: Unsupported browser.");
        }
    </script>
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