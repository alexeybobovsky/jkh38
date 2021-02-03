<?php
require_once('../classes/global_class.php');
$CNT = new GetContent;
$CONST = $CNT->GetAllConfig();
$ROUT= new Routine;
/*echo 'dsa';
print_r($USER);*/
$USER->CheckUserStatus();
/*
if($USER->id>1)
	Error_Reporting(0);
*/ 

//$REDIRECT = $messBody = '';
//$tplDir = 'tmp/';
$USER->curCity['id'] = 1;
$USER->curCity['name'] = 'Иркутск';
$TPL= new TEMPLATE;
if(isset($_SESSION['editor']))
	unset($_SESSION['editor']);
$ACL = new ACL_class;
/*******************************************************/
/*проверка текущего статуса объектов относительно времени*/

$ACNT = new GetAdmin;
$ASET = new SetAdmin;


//print_r($_POST);
if($usersList = $ACNT->getAllQueryedUsers()) 	$ASET->CheckQueryedUsersStatus($usersList, $CONST['regConfirmExpTimе']);
if($remUsersList = $ACNT->getAllRemindedUsers())$ASET->CheckRemindedUsersStatus($remUsersList, $CONST['passwordRemindActivationTime']);



/*******************************************************/
$HISTORY_SKIP = 0;
$templates = array();
$SMRT = array();
//include('header.php');
//print_r($_SESSION);
include('menu.php');

if(isset($_SESSION['MESSAGE']))
	{
/*	print_r($_SERVER);
	unset($_SESSION['MESSAGE']);;*/
	/*временно убрано - надо разбираться почему по 6 раз подряд срабатывает*/
//	$_SESSION['MESSAGE_404'] = $_SESSION['MESSAGE'];
	unset($_SESSION['MESSAGE']);
//	$REDIRECT = 'http://'.$_SERVER['HTTP_HOST'].'/404/';
/*	print_r($_SESSION['MESSAGE']); 
	unset($_SESSION['MESSAGE']);
	$REDIRECT = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];*/
	$REDIRECT = 'http://'.$_SERVER['HTTP_HOST'].'/404/';
	}
else
	{	
//	echo '0';
	if(($allMenu['handler'])&&($ACL->GetClosedParentRight($allMenu['curNodeId'])))
		{
		//echo $allMenu['handler'];
		$f = $CNT->GetChildren1Level($allMenu['curNodeId'], 0, 0);
		$denied = 0;
		if(is_array($post))
			$start = array_search ($allMenu['curNodeName'], $post);
		else
			$start = false;		
		if($start === false)
			$start = 0;
		for($i=0; $i<count($f); $i++)
			{
			for($m=$Start; $m<=count($post); $m++)
				{
				if(($f[$i]['catalog']['sc_name'] == $post[$m])&&($f[$i]['catalog']['sc_id'])&&(!$ACL->GetClosedParentRight($f[$i]['catalog']['sc_id'])))
					$denied++;
				}
			}
		if(!$denied)
			{
			include($allMenu['handler']);	
			}
		else
			$messBody=($CONST['debugMode'])?'Запрошенная Вами страница не найдена (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')':'Запрошенная Вами страница не найдена';			
		}
	elseif(($post[1]))
		{
//		echo $post[1];
		if($allMenu['lastLink'])
			{
			$tmpArr = explode('/', $allMenu['lastLink']);
			$lastLink =  $tmpArr[count($tmpArr)-2];
			}
		else
			$lastLink =  $post[1];
		$handler = $CNT->GetHandler($lastLink, -1 /*0*/);
//		echo '4';
//		print_r($handler);
		if(($handler['sc_handler'])&&($ACL->GetClosedParentRight($handler['sc_id'])))
			{
			
			$nodeLink = $CNT->GetCurBlockBody($handler['sc_id'], array('link'));
//			print_r($nodeLink);
			if((!isset($nodeLink['link']['value']))||((isset($nodeLink['link']['value']))&&($ROUT->UriCompare($nodeLink['link']['value'], $lastLink))))
				{
				$handlerId = $handler['sc_id'];
				if(is_file('../index/'.$handler['sc_handler']))
					include($handler['sc_handler']);
				else
					$messBody=($CONST['debugMode'])?'Запрошенная Вами страница не найдена (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')':'Запрошенная Вами страница не найдена';
//				print_r($handler);
				}
			else
				$messBody=($CONST['debugMode'])?'Запрошенная Вами страница не найдена (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')':'Запрошенная Вами страница не найдена';							
			}
		elseif($handler['sc_id'])
			{
//				echo '3';
//			print_r($handler);
			$firstHandler = '';
			$stop = 0;
			$parId = $handler['sc_id'];
			while(!$stop)
				{
				$child = $CNT->GetFirstChild($parId, 0, -1);
				if($child)
					{					
					$parents[] = $parId;
					if ($child['catalog']['sc_handler'])
						{
						$firstHandler = $child['catalog']['sc_handler'];
						$handlerId = $child['catalog']['sc_id'];
						$curId = $child['catalog']['sc_id'];
						$keyWord = $child['catalog']['sc_name'];
						$stop ++;
						}
					else
						{
						$parId = $child['catalog']['sc_id'];
						}
					}
				else
					{
					$stop ++;
					}
				}
			if(($firstHandler)&&($ACL->GetClosedParentRight($curId)))
				{
				for($i = 0; $i<count($menuItems); $i++)
					{
					for($l=0; $l<count($parents); $l++)
						{
						if($menuItems[$i]['catalog']['sc_id'] == $parents[$l])
							{
							$curMenu[$menuItems[$i]['level']] = $i;
							$curParent = $parents[$l];
							}						
						}
					if(($menuItems[$i]['catalog']['sc_name'] == $keyWord)&&($menuItems[$i]['catalog']['sc_parId']==$curParent ))
						{
						$curMenu[$menuItems[$i]['level']] = $i;
						}
					}
				$NAV->SetPos($curMenu, $history);
				$allMenu = $TPL->TPL_GetMenu($menuItems);				
				include($firstHandler);				
				}
			else
				$messBody=($CONST['debugMode'])?'Запрошенная Вами страница не найдена (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')':'Запрошенная Вами страница не найдена';
			}
		else{
			$messBody=($CONST['debugMode'])?'Запрошенная Вами страница не найдена (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')'.$uri:'Запрошенная Вами страница не найдена';
			$messBody .= print_r($_SERVER, true);	
			$messBody .= print_r($_SESSION, true);	
			
			}
		}
	elseif(!$post[1])
		{
		//echo '2';
		$handlerFirst = '';
		$parId = 0;
		$stop = 0;
		do
			{
			$child = array();			
			$child = $CNT->GetFirstChild($parId, 1, -1);
			if(is_array($child))
				{
				$handlerFirst = $child['catalog']['sc_handler'];
				$handlerId = $child['catalog']['sc_id'];
				$parId = $child['catalog']['sc_id'];
				$stop = 1;
				}
			else
				{
				$stop = 1;
				}
			}
		while(!$stop);
//		print_r($menuItems);
		if(($handlerFirst)&&($ACL->GetClosedParentRight($child['catalog']['sc_id'])))
			{
			for($i = 0; $i<count($menuItems); $i++)
				{
				if($menuItems[$i]['catalog']['sc_handler'] == $handlerFirst )
					{
					$curMenu[$menuItems[$i]['level']] = $i;
					}
				}
			$NAV->SetPos($curMenu, $history);
//				print_r($curMenu);
			$allMenu = $TPL->TPL_GetMenu($menuItems);				
			include($handlerFirst);
			}
		else
			$messBody=($CONST['debugMode'])?'Запрошенная Вами страница не найдена (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')':'Запрошенная Вами страница не найдена';			
		}
	else
		$messBody=($CONST['debugMode'])?'Запрошенная Вами страница не найдена (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')':'Запрошенная Вами страница не найдена';
	if($messBody)
		{
		//print_r($messBody);
		$MESS = new Message('Error', 'ERROR 404', $messBody, $NAV->GetPrewURI());											
		$_SESSION['MESSAGE'] = $MESS;		
//		$REDIRECT = 'http://'.$_SERVER['SERVER_NAME'].'/404/';
		$REDIRECT = 'http://'.$_SERVER['HTTP_HOST'].'/404/';
		}
	}
if(!$REDIRECT)
	{
	if((in_array($tplDir.'header.tpl', $templates))||
		(in_array($tplDir.'headerGB.tpl', $templates))||
		(in_array($tplDir.'headerNew.tpl', $templates))||
		(in_array($tplDir.'headerYM.tpl', $templates))||
		(in_array($tplDir.'headerSimple.tpl', $templates))||
		(in_array($tplDir.'headerPan.tpl', $templates))||
		(in_array($tplDir.'headerGB_YM_preload.tpl', $templates)))
		{
		if(!$USER->registered)
			{
			$login = 1; //$RTPL->showLogin();		
			$SMRT_TMP['name'] = 'login';
			$SMRT_TMP['body'] = $login;
			$SMRT['modules'][] = $SMRT_TMP;				
			}
		else
			{
			$SMRT_TMP['name'] = 'logout';
			$SMRT_TMP['body'] = 1;		
			$SMRT['modules'][] = $SMRT_TMP;	
			}
		}
			
	if (!$allMenu['handler'])
		$allMenu = $TPL->ResetMenu($allMenu);
		
	if((!$allMenu['lastTitle'])&&(!$MESS))
		{
		$title = $CNT->GetCurBlockBody($handlerId, array('title', 'caption'));
		$allMenu['lastTitle'] = (is_array($title['title']))?$title['title']['value']:$title['caption']['value'];
		}
	elseif(/*($messBody)||*/$MESS->Type == 'Error')
		{
		$allMenu['lastTitle'] = 'Ошибка!';
		}
	elseif(/*($messBody)||*/$MESS->Type == 'Info')
		{
		$allMenu['lastTitle'] = 'Информация';
		}
	if(count($templates))	
		{
		if($CONST['dinamicUTF8Convert'])
			{
			$charset = 'UTF-8';
			header('Content-type: text/html; charset='.$charset);
			}

	/*	echo '2 - <br>';*/
		if(!in_array('selfCloseBlank.tpl', $templates))	
			{
			if((!in_array($tplDir.'AdminHeader.tpl', $templates))&&
				(!in_array($tplDir.'AdminHeaderGB.tpl', $templates))&&
				(!in_array($tplDir.'mainPage.tpl', $templates))&&
				(!$mapTpl = in_array($tplDir.'mapGlobal.tpl', $templates)))
				//$templates[] = $tplDir.'rightArea.tpl';
				{}
			elseif($mapTpl){}
//				$templates[] = $tplDir.'rightArea.tpl';
				
			if((in_array($tplDir.'header.tpl', $templates))||
				(in_array($tplDir.'headerGB.tpl', $templates))||
				(in_array($tplDir.'headerYM.tpl', $templates))||
				(in_array($tplDir.'headerPan.tpl', $templates))||
				(in_array($tplDir.'headerGB_YM_preload.tpl', $templates)))
				{
				$templates[] = $tplDir.'footer.tpl';
				}
			if((in_array($tplDir.'header.tpl', $templates))||
				(in_array($tplDir.'headerSimple.tpl', $templates))||
				(in_array($tplDir.'headerGB.tpl', $templates))||
				(in_array($tplDir.'headerYM.tpl', $templates))||
				(in_array($tplDir.'headerPan.tpl', $templates))||
				(in_array($tplDir.'headerNew.tpl', $templates))||
				(in_array($tplDir.'headerGB_YM_preload.tpl', $templates))||
				(in_array('AdminHeader.tpl', $templates))||
				(in_array('AdminHeaderGB.tpl', $templates)))
				{
				/************************************************************************************/
				$SMRT['header']= $headerPar;
				$SMRT_TMP['name'] = 'menu';
				$SMRT_TMP['body'] = $allMenu;
				$SMRT['modules'][] = $SMRT_TMP;

				/************************************************************************************/
				if((!in_array('Message.tpl', $templates))&&(!strpos($_SERVER['QUERY_STRING'], "."))&&(!$HISTORY_SKIP))
					{			
					$NAV->SetCurURI();
					}
				}
			if(!isset($smarty))
				{
				require_once('../htdocs/includes/Smarty/setup.php');
				$smarty = new Smarty_CMS;	
				$smarty->init();
				}
			include('blocs.php');
//			print_r($_SERVER);
			$SMRT['modules'][] =  array('name' => 'client', 'body' => array('city' => $USER->curCity, 
																			'name' => $USER->appName, 
																			'version'=>$USER->appVersion, 
																			'mngAct'=>($isMng)?$mngAct:'',
																			'isMng'=>($isMng)?1:0,
																			'URL'=>$_SERVER['SCRIPT_URL'],
																			'URI'=>$_SERVER['SCRIPT_URI']
																			));		
			
			if($SMRT['header'])	
				$smarty->assign('header',$SMRT['header']);
			if($SMRT['menu'])			
				$smarty->assign('menu',$SMRT['menu']);
			if(count($SMRT['modules']))	
				{
	//			print_r($SMRT);
				$i=0;
				while($SMRT['modules'][$i])
					{			
					$smarty->assign($SMRT['modules'][$i]['name'], $SMRT['modules'][$i]['body']);
					$i++;
					}	
				}
			}
		else
			{
	//		echo 'selfCloseBlank.tpl';
			}
		
		$i=0;
/*		print_r($templates);*/
		while($templates[$i])
			{		
			if(isset($caching[$templates[$i]]['lifetime']))
				{
				$smarty->caching = 2;
				$smarty->cache_lifetime = $caching[$templates[$i]]['lifetime'];
				}
			else
				{
				$smarty->caching = 0;
				}
			if($CONST['dinamicUTF8Convert'])
				{
//				$charset = ($CONST['dinamicUTF8Convert'])?'windows-1251':'UTF-8';
/*				$strout = $smarty->fetch($templates[$i]);
				echo $strout = iconv("windows-1251","UTF-8",$strout);	
*/				
//				$charset = 'UTF-8';
//				header('Content-type: text/html; charset='.$charset);
				$smarty->display($templates[$i]);
				
				}
			else
				$smarty->display($templates[$i]);
			$i++;
			}	
		}
	}
else
	{
	header('Location: '.$REDIRECT);	
	}
?>