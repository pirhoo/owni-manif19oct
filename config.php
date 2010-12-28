<?php
if (!defined('INPHP')) {die("This file cannot be accessed directly.");}

// SERVER configs

$config = array();

/*
* PROD
*/

$config["prod"]["status"] = "prod";
$config["prod"]["server_name"] = "app.owni.fr";
$config["prod"]["basehref"] = "http://app.owni.fr/manif19102010/";
$config["prod"]["email"] = "";
$config["prod"]["email_sender"] = $config["prod"]["server_name"];
$config["prod"]["admin_email"] = $config["prod"]["email"];

// Parametres mySQL 
$config["prod"]["sql"] = array();
$config["prod"]["sql"]["server"] = "localhost"; // Serveur mySQL
$config["prod"]["sql"]["base"] = "appowni"; // Base de donnees mySQL
$config["prod"]["sql"]["login"] = "appowni"; // Login de connection a mySQL
$config["prod"]["sql"]["password"] = "pheePh3Ienga"; // Mot de passe pour mySQL
$config["prod"]["sql"]["tableprefix"] = "twitter_";
$config["prod"]['smtp_host'] = 'localhost';
$config["prod"]['smtp_port'] = 25;
$config["prod"]['smtp_username'] = '';
$config["prod"]['smtp_password'] = '';

/*
* ADMIN
*/

$config["admin"]["status"] = "admin";
$config["admin"]["server_name"] = "owniapps.dev";
$config["admin"]["basehref"] = "http://owniapps.dev/manif19oct";
$config["admin"]["email"] = "ja@jeromealexandre.com";
$config["admin"]["email_sender"] = $config["admin"]["server_name"];
$config["admin"]["admin_email"] = $config["admin"]["email"];

// Parametres mySQL 
$config["admin"]["sql"] = array();
$config["admin"]["sql"]["server"] = "localhost"; // Serveur mySQL
$config["admin"]["sql"]["base"] = "appowni"; // Base de donnees mySQL
$config["admin"]["sql"]["login"] = "root"; // Login de connection a mySQL
$config["admin"]["sql"]["password"] = "k1387069"; // Mot de passe pour mySQL
$config["admin"]["sql"]["tableprefix"] = "twitter_";
$config["admin"]['smtp_host'] = 'localhost';
$config["admin"]['smtp_port'] = 25;
$config["admin"]['smtp_username'] = '';
$config["admin"]['smtp_password'] = '';

$config["admin"]["flickr_key"] = 'dfaf99a7617bf8e3644137d2d663ef1a';
$config["admin"]["flickr_secret"] = 'd8dde4390ee36b69';

/*
* DEV
*/

$config["dev"]["status"] = "dev";
$config["dev"]["server_name"] = "owniapps.dev";
$config["dev"]["basehref"] = "http://owniapps.dev/manif19oct";
$config["dev"]["email"] = "ja@jeromealexandre.com";
$config["dev"]["email_sender"] = $config["dev"]["server_name"];
$config["dev"]["admin_email"] = $config["dev"]["email"];
$config["dev"]["date_format"] = "d.m.Y @ H:i:s";

// Parametres mySQL
$config["dev"]["sql"] = array();
$config["dev"]["sql"]["server"] = "localhost"; // Serveur mySQL
$config["dev"]["sql"]["base"] = "appowni"; // Base de donnees mySQL
$config["dev"]["sql"]["login"] = "root"; // Login de connection a mySQL
$config["dev"]["sql"]["password"] = "k1387069"; // Mot de passe pour mySQL
$config["dev"]["sql"]["tableprefix"] = "twitter_";

// SMTP
$config["dev"]['smtp_host'] = 'localhost';
$config["dev"]['smtp_port'] = 25;
$config["dev"]['smtp_username'] = '';
$config["dev"]['smtp_password'] = '';

$config["dev"]["flickr_key"] = 'dfaf99a7617bf8e3644137d2d663ef1a';
$config["dev"]["flickr_secret"] = 'd8dde4390ee36b69';

// Where are we?
if (!defined('CONFIG_STATUS')) {
	foreach ($config as $status => $conf) {
		$http_host = ($_SERVER["HTTP_HOST"])?$_SERVER["HTTP_HOST"]:$_SERVER['SERVER_NAME'];
		if (stristr($http_host,$conf["server_name"])) {
			define('CONFIG_STATUS', $status);
			break;
		}
	}
}

$config = $config[CONFIG_STATUS];

?>
