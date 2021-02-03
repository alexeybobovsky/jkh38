<?
require_once("../classes/TPL_manage_class.php");
$templates = array();	
$templates[] = 'AdminHeader.tpl';
$templates[] = 'AdminMenu.tpl';
$patherror = 0;
/*
$curURL = $ROUT->GetStartedUrl($allMenu['curLabel']['link'], $uri);
$curURLLvl = $ROUT->GetCurrentLevelOfURL($curURL)+1;
*/
//$curURLLvl = $ROUT->GetStartedUrlLvl($allMenu['curLabel']['link'], $allMenu['activeLink'], $uri)+1;
$realPath = $ROUT->GetStartedUrlLvl($allMenu['curLabel']['link'], $allMenu['activeLink'], $uri);
$curURL = $realPath['url'];
$curURLLvl = $realPath['lvl'];

//$act = ($post[$curURLLvl])?($post[$curURLLvl]):'manage';
//if(!$post[2])
if(!$post[$curURLLvl])
	{
	$MTPL = new MANAGE_TEMPLATE;
	$CNT->GetChildren(0, 0, 2, 0, 0, 1, 0, 0);
	$tree = $CNT->child;
	$CNT->Reset();
/*	$smarty->assign('Tree', $TPL->TPL_GetManageTree( $tree));		
	$smarty->assign('Table', $TPL->TPL_GetManageEditNewNode());		*/
/*	$SMRT_TMP['name'] = '_tree';
	$SMRT_TMP['body'] = $tree;
	$SMRT['modules'][] = $SMRT_TMP;*/
	$SMRT_TMP['name'] = 'Tree';
	$SMRT_TMP['body'] = $MTPL->TPL_GetManageTree($tree);
	$SMRT['modules'][] = $SMRT_TMP;
	$SMRT_TMP['name'] = 'Table';
	$SMRT_TMP['body'] = $MTPL->TPL_GetManageEditNewNode($curURL);
	$SMRT['modules'][] = $SMRT_TMP;
//	$templates[] = 'Message.tpl';	
	$templates[] = 'manage_tree.tpl';	
	$templates[] = 'AdminFooter.tpl';
	}
elseif($post[$curURLLvl] == 'rights')
	{
	if($post[$curURLLvl+1])
		{			
		$ACNT = new GetAdmin;
		$ATPL = new ADMIN_TEMPLATE;
//			echo $post[3].'<br>';
		$templates = array();
		$right_tmp = $ACL->GetAllRight($post[$curURLLvl+1], 'SiteCatalog', 'sc_id');
		$right = $ATPL->RightList($post[$curURLLvl+1], $right_tmp);
		$ret['list'] = $ATPL->UsersWithoutRightsList($ACNT->GetAllUsers(0, 1), $ACNT->GetAllGroups(0), $right['userList']);
		$ret['node'] = $post[$curURLLvl+1];
		$SMRT_TMP['name'] = 'users';
		$SMRT_TMP['body'] = $ret;
		$SMRT['modules'][] = $SMRT_TMP;	
		$SMRT_TMP['name'] = 'header';
		$SMRT_TMP['body'] = 'Права доступа';
		$SMRT['modules'][] = $SMRT_TMP;
//					$full_ret['header']  = 'Права доступа';
		$SMRT_TMP['name'] = 'rights';
		$SMRT_TMP['body'] = $right;
		$SMRT['modules'][] = $SMRT_TMP;
		$templates[] = 'GreyBoxConsole_header.tpl';	
		$templates[] = 'rights.tpl';	
		$templates[] = 'AdminFooter.tpl';
		}
	else
		{
		$MESS = new Message('Error', 'ERROR 404', 'Запрошенная Вами страница не найдена', $NAV->GetPrewURI());														
		}	
	}
elseif($post[$curURLLvl] == 'SetDin') /*редактирование узлов с помощью jquery*/
	{
	require_once("../classes/SetContent_class.php");
	$tmp_arr = explode('_', $_POST['id']);
//	$TM = new TimeMesure ('log.txt');
	$SET = new SetContent();
	/*print_r($tmp_arr);	*/
	$setType = $tmp_arr[0];
	$setObj = $tmp_arr[2]; 
	$setNodeId = $tmp_arr[1]; 
//	$TM->TimeCalc($_POST['value']);			
	switch($setType)
		{
		case 'idNodeMain':
			{
			switch($setObj)
				{
				case 'name':
					{
//					$TM->TimeCalc('name');		
					//iconv("UTF-8","windows-1251",$_POST['value']);
					$successMes = $_POST['value'];
					$failureMes = 'update error';
					if($SET->SetNodPar($setNodeId, $setObj, iconv("UTF-8","windows-1251",$_POST['value'])))
						echo $successMes;
					else
						echo $failureMes;
					} break;
				case 'system':
					{
//					$TM->TimeCalc('menu');			
					$successMes = ($_POST['value'])?'<font color="green">true</font>':'<font color="red">false</font>';
					$failureMes = 'update error';
					if($SET->SetNodPar($setNodeId, $setObj, $_POST['value']))
						echo $successMes;
					else
						echo $failureMes;
					} break;
				case 'menu':
					{
//					$TM->TimeCalc('menu');			
					$successMes = ($_POST['value'])?'<font color="green">true</font>':'<font color="red">false</font>';
					$failureMes = 'update error';
					if($SET->SetNodPar($setNodeId, $setObj, $_POST['value']))
						echo $successMes;
					else
						echo $failureMes;
					} break;
				case 'published':
					{
//					$TM->TimeCalc('menu');			
					$successMes = ($_POST['value'])?'<font color="green">true</font>':'<font color="red">false</font>';
					$failureMes = 'update error';
					if($SET->SetNodPar($setNodeId, $setObj, $_POST['value']))
						echo $successMes;
					else
						echo $failureMes;
					} break;
				case 'lang':
					{
//					$TM->TimeCalc('lang');			
					$langAr = $CNT->GetAllLanguages(2);	
					$successMes = $langAr[$_POST['value']];
					$failureMes = 'update error';
					if($SET->SetNodPar($setNodeId, $setObj, $_POST['value']))
						echo $successMes;
					else
						echo $failureMes;
					} break;
				case 'handler':
					{
					$successMes = $_POST['value'];
					$failureMes = 'update error';
					if($SET->SetNodPar($setNodeId, $setObj, iconv("UTF-8","windows-1251",$_POST['value'])))
						echo $successMes;
					else
						echo $failureMes;
					} break;
				case 'order':
					{
//					$TM->TimeCalc('order');			
					$failureMes = 'update error';
					$error = 0;
					$brothers = $CNT->GetBrothers($setNodeId);
					$newPosition = $_POST['value'];
					for($i=0; $i<count($brothers); $i++)
						{
						if($brothers[$i]['catalog']['sc_id'] == $setNodeId)
							$oldPosition = $i;
						}
/*					$TM->TimeCalc(' old Position is '.$oldPosition);	
					$TM->TimeCalc(' new position is '.$newPosition);	*/
					$D = $oldPosition - $newPosition;
					for($i=0; $i<count($brothers); $i++)
						{
						if(($i<$oldPosition)&&($i<$newPosition))
							{
							$curNodeId = $brothers[$i]['catalog']['sc_id'];
							$curIndex = $i;
//							$TM->TimeCalc('1');
							}
						elseif(($i==$newPosition))
							{
							$curNodeId = $brothers[$oldPosition]['catalog']['sc_id'];
							$curIndex = $i;
							$successMes  = $i;
//							$TM->TimeCalc('2');
							}
						elseif(($i>$newPosition)&&($i>$oldPosition))
							{
							$curNodeId = $brothers[$i]['catalog']['sc_id'];
							$curIndex = $i;
//							$TM->TimeCalc('3');
							}
						/******************$D>0***************************/
						elseif(($D>0)&&($i>$newPosition)&&($i<=$oldPosition))
							{
							$curNodeId = $brothers[$i-1]['catalog']['sc_id'];
							$curIndex = $i;
//							$TM->TimeCalc('4');
							}
						/******************$D<0***************************/							
						elseif(($D<0)&&($i>=$oldPosition)&&($i<$newPosition))
							{
							$curNodeId = $brothers[$i+1]['catalog']['sc_id'];
							$curIndex = $i;
//							$TM->TimeCalc('5');
							}
//						$curNodeName = $CNT->GetCurNode($curNodeId);
//						$TM->TimeCalc('new order of '.$curNodeName['catalog']['sc_name'].' is '.$curIndex);							
						if(!$SET->SetNodPar($curNodeId, $setObj, $curIndex))
							{
							$error ++;
							}
						}					
					if(!$error)
						echo $successMes;
					else
						echo $failureMes;						
					} break;
				}			
			}
		
		}
	$templates = array();	
	}	
elseif($post[$curURLLvl] == 'set')
	{
	require_once("../classes/SetContent_class.php");
	$SET = new SetContent();			
	if($post[$curURLLvl+1] == 'del') /*удаление узла*/
		{
		if(isset($PARENT_ID))
			{
			$delCnt = 0;
			$CNT->GetChildren($PARENT_ID, 0, 0, 0, 0, 3, 0, 0);
			$nodes = $CNT->child;
			$CNT->Reset();		
			$nodes[] = $CNT->GetCurNode($PARENT_ID, 0);
			for($i=0; $i<count($nodes); $i++)
				{
				$delCnt = $SET->DeleteNode($nodes[$i]['catalog']['sc_id'], 1);
				}
			if($delCnt == $i )
				{
//				$MESS = new Message('Info', 'Успешное удаление',  'Объект удален', $NAV->GetPrewURI());								
				$REDIRECT = 'http://'.$NAV->GetPrewURI();
				}
			else
				$MESS = new Message('Error', 'Ошибка удаления',  'Не удалось удалить объект', $NAV->GetPrewURI());										
			}
		else
			{
			$patherror ++;
			}
		}
	elseif($post[$curURLLvl+1] == 'rightEdit') /*редактирование прав доступа*/
		{
		$tmp_arr1 = explode('#', $id /*$_POST['id']*/);		
		$tmp_arr2 = explode('_', $tmp_arr1[0]);
		$tmp_arr3 = explode('*', $tmp_arr2[0]);
		$rightId = $tmp_arr1[1];
		$userId = $tmp_arr2[1];
		$objId = $tmp_arr3[1];
		/*
		$rightId = 1;
		$userId = 1;
		$objId = 86;
		$value = 3;
		*/
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
	elseif($post[$curURLLvl+1] == 'right') /*редактирование прав доступа*/
		{
		$skipMess = 1;
		$templates = array();	
		if(isset($user))
			$res = $ACL->SetRight($node, $user, 'SiteCatalog', 'sc_id', 1);
		elseif(isset($rightId))
			$res = $ACL->DeleteRight($rightId);				
		if(!$res['error'])
			{
			$ACNT = new GetAdmin;
			$ATPL = new ADMIN_TEMPLATE;
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
			$right_tmp = $ACL->GetAllRight($node, 'SiteCatalog', 'sc_id');
			$right = $ATPL->RightList($node, $right_tmp);
			$ret['list'] = $ATPL->UsersWithoutRightsList($ACNT->GetAllUsers(0, 1), $ACNT->GetAllGroups(0), $right['userList']);
			$ret['node'] = $node;
			$smarty->assign('users', $ret);
			$smarty->assign('rights', $right);
			$table = $smarty->fetch('rightsList.tpl');
			$successMes = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8", $table):$table;	
			echo $successMes;
			}		
		else
			{
			echo $res['errorMsg'];
			}						
		}
	elseif($post[$curURLLvl+1] == 'delPar') /*удаление свойства узла*/
		{
		if($post[$curURLLvl+2])
			{
			$delCnt = 0;
			$delCnt = $SET->DeleteParam($post[$curURLLvl+2]);
			if($delCnt)
				$MESS = new Message('Info', 'Успешное удаление',  'Объект удален', $NAV->GetPrewURI());								
			else
				$MESS = new Message('Error', 'Ошибка удаления',  'Не удалось удалить объект', $NAV->GetPrewURI());										
			}
		else
			{
			$patherror ++;
			}
		}
	elseif($post[$curURLLvl+1] == 'editNodePar')  /*редактирование параметра*/
		{
//		echo '<hr>'.$post[4];
		if(get_magic_quotes_gpc())
			{
			$value = stripslashes($PAR_VALUE);
			$name = stripslashes($PAR_NAME);
			}
		else
			{
			$value = $PAR_VALUE;
			$name = $PAR_NAME;
			}
		$error = 0;
		$body = array('name' => $PAR_NAME, 'value' => mysql_escape_string($value)/*, 'type' => $PAR_TYPE*/);
		if(($PAR_TYPE=='PAGEHEADER')||($PAR_TYPE=='VARCHAR'))
			{
			$body['type'] = 'varchar';
			}
		elseif(($PAR_TYPE=='INFOBLOCK')||($PAR_TYPE=='TEXT'))
			{
			$body['type'] = 'text';
			}
		if(!$SET->editAddPar($body, $post[$curURLLvl+2], 0))
			{
			$error ++;
			}		
		$templates = array();	
		if($error)
			echo '<hr>bad!!!';
		else
			{
			$templates[] = 'selfCloseBlank.tpl';					
			}
		}
	elseif($post[$curURLLvl+1] == 'addNewNodePar') /*добавление нового параметра*/
		{
		$templates = array();	
//		echo '<hr>'.$post[4];
		if(get_magic_quotes_gpc())
			{
			$value = stripslashes($PAR_VALUE);
			}
		else
			{
			$value = $PAR_VALUE;
			}
		$error = 0;
		$body = array('name' => $PAR_NAME, 'value' => mysql_escape_string($value)/*, 'type' => $PAR_TYPE*/);
		if(($PAR_TYPE=='PAGEHEADER')||($PAR_TYPE=='VARCHAR'))
			{
			$body['type'] = 'varchar';
			}
		elseif(($PAR_TYPE=='INFOBLOCK')||($PAR_TYPE=='TEXT'))
			{
			$body['type'] = 'text';
			}
		if(!$SET->AddParToNode($body, $post[$curURLLvl+2]))
			{
			$error ++;
			}		
		$templates = array();	
		if($error)
			echo '<hr>bad!!!';
		else
			{
			$MESS = new Message('Info', 'Успешное создание',  'Параметр успешно добавлен', $NAV->GetPrewURI());								
			$_SESSION['MESSAGE'] = $MESS;
			$templates = array();
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
			$smarty->assign('nodeId', $post[$curURLLvl+2]);
			$smarty->display('selfCloseBlank.tpl');
			}
			//echo '<hr>good!!!';	
//		print_r($_POST);
		}
	elseif(!$post[$curURLLvl+1])  /*добавление нового узла*/
		{				
		/*print_r($_POST);		
		print_r($_FILES);		*/
		$param = array('name' => $NODE_NAME, 'parId' => $PARENT_ID, 'order' => $NODE_ORDER, 'lang' => $NODE_LANG);
		if($NODE_ORDER == 'last')
			{			
//			GetChildren1Level($PARENT_ID, 0, 0)
			$param['order'] = count($CNT->GetChildren1Level($PARENT_ID, 0, 0));
			}
		elseif($NODE_ORDER == 'first')
			{
			$setObj = 'order';
			$newPosition = 0;
			$error = 0;
			$brothers = $CNT->GetBrothers($PARENT_ID);
			for($i=0; $i<count($brothers); $i++)
				{
				if($i<$newPosition)
					{
					$curNodeId = $brothers[$i]['catalog']['sc_id'];
					$curIndex = $i;
					}
				elseif($i==$newPosition)
					{
					$curNodeId = -1;
					$curIndex = -1;
					}
				elseif($i>$newPosition)
					{
					$curNodeId = $brothers[$i]['catalog']['sc_id'];
					$curIndex = $i+1;
					}
				if($curNodeId>=0)
					{
					if(!$SET->SetNodPar($curNodeId, $setObj, $curIndex))
						{
						$error ++;
						}		
					}
				}
			$param['order'] = $newPosition;								
			}
		if($_FILES['NODE_HANDLER']['name'])
			{
			if (is_uploaded_file($_FILES['NODE_HANDLER']['tmp_name']))
				{
				$path = $CONST['PATH_HANDLER'];
				copy($_FILES['NODE_HANDLER']['tmp_name'], '../'.$path.'/'.$_FILES['NODE_HANDLER']['name']);							
				$param['handler'] = $_FILES['NODE_HANDLER']['name'];			
				}		
			}
		else
			$param['handler'] = '';			
		if (isset($NODE_MENU))
			{
			$param['is_menu'] = 1;
			$tmpBody['name'] = 'caption';
			$tmpBody['value'] = $MENU_LABEL;
			$tmpBody['type'] = 'varchar';
			$body[] = $tmpBody;			
			$tmpBody['name'] = 'link';
			$tmpBody['value'] = $MENU_LINK;
			$tmpBody['type'] = 'varchar';
			$body[] = $tmpBody;			
			}
		else
			{
			$body = array();
			$param['is_menu'] = 0;		
			}
/*		echo '<hr>';
		print_r($param);*/
		$error += $SET->CreateNodeWithBody($param, $body);
		if($error)
			{
			$MESS = new Message('Info', 'Успешное создание',  'Объект успешно создан', $NAV->GetPrewURI());								
			}		
		else
			{
			$MESS = new Message('Error', 'Ошибка создания',  'Не удалось создать объект', $NAV->GetPrewURI());										
			}
		}
	}
else
	{
	$patherror++;
	}
if($patherror)
	{
	$MESS = new Message('Error', 'ERROR 404', 'Запрошенная Вами страница не найдена', $NAV->GetPrewURI());											
	}
if((!$skipMess)&&(isset($MESS)))
	{
	$_SESSION['MESSAGE'] = $MESS;
	if(!in_array('selfCloseBlank.tpl', $templates))
		header('Location: /');
	}
elseif($REDIRECT)
	{
	header('Location: '.$REDIRECT);	
	}

//$smarty->display('manage_tree.tpl');
?>