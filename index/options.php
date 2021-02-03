<?
$realPath = $ROUT->GetStartedUrlLvl($allMenu['curLabel']['link'], $allMenu['activeLink'], $uri);
$curURL = 		$realPath['url'];
$curURLLvl = $realPath['lvl'];
$curURLLvl = $realPath['lvl'];
if((!trim($post[$curURLLvl])))
	$act = $allMenu['curNodeName'];
else
	$act = trim($post[$curURLLvl]);
require_once("../classes/TPL_admin_class.php");
/*	
require_once("../classes/uob/GetRef_class.php");			
require_once("../classes/uob/TPL_Ref_class.php");			

$getUORef = new GetReferences;
*/
//echo $act;
$TPL_admin = new ADMIN_TEMPLATE;
$error = 0;
switch ($act)
	{
	case 'set': /*форма редактирования объекта (AJAX)*/
		{
		$HISTORY_SKIP = 1;		
		$error=0;

		if(($post[$curURLLvl+1] == 'edit')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&(intval($_POST['curNode'])))
			{
//			print_r($_POST);
			$curNode = intval($_POST['curNode']);
			$setCNT = new SetContent;			
//			$_SESSION['curNode'] = $curNode;
			$param = array();
			while (list ($key, $val) = each ($_POST)) 
				{
				if((!strstr($key, 'CMP_'))&&(isset($_POST['CMP_'.$key]))&&(trim($_POST['CMP_'.$key]) != trim($_POST[$key])))
					$param[$key] = $val;	
				}
			//print_r($param);				
			$res = $setCNT->updateConst($curNode,  $param);
			if($res['error'])
				{
				$error ++;
				}	
			else
				{
				$REDIRECT = $_POST['_REFERRER'];
				}				
			}	
		elseif(($post[$curURLLvl+1] == 'new')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1))
			{
//			print_r($_POST);
			$param['name'] = trim($_POST['NAME']);
			$param['value'] = trim($_POST['VALUE']);

			$value = (get_magic_quotes_gpc())?stripslashes(trim($_POST['ABOUT'])):trim($_POST['ABOUT']);	
			$value = mysql_escape_string(trim($value));
			$param['about'] = 	$value;
			
			$setCNT = new SetContent;			
			$res = $setCNT->createConst($param);
			if($res['error'])
				{
				$error ++;
				}	
			else
				{
				$REDIRECT = $_POST['_REFERRER'];
				}
			/*
*/
			}
		elseif(($post[$curURLLvl+1] == 'delete')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&(intval($_POST['curNode'])))
			{
			$curNode = intval($_POST['curNode']);
			$setCNT = new SetContent;			
			$res = $setCNT->deleteConst($curNode);
			if($res['error'])
				{
				$error ++;
				}	
			else
				{
				$REDIRECT = $_POST['_REFERRER'];
				}
			}
		else
			{
			$error ++;
			}
		if($error)
			{
			$errMess = 'Не удалось изменить запись! ';
			$errMess .= ($CONST['debugMode'])?': System Message - '.$res['errorMsg']:'';
			$MESS = new Message('Error', 'Изменение записи', $errMess, $NAV->GetPrewURI());														
			}
		}
	break;
	case 'showObjectEdit': /*управление слоями*/
		{
		if(($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)/*&&(intval($_POST['curNode']))*/)
			{
			$HISTORY_SKIP = 1;		
			$templates = array();
			$conf = $CNT->GetConfigValueById($_POST['node']);
//			print_r($conf);
//			$templates = array();
			$contentArea[]['label'] = 'Изменение константы';
			$contentHTML[] = $TPL_admin->ShowConstEdit($conf, $curURL);
			$contentArea[]['label'] = 'Удаление константы';
			$contentHTML[] = $TPL_admin->ShowConstDelete($conf['conf_id'], $curURL);							
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
			$smarty->assign('contentArea', $contentArea);
			$smarty->assign('contentHTML', $contentHTML);
			$table = $smarty->fetch('constantsOptions.tpl');
			echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
			}
		else
			{
			$error = 404;
			}
		} break;
	case 'options': /*управление слоями*/
		{
		if(($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)/*&&(intval($_POST['curNode']))*/)
			{
			$configTable = $CNT->GetAllConfigTable();			
//			print_r($configTable);
			$contentArea[]['label'] = 'Новая константа';
			$contentHTML[] = $TPL_admin->ShowConstNew($curURL);
			$SMRT_TMP = array();			
			$SMRT_TMP['name'] = 'contentHTML';
			$SMRT_TMP['body'] = $contentHTML;
			$SMRT['modules'][] = $SMRT_TMP;	
			$SMRT_TMP = array();			
			$SMRT_TMP['name'] = 'contentArea';
			$SMRT_TMP['body'] = $contentArea;
			$SMRT['modules'][] = $SMRT_TMP;	
			$SMRT_TMP = array();			
			$SMRT_TMP['name'] = 'orders';
			$SMRT_TMP['body'] = $TPL_admin->showObjectsList($configTable, $curURL);
			$SMRT['modules'][] = $SMRT_TMP;	
			$SMRT_TMP = array();
			$SMRT_TMP['name'] = 'formLabel';
			$SMRT_TMP['body']['tree'] = 'Список констант';
			$SMRT_TMP['body']['options'] = 'Опции';
			$SMRT['modules'][] = $SMRT_TMP;
			$templates = array();
			$templates[] = 'AdminHeader.tpl';
			$templates[] = 'AdminMenu.tpl';
			$templates[] = 'options.tpl';
			$templates[] = 'AdminFooter.tpl';
			}
		else
			{
			$error = 404;
			}
		} break;
	default :
		{
		$HISTORY_SKIP ++;
		$error = 404;
		}
	};
if($error==404)	
	{
	$HISTORY_SKIP = 1;		
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