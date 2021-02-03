<?
require_once("../classes/TPL_access_class.php");
require_once("../classes/GetAccess_class.php");
//require_once("../classes/TPL_info_class.php");
$realPath = $ROUT->GetStartedUrlLvl($allMenu['curLabel']['link'], $allMenu['activeLink'], $uri);
$curURL = $realPath['url'];
$curURLLvl = $realPath['lvl'];

$act = ($post[$curURLLvl])?($post[$curURLLvl]):'manage';

//$IATPL = new INFOADMIN_TEMPLATE;
//$ICNT = new GetInfo;
$ACNT = new GetAdmin;
$AcCNT = new GetAccess;
$AcTPL = new ACCESS_TEMPLATE;

$error = 0;
switch ($act)
	{
	case 'manage': /*управление доступом*/
		{
		$templates = array();
		$templates[] = 'AdminHeader.tpl';
		$templates[] = 'AdminMenu.tpl';

		$tree = $AcCNT->GetSiteTree();
		$SMRT_TMP['name'] = 'Tree';
		$SMRT_TMP['body'] = $AcTPL->getTree($tree);
		$SMRT_TMP['body']['admin'] = 1;
		$SMRT['modules'][] = $SMRT_TMP;
		
		/**/
		$SMRT_TMP['name'] = 'Properties';
		$SMRT_TMP['body'] = $AcTPL->getPropertiesForm();
		$SMRT['modules'][] = $SMRT_TMP;
		$SMRT_TMP['name'] = 'Form';
		$SMRT_TMP['body'] = $AcTPL->ShowArchive($curURL);
		$SMRT['modules'][] = $SMRT_TMP;
		$templates[] = 'access.tpl';
	
		
/*		$SMRT_TMP['name'] = 'orders';
		$SMRT_TMP['body'] = $tree;
//		$SMRT_TMP['body'] = $ATPL->showUsersList($userList);// $userList; 
		$SMRT['modules'][] = $SMRT_TMP;/*	*/
		$templates[] = 'AdminFooter.tpl';

		}
	break;	
	case 'set':
		{
		$ASET = new SetAdmin;
		if(($post[$curURLLvl+1] == 'rightEdit')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>3)) /*04_09_2007*/
			{
			$tmp_arr1 = explode('#', /*$id*/ $_POST['id']);		
			$tmp_arr2 = explode('_', $tmp_arr1[0]);
			$tmp_arr3 = explode('*', $tmp_arr2[0]);
			$rightId = $tmp_arr1[1];
			$userId = $tmp_arr2[1];
			$objId = $tmp_arr3[1];
			$rightAlias = array(0 =>'нет доступа', 1 => 'чтение', 3 => 'запись', 7 => 'полный доступ');
			$skipMess = 1;
			$templates = array();						
			$successMes = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8", $rightAlias[$value]):$rightAlias[$value];	
			$failureMes = 'update error';
			$result = $ACL->EditRight($rightId, $userId, 'SiteCatalog', 'sc_id', $value, $objId);
			if($result['update'])
				$successMes .= '<!--updateNow_'.$objId.'_-!>';
			if(!$result['error'])
				echo $successMes;
			else
				echo $failureMes;
			}
		elseif(($post[$curURLLvl+1] == 'rightMove')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>3)) /*04_09_2007*/
			{
//			print_r($post); print_r($_POST);
//			echo $showUsers;
			$skipMess = 1;
			$templates = array();	
			if(isset($user))
				$res = $ACL->SetRight($node, $user, 'SiteCatalog', 'sc_id', 1);
			elseif(isset($rightId))
				$res = $ACL->DeleteRight($rightId);				
			if(!$res['error'])
				{
				$right_tmp = $ACL->GetAllRight($node, 'SiteCatalog', 'sc_id');
				$right = $AcTPL->RightList($node, $right_tmp);
				$ret['list'] = $AcTPL->UsersWithoutRightsList($AcCNT->GetUserForRightManip(), $ACNT->GetAllGroups(0), $right['userList'], $showUsers);
//				$ret['list'] = $AcTPL->UsersWithoutRightsList($ACNT->GetAllUsers(0, 1), $ACNT->GetAllGroups(0), $right['userList']);
				$ret['node'] = $node;
				require_once('../htdocs/includes/Smarty/setup.php');
				$smarty = new Smarty_CMS;
				$smarty->assign('users', $ret);
				$smarty->assign('rights', $right);
				$table = $smarty->fetch('accessList.tpl');
				$successMes = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8", $table):$table;	
				echo $successMes;
				}		
			else
				{
				echo $res['errorMsg'];
				}						
/*			
			$tmp_arr1 = explode('#',  $_POST['id']);		
			$tmp_arr2 = explode('_', $tmp_arr1[0]);
			$tmp_arr3 = explode('*', $tmp_arr2[0]);
			$rightId = $tmp_arr1[1];
			$userId = $tmp_arr2[1];
			$objId = $tmp_arr3[1];
			$rightAlias = array(0 =>'нет доступа', 1 => 'чтение', 3 => 'запись', 7 => 'полный доступ');
			$skipMess = 1;
			$templates = array();						
			$successMes = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8", $rightAlias[$value]):$rightAlias[$value];	
			$failureMes = 'update error';
			$result = $ACL->EditRight($rightId, $userId, 'SiteCatalog', 'sc_id', $value, $objId);
			if($result['update'])
				$successMes .= '<!--updateNow_'.$objId.'_-!>';
			if(!$result['error'])
				echo $successMes;
			else
				echo $failureMes;*/
			}
		else
			{
			echo 'undefined error';
//			$error = 404;
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
/*	print_r($post);
	print_r($_POST);*/
	$messBodyNews=($CONST['debugMode'])?'«апрошенна€ ¬ами страница не найдена (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')':'«апрошенна€ ¬ами страница не найдена';
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