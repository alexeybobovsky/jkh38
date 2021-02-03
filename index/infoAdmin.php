<?
require_once("../classes/TPL_info_class.php");
require_once("../classes/GetInfo_class.php");
$realPath = $ROUT->GetStartedUrlLvl($allMenu['curLabel']['link'], $allMenu['activeLink'], $uri);
$curURL = $realPath['url'];
$curURLLvl = $realPath['lvl'];
$act = ($post[$curURLLvl])?($post[$curURLLvl]):'manage';
$IATPL = new INFOADMIN_TEMPLATE;
$ICNT = new GetInfo;
$error = 0;
switch ($act)
	{
	case 'manage': /*управление информационными блоками*/
		{
		$templates = array();
		$templates[] = 'AdminHeader.tpl';
		$templates[] = 'AdminMenu.tpl';
		$tree = $ICNT->GetInfoTree(0, 2);
//		print_r($tree);
		$SMRT_TMP['name'] = 'Tree';
		$SMRT_TMP['body'] = $IATPL->getTree($tree);
		$SMRT_TMP['body']['admin'] = 1;
		$SMRT['modules'][] = $SMRT_TMP;
			
		$SMRT_TMP['name'] = 'Properties';
		$SMRT_TMP['body'] = $IATPL->getPropertiesForm();
		$SMRT['modules'][] = $SMRT_TMP;
		
		$SMRT_TMP['name'] = 'Form';
		$SMRT_TMP['body'] = $IATPL->ShowArchive();
		$SMRT['modules'][] = $SMRT_TMP;
		$templates[] = 'infoArchive.tpl';
		$templates[] = 'AdminFooter.tpl';
		}
	break;
	case 'new':
		{
		if($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)
			{		
//			print_r($_POST);
			$brotherList = $ICNT->GetFutureBrotherList($_POST['curNodeSUBMIT']);				
			$templates = array();
			$templates[] = 'AdminHeader.tpl';
			$templates[] = 'AdminMenu.tpl';
			$SMRT_TMP['name'] = 'news';
			$SMRT_TMP['body'] = $IATPL->newNode($_POST['curNodeSUBMIT'], $brotherList, $curURL);
			$SMRT['modules'][] = $SMRT_TMP;	
			$templates[] = 'newsEdit.tpl';	
//			$templates[] = 'articleArchive.tpl';
			$templates[] = 'AdminFooter.tpl';
			$HISTORY_SKIP ++;
			}
		else
			{
			$error = 404;
			}
		}
	break;
	case 'newBody':
		{
		if($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)
			{		
//			print_r($_POST);
			$templates = array();
			$templates[] = 'AdminHeader.tpl';
			$templates[] = 'AdminMenu.tpl';
			$SMRT_TMP['name'] = 'news';
			$SMRT_TMP['body'] = $IATPL->newInfo($_POST['curNodeInfo'], $curURL);
			$SMRT['modules'][] = $SMRT_TMP;	
			$templates[] = 'newsEdit.tpl';	
//			$templates[] = 'articleArchive.tpl';
			$templates[] = 'AdminFooter.tpl';
			$HISTORY_SKIP ++;
			}
		else
			{
			$error = 404;
			}
		}
	break;
	case 'set':
		{
		if(($post[$curURLLvl+1] == 'editOrder')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($_POST['curOrNode']))			
			{
//			print_r($_POST);			
			$_SESSION['curNode'] = $_POST['curOrNode'];
			if((isset($_POST['Order']))&&($_POST['Order']!= $_POST['curOrder']))
				{
				$SET = new SetContent();			
				$retCat = $SET->changeOrder($_POST['curOrNode'], $_POST['Order']);								
				}
//			echo $retCat;
			if($retCat)
				{
				$errMess = 'Не удалось изменить раздел! ';
				$MESS = new Message('Error', 'Изменение раздела', $errMess, $NAV->GetPrewURI());											
				}	
			else
				{
				$REDIRECT = 'http://'.$NAV->GetPrewURI();
				}
			}
		elseif(($post[$curURLLvl+1] == 'editPar')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($_POST['curNode']))			
			{
			$_SESSION['curNode'] = $_POST['curNode'];
			if(($_POST['Category'])&&($_POST['curCat']!= $_POST['Category']))
				{
				$children = $CNT->GetChildren1Level($_POST['Category'], 0, 0);
				$position = 0;
				for($i=0; $i<count($children); $i++)
					{
					if($children[$i]['catalog']['sc_order']> $position)
						$position = $children[$i]['catalog']['sc_order'];
					}
				$SET = new SetContent();			
				$retCat = $SET->SetNodPar($_POST['curNode'], 'sc_parId', $_POST['Category']);								
				if($retCat)
					{
					$retCat = $SET->SetNodPar($_POST['curNode'], 'order', $position+1);													
					}
				}
			if(!$retCat)
				{
				$errMess = 'Не удалось изменить раздел! ';
				$MESS = new Message('Error', 'Изменение раздела', $errMess, $NAV->GetPrewURI());											
				}	
			else
				{
				$REDIRECT = 'http://'.$NAV->GetPrewURI();
				}		
			}
		elseif(($post[$curURLLvl+1] == 'edit')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($_POST['parNodeId']))
			{
//			echo 'qeeeqweq';
//			print_r($_POST);
			$SET = new SetContent();			
			$error = 0;
			$_SESSION['curNode'] = $_POST['parNodeId'];
			$menu = (isset($_POST['isMenu']))?1:0;
			if($_POST['NAME'] != $_POST['oldName'])
				{
				$node = $CNT->GetCurNode($_POST['parNodeId'], -1);
				$tmpBody['name'] = 'link';
				$tmpBody['value'] = $CNT->GetNodePath($node['catalog']['sc_parId'], 1).'/'.$_POST['NAME'].'/';
				$tmpBody['type'] = 'varchar';
				$whereIn = array('sc_id' => $_POST['parNodeId'], 'sb_name' => 'link');
				
				if(!$SET->SetNodPar($_POST['parNodeId'], 'name', $_POST['NAME']))
					$error ++;	
				elseif(!$SET->editAddPar($tmpBody, 0, $whereIn))
					$error ++;	
				}
			if((!$error)&&($_POST['order'] != $_POST['oldOrder']))
				{
				if($SET->changeOrder($_POST['parNodeId'], $_POST['order']))
					$error ++;				
				}
			if((!$error)&&($menu != $_POST['oldMenu']))
				{
				if(!$SET->SetNodPar($_POST['parNodeId'], 'menu', $menu))
					$error ++;				
				}
				
			if((!$error)&&($_POST['titleShort'] != $_POST['oldTitleShort']))
				{				
				$tmpBody['name'] = 'caption';
				$tmpBody['value'] = $_POST['titleShort'];
				$tmpBody['type'] = 'varchar';
				$whereIn = array('sc_id' => $_POST['parNodeId'], 'sb_name' => 'caption');				
				if(!$SET->editAddPar($tmpBody, 0, $whereIn))
					$error ++;				
				}
			if((!$error)&&($_POST['titleFull'] != $_POST['oldTitleFull']))
				{
				$tmpBody['name'] = 'title';
				$tmpBody['value'] = $_POST['titleFull'];
				$tmpBody['type'] = 'varchar';
				$whereIn = array('sc_id' => $_POST['parNodeId'], 'sb_name' => 'title');				
				$title = $CNT->GetCurBlockBody($_POST['parNodeId'], array('title'));
				if(is_array($title['title']))
					{
					if(!$SET->editAddPar($tmpBody, 0, $whereIn))
						$error ++;				
					}
				else
					{
					if(!$SET->AddParToNode($tmpBody, $_POST['parNodeId']))
						$error ++;				
					}
				}
			if(!$error)
				{
				$tmpBody['name'] = 'textblock';
				$tmpBody['value'] = (get_magic_quotes_gpc())?$_POST['BODY']:addslashes($_POST['BODY']);
//				$tmpBody['value'] = $_POST['BODY'];
				$tmpBody['type'] = 'text';
				$whereIn = array('sc_id' => $_POST['parNodeId'], 'sb_name' => 'textblock');				
				if(!$SET->editAddPar($tmpBody, 0, $whereIn))
					$error ++;				
				}
			if($error)
				{
				$errMess = 'Не удалось изменить раздел! ';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				echo $errMess;
//				$MESS = new Message('Error', 'Изменение новости', $errMess, $NAV->GetPrewURI());											
				}	
			else
				{
				$REDIRECT = $_POST['_REFERRER'];
//				$REDIRECT = 'http://'.$NAV->GetPrewURI();
				}
			}
		elseif(($post[$curURLLvl+1] == 'addInfo')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)) /*02_04_2007*/
			{
//			print_r($_POST);
			$res = 0;
			if(($_POST['BODY'])&&($_POST['parNodeId']))
				{
				$SET = new SetContent();			
				$tmpBody['name'] = 'textblock'; 
				$tmpBody['value'] = (get_magic_quotes_gpc())?$_POST['BODY']:addslashes($_POST['BODY']);
				$tmpBody['type'] = 'text';
				$res = $SET->AddParToNode($tmpBody, $_POST['parNodeId']);
				if($res)
					{
					$res = $SET->SetNodPar($_POST['parNodeId'], 'handler', 'info.php');
					}				
				}
			if(!$res)
				{
				$errMess = 'Не удалось добавить информацию! ';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', 'Добавление информационного блока', $errMess, $NAV->GetPrewURI());											
				}	
			else
				{
				$REDIRECT = 'http://'.$NAV->GetPrewURI();
				}	
			}
		elseif(($post[$curURLLvl+1] == 'add')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)) /*02_04_2007*/
			{
//			print_r($_POST);
			$res = 1;
			if(($_POST['NAME'])&&($_POST['parNodeId']>=0))
				{
				$SET = new SetContent();			
				$param['name'] = $_POST['NAME'];
				$param['parId'] = $_POST['parNodeId'];
				$param['lang'] = 1;
				$param['handler'] = ($_POST['BODY'])?'info.php':'';
				$param['is_menu'] = ($_POST['isMenu'])?1:0;
				$param['sc_published'] = 1;
				$param['is_menu'] = 1;
				$tmpBody['name'] = 'link';
				$tmpBody['value'] = $CNT->GetNodePath($_POST['parNodeId'], 1).'/'.$_POST['NAME'].'/';//$_POST['titleShort'];
				$tmpBody['type'] = 'varchar';
				$body[] = $tmpBody;			
				if($_POST['titleShort'])
					{
					$tmpBody['name'] = 'caption';
					$tmpBody['value'] = $_POST['titleShort'];
					$tmpBody['type'] = 'varchar';
					$body[] = $tmpBody;		
					}
				if($_POST['BODY'])
					{
					$tmpBody['name'] = 'textblock'; 
					$tmpBody['value'] = (get_magic_quotes_gpc())?$_POST['BODY']:addslashes($_POST['BODY']);
					$tmpBody['type'] = 'text';
					$body[] = $tmpBody;			
					}				
				if($_POST['titleShort'] != $_POST['titleFull'])
					{
					$tmpBody['name'] = 'title';
					$tmpBody['value'] = $_POST['titleFull'];
					$tmpBody['type'] = 'varchar';
					$body[] = $tmpBody;			
					}
				$lastId = $SET->CreateNodeWithBody($param, $body);
				if($lastId)
					{
					$res = $SET->changeOrder($lastId, $_POST['order']);
					}
				if(!$res)
					{
					$ret = $ACL->GetClosedParentRight($_POST['parNodeId']);
					if($ret<7)					
						{					
//					echo $res;
						$retACL = $ACL->SetRight($lastId, $USER->id, 'SiteCatalog', 'sc_id', 7);
						}
//					if(!$retACL)
//						$res++;
					}
				}
//			echo $res;
			if($res)
				{
				$errMess = 'Не удалось добавить раздел! ';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', 'Добавление раздела', $errMess, $NAV->GetPrewURI());											
				}	
			else
				{
				$REDIRECT = $_POST['_REFERRER'];
//				$REDIRECT = 'http://'.$NAV->GetPrewURI();
				}	
			}
		elseif(($post[$curURLLvl+1] == 'menu')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($_POST['curNode']))/*03_04_2007*/
			{
//			print_r($_POST);
			$_SESSION['curNode'] = $_POST['curNode'];
			$SET = new SetContent;
			$res = $SET->changeMenuStatus($_POST['curNode']);			
//			print_r($res);
			if($res['error'])
				{
				if($res['old_status'])
					{
					$msg['header'] = 'Изменение раздела';
					$msg['fault']['body'] = 'Не удалось убрать раздел из меню - '.$res['errorMsg'];
					}
				else
					{
					$msg['header'] = 'Изменение раздела';
					$msg['fault']['body'] = 'Не удалось добавить раздел в меню - '.$res['errorMsg'];
					}			
				$msg['fault']['body'] .= ($CONST['debugMode'])?' :'.$res['errorMsg']:'';
				$MESS = new Message('Error', $msg['header'], $msg['fault']['body'], $NAV->GetPrewURI());											
				}	
			else
				{
				$REDIRECT = 'http://'.$NAV->GetPrewURI();
				}
			}
		elseif(($post[$curURLLvl+1] == 'status')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($_POST['curNode']))/*03_04_2007*/
			{
			//print_r($_POST);
			$_SESSION['curNode'] = $_POST['curNode'];
			$SET = new SetContent;
			$res = $SET->ChangeStatus($_POST['curNode']);			
//			print_r($res);
			if($res['error'])
				{
				if($res['old_status'])
					{
					$msg['header'] = 'Скрытие раздела';
					$msg['fault']['body'] = 'Не удалось скрыть раздел'.$res['errorMsg'];
					}
				else
					{
					$msg['header'] = 'Открытие раздела';
					$msg['fault']['body'] = 'Не удалось открыть раздел'.$res['errorMsg'];
					}			
				$msg['fault']['body'] .= ($CONST['debugMode'])?' :'.$res['errorMsg']:'';
				$MESS = new Message('Error', $msg['header'], $msg['fault']['body'], $NAV->GetPrewURI());											
				}	
			else
				{
				$REDIRECT = 'http://'.$NAV->GetPrewURI();
				}
			}
		elseif(($post[$curURLLvl+1] == 'del')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($_POST['curNode'])) /*03_04_2007*/
			{
			//print_r($_POST);
			$SET = new SetContent();
			$res = $SET->DeleteNode($_POST['curNode'], 1);
			if(!$res)
				{
				$errMess = 'Не удалось удалить объект! ';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', 'Удаление объекта', $errMess, $NAV->GetPrewURI());											
				}	
			else
				{
				$REDIRECT = 'http://'.$NAV->GetPrewURI();
				}				
			}
		elseif(($post[$curURLLvl+1] == 'delInfo')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($_POST['curNode'])) /*03_04_2007*/
			{
			$SET = new SetContent();
			$res = $SET->DeleteParamByName($_POST['curNode'], 'textblock');
			if($res)		
				{
				$res = $SET->SetNodPar($_POST['curNode'], 'handler', '');
				}
			if(!$res)
				{
				$errMess = 'Не удалось удалить объект! ';
				$errMess .= ($CONST['debugMode'])?': System Message - '.$ret['errorMsg']:'';
				$MESS = new Message('Error', 'Удаление объекта', $errMess, $NAV->GetPrewURI());											
				}	
			else
				{
				$REDIRECT = 'http://'.$NAV->GetPrewURI();
				}				
			}
		else
			{			
			$error = 404;
			}
		$HISTORY_SKIP ++;
		}
	break;
	case 'edit':
		{
		if(($_POST['curNodeSUBMIT'])&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>=3))
			{
			$brotherList = $ICNT->GetBrotherList($_POST['curNodeSUBMIT']);				
			$content = $CNT->GetCurNode($_POST['curNodeSUBMIT'], 0);
			$templates = array();
			$templates[] = 'AdminHeader.tpl';
			$templates[] = 'AdminMenu.tpl';
			$SMRT_TMP['name'] = 'news';
			$SMRT_TMP['body'] = $IATPL->editNode($_POST['curNodeSUBMIT'], $brotherList, $content, $curURL);
			$SMRT['modules'][] = $SMRT_TMP;	
			$templates[] = 'newsEdit.tpl';	
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