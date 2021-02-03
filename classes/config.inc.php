<?php
if(($_SERVER['SERVER_NAME'] == 'localhost')||($_SERVER['SERVER_NAME'] == 'www.jkh38.ru')||($_SERVER['SERVER_NAME'] == '84.201.133.157')||($_SERVER['SERVER_NAME'] == 'jkh38.ru'))
{
	$SQLhost = "localhost";
	$SQLdb = "gis";
	$SQLus = "gis";
	$SQLpw = "1kbt7lbc";
}
else
{
	$SQLhost = "db40.valuehost.ru";
	$SQLdb = "alexeybobo_gis";
	$SQLus = "alexeybobo_gis";
	$SQLpw = "";
}
//echo 'adsad';
$tplDir = 'gis/';
$siteName = 'www.jkh38.ru';
$messBody = '';
setlocale (LC_TIME, "");
Error_Reporting(E_ALL & ~E_NOTICE);
?>
