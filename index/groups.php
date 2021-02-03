<?
require_once("../classes/TPL_info_class.php");
require_once("../classes/GetInfo_class.php");
require_once("../classes/TPL_admin_class.php");

$realPath = $ROUT->GetStartedUrlLvl($allMenu['curLabel']['link'], $allMenu['activeLink'], $uri);
$curURL = $realPath['url'];
$curURLLvl = $realPath['lvl'];

$act = ($post[$curURLLvl])?($post[$curURLLvl]):'groups';

$IATPL = new INFOADMIN_TEMPLATE;
$ICNT = new GetInfo;

$ACNT = new GetAdmin;
$ATPL = new ADMIN_TEMPLATE;

$error = 0;
switch ($act)
	{
	case 'groups': /*���������� ��������*/
		{
		$templates = array();
		$templates[] = 'AdminHeader.tpl';
		$templates[] = 'AdminMenu.tpl';
		$groupList = $ACNT->GetGroups(2, 0);
		for($i=0; $i<count($groupList); $i++)
			{
			$groupList[$i]['users'] = $ACNT->GetGroupUsers(2, $groupList[$i]['user_id']);
//			$usersInGroups[$groupList[$i]['user_id']] = $ACNT->GetGroupUsers(2, $groupList[$i]['user_id']);
			}
//		$groupList = $ACNT->GetAllGroups(0);

		$SMRT_TMP['name'] = 'orders';
		$SMRT_TMP['body'] = $ATPL->showGroupsList($groupList, $curURL);// $userList; 
//		$SMRT_TMP['body'] = $ATPL->showUsersList($userList);// $userList; 
		$SMRT['modules'][] = $SMRT_TMP;	
		
		$templates[] = 'usersAdmin.tpl';

		$SMRT_TMP = array();
		$SMRT_TMP['name'] = 'formLabel';
		$SMRT_TMP['body']['tree'] = '������';
		$SMRT_TMP['body']['options'] = '�����';
		$SMRT['modules'][] = $SMRT_TMP;
		
		$SMRT_TMP = array();			
		$SMRT_TMP['name'] = 'contentHTML';
		$SMRT_TMP['body'][] = $ATPL->showGroupAdd($curURL);
		$SMRT['modules'][] = $SMRT_TMP;

//		$templates[] = 'users.tpl';	
		$templates[] = 'AdminFooter.tpl';

		}
	break;
	case 'set':
		{
		$ASET = new SetAdmin;
		if(($post[$curURLLvl+1] == 'add')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)) /*05_09_2007*/
			{
			$grName = trim($_POST['NAME']);
			if($grName)
				$res = $ASET->CreateGroup($grName);
			else
				$res['error'] = '������ ������ ���';
			if($res['error'])
				{
				$errMess = '�� ������� ������� ������������';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', '������ ��������', $errMess, $NAV->GetPrewURI());											
				}		
			$REDIRECT = 'http://'.$NAV->GetPrewURI();
			}
		elseif(($post[$curURLLvl+1] == 'edit')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($_POST['curNode'])) /*02_04_2007*/
			{
			$ret = 0;
			if(trim($_POST['OLDNAME'])!=trim($_POST['NEWNAME']))
				$ret = $ASET->SetUserPar($_POST['curNode'], 'user_name', trim($_POST['NEWNAME']));				
			if($ret)
				$_SESSION['curNode'] = $_POST['curNode'];
			else
				{
				$errorMsg = '������� ������ ����������';
				if($CONST['debugMode'])
					$errorMsg = $res['errorMsg'];
				elseif($res['error']>1)
					$errorMsg = $res['errorMsg'];													
				$MESS = new Message('Error', '��������� ������', '� ���� ������������� ������ ��������� ��������� ������: '.$errorMsg, $NAV->GetPrewURI());											
				}
			$REDIRECT = 'http://'.$NAV->GetPrewURI();
			}
		elseif(($post[$curURLLvl+1] == 'status')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($_POST['curNode']))/*03_04_2007*/
			{
//			print_r($_POST);
			$_SESSION['curNode'] = $_POST['curNode'];
			if(!$ASET->SetUserPar($_POST['curNode'], 'user_registation_status', $_POST['Category']))
				{
				$errMess = '�� ������� �������� ������';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', '������ ��������� �������',  $errMess, $NAV->GetPrewURI());										
				}
			$REDIRECT = 'http://'.$NAV->GetPrewURI();
			}
		elseif(($post[$curURLLvl+1] == 'delete')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($_POST['curNode'])) /*03_04_2007*/
			{
//			print_r($_POST);
			if($_POST['DELUSER'])
				{
				$usersToGroup = $ACNT->GetGroupUsers(2, $_POST['curNode']);
				for($i=0; $i<count($usersToGroup); $i++)
					{
					$ret = $ASET->DeleteUser($usersToGroup[$i]['user_id']);
					}
				}
			$ret = $ASET->DeleteUser($_POST['curNode']);
			if($ret['error'])
				{
				$errMess = '�� ������� ������� ������';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', '������ ��������',  $errMess, $NAV->GetPrewURI());										
				}	
			$REDIRECT = 'http://'.$NAV->GetPrewURI();
			}
		elseif(($post[$curURLLvl+1] == 'members')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1) &&($_POST['curNode']))/*03_04_2007*/
			{
//			print_r($_POST);
//			$groupsToUser = $ACNT->GetGroupUsers(2, $_POST['curNode']);
			
//			$groupsToUser = $ACNT->GetUserGroups(0, $_POST['curNode']);
			$users = $ACNT->GetAllUsers(0, 0);
			$groupsAll = $ACNT->GetGroups(2,0);
			$error = 0;
			for($i=0; $i<count($users); $i++)
				{
				if(($_POST['User_'.$users[$i]['user_id']])&&(!$USER->userInGroup($users[$i]['user_id'], $_POST['curNode'])))
					{
					$ret = $ASET->AddUserToGroup($users[$i]['user_id'], $_POST['curNode']);
					if($ret['error'])
						{
						$error++;
						}					
					}
				elseif(!($_POST['User_'.$users[$i]['user_id']])&&($USER->userInGroup($users[$i]['user_id'], $_POST['curNode'])))
					{
					$ret = $ASET->RemoveUserFromGroup($users[$i]['user_id'], $_POST['curNode']);
					if($ret['error'])
						{
						$error++;
						}					
					}					
				}
			if($error)
				{
				$errMess = '�� ������� �������� ������ ������';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', '������ ���������', $errMess, $NAV->GetPrewURI());											
				}
			else
				{
				$_SESSION['curNode'] = $_POST['curNode'];
				}
			
			
/*			$_SESSION['curNode'] = $_POST['curNode'];
			if(!$ASET->SetUserPar($_POST['curNode'], 'user_registation_status', $_POST['Category']))
				{
				$errMess = '�� ������� �������� ������';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', '������ ��������� �������',  $errMess, $NAV->GetPrewURI());										
				}*/
			$REDIRECT = 'http://'.$NAV->GetPrewURI();
			}
		elseif(($post[$curURLLvl+1] == 'clear')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1) &&($_POST['curNode']))/*03_04_2007*/
			{
			$ret = $ASET->ClearGroup($_POST['curNode']);
			if($ret['error'])
				{
				$errMess = '�� ������� �������� ������';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', '������ �������', $errMess, $NAV->GetPrewURI());											
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
	default :
		{
		$error = 404;
		}
	};
if($error==404)	
	{
	$messBodyNews=($CONST['debugMode'])?'����������� ���� �������� �� ������� (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')':'����������� ���� �������� �� �������';
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