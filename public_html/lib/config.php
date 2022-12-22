<?php

$ini = @parse_ini_file(".env");
if($ini && isset($ini["DB_URL"])){
    //load local .env file
    $db_url = parse_url($ini["DB_URL"]);
}
else{
    //load from heroku env variables
    $db_url      = parse_url(getenv("DB_URL"));
}
$dbhost   = 'us-cdbr-east-06.cleardb.net';
$dbuser = 'bf5c9c52787627';
$dbpass = '0031a94e';
$dbdatabase       = 'heroku_5d4f3ad9672cb2d';
?>