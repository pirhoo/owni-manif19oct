<?php

define ('INPHP', 1);

require_once ("config.php");
require_once ("mysql.php");
require_once ("essentials.php");
$mysql = new Mysql();

$geocode = trim($_GET["geocode"]);

$output = new stdClass();
$output->status = "200 OK";
$output->error = "";
$output->message = "";
$output->type = "";
$output->query = "";
$output->results = array();


if (!empty($geocode)) {
    $output->type = "city";
    $output->query = $geocode;
    list ($lat, $lng) = explode (",", $geocode);
    $mysql->query ("SELECT * FROM `".$config["sql"]["tableprefix"]."cache` WHERE `lat` = '$lat' AND `lng` = '$lng' ORDER BY `created_at` DESC");
    $results = $mysql->result;
    for($i=0;$i<$mysql->num_rows; $i++) {
        $output->results[$i] = $mysql->result[$i];
        $output->results[$i]->time_ago = getTimeAgo($mysql->result[$i]->created_at);
    }
}
else {
    $output->update = file_get_contents($config["basehref"]."/twitter23.php?silent=true");
    
    if ($mysql->query ("SELECT DISTINCT `city`, `lat`, `lng` FROM `".$config["sql"]["tableprefix"]."cache` WHERE 1")) {
        $results = $mysql->result;

        $output->type = "count";


         $i=0;
         foreach ($results as $result) {
            if (!empty($result->city)) {
                $output->results[$i] = new stdClass();
                $output->results[$i]->city = $result->city;
                $output->results[$i]->lat = $result->lat;
                $output->results[$i]->lng = $result->lng;
                list ($output->results[$i]->count) = $mysql->get_row ("SELECT DISTINCT SUM(1) FROM `".$config["sql"]["tableprefix"]."cache` WHERE `city` = '".input2query($result->city)."'");
                $i++;
            }
        }

    }
    else {$message = $mysql->error; $output->error = "true";}
}
echo (json_encode($output));
?>
