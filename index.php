<?php
define ('INPHP', 1);

error_reporting (0);
ini_set('display_errors', 0);

require_once ("config.php");
require_once ("mysql.php");
require_once ("essentials.php");
$mysql = new Mysql();

// inventaire des villes en statique
$script = '';
$body = '';

$width = 768;
$zoomlevel = 6;
$embed = (int)$_GET["embed"];
if ($embed) {
    $width = 594;
    $zoomlevel = 5;
}

$mapcenter = '46.7976380,1.9137490';

$geocode = '';
$liste = array();

// update db
file_get_contents($config["basehref"]."/twitter23.php?silent=true");

$mysql->query ("SELECT DISTINCT `city`, `lat`, `lng` FROM `".$config["sql"]["tableprefix"]."cache` WHERE `city` != '' AND `lat` != '' AND `lng` != ''");
$results = $mysql->result;

for ($i=0; $i<count($results); $i++) {
    $result = $results[$i];
    // build list
    $tweets = '';
    $mysql->query ("SELECT * FROM `".$config["sql"]["tableprefix"]."cache` WHERE `lat` = '".$result->lat."' AND `lng` = '".$result->lng."' ORDER BY `created_at` DESC");
    foreach ($mysql->result as $tweet) {
        $tweets .= '<li><strong>'.$tweet->from_user.'</strong> '.text2link(($tweet->text)).' <em>'.getTimeAgo($tweet->created_at).'</em></li>';
    }
    $liste[$result->city] = '<li onclick="javascript: openWin ('.$i.')">'.$result->city.' ('.$mysql->num_rows.')</li>';
    $script .= '
markers['.$i.'] = new google.maps.Marker({
      position:  new google.maps.LatLng('.$result->lat.",".$result->lng.'),
          icon: \'picto_carte2.png\',
      map: map, 
      title:"'.$result->city.'"
  });  
infowindows['.$i.'] = new google.maps.InfoWindow({
    content: "<div style=\"font-family:Arial,sans-serif; font-size:13px;\" class=\"iwTweets\"><p>'.$result->city.'</p><ul><p>'.$mysql->num_rows.' tweet'.($mysql->num_rows>1?'s':'').'</p>'.(addslashes($tweets)).' </ul></div>"
});
google.maps.event.addListener(markers['.$i.'], \'click\', function() {
    openWin ('.$i.')
});

';
}
ksort ($liste);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript">

var openWindow = false;
var markers = [];
var infowindows = [];
var map;
var geocoder;

function initialize() {

    mapLatLng = new google.maps.LatLng(<?php echo ($mapcenter); ?>);
    var myOptions = {
      zoom: <?php echo ($zoomlevel); ?>,
      center: mapLatLng,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControl: false,
      scrollwheel: false,
      streetViewControl: false
    };
    geocoder = new google.maps.Geocoder();
    map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
	$('#location').focus();

    <?php echo $script;?>
}
function openWin (index) {
    var win = infowindows[index];
    var marker = markers[index];
      if (openWindow) {openWindow.close();}
      win.open(map,marker);
      openWindow = win;
}
function jumpTo(location) {
   geocoder.geocode({ address: location }, function(results, status) {
	if (results[0]) {
    	map.fitBounds(results[0].geometry.viewport);
	}
   });
   return false;
 }
</script>
<style type="text/css">
.iwTweets {font-size: 0.8em;}
 body ul {padding: 0px; margin: 0px;}
 ul li {padding: 0px; margin: 0px; list-style-type: none;}
 ul li {padding-top: 4px; list-style-type: none;}
.iwTweets p {font-weight: bold; font-size: 1.4em;}
.iwTweets ul p {font-weight: normal; font-size: 1em;}
#liste {background-color:white;
    font-size:0.9em;
    height:590px;
    padding:5px;
    display: block;
    float: left;
    width:100px;
    overflow: auto;
}
#liste li {font-size: 0.9em;cursor: pointer;}

#liste form {padding-top:20px;font-size: 0.9em;}
#liste input {width: 80px;font-size: 0.8em;}

#map_canvas {
    width: <?php echo ($width-110) ?>px;
    height: 600px;
    display: block;
    float: left;
}
#container{
    width: <?php echo $width ?>px;
    height: 600px;
    border: solid black 1px;
}
</style>
<base target="_blank">
</head>
<body onload="initialize()">
    <div id="container">
      <div id="map_canvas"></div>
      <div id="liste" style="font-family:Arial,sans-serif; font-size:13px;"><p>slogans twittés</p>
          <ul><?php echo implode($liste); ?></ul>
          <form id="searchform" action="" onSubmit="javascript: return jumpTo(this.location.value);">Recherche (ville):<br />
        <input name="location" id="location" type="text"/>
        <input type="submit" id="search" value="rechercher" />
        </form>
      </div>
    </div>
  <?php echo $body; ?>
</body>
</html>