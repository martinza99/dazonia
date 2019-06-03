<?php
    require_once "login/sql.php";
echo'
<pre>
{
    "Version": "12.4.1",
    "Name": "localhost",
    "DestinationType": "ImageUploader",
    "RequestMethod": "POST",
    "RequestURL": "'.$domain.'/upload.php",
    "Body": "MultipartFormData",
    "Arguments": {
        "username": "USERNAME",
        "password": "PASSWORD",
        "hideLink": "true"
    },
    "FileFormName": "file"
}
</pre>';
?>