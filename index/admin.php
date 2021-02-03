<?php
$templates = array();	
$templates[] = 'AdminHeader.tpl';
$templates[] = 'AdminMenu.tpl';

$realPath = $ROUT->GetStartedUrlLvl($allMenu['curLabel']['link'], $allMenu['activeLink'], $uri);
$curURL = $realPath['url'];
$curURLLvl = $realPath['lvl'];

//echo '<br>lastLink = '.$allMenu['lastLink'].'; uri = '.$uri.'; curLvl = '.$curURLLvl.'<br>';
$act = ($post[$curURLLvl])?($post[$curURLLvl]):'users';

//$act = ($post[2])?$post[2]:'users';
require_once("../classes/TPL_admin_class.php");

$ACNT = new GetAdmin;
$ATPL = new ADMIN_TEMPLATE;
switch ($act)
	{
	case 'set': 
		{
		$SET = new SetAdmin;
		if($post[$curURLLvl+1])
			{
			switch ($post[$curURLLvl+1])
				{
				case 'rightEdit': 
					{
//					$TM = new TimeMesure ('log_admin_set.txt');
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
//					$TM->TimeCalc($xml);			
					}
				break;				
				case 'right': 
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
				break;				
				case 'rmUser': 
					{
					if($post[$curURLLvl+2])
						{
						$userId = $post[$curURLLvl+2];
						$ret = $SET->DeleteUser($userId);
						if(!$ret['error'])
							{
							$MESS = new Message('Info', 'Успешное удаление',  'Объект удален', $NAV->GetPrewURI());								
							}		
						else
							{
							$MESS = new Message('Error', 'Ошибка удаления',  'Не удалось удалить объект', $NAV->GetPrewURI());										
							}						
						}
					else		
						{
						$MESS = new Message('Error', 'Ошибка удаления',  'Не удалось удалить объект', $NAV->GetPrewURI());										
						}
					}
				break;				
				case 'adduser': 
					{
//					print_r($_POST);
					$templates[] = 'selfCloseBlank.tpl';	
					if($USER_PSW_1 == $USER_PSW_2)
						{
						$ret = $SET->CreateUser($USER_NAME, $USER_EMAIL, $USER_PSW_1);
						if($ret['error'])
							{
							$MESS = new Message('Error', 'Ошибка создания',  'Не удалось создать пользователя', $NAV->GetPrewURI());										
							}
						else
							{
							$userId = $ret['last_id'];
							for($i=0; $i<count($USER_GROUPS); $i++)
								{
								$ret = $SET->AddUserToGroup($userId, $USER_GROUPS[$i]);
								if($ret['error'])
									{
									$MESS = new Message('Error', 'Ошибка создания',  'Не удалось создать пользователя', $NAV->GetPrewURI());										
									}
								else
									{
									$MESS = new Message('Info', 'Успешное создание',  'Пользователь успешно создан', $NAV->GetPrewURI());								
									}
								}
							}
						}
					else
						$MESS = new Message('Error', 'Ошибка создания',  'Не удалось создать пользователя: пароли не совпадают', $NAV->GetPrewURI());										
					}
				break;
				case 'addgroup': 
					{
//					print_r($_POST);
					$ret = $SET->CreateGroup($USER_NAME);
					$templates[] = 'selfCloseBlank.tpl';	
					if($ret['error'])
						{
						$MESS = new Message('Error', 'Ошибка создания',  'Не удалось создать группу', $NAV->GetPrewURI());										
						}
					else
						{
						$groupId = $ret['last_id'];
						for($i=0; $i<count($USER_2USER); $i++)
							{
							$ret = $SET->AddUserToGroup($USER_2USER[$i], $groupId);
							if($ret['error'])
								{
								$MESS = new Message('Error', 'Ошибка создания',  'Не удалось создать группу', $NAV->GetPrewURI());										
								}
							}
						}
					if(!$MESS)
						$MESS = new Message('Info', 'Успешное создание',  'Группа успешно создана', $NAV->GetPrewURI());														
					}
				break;
				default: 
					{
					$MESS = new Message('Error', 'ERROR 404', 'Запрошенная Вами страница не найдена', $NAV->GetPrewURI());								
					}
				}
			}
		else
			{
			$MESS = new Message('Error', 'ERROR 404', 'Запрошенная Вами страница не найдена', $NAV->GetPrewURI());											
			}		
		//$templates = array();
/*		if((!$skipMess)&&(isset($MESS)))
			{
			$_SESSION['MESSAGE'] = $MESS;
			header('Location: http://'.$NAV->GetPrewURI());	

			}*/
		}
	break;
	case 'userProperties': 
		{
		if($post[$curURLLvl+1])
			{
			$templates = array();
			$ret['curUser'] = $ACNT->GetCurUser('id', $post[$curURLLvl+1]);
			$up = $ATPL->UserProperties($ret['curUser']);
			$SMRT_TMP['name'] = 'up';
			$SMRT_TMP['body'] = $up;
			$SMRT['modules'][] = $SMRT_TMP;	
			$SMRT_TMP['name'] = 'header';
			$SMRT_TMP['body'] = 'Дополнительные свойства';
			$SMRT['modules'][] = $SMRT_TMP;
			$templates[] = 'GreyBoxConsole_header.tpl';	
			$templates[] = 'UserProperties.tpl';	
			$templates[] = 'AdminFooter.tpl';
			}
		else
			{
			$MESS = new Message('Error', 'ERROR 404', 'Запрошенная Вами страница не найдена', $NAV->GetPrewURI());											
			}
		}
	break;
	case 'users': 
		{
		$userList = $ACNT->GetAllUsers(0, 1);
		$groupList = $ACNT->GetAllGroups(0);
		$SMRT_TMP['name'] = 'users';
		$SMRT_TMP['body'] = $ATPL->UserList($userList);// $userList;
		$SMRT_TMP['body'] = $ATPL->UserList($userList);// $userList;
		$SMRT['modules'][] = $SMRT_TMP;	
		$SMRT_TMP['name'] = 'groups';
		$SMRT_TMP['body'] = $ATPL->GroupList($groupList);// $userList;
		$SMRT['modules'][] = $SMRT_TMP;	
		$templates[] = 'users.tpl';	
		$templates[] = 'AdminFooter.tpl';
		}
	break;
	case 'rights': 
		{
		if($post[$curURLLvl+1])
			{			
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
	break;
	case 'members': 
		{
		if($post[$curURLLvl+1])
			{
			$templates = array();
			$ret['curGroup'] = $ACNT->GetCurUser('id', $post[$curURLLvl+1]);
			$ret['groupUserList'] = $ACNT->GetGroupUsers(0, $post[$curURLLvl+1]);
			$ret['AllUsersList'] = $ACNT->GetAllUsers(0, 0);
			$SMRT_TMP['name'] = 'ret';
			$SMRT_TMP['body'] = $ret;
			$SMRT['modules'][] = $SMRT_TMP;	
			$SMRT_TMP['name'] = 'header';
			$SMRT_TMP['body'] = 'Члены группы';
			$SMRT['modules'][] = $SMRT_TMP;
			$groups = $ATPL->Members($ret);
			$SMRT_TMP['name'] = 'groups';
			$SMRT_TMP['body'] = $groups;
			$SMRT['modules'][] = $SMRT_TMP;	
			$templates[] = 'GreyBoxConsole_header.tpl';	
			$templates[] = 'membership.tpl';	
			$templates[] = 'AdminFooter.tpl';
			}
		else
			{
			$MESS = new Message('Error', 'ERROR 404', 'Запрошенная Вами страница не найдена', $NAV->GetPrewURI());											
			}
		}
	break;
	case 'membership': 
		{
		if($post[$curURLLvl+1])
			{
			$templates = array();
			$ret['curUser'] = $ACNT->GetCurUser('id', $post[$curURLLvl+1]);
			$ret['userGroupList'] = $ACNT->GetUserGroups(0, $post[$curURLLvl+1]);
			$ret['AllGroupList'] = $ACNT->GetGroups(2, 0);
//			$ret['AllGroupList'] = $ACNT->GetAllGroups(0);
//			print_r($ret);
/*			$SMRT_TMP['name'] = 'ret';
			$SMRT_TMP['body'] = $ret;
			$SMRT['modules'][] = $SMRT_TMP;	*/
			$groups = $ATPL->Membership($ret);
			$SMRT_TMP['name'] = 'header';
			$SMRT_TMP['body'] = 'Членство в группах';
			$SMRT['modules'][] = $SMRT_TMP;
			$SMRT_TMP['name'] = 'groups';
			$SMRT_TMP['body'] = $groups;
			$SMRT['modules'][] = $SMRT_TMP;	
			$templates[] = 'GreyBoxConsole_header.tpl';	
			$templates[] = 'membership.tpl';	
			$templates[] = 'AdminFooter.tpl';
			}
		else
			{
			$MESS = new Message('Error', 'ERROR 404', 'Запрошенная Вами страница не найдена', $NAV->GetPrewURI());											
			}
		}
	break;
	case 'addUser': 
		{
		$templates = array();/*	*/
		$addUser = $ATPL->addUser($ACNT->GetGroups(0, 0));
		
		$SMRT_TMP['name'] = 'header';
		$SMRT_TMP['body'] = 'Новый пользователь';
		$SMRT['modules'][] = $SMRT_TMP;
		
		$SMRT_TMP['name'] = 'addUser';
		$SMRT_TMP['body'] = $addUser;
		$SMRT['modules'][] = $SMRT_TMP;	
		$templates[] = 'GreyBoxConsole_header.tpl';	
		$templates[] = 'addUser.tpl';	
		$templates[] = 'AdminFooter.tpl';
		}
	break;
	case 'addGroup': 
		{
		$templates = array();/*	*/
		$addUser = $ATPL->addGroup($ACNT->GetAllUsers(0, 0));
		$SMRT_TMP['name'] = 'addUser';
		$SMRT_TMP['body'] = $addUser;
		$SMRT['modules'][] = $SMRT_TMP;	
		$SMRT_TMP['name'] = 'header';
		$SMRT_TMP['body'] = 'Новая группа';
		$SMRT['modules'][] = $SMRT_TMP;
		$templates[] = 'GreyBoxConsole_header.tpl';	
		$templates[] = 'addUser.tpl';	
		$templates[] = 'AdminFooter.tpl';
		}
	break;
	case 'SetDin': 
		{
		$templates = array();	
		$SET = new SetAdmin;
		if($_POST['id'])
			{
//			$TM = new TimeMesure ('log_XML_.txt');
			$tmp_arr = explode('#', $_POST['id']);
			$type = $tmp_arr[0];
			$id = $tmp_arr[1];
			switch($type)
				{
				case 'user_email':
					{
					}	 
				case 'user_name':
					{
					$successMes = $_POST['value'];
					$failureMes = 'update error';
					if($SET->SetUserPar($id, $type, iconv("UTF-8","windows-1251",$_POST['value'])))
						echo $successMes;
					else
						echo $failureMes;				
					}	break; 
				case 'user_password':
					{
					$successMes = '*********';
					$failureMes = 'update error';
					if($SET->SetUserPar($id, $type, iconv("UTF-8","windows-1251",$_POST['value'])))
						echo $successMes;
					else
						echo $failureMes;				
					}	break;
				case 'user_registation_status':
					{
					$opt = array('Q' => 'Запрос', 'A'=>'Активирован', 'B' => 'заблокирован');
					switch($_POST['value'])
						{
						case 'Q': $successMesTmp = '<font color = "blue">'.$opt[$_POST['value']].'</font>'; break;//iconv("windows-1251","UTF-8",'<font color = "blue">'.$opt[$_POST['value']].'</font>')
						case 'A': $successMesTmp = '<font color = "green">'.$opt[$_POST['value']].'</font>'; break;//iconv("windows-1251","UTF-8",'<font color = "green">'.$opt[$_POST['value']].'</font>'); break;
						case 'B': $successMesTmp = '<font color = "red">'.$opt[$_POST['value']].'</font>'; break;//iconv("windows-1251","UTF-8",'<font color = "red">'.$opt[$_POST['value']].'</font>'); break;
						}
					$successMes = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8", $successMesTmp):$successMesTmp;							
					$failureMes = 'update error';
					if($SET->SetUserPar($id, $type, $_POST['value']))
						echo $successMes;
					else
						echo $failureMes;				
					}	break;
				
				}
//			$TM->TimeCalc($successMes);			
			}
		elseif($post[$curURLLvl+1] == 'membership')
			{
			if($_POST['user'])
				{
				$userGroupList = $ACNT->GetUserGroups(0, $_POST['user']);
				for($i=0; $i<count($userGroupList); $i++)
					{
					$userGroup[] = $userGroupList[$i]['group_id'];
					}					
				$allGroupList = $ACNT->GetAllGroups(0);
				$tmp_arr1 = explode ('&', $_POST['list']);
				for($i=0; $i<count($tmp_arr1); $i++)
					{
					$tmp_arr2 = explode ('_', $tmp_arr1[$i]);
					$grId[] = $tmp_arr2[1];
					}
				for($i = 0; $i< count($allGroupList); $i++)
					{
					if(in_array($allGroupList[$i]['user_id'], $grId))
						{
						if(!in_array($allGroupList[$i]['user_id'], $userGroup))
							{
							$ret = $SET->AddUserToGroup($_POST['user'], $allGroupList[$i]['user_id']);
							if($ret)
								{
//								$TM->TimeCalc('Add - error - '.$ret);
								}
							else
								{
//								$TM->TimeCalc('Add - success!!!');
								}
							
							}
						}
					else
						{
						if(in_array($allGroupList[$i]['user_id'], $userGroup))
							{
							$ret = $SET->RemoveUserFromGroup($_POST['user'], $allGroupList[$i]['user_id']);						
							if($ret)
								{
//								$TM->TimeCalc('Remove - error - '.$ret);			
								}
							else
								{
//								$TM->TimeCalc('Remove - success!!!');
								}
							}
						}
					}
				}
			}
		elseif($post[$curURLLvl+1] == 'members')
			{
//			$TM = new TimeMesure ('logGr.txt');			
			if($_POST['user'])
				{
/*				$TM->TimeCalc('user - '.$_POST['user']);			
				$TM->TimeCalc('list - '.$_POST['list']);		*/
				$SET->ClearGroup($_POST['user']);
				$tmp_arr1 = explode ('&', $_POST['list']);
				for($i=0; $i<count($tmp_arr1); $i++)
					{
					$tmp_arr2 = explode ('_', $tmp_arr1[$i]);
					$usId[] = $tmp_arr2[1];
//					$TM->TimeCalc('in group'.$i.': '.$tmp_arr2[1]);			
					}				
				for($l = 0; $l< count($usId); $l++)
					{
					$ret = $SET->AddUserToGroup($usId[$l], $_POST['user']);
					if($ret)
						{
//						$TM->TimeCalc('Add - error - '.$ret['errorMsg']);
						}
					else
						{
//						$TM->TimeCalc('Add - success!!!');
						}
					}
				}
			}
		}
	break;
	default :
		{
		$MESS = new Message('Error', 'ERROR 404', 'Запрошенная Вами страница не найдена', $NAV->GetPrewURI());											
/*		$SMRT_TMP['name'] = 'MESS';
		$SMRT_TMP['body'] = $MESS;
		$SMRT['modules'][] = $SMRT_TMP;
		$templates[] = 'Message.tpl';				
		$templates[] = 'AdminFooter.tpl';*/
		}
	};
if(/*($act!='set')*/(!$skipMess)&&(isset($MESS)))
	{
	$_SESSION['MESSAGE'] = $MESS;
//	header('Location: /');
	if(!in_array('selfCloseBlank.tpl', $templates))
//		header('Location: http://'.$NAV->GetPrewURI());	
		header('Location: /');
	}

?>