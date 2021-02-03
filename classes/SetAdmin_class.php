<?php
class SetAdmin
	{
	var $last_query;
	var $last_id;
	function CheckRemindedUsersStatus($list, $period) /*17_10_07 проверка статуса неподтвержденных новых пользователей */
		{
		$queryUpdatePre = 'UPDATE `users` set `user_reminded_pasword` = \'\',  `user_reminded_code` = \'\' where `user_id` = \'';
		$LNK= new DBLink;				
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		for($i=0; $i<count($list); $i++)
			{
			$pub = strtotime($list[$i]['user_reminded_date']);
			if(($pub+$period)<time())
				{
				$queryUpdate = $queryUpdatePre.$list[$i]['user_id'].'\'';
				$LNK->Query ($queryUpdate);						
				}
			}		
		}
	function CheckQueryedUsersStatus($list, $period) /*17_10_07 проверка статуса неподтвержденных новых пользователей */
		{
		for($i=0; $i<count($list); $i++)
			{
			$pub = $list[$i]['user_regdate'];
			if(($pub+$period)<time())
				{
//				echo $list[$i]['dt_status'].'<br>';
				$this->DeleteUser($list[$i]['user_id']);
				}
			}		
		}
	function setUserInfo($param, $userId) //обновляет параметры пользователя  (12_03_07)
		{
//		global $USER;
		$error = 0;		
		if((($param['USER_PSW_2'])||($param['USER_PSW_1']))&&(($param['USER_PSW_2'])!=($param['USER_PSW_1'])))
			{
			$ret['error'] = 2;
			$ret['errorMsg'] = 'Указанные пароли не совпадают!';
			$error ++;
			}		
		if(!$error)
			{
			$update = '';
			if($param['UserRegName'])
				$update .= ' user_name = '.$param['UserRegName'];
			if($param['EMail'])
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_email = \''.$param['EMail'].'\'';
				}
			if(isset($param['FName']))
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_first_name = \''.$param['FName'].'\'';
				}
			if(isset($param['SName']))
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_so_name = \''.$param['SName'].'\'';
				}
			if(isset($param['LName']))
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_last_name = \''.$param['LName'].'\'';
				}
				
			if(isset($param['USER_ZIP']))
				{
				$update .= ($update)?', ':' ';
				$update .= ' zipCode = \''.$param['USER_ZIP'].'\'';
				}
			if(isset($param['USER_SITY']))
				{
				$update .= ($update)?', ':' ';
				$update .= ' city = \''.$param['USER_SITY'].'\'';
				}
			if(isset($param['USER_PHONE']))
				{
				$update .= ($update)?', ':' ';
				$update .= ' phone = \''.$param['USER_PHONE'].'\'';
				}
			if(isset($param['USER_ADR']))
				{
				$update .= ($update)?', ':' ';
				$update .= ' address = \''.$param['USER_ADR'].'\'';
				}
				
			if(isset($param['UIN']))
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_icq_uin = '.$param['UIN'];
				}
			if(isset($param['About']))
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_about = \''.trim(strip_tags($param['About'].'\'', '<a><b><i><u>'));
				}
			if(isset($param['avatar']))
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_avatar = \''.$param['avatar'].'\'';
				}
			if($param['USER_PSW_1'])
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_password = PASSWORD(\''.$param['USER_PSW_1'].'\')';
				}
			if($update)
				{
				$query = 'UPDATE `users` set '.$update.' WHERE user_id = \''.$userId.'\'';		
				$LNK= new DBLink;		
//			$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
				$LNK->Query ($query);
				if($LNK->error)
					{
					$ret['error'] = 1;
					$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
					}
				else
					{
					$ret['error'] = 0;
					$ret['last_id'] = $LNK->last_id;// = $LNK->error_string;
					}
				}
			else
				{
				$ret['error'] = 0;
				}
			}
		return $ret;		
		}
	function RemoveUserFromAll($user) //05_09_2007исключение пользователя из всех групп
		{
		$ret = array();
		$query_delete = 'Delete from userToUser WHERE user_id = '.$user;
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query_delete);		
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$ret['error'] = 0;
			}
		return $ret;
		}
	function CreateActiveUser($post) /*04_09_2007 добавляет пользователя*/
		{
/*		print_r($post);
		echo '<hr>';*/
		$query_insert = 'INSERT INTO users SET ';
		$ret = array();
		$regDate = time();
		if($post['USER_NAME']) 
			$query_insert .= ' user_name = \''.$post['USER_NAME'].'\'';
		if($post['USER_PSW_1']) 
			$query_insert .= ', user_password = PASSWORD(\''.$post['USER_PSW_1'].'\')';
		if($post['USER_EMAIL']) 
			$query_insert .= ', user_email = \''.$post['USER_EMAIL'].'\'';
		if($post['USER_ZIP']) 
			$query_insert .= ', zipCode = \''.$post['USER_ZIP'].'\'';
		if($post['USER_SITY']) 
			$query_insert .= ', city = \''.$post['USER_SITY'].'\'';
		if($post['USER_ADR']) 
			$query_insert .= ', address = \''.$post['USER_ADR'].'\'';
		if($post['USER_PHONE']) 
			$query_insert .= ', phone = \''.$post['USER_PHONE'].'\'';
		if($post['FName']) 
			$query_insert .= ', user_first_name = \''.$post['FName'].'\'';
		if($post['SName']) 
			$query_insert .= ', user_so_name = \''.$post['SName'].'\'';
		if($post['LName']) 
			$query_insert .= ', user_last_name = \''.$post['LName'].'\'';
		$query_insert .= ', user_regdate = \''.$regDate.'\', user_registation_status = \'A\'';
//		echo $query_insert;
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query_insert);		
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$ret['error'] = 0;
			$ret['last_id'] = $LNK->last_id;// = $LNK->error_string;
			}
		return $ret;
		}
	function CreatePasRemind($code, $pswrd,  $id) /*24_05_2007 генерирует пароль и кодовое слово для восстановления пароля*/
		{
		$ret = array();
		$regDate = time();
		$query_update = 'UPDATE users SET user_reminded_code = \''.$code.'\', user_reminded_pasword = PASSWORD(\''.$pswrd.'\'), 
							user_reminded_date = FROM_UNIXTIME( \''.time().'\' )
							WHERE user_id = '.$id;
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query_update);		
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$ret['error'] = 0;
			$ret['last_id'] = $LNK->last_id;// = $LNK->error_string;
			}
		return $ret;
		}
	function CreateUserQuery($post, /*$name, $email, $psw, */$sid)
		{
		$ret = array();
		$regDate = time();
		$query_insert = 'INSERT INTO users SET user_name = \''.trim($post['USER_NAME']).'\', user_password = PASSWORD(\''.$post['USER_PSW_1'].'\'), 
							user_email = \''.trim($post['USER_EMAIL']).'\', user_regdate = \''.$regDate.'\', 
							zipCode = \''.$post['USER_ZIP'].'\', city = \''.$post['USER_SITY'].'\', 
							address = \''.$post['USER_ADR'].'\', phone = \''.$post['USER_PHONE'].'\', 
							user_first_name = \''.$post['USER_FNAME'].'\', user_so_name = \''.$post['USER_SNAME'].'\', 
							user_last_name = \''.$post['USER_LNAME'].'\', 
							displayName = \''.trim($post['USER_NAME']).'\', 
							user_registation_status = \'Q\', user_sid = \''.$sid.'\'';
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query_insert);		
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$ret['error'] = 0;
			$ret['last_id'] = $LNK->last_id;// = $LNK->error_string;
			}
		return $ret;
		}
	function ClearGroup($grId)
		{
		$ret = array();
		$query_delete2 = 'Delete from userToUser WHERE group_id = '.$grId;
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query_delete2);		
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$ret['error'] = 0;				
			}
		return $ret;
		}
	function DeleteUser($userId)
		{
		$ret = array();
		$query_delete1 = 'Delete from users WHERE user_id = '.$userId.' AND is_system !=1';
		$query_delete2 = 'Delete from userToUser WHERE user_id = '.$userId.' OR group_id = '.$userId;
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query_delete1);		
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$LNK->Query ($query_delete2);		
			if($LNK->error)
				{
				$ret['error'] = 1;
				$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
				}			
			else
				$ret['error'] = 0;				
			}
		return $ret;
		}
	function CreateGroup($name)
		{
		$ret = array();
		$regDate = time();
		$query_insert = 'INSERT INTO users SET user_name = \''.$name.'\', is_group = 1';
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query_insert);		
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$ret['error'] = 0;
			$ret['last_id'] = $LNK->last_id;// = $LNK->error_string;
			}
		return $ret;
		}
	function CreateUser($name, $email, $psw)
		{
		$ret = array();
		$regDate = time();
		$query_insert = 'INSERT INTO users SET user_name = \''.$name.'\', user_password = PASSWORD(\''.$psw.'\'), user_email = \''.$email.'\', user_regdate = \''.$regDate.'\', user_registation_status = \'A\'';
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query_insert);		
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$ret['error'] = 0;
			$ret['last_id'] = $LNK->last_id;// = $LNK->error_string;
			}
		return $ret;
		}
	function RemoveUserFromGroup($user, $group)
		{
		$ret = array();
		$query_delete = 'Delete from userToUser WHERE user_id = '.$user.' AND group_id = '.$group;
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query_delete);		
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$ret['error'] = 0;
			}
		return $ret;
		}
	function AddUserToGroup($user, $group)
		{
		$ret = array();
		$query_update = 'INSERT INTO userToUser SET user_id = '.$user.', group_id = '.$group;
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query_update);		
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$ret['error'] = 0;
			}
		return $ret;
		}
	function SetUserPar($id, $parName, $parVal)
		{
/*		switch($parName)
			{
			case 'name': $colName = 'sc_name'; break;
			case 'lang': $colName = 'lang_id'; break;
			case 'menu': $colName = 'sc_menu'; break;
			case 'published': $colName = 'sc_published'; break;
			case 'handler': $colName = 'sc_handler'; break;
			case 'order': $colName = 'sc_order'; break;
			}*/
		if($parName == 'user_password')
			$parVal = "PASSWORD( '".$parVal."' )";
		else
			$parVal = "'".$parVal."'";
		$ret = 0;
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$query_update = 'UPDATE users SET '.$parName.' = '.$parVal.' where user_id = '.$id;
		$LNK->Query ($query_update);		
		if(!$LNK->error)
			{
			$ret++;// = $LNK->error_string;
			}
		return $ret;
		}
	}
?>