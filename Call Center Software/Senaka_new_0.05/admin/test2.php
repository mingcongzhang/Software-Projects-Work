<?php

//echo __DIR__;

//this is a testing!
$ACTIVATION_DIR = 'http://'.$_SERVER['SERVER_NAME'].'/~mingcong/activate.php';
$tokens = explode('/',rawurldecode(__DIR__));
echo $tokens[sizeof($tokens)-3]."<br/>";


$test1_dir = 'http://'.$_SERVER['SERVER_NAME'];
echo $test1_dir;

echo DIRECTORY_SEPARATOR;

$result = $ACTIVATION_DIR.DIRECTORY_SEPARATOR.'activate.php';
echo $result;
?>
