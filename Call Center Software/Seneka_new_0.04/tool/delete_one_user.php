
<?php

define('INCLUDE_CHECK',true);

require_once("includes/config_mongo.php");
$collection_user->remove(array('firstname'=>"gsgrehergergregerger"), array("justOne" => true, "w" => 1));

?>