<?php
//print(99>>2);
var_dump($_SERVER['HTTP_ACCEPT_LANGUAGE']);
$test = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
var_dump($test['0']);
?>