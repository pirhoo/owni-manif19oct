<?php
define ('INPHP', 1);

require_once ("config.php");
require_once ("mysql.php");
require_once ("essentials.php");

$mysql = new Mysql();

$cities = array("brive", "bruay", "cahors", "carcassonne", "chambery", "chartres", "chateaudun", "chateauroux", "chaumont", "cholet", "digne", "dreux","Poitiers", "Sens", "Avignon", "Toulon", "Draguignan", "Montauban", "Albi", "Castres", "Abbeville", "Ham", "Doullens", "Niort", "Bessuire", "Thouars", "Rouen", "Lillebonne", "Fecamp", "Eu", "Dieppe", "Annecy", "Colmar", "Bayonne", "Calais", "Bethune","LeHavre", "belfort", "paris", "bordeaux", "marseille", "lyon", "lille", "nantes", "biarritz", "toulouse", "strasbourg", "brest", "rennes", "orleans", "grenoble", "nice", "montpellier", "perpignan", "bergerac", "limoges", "reims", "dijon", "arras", "amiens", "besancon", "le mans", "laval", "tours", "angers", "Pau", "Clermont", "Nancy", "Metz", "Mulhouse", "Cannes", "Narbonne", "nimes");
$geocodes = array();
$max_id = "";
$since_id = "";
$updates = 0;

// spare google
$mysql->query ("SELECT DISTINCT `city`, `lat`, `lng` FROM `".$config["sql"]["tableprefix"]."cache` WHERE 1");
foreach ($mysql->result as $result) {
    $geocodes[$result->city] = $result->lat.",".$result->lng;
}

// lecture de dernier tweet
list ($since_id) = $mysql->get_row ("SELECT * FROM `".$config["sql"]["tableprefix"]."cache` WHERE 1 ORDER BY `created_at` DESC LIMIT 1");


$feed = get_feed ("?q=%2319oct+%23slogan&since_id=".$since_id);


store_incache($feed);

function get_geocode ($query) {
    $ggresponse = json_decode(@file_get_contents ("http://maps.google.com/maps/geo?q=".urlencode($query)."&output=json"));
    if ($ggresponse->Status->code == 200 AND count($ggresponse->Placemark == 1)) {
            $placemark = $ggresponse->Placemark[0];
            $lat = $placemark->Point->coordinates[1];
            $lng = $placemark->Point->coordinates[0];
            $city = $placemark->AddressDetails->Country->AdministrativeArea->SubAdministrativeArea->Locality->LocalityName;
    }
    else {
        return false;
    }
    return "$lat,$lng,$city";
}

function store_incache ($feed) {
    global $mysql, $cities, $updates, $geocodes;
    if (count($feed->results)>0) {
        foreach ($feed->results as $result) {
            // search for a ciy tag
            foreach ($cities as $city) {
                if (stristr($result->text, "#".$city)) {
                    $result->city = $city;
                    if (empty($geocodes[$city])) {
                        $geocodes[$city] = get_geocode ($city.", France");
                    }
                    list ($result->lat, $result->lng, $result->city) = explode(",", $geocodes[$city]);
                    break;
                }
            }
            
            if (!$mysql->query ("INSERT INTO  `".$mysql->server["sql"]["tableprefix"]."cache` (
                `id` ,
                `created_at` ,
                `from_user_id` ,
                `from_user` ,
                `to_user_id` ,
                `iso_language_code` ,
                `metadata` ,
                `geo` ,
                `text` ,
                `profile_image_url` ,
                `city`, `lat`, `lng`
                )
                VALUES ('{$result->id}',  '".date ("Y-m-d H:i:s", strtotime($result->created_at))."',  '{$result->from_user_id}', '".input2query($result->from_user)."', '{$result->to_user_id}',  '{$result->iso_language_code}', '".serialize($result->metadata)."',  '".serialize($result->geo)."',  '".input2query($result->text)."',  '".input2query($result->profile_image_url)."',  '".input2query($result->city)."',  '".input2query($result->lat)."',  '".input2query($result->lng)."');
                ")) {
                  //var_dump ($mysql); exit;
                }
            $updates++;
        }
        if ($feed->next_page != "") {
            $feed = get_feed ($feed->next_page);
            store_incache ($feed);
        }
    }
    return true;
}

function get_feed ($query) {
    return json_decode(@file_get_contents("http://search.twitter.com/search.json".$query));
}

if (empty($_GET["silent"])) {
    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>'.$updates ." tweets ajout&eacute;s";

    $mysql->query ("SELECT * FROM `".$config["sql"]["tableprefix"]."cache` WHERE 1 ORDER BY `created_at` DESC");
    foreach ($mysql->result as $result) {
       // $result->text = User2link(URL2link($result->text));
        $result->time_ago = getTimeAgo($result->created_at);
        echo ('<div>
          <p><strong>'.htmlspecialchars($result->from_user).'</strong>&nbsp;'.($result->text).'&nbsp;<em>'.$result->time_ago.'</em></p>
    </div>
    ');
    echo '    </body>
</html>';
    }   
}
else {
    echo "ok";
}
?>
