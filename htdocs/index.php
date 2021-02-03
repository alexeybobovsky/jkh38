<?php
//global $uri;
/*
if($_SERVER['SERVER_NAME']!='gorod-avto.com')www.gorod-avto.com www.xn----8sbgbf3daemz.xn--p1ai xn----8sbgbf3daemz.xn--p1ai xn--80aebe3cadlw.xn--p1ai
	echo $_SERVER['SERVER_NAME'];*/
//print_r($_SERVER);
/*var_dump($_GET);
var_dump($_POST); */
$skip=0;
if(isset($_GET['uri']))
	$uri = $_GET['uri'];
elseif(isset($_POST['uri']))
	$uri = $_POST['uri']; 
//	echo $uri.'<br>';
switch ($_SERVER['SERVER_NAME'])
	{
	case 'jkh38.ru':
		{
		}	
	case 'www.jkh38.ru':
		{
		}	
	case 'jkk-38.club':
		{
//		$uri = $_SERVER['REQUEST_URI'];
//		echo $uri.'<br>';
//		session_start();	
		}	
	case 'www.jkk-38.club':
		{
//		$uri = $_SERVER['REQUEST_URI'];
//		echo $uri.'<br>';
//		session_start();	
//		echo 'jkh-38';
		}
	case '51.15.142.59':
		{
//		$uri = $_SERVER['REQUEST_URI'];
//		echo $uri.'<br>';
//		session_start();	
		}
	case 'localhost':
		{
		$uri = $_SERVER['REQUEST_URI'];
//		echo $uri.'<br>';
//		session_start();	
		}
	case 'www.gisdo.ru':
		{
		if (!isset($uri))
			{
//			echo 'www.gisdo.ru : uri = NULL';
			$uri = 0;
			}
/*		if((strlen($uri)>3)&&($uri)&&((strpos($uri, "/login/")===false)&&(strpos($uri, "/404/")===false)&&(strpos($uri, "/spddl/")===false))&&(strrpos( $uri, '/') == ($strlen = strlen($uri)-1)))
			{
//			echo 'uri = '.$uri;
			$newUri = substr($uri, 0, $strlen);
//			echo '301 -> '.$newUri;
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: http://".$_SERVER['SERVER_NAME'].$newUri);
			$skip = 1;
			}
		if(!$skip)
			{*/
//				echo '<br>_SERVER[REQUEST_URI] = '.$_SERVER['REQUEST_URI'];
			if ($uri)
				{
//				echo $uri;
//				echo '<br>uri = '.$uri;
				$post = explode('/', $uri);
				unset($post[0]);
				}
			else
				{
				$post = 0;
				}		
			include('../index/index.php');
			/*if (($_SERVER['REQUEST_URI'])&&
					((strpos($_SERVER['REQUEST_URI'], "index.php?option=")!==false)||
					(strpos($_SERVER['REQUEST_URI'], "index.php?article="))||
					(strpos($_SERVER['REQUEST_URI'], "index.php?Itemid="))||
					(strpos($_SERVER['REQUEST_URI'], "index.php?category_id="))))
				{
				$uri = $_SERVER['REQUEST_URI'];
				$oldSupport = true;
				include('../index/oldSupport.php');
				}
			else
				{
				include('../index/index.php');
				}
				
			}*/
		} break;
	default :
		{
		
//		echo 'http://www.gorod-avto.com'.$_SERVER['REQUEST_URI'];		
		$REDIRECT = 'http://'.$_SERVER['HTTP_HOST'];		
		header('Location: '.$REDIRECT);			
		} break;
	}	
?>
