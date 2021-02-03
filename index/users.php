<?php
echo __FILE__;
//require_once("../classes/TPL_info_class.php");
//require_once("../classes/GetInfo_class.php");
require_once("../classes/TPL_admin_class.php");
$realPath = $ROUT->GetStartedUrlLvl($allMenu['curLabel']['link'], $allMenu['activeLink'], $uri);
$curURL = $realPath['url'];
$curURLLvl = $realPath['lvl'];

echo $act = ($post[$curURLLvl])?($post[$curURLLvl]):'users';

//$IATPL = new INFOADMIN_TEMPLATE;
//$ICNT = new GetInfo;

$ACNT = new GetAdmin;
$ATPL = new ADMIN_TEMPLATE;
//$charset = ($CONST['dinamicUTF8Convert'])?'windows-1251':'UTF-8';
$charset = 'UTF-8';
header('Content-type: text/html; charset='.$charset);

$error = 0;
switch ($act)
	{
	case 'users': /*управление пользователями*/
		{
		if(count($_POST))
			{
			if(trim($_POST['fltrName']))
				$_SESSION['filterUsers']['fltrName'] = trim($_POST['fltrName']);
			elseif($_SESSION['filterUsers']['fltrName'])
				unset($_SESSION['filterUsers']['fltrName']);		
				
			if($_POST['fltrGroups'])
				$_SESSION['filterUsers']['fltrGroups'] = $_POST['fltrGroups'];
			elseif($_SESSION['filterUsers']['fltrGroups'])
				unset($_SESSION['filterUsers']['fltrGroups']);		
			}
		$templates = array();
		$templates[] = 'AdminHeader.tpl';
		$templates[] = 'AdminMenu.tpl';
		$userList = $ACNT->GetAllUsers(2, 0);
		$groupList = $ACNT->GetGroups(2, 1);

		$SMRT_TMP['name'] = 'orders';
		$SMRT_TMP['body'] = $ATPL->showUsersList($userList, $curURL);// $userList; 
//		$SMRT_TMP['body'] = $ATPL->showUsersList($userList);// $userList; 
		$SMRT['modules'][] = $SMRT_TMP;	
		
		$SMRT_TMP = array();
		$SMRT_TMP['name'] = 'filter';
		$SMRT_TMP['body'] = $ATPL->showUsersFilter($groupList);// $userList; 
//		$SMRT_TMP['body'] = $ATPL->showUsersList($userList);// $userList; 
		$SMRT['modules'][] = $SMRT_TMP;	
		
		$templates[] = 'usersAdmin.tpl';

		$SMRT_TMP = array();
		$SMRT_TMP['name'] = 'formLabel';
		$SMRT_TMP['body']['tree'] = 'Пользователи';
		$SMRT_TMP['body']['options'] = 'Опции';
		$SMRT['modules'][] = $SMRT_TMP;
		
		$SMRT_TMP = array();			
		$SMRT_TMP['name'] = 'contentHTML';
		$SMRT_TMP['body'][] = $ATPL->showUserAdd($curURL);
		$SMRT['modules'][] = $SMRT_TMP;
		
//		print_r($userList);
//		$templates[] = 'users.tpl';	
		$templates[] = 'AdminFooter.tpl';

		}
	break;
	case 'set':
		{
		$ASET = new SetAdmin;
		if(($post[$curURLLvl+1] == 'addUser')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)) /*04_09_2007*/
			{
/*			print_r($_POST);
			echo '<hr>';*/
			while (list ($key, $val) = each ($_POST)) 
				{
				$newVal = trim($val);
				if(($newVal)&&(!is_array($_POST[$key])))
					$_POST[$key] = $newVal;
				elseif(!is_array($_POST[$key]))
					unset($_POST[$key]);
				}
			$res = $ASET->CreateActiveUser($_POST);
			if(!$res['error'])
				{
				$userId = $res['last_id'];			
				for($i=0; $i<count($_POST['USER_GROUPS']); $i++)
					{
					$ret = $ASET->AddUserToGroup($userId, $_POST['USER_GROUPS'][$i]);
					if($ret['error'])
						{
						$errMess = 'Не удалось создать пользователя';
						$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
						$MESS = new Message('Error', 'Ошибка создания', $errMess, $NAV->GetPrewURI());											
						}
					}
				}
			else
				{
				$errMess = 'Не удалось создать пользователя';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', 'Ошибка создания', $errMess, $NAV->GetPrewURI());											
				}		
			$REDIRECT = 'http://'.$NAV->GetPrewURI();
			}
		elseif(($post[$curURLLvl+1] == 'info')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($_POST['curUserId'])) /*02_04_2007*/
			{
//			print_r($_POST);
/*					print_r($_POST);
					print_r($_FILES);*/
			$tmpKey = '';
			$tmpVal = '';
			$param = array();
			while (list ($key, $val) = each ($_POST)) 
				{
				if(($key == 'CMP_'.$tmpKey)&&($val != $tmpVal))
					{
//							echo "$tmpKey => $tmpVal<br />\n";
					$param[$tmpKey] = $tmpVal;
					}
				elseif($key != 'CMP_'.$tmpKey)
					{
					$tmpKey = $key;
					$tmpVal = $val;							
					}							
				}					
			if($_POST['AvatarSelect'])
				{
				$param['avatar'] = $CONST['srcAvatarSys'].$_POST['AvatarSelect'];						
				}
			if(($UploadAvatar)&&($ROUT->CheckAndMoveUploadedFile('UploadAvatar', $_FILES,   $CONST['sizeUpAvatarMax'],$CONST['relPathPref'].$CONST['srcAvatarUsr'].$_FILES['UploadAvatar']['name'])))
				{
//						echo 'good!';
				$param['avatar'] = $CONST['srcAvatarUsr'].$_FILES['UploadAvatar']['name'];
				}
//				print_r($param);
			if(count($param))
				{
				$_SESSION['curNode'] = $_POST['curUserId'];
				$param['userId'] = $_POST['curUserId'];
				$res = $ASET->setUserInfo($param, $_POST['curUserId']);
				if($res['error'])						
					{
					$errorMsg = 'Причина ошибки неизвестна';
					if($CONST['debugMode'])
						$errorMsg = $res['errorMsg'];
					elseif($res['error']>1)
						$errorMsg = $res['errorMsg'];													
					$MESS = new Message('Error', 'Узменение персональной информации', 'В ходе изменения персональной информации произошла следующая ошибка: '.$errorMsg, $NAV->GetPrewURI());											
					}
				}					
			$REDIRECT = 'http://'.$NAV->GetPrewURI();
			}
		elseif(($post[$curURLLvl+1] == 'status')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($_POST['curNode']))/*03_04_2007*/
			{
//			print_r($_POST);
			$_SESSION['curNode'] = $_POST['curNode'];
			if(!$ASET->SetUserPar($_POST['curNode'], 'user_registation_status', $_POST['Category']))
				{
				$errMess = 'Не удалось изменить статус';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', 'Ошибка изменения статуса',  $errMess, $NAV->GetPrewURI());										
				}
			$REDIRECT = 'http://'.$NAV->GetPrewURI();
			}
		elseif(($post[$curURLLvl+1] == 'delete')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($_POST['curNode'])) /*03_04_2007*/
			{
//			print_r($_POST);
			$ret = $ASET->DeleteUser($_POST['curNode']);
			if($ret['error'])
				{
				$errMess = 'Не удалось удалить объект';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', 'Ошибка удаления',  $errMess, $NAV->GetPrewURI());										
				}	
			$REDIRECT = 'http://'.$NAV->GetPrewURI();
			}
		elseif(($post[$curURLLvl+1] == 'membership')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1) &&($_POST['curNode']))/*03_04_2007*/
			{
//			print_r($_POST);
//			$groupsToUser = $ACNT->GetUserGroups(0, $_POST['curNode']);
			$groupsAll = $ACNT->GetGroups(2,1);
			$error = 0;
			for($i=0; $i<count($groupsAll); $i++)
				{
				if(($_POST['Group_'.$groupsAll[$i]['user_id']])&&(!$USER->userInGroup($_POST['curNode'], $groupsAll[$i]['user_id'])))
					{
					$ret = $ASET->AddUserToGroup($_POST['curNode'], $groupsAll[$i]['user_id']);
					if($ret['error'])
						{
						$error++;
						}					
					}
				elseif(!($_POST['Group_'.$groupsAll[$i]['user_id']])&&($USER->userInGroup($_POST['curNode'], $groupsAll[$i]['user_id'])))
					{
					$ret = $ASET->RemoveUserFromGroup($_POST['curNode'], $groupsAll[$i]['user_id']);
					if($ret['error'])
						{
						$error++;
						}					
					}					
				}
			if($error)
				{
				$errMess = 'Не удалось изменить членство для пользователя';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', 'Ошибка изменения', $errMess, $NAV->GetPrewURI());											
				}
			else
				{
				$_SESSION['curNode'] = $_POST['curNode'];
				}
			$REDIRECT = 'http://'.$NAV->GetPrewURI();
			}
		elseif(($post[$curURLLvl+1] == 'exclude')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1) &&($_POST['curNode']))/*03_04_2007*/
			{
			$ret = $ASET->RemoveUserFromAll($_POST['curNode']);
			if($ret['error'])
				{
				$errMess = 'Не удалось Исключить пользователя';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', 'Ошибка очистки', $errMess, $NAV->GetPrewURI());											
				}
			else
				{
				$_SESSION['curNode'] = $_POST['curNode'];
				}
			$REDIRECT = 'http://'.$NAV->GetPrewURI();
			}
		else
			{			
			$error = 404;
			}
		$HISTORY_SKIP ++;
		}
	break;
	case 'add':
		{
//		print_r($_POST);
		if($ACL->GetClosedParentRight($allMenu['curNodeId'])>=3)
			{
//			$groupList = $ACNT->GetAllGroups(0);
			$groupList = $ACNT->GetGroups(2, 0);
			$templates = array();
			$templates[] = 'AdminHeader.tpl';
			$templates[] = 'AdminMenu.tpl';
			$addUser = $ATPL->showAddUserForm($groupList, $curURL);		
			$SMRT_TMP['name'] = 'addUser';
			$SMRT_TMP['body'] = $addUser;
			$SMRT['modules'][] = $SMRT_TMP;			
			$templates[] = 'addUser.tpl';	
			$templates[] = 'AdminFooter.tpl';
			$HISTORY_SKIP ++;
			}
		else
			{
			$error = 404;
			}
		
		}
	break;
	case 'edit':
		{
//		print_r($_POST);
		if(($_POST['curNode'])&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>=3))
			{
			$templates = array();
			$templates[] = 'AdminHeader.tpl';
			$templates[] = 'AdminMenu.tpl';
			$addUser = $ATPL->showEditInfo($USER->getUserParamAll($_POST['curNode']), $curURL);		
			$SMRT_TMP['name'] = 'addUser';
			$SMRT_TMP['body'] = $addUser;
			$SMRT_TMP['body']['admin'] = 1;
			$SMRT['modules'][] = $SMRT_TMP;			
			$templates[] = 'addUser.tpl';	
			$templates[] = 'AdminFooter.tpl';
			$HISTORY_SKIP ++;
			}
		else
			{
			$error = 404;
			}
		
		}
	break;
	default :
		{
		$error = 404;
		}
	};
if($error==404)	
	{
	$messBodyNews=($CONST['debugMode'])?'Запрошенная Вами страница не найдена (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')':'Запрошенная Вами страница не найдена';
	$MESS = new Message('Error', 'ERROR 404', $messBodyNews, $NAV->GetPrewURI());											
	}
if(isset($MESS))
	{		
/*	echo '<hr>';
	print_r($MESS);*/

	$_SESSION['MESSAGE'] = $MESS;
	header('Location: /');	
	}
elseif($REDIRECT)
	{
	header('Location: '.$REDIRECT);	
	}
?>