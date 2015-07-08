<?php

$mongo_user = "mingcong";
$mongo_pass = "test12";
$mongo_url = "ds039778.mongolab.com:39778/solidbase";

// Get date in this format: 2012-08-05 08:00:00
date_default_timezone_set('America/New_York');
$date = date('Y-m-d H:i:s');
echo "current time: ".$date."\n\n";

$table = "calls";
$db = "solidbase";
try {
        $connection = new Mongo('mongodb://'.$mongo_user.':'.$mongo_pass.'@'.$mongo_url);
        $database   = $connection->selectDB($db);
        $collection = $database->selectCollection($table);
}
catch(MongoConnectionException $e) {
        die("Failed to connect to database ".$e->getMessage());
}

$cursor = $collection->find();

while ($cursor->hasNext()):
	$task = $cursor->getNext(); 
	print_r($task);
/*    [c_sid] => CAd959f2d3796c33c180454d19031c11f1
    [parent_c_sid] =>
    [a_sid] => ACa735d99f0dd5b639994ec06fe5fe311a
    [from] => +19543027144
    [to] => +19547343728
    [status] => completed
    [start_time] => Tue, 13 Aug 2013 18:56:23 +0000
    [end_time] => Tue, 13 Aug 2013 18:56:46 +0000
    [duration] => 23
    [direction] => outbound-api

*/
endwhile;

?>

