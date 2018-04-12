<?php
//print(99>>2);
//echo $_SERVER['HTTP_ACCEPT_LANGUAGE'];
$test = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
var_dump($test['0']);

?>