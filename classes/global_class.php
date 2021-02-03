<?php
require_once("config.inc.php");
require_once("MySQLi_class.php");
require_once("Nav_class.php");
require_once("User_class.php");
require_once("ACL_class.php");
require_once("Message_class.php");
require_once("Rout_class.php");
/**************************************/
require_once("GetContent_class.php");
require_once("GetAdmin_class.php");
/**************************************/
require_once("SetAdmin_class.php");
/**************************************/
require_once("TPL_class.php");
session_start();
if(!isset($_SESSION['USER']))	
	{
	$CNT = new GetContent;
	$USER = new CurrentUser($_SERVER['REMOTE_ADDR'], $CNT->GetLangDefault());
	$_SESSION['USER'] = $USER;
//	print_r($USER);
//	= ;
//	session_register('USER');
	}
else 
	$USER = $_SESSION['USER'];
if(!isset($_SESSION['NAV']))	
//if(!session_is_registered('NAV'))
	{
	$NAV = new Navigation ();
//	session_register('NAV');
	$_SESSION['NAV'] = $NAV;
	}
else
	$NAV = $_SESSION['NAV'];
?>