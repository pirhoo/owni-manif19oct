<?php
if (!defined('INPHP')) {die("This file cannot be accessed directly.");}

function input2query ($value,$value_is_html=false) {
	$value = trim($value);
	if (!$value_is_html) {
		$value = strip_tags($value);
	}
	if(!get_magic_quotes_gpc()) {$value = addslashes($value);}
	return $value;
}

function input2form ($value,$value_is_html=false) {
	$value = trim(stripslashes($value));
	if(get_magic_quotes_gpc()) {$value = stripslashes($value);}
	if (!$value_is_html) {
		$value = htmlentities($value);
	}
	else {
		$value = strip_tags($value);
	}
	return $value;
}

function sql2html ($value,$value_is_html=false) {
	$value = trim(stripslashes($value));
	if (!$value_is_html) {
		$value = nl2br(htmlentities($value));
	}
	return $value;
}

function sql2form ($value,$edit_asis=true) {
	$value = trim(stripslashes($value));
	if ($edit_asis) {
		$value = htmlentities($value);
	}
	return $value;
}

function formatName ($var,$nospace = " ") {
	$tobereplaced = array("�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","�","-","/",";",":","�","'","\"");
	$replaceby = array("O","O","I","I","A","E","E","E","C","e","e","e","o","o","i","i","a","c"," "," "," "," ",""," "," ");
	$var = str_replace($tobereplaced,$replaceby,trim($var));
	return str_replace(" ",$nospace,$var);
}

function generate_uid ($length=32) {
	$uid = md5(time()*rand());
	if (!empty($length) AND $length<strlen($uid)) {
		$uid=substr($uid,rand(0,(32-$length)),$length);
	}
	return $uid;
}

function sanitize_string( $string ) {
	$gstring = strtolower(html_entity_decode(htmlspecialchars_decode(trim($string))));
	$tobereplaced = array("�","�","�","�","�","�","�","�","�","�","�", "�", "�");
	$replaceby = array("e","e","e","e","o","o","i","i","a","a","c", "u", "u");
	$gstring = str_replace($tobereplaced,$replaceby,$gstring);
	for ($i=0; $i<strlen($string); $i++) {
		if (ereg("([^[:alnum:]]+)", substr ($string, $i, 1), $regs)) {
			$gstring = str_ireplace ($regs[1], "-", $gstring);
		}
	}
	$gstring = trim($gstring, '.-_');
	return $gstring;
}

function User2link ($text) {
	if (ereg("^@([_A-Za-z0-9]+)",$text, $regs)) {
		foreach ($regs as $reg) {
			if (ereg("^([_A-Za-z0-9]+)",$reg)) $text = str_replace ($reg, "<a href=\"http://www.twitter.com/".$reg."\">{$reg}</a>", $text);
		}
	}
	return $text;
}

function URL2link ($text) {
	return ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $text);
}

function text2link($str='') {
        if($str=='' or !preg_match('/(ftp|http|www\.|@)/i', $str)) {
                return $str;
        }

        $str = preg_replace("/([ \t]|^)www\./i", "\\1http://www.", $str);
        $str = preg_replace("/([ \t]|^)ftp\./i", "\\1ftp://ftp.", $str);
        $str = preg_replace("/(http:\/\/[^ )\r\n!]+)/i", "<a href=\"\\1\">\\1</a>", $str);
        $str = preg_replace("/(https:\/\/[^ )\r\n!]+)/i", "<a href=\"\\1\">\\1</a>", $str);
        $str = preg_replace("/(ftp:\/\/[^ )\r\n!]+)/i", "<a href=\"\\1\">\\1</a>", $str);
        $str = preg_replace("/([-a-z0-9_]+(\.[_a-z0-9-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)+))/i", "<a href=\"mailto:\\1\">\\1</a>", $str);

        return $str;
}  

function getTimeAgo ($created_at=0) {
	if (!empty($created_at)) {
		$timediff = time()-strtotime($created_at);
		if ($timediff < 60) {
			return "less than a minute ago";
		}
		else {
			if ($timediff < 120) {
				return "about a minute ago";
			}
			else {
				if ($timediff < (60 * 60)) {
					return (int)($timediff / 60)." minutes ago";
				}
				else {
					if ($timediff < (120 * 60)) {
						return  "about an hour ago";
					}
					else {
						if ($timediff < (24 * 60 * 60)) {
							return "about " + (int)($timediff / 3600). " hours ago";
						}
						else {
							if ($timediff < (48 * 60 * 60)) {
								return "1 day ago";
							}
							else {
								return (int)($timediff / 86400)." days ago";
							}
						}
					}
				}
			}
		}
	}
}
?>