<?php
//require_once("main.inc.php");
class CurrentUser
	{
    var $name;
    var $displayName;
	var $id;
	var $sId;
    var $ip;
	var $reg_error;
	var $registered;
	var $language;
	var $appName;
	var $appVersion;
	var $providerName;
	var $providerTitle;
	var $providerId;
	var $isMng;
	function createExtendedUser($userName, $profile, $providerId) //создать внешнего пользователя     (2013_05_06)
		{
		$ret = array();
		$regDate = time();
		if($profile->displayName)
			$displayName = $profile->displayName;
		else
			{			
			$displayName = '';
			if($profile->firstName)
				$displayName .= $profile->firstName;
			if($profile->lastName)
				{
				if ($displayName!='')
					$displayName .= ' ';
				$displayName .= $profile->lastName;					
				}
			if ($displayName == '')			
				$displayName .= $profile->email;
			}
		$optAdd = '';
		$optAdd .= ($profile->webSiteURL) 	? ' webSiteURL = \''.$profile->webSiteURL.'\', ' : '';
		$optAdd .= ($profile->profileURL) 	? ' profileURL = \''.$profile->profileURL.'\', ' : '';
		$optAdd .= ($profile->photoURL) 	? ' user_avatar = \''.$profile->photoURL.'\', ' : '';
		$optAdd .= ($profile->description) 	? ' user_about = \''.$profile->description.'\', ' : '';
		$optAdd .= ($profile->firstName) 	? ' user_first_name = \''.$profile->firstName.'\', ' : '';
		$optAdd .= ($profile->lastName) 	? ' user_last_name = \''.$profile->lastName.'\', ' : '';
		$optAdd .= ($profile->gender) 		? ' gender = \''.$profile->gender.'\', ' : '';
		$optAdd .= ($profile->age) 			? ' age = \''.$profile->age.'\', ' : '';
		$optAdd .= ($profile->birthDay) 	? ' birthDay = \''.$profile->birthDay.'\', ' : '';
		$optAdd .= ($profile->birthMonth) 	? ' birthMonth = \''.$profile->birthMonth.'\', ' : '';
		$optAdd .= ($profile->birthYear) 	? ' birthYear = \''.$profile->birthYear.'\', ' : '';
		$optAdd .= ($profile->email) 		? ' user_email = \''.$profile->email.'\', ' : '';
		$optAdd .= ($profile->emailVerified)? ' emailVerified = \''.$profile->emailVerified.'\', ' : '';
		$optAdd .= ($profile->phone) 		? ' phone = \''.$profile->phone.'\', ' : '';
		$optAdd .= ($profile->address) 		? ' address = \''.$profile->address.'\', ' : '';
		$optAdd .= ($profile->country) 		? ' country = \''.$profile->country.'\', ' : '';
		$optAdd .= ($profile->region) 		? ' region = \''.$profile->region.'\', ' : '';
		$optAdd .= ($profile->city) 		? ' city = \''.$profile->city.'\', ' : '';
		$optAdd .= ($profile->zip) 			? ' zipCode = \''.$profile->zip.'\', ' : '';
		$pswrd = $userName.$regDate;
		$query_insert = 'INSERT INTO users 
						SET user_name = \''.$userName.'\', 
						user_password = PASSWORD(\''.$pswrd.'\'), '.$optAdd.'
						up_id = \''.$providerId.'\', 
						user_regdate = \''.$regDate.'\', 
						displayName = \''.$displayName.'\', 
						user_registation_status = \'A\'';
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
			$ret['displayName'] = $displayName;
			$ret['last_id'] = $LNK->last_id;// = $LNK->error_string;
			}
		return $ret;
		}
	function createSimpleUser($userName, $pswrd) //создать внешнего пользователя     (2019_06_17)
		{
		$ret = array();
		$regDate = time();
		$displayName = '';
		$optAdd = '';

//		$pswrd = $userName.$regDate;
		$query_insert = 'INSERT INTO users 
						SET user_name = \''.$userName.'\', 
						user_password = PASSWORD(\''.$pswrd.'\'), '.$optAdd.'
						user_regdate = \''.$regDate.'\', 
						displayName = \''.$displayName.'\', 
						user_registation_status = \'A\'';
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
	function checkProvUser($userName) //зареган ли внешний пользовател у нас?     (2013_05_06)
		{
		$query = 'SELECT * FROM users WHERE user_name = \''.$userName.'\' 
										AND up_id > \'0\' 
										AND user_registation_status = \'A\'';
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query);
		$LNK->Close_link();
		$ret_arr  = array('user_name', 'user_id', 'displayName');	
		$ret = ($LNK->GetNumRows()) ? $LNK->GetData($ret_arr, false) : 0;			
		return $ret;
		}
	function getProvider($provName) //получает провайдера OAUTH   (2013_05_06)
		{
		$ret = 0;
		$query = 'SELECT * FROM userProviders 
							WHERE up_name = \''.$provName.'\' ';		
		$ret_arr  = array('up_id', 'up_name', 'up_fullName', 'up_logo');		
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if ($LNK->GetNumRows())
			{
			$ret = $LNK->GetData($ret_arr, false);
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		
		}
	function getUserListByNameBegin($userName, $systemInc)
		{
		$systemAdd = ($systemInc)?'':' AND is_system = \'0\' ';
		$query = 'SELECT * FROM users 
					WHERE user_name LIKE \''.$userName.'%\'  
					AND user_registation_status = \'A\' 
					AND is_group = \'0\' '.$systemAdd.' 					 
					ORDER BY user_name ';
		$LNK= new DBLink;		
		$ret_arr  = array('user_id', 'user_name', 'is_group', 'is_system', 'user_password',
						'user_reminded_pasword', 'user_email', 'user_regdate', 'user_status', 
						'user_last_ip', 'user_time_last_update', 'user_registation_status', 
						'user_avatar', 'user_sid', 'user_first_name', 'user_so_name', 
						'user_last_name', 'user_icq_uin', 'user_about',
						'zipCode', 'city', 'phone', 'address');		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query);
		if($LNK->GetNumRows())
			{
			$ret = $LNK->GetData($ret_arr, true);
			}
		else
			$ret = 0;
		return $ret;		
		}
	function getLatestRegisteredUsers($limit) //получает все параметры пользователя  (09_03_07)
		{
		$query = 'SELECT * FROM `users` WHERE user_registation_status = \'A\' order by user_regdate DESC LIMIT '.$limit;		
		$ret_arr  = array('user_id', 'user_name', 'is_group', 'is_system', 'user_password',
						'user_reminded_pasword', 'user_email', 'user_regdate', 'user_status', 
						'user_last_ip', 'user_time_last_update', 'user_registation_status', 
						'user_avatar', 'user_sid', 'user_first_name', 'user_so_name', 
						'user_last_name', 'user_icq_uin', 'user_about',
						'zipCode', 'city', 'phone', 'address');		
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query);
		if($LNK->GetNumRows())
			{
			$ret = $LNK->GetData($ret_arr, true);
//			$ret = $ret_tmp[$outPar];
			}
//			$ret = $LNK->GetData($ret_arr, false);
		else
			$ret = '';
//		$LNK->Close_link();
		return $ret;		
		}
	function GetGroupUsers($gId) /*получает всех пользователей, которые входят в группу*/
		{
		$ret = 0;
		$query = 'SELECT * FROM users, userToUser WHERE userToUser.group_id = '.$gId.' AND userToUser.user_id = users.user_id AND users.is_group = 0';		
		$ret_arr  = array('user_id', 'user_name', 'is_system', 'user_email', 'user_regdate', 'user_status', 
						'user_last_ip', 'user_time_last_update', 'user_registation_status', 
						'user_avatar', 'user_sid', 'user_first_name', 'user_so_name', 
						'user_last_name', 'user_icq_uin', 'user_about',
						'zipCode', 'city', 'phone', 'address', 'message_notify');		
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if ($LNK->GetNumRows())
			{
			$ret = $LNK->GetData($ret_arr, true);
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		}	
	function getAllUsersGroups($user) //Вытаскивает все группы, в которых состоит пользователь
		{
		$query = 'SELECT * FROM `userToUser` WHERE user_id = '.$user;		
		$ret_arr  = array('user_id', 'group_id');
		$LNK= new DBLink;		
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData($ret_arr, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;		
		}
	function CheckUserPasRemind($sid) //25_05_2007 проверка подтверждения восстановления пароля 
		{
		$query = 'SELECT * FROM users WHERE user_reminded_code = \''.$sid.'\'';
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$ret_arr  = array('user_id', 'user_reminded_pasword');		
		$LNK->Query ($query);
		if($LNK->GetNumRows())
			{
			$ret_tmp = $LNK->GetData($ret_arr, false);
			$query_update = 'UPDATE users SET user_password = \''.$ret_tmp['user_reminded_pasword'].'\',  
							user_reminded_pasword = \'\', user_reminded_code = \'\'
							WHERE user_id = \''.$ret_tmp['user_id'].'\'';
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
			$ret['error'] = 0;
			}
		else
			{
			$ret['error'] = 1;			
			}
		return $ret;
		}	
	function getUserEmail($userName) //получает адрес электронной почты пользователя по имени (24_05_07)
		{
		$query = 'SELECT * FROM `users` WHERE user_name = \''.$userName.'\'';		
		$ret_arr  = array('user_id', 'user_email', 'user_name');		
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query);
		if($LNK->GetNumRows())
			{
			$ret = $LNK->GetData($ret_arr, false);
//			$ret = $ret_tmp[$outPar];
			}
//			$ret = $LNK->GetData($ret_arr, false);
		else
			$ret = '';
//		$LNK->Close_link();
		return $ret;		
		}
	function delShopCookie() //17_05_2007 удаление  куки магазина  
		{
		global $CONST;
		if($_COOKIE[$CONST['shopCookieName']])
			{
			setcookie($CONST['shopCookieName'], $_COOKIE[$CONST['shopCookieName']], time()-$CONST['shopCookieTime'], '/');				
			}
		}
	function setShopCookie() //17_05_2007 проверка и установка куки магазина  
		{
		global $CONST;
		if(!$_COOKIE[$CONST['shopCookieName']])
			{
			$LNK= new DBLink;
//			$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
			do
				{
				$present = 0;
				$cookieValue=md5($_SERVER['REMOTE_ADDR'].time().mt_rand(0, 1000000));
				$queryCheckTmp = 'SELECT * FROM `shop_order_temp` WHERE order_Id = \''.$cookieValue.'\'';		
				$queryCheckFin = 'SELECT * FROM `shop_order_number` WHERE order_Id = \''.$cookieValue.'\'';		
				$LNK->Query($queryCheckTmp);
				if($LNK->GetNumRows())
					$present ++;
				$LNK->Query($queryCheckFin);
				if($LNK->GetNumRows())
					$present ++;
				} 
			while($present);			
			setcookie($CONST['shopCookieName'], $cookieValue, time()+$CONST['shopCookieTime'], '/');				
			}
		}
	function setUserInfo($param) //обновляет параметры пользователя  (12_03_07)
		{
		global $USER;
		$error = 0;		/*
		if((($param['USER_PSW_2'])||($param['USER_PSW_1']))&&(($param['USER_PSW_2'])!=($param['USER_PSW_1'])))
			{
			$ret['error'] = 2;
			$ret['errorMsg'] = 'Указанные пароли не совпадают!';
			$error ++;
			}		*/
/*		if($param['USER_PSW_1'])
			$error++;
*/			
		if(!$error)
			{
			$update = '';
			if($param['UserRegName'])
				$update .= ' user_name = '.htmlspecialchars(trim($param['UserRegName']),ENT_QUOTES);
			if($param['EMail'])
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_email = \''.htmlspecialchars(trim($param['EMail']), ENT_QUOTES).'\'';
				}
			if($param['FName'])
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_first_name = \''.htmlspecialchars(trim($param['FName']), ENT_QUOTES).'\'';
				}
			if($param['SName'])
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_so_name = \''.htmlspecialchars(trim($param['SName']), ENT_QUOTES).'\'';
				}
			if($param['LName'])
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_last_name = \''.htmlspecialchars(trim($param['LName']), ENT_QUOTES).'\'';
				}
				
			if($param['USER_ZIP'])
				{
				$update .= ($update)?', ':' ';
				$update .= ' zipCode = \''.htmlspecialchars(trim($param['USER_ZIP']), ENT_QUOTES).'\'';
				}
			if($param['USER_SITY'])
				{
				$update .= ($update)?', ':' ';
				$update .= ' city = \''.htmlspecialchars(trim($param['USER_SITY']), ENT_QUOTES).'\'';
				}
			if($param['USER_PHONE'])
				{
				$update .= ($update)?', ':' ';
				$update .= ' phone = \''.htmlspecialchars(trim($param['USER_PHONE']), ENT_QUOTES).'\'';
				}
			if($param['USER_ADR'])
				{
				$update .= ($update)?', ':' ';
				$update .= ' address = \''.htmlspecialchars(trim($param['USER_ADR']), ENT_QUOTES).'\'';
				}
				
			if($param['UIN'])
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_icq_uin = '.intval(trim($param['UIN']));
				}
			if($param['subsMes'])
				{				
				$update .= ($update)?', ':' ';
				$update .= ' message_notify = '.(($param['subsMes']=='on')?1:0);
				}
			if($param['About'])
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_about = \''.trim(strip_tags($param['About'], '<a><b><i><u>')).'\'';
				}
			if($param['avatar'])
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_avatar = \''.$param['avatar'].'\'';
				}
			if($param['PSW'])
				{
				$update .= ($update)?', ':' ';
				$update .= ' user_password = PASSWORD(\''.$param['PSW'].'\')';
				}
			if($update)
				{
				$query = 'UPDATE `users` set '.$update.' WHERE user_id = \''.$USER->id.'\'';		
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
		else
			{
			$ret['error'] = 1;
//			$ret['errorMsg'] = 'smt happend';
			}
		return $ret;		
		}
	
	function getUserParamAll($userId) //получает все параметры пользователя  (09_03_07)
		{
		$query = 'SELECT * FROM `users` WHERE user_id = \''.$userId.'\'';		
		$ret_arr  = array('user_id', 'user_name', 'is_group', 'is_system', 'user_password',
						'user_reminded_pasword', 'user_email', 'user_regdate', 'user_status', 
						'user_last_ip', 'user_time_last_update', 'user_registation_status', 
						'user_avatar', 'user_sid', 'user_first_name', 'user_so_name', 
						'user_last_name', 'user_icq_uin', 'user_about',
						'zipCode', 'city', 'phone', 'address', 'message_notify');		
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query);
		if($LNK->GetNumRows())
			{
			$ret = $LNK->GetData($ret_arr, false);
//			$ret = $ret_tmp[$outPar];
			}
//			$ret = $LNK->GetData($ret_arr, false);
		else
			$ret = '';
//		$LNK->Close_link();
		return $ret;		
		}
	function GetAuthCookieValue($user_name) //вычисляет уникальный md5 хэш для конкретного пользователя - для авторизационной куки (07_01_23)
		{
		$query = 'SELECT * FROM users WHERE user_name = \''.$user_name.'\'';
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$ret_arr  = array('user_name', 'user_regdate');		
		$LNK->Query ($query);
		if($LNK->GetNumRows())
			{
			$ret_tmp = $LNK->GetData($ret_arr, false);		
			$ret['value'] = md5($ret_tmp['user_name'].$ret_tmp['user_regdate']);
			$ret['error'] = 0;
			}
		else
			{
			$ret['error'] = 1;			
			}
		return $ret;
		}	
	function CheckUserLastSID($uid) //проверка последнего логина юзера по куке (07_01_22)
		{
		$query = 'SELECT * FROM users
					LEFT JOIN userProviders ON userProviders.up_id = users.up_id
					WHERE is_group = 0 
					AND user_registation_status = \'A\'';
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$ret_arr  = array('user_name', 'user_regdate', 'displayName', 'up_id', 'up_name', 'up_fullName');		
		$LNK->Query ($query);
		if($LNK->GetNumRows()>1)
			{
			$ret_tmp = $LNK->GetData($ret_arr, true);
			$i=0;
			$stop = 0;
			do
				{
				$_uid = md5($ret_tmp[$i]['user_name'].$ret_tmp[$i]['user_regdate']);
				if ($uid == $_uid)
					{
//					echo 'yeah';
					$ret['user_name'] 		= $ret_tmp[$i]['user_name'];
					$ret['displayName'] 	= ($ret_tmp[$i]['displayName']) ? $ret_tmp[$i]['displayName'] : $user_name;
					$ret['providerName']  	= $ret_tmp[$i]['up_name'];
					$ret['providerTitle']  	= $ret_tmp[$i]['up_fullName'];
					$ret['providerId'] 		= $ret_tmp[$i]['up_id'];

/*					$ret['displayName'] = ($ret_tmp[$i]['displayName']) ? $ret_tmp[$i]['displayName'] : $user_name;
					$ret['displayName'] = ($ret_tmp[$i]['displayName']) ? $ret_tmp[$i]['displayName'] : $user_name;
*/					
					$stop++;
					}
				$i++;
				}
			while(($i<count($ret_tmp))&&(!$stop));
			if($stop)
				{
/*				$ret['user_name'] = $user_name;
				$ret['displayName'] = $displayName;*/
				$ret['error'] = 0;
				}
			else
				{
				$ret['error'] = 1;
				}
			}
		else
			{
			$ret['error'] = 1;			
			}
		return $ret;
		}	
	function ConfirmUser($sid) //подтверждает статус пользователя (А) (07_01_20)
		{
		$query = 'UPDATE users SET user_status = \'ON\',  user_registation_status = \'A\' WHERE user_sid = \''.$sid.'\' AND user_registation_status = \'Q\'';
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
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
		return $ret;
		}	
	function CheckUserRegConfirm($sid) //проверка подтверждения регистрации (07_01_20)
		{
		$query = 'SELECT * FROM users WHERE user_sid = \''.$sid.'\' AND user_registation_status = \'Q\'';
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$ret_arr  = array('user_name', 'user_id');		
		$LNK->Query ($query);
		if($LNK->GetNumRows())
			{
			$ret_tmp = $LNK->GetData($ret_arr, false);
			$ret['user_name'] = $ret_tmp['user_name'];
			$ret['user_id'] = $ret_tmp['user_id'];
			$ret['error'] = 0;
			}
		else
			{
			$ret['error'] = 1;			
			}
		return $ret;
		}	
	function getSystemGroup($outPar) //получает группу 'ВСЕ остальные'
		{
		$query = 'SELECT * FROM `users` WHERE is_group = 1 AND is_system = 1';		
		$ret_arr  = array($outPar);		
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query);
		if($LNK->GetNumRows())
			{
			$ret_tmp = $LNK->GetData($ret_arr, false);
			$ret = $ret_tmp[$outPar];
//			echo $ret;
			}
//			$ret = $LNK->GetData($ret_arr, false);
		else
			$ret = '';
//		$LNK->Close_link();
		return $ret;		
		}
	function getUserParam($inParName, $inParValue, $outPar) //получает параметр пользователя 
		{
		$query = 'SELECT * FROM `users` WHERE '.$inParName.' = \''.$inParValue.'\'';		
		$ret_arr  = array($outPar);		
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query);
		if($LNK->GetNumRows())
			{
			$ret_tmp = $LNK->GetData($ret_arr, false);
			$ret = $ret_tmp[$outPar];
			}
//			$ret = $LNK->GetData($ret_arr, false);
		else
			$ret = '';
//		$LNK->Close_link();
		return $ret;		
		}
	function userInGroup($user, $group) //определяет входит ли пользователь в группу
		{
		$query = 'SELECT * FROM `userToUser` WHERE group_id = '.$group.' AND user_id = '.$user;		
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query);
//		$LNK->Close_link();
		return $LNK->GetNumRows();		
		}
	function isGroup($userId) //определяет является ли user группой
		{
		$query = 'SELECT * FROM `users` WHERE user_id = '.$userId.' AND is_group =1';		
		$LNK= new DBLink;		
		$LNK->Query ($query);
//		$LNK->Close_link();
		return $LNK->GetNumRows();		
		}
	function isConfederate($user, $protoUser) //определяет является ли user "одногруппником" protoUser-у
		{
		$query = 'SELECT * FROM `userToUser` WHERE group_id IN (select group_id from userToUser where user_id = '.$protoUser.') AND user_id = \''.$user.'\'';		
		$LNK= new DBLink;		
		$LNK->Query ($query);
//		$LNK->Close_link();
		return $LNK->GetNumRows();		
		}
    function CurrentUser ($ip, $language)
    	{		
		$this->ip = $ip;
		$this->language = $language;
		$this->registered = false;
		$this->name = '';
		$this->sId = '';		
        }
	function CheckUser($user_name, $user_psw)
		{
//		error_log('[INFO] AUTH CU name="'.$user_name.'"; pswd="'.$user_psw.'" ');
		$query = 'SELECT * FROM users WHERE user_name = \''.$user_name.'\' AND user_password = PASSWORD(\''.$user_psw.'\') AND user_registation_status = \'A\'';
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);
		$LNK->Query ($query);
		$LNK->Close_link();
		$ret_arr  = array('user_name', 'user_id', 'displayName');	
		$ret = ($LNK->GetNumRows()) ? $LNK->GetData($ret_arr, false) : 0;			
//		error_log('[INFO] AUTH CU '.print_r($ret,true));
		return $ret;
		}	
	function logoff()
		{
		$currtime = time();
		global $USER;
/*		$USER->name = '';
		$USER->registered = false;						
		$USER->sId = $sid;*/
		$query = "UPDATE users SET user_status = 'OFF', user_time_last_update = '".$currtime."', user_last_ip = '".$this->ip."' ,  user_sid = '' 
								WHERE user_name = '".$USER->name."'";
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query);
		if(!$LNK->error)
			$ret =  true;
		else
			$ret =  false;
		$LNK->Close_link();			
		return $ret;
		}
	function UpdateUserStatus($User_Name, $usrParam)
		{
		$currtime = time();
		$sid = session_id();
		$query = "UPDATE users SET user_status = 'ON', user_time_last_update = '".$currtime."', user_last_ip = '".$this->ip."',  user_sid = '".$sid."' 
								WHERE user_name = '".$User_Name."'";
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query ($query);
//		$LNK->Close_link();
		if(!$LNK->error)
			{
			global $USER;
			$ret_val =  true;
			$USER->name = $User_Name;
			$USER->displayName = $usrParam['displayName'];			
			$USER->registered = true;						
			$USER->sId = $sid;
			if(isset($usrParam['providerName']))
				{
				$USER->providerName = 	$usrParam['providerName'];						
				$USER->providerTitle = 	$usrParam['providerTitle'];						
				$USER->providerId = 	$usrParam['providerId'];										
				}
			}
		else
			$ret_val =  false;
		return $ret_val;			
		}
	function CheckUserStatus()
		{
		global $USER, $CONST /*$exptime, */ /*, $regConfirmExpTimе , $cookieName, $cookieTime*/;		
		$sid = session_id();		
//		$this->setShopCookie();
		/***********************проверка кук*********************************************/
		if($_COOKIE[$CONST['cookieName']]&&(!$USER->registered))
			{
			$res = $this->CheckUserLastSID($_COOKIE[$CONST['cookieName']]); 
			if(!$res['error'])
				{
				$USER->UpdateUserStatus($res['user_name'], $res);
				}
			elseif($res['error'])
				{
				setcookie($CONST['cookieName'],$_COOKIE[$CONST['cookieName']], time()-$CONST['cookieTime']);
				}
			}
		/********************************************************************/
		/*******************************проверка подключенных и отключенных пользователей*************************************/
		$currtime = time();
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$query = 'SELECT * FROM users					
							LEFT JOIN userProviders ON userProviders.up_id = users.up_id
							WHERE user_sid=\''.$sid.'\'';
		$LNK->Query ($query);
		if($LNK->GetNumRows())
			{
			$ret = $LNK->GetData(array('user_id', 'user_name', 'displayName', 'up_id', 'up_name', 'up_fullName'), false);
			$USER->id = $ret['user_id'];
			$USER->name = $ret['user_name'];
			$USER->displayName = ($ret['displayName']) ? $ret['displayName'] : $ret['user_name'];
			$USER->registered = true;
			$USER->sId = $sid;
			$USER->providerName  	= $ret['up_name'];
			$USER->providerTitle  	= $ret['up_fullName'];
			$USER->providerId 		= $ret['up_id'];
			$query = "UPDATE users SET user_status = 'ON', user_time_last_update = '".$currtime."' WHERE user_sid = '".$sid."'";
			$LNK->Query ($query);			
			$ret_val = 1;		
			}
		else
			{
			$USER->name = 'anonymous';			
			$USER->displayName = 'Гость';
			$USER->id = $this->getUserParam('user_name', $USER->name, 'user_id');
			$USER->registered = false;
			$USER->sId = '';
			$ret_val = 0;
			}
		$query = "SELECT user_time_last_update, user_name FROM users WHERE user_status = 'ON'";
		$LNK->Query ($query);
		if($LNK->GetNumRows())
			{
			$ret = $LNK->GetData(array('user_time_last_update', 'user_name'), true);
			for($i=0;$i<count($ret); $i++)
				{
				$LastTime = $ret[$i]['user_time_last_update'];			
				$User = $ret[$i]['user_name'];
				if (($currtime-$LastTime)>$CONST['onlineExpTime'])
					{
					$query = 'UPDATE users SET user_status = \'OFF\' WHERE user_name = \''.$User.'\'';
					$LNK->Query ($query);				
					}
				}		
			}
		/********************************************************************/
		/*******************************проверка времени жизни запрошенных пользователей*************************************/
		$query = "SELECT user_regdate, user_name FROM users WHERE user_registation_status = 'Q' AND is_group = 0";
		$LNK->Query ($query);
		if($LNK->GetNumRows())
			{
			$ret = $LNK->GetData(array('user_regdate', 'user_name'), true);
			for($i=0;$i<count($ret); $i++)
				{
				$LastTime = $ret[$i]['user_regdate'];			
				$User = $ret[$i]['user_name'];
				if (($currtime - $LastTime)>$CONST['regConfirmExpTimе'])
					{
					$query = 'delete from users  WHERE user_name = \''.$User.'\'';
					$LNK->Query ($query);				
					}
				}		
			}
//		echo $resCook;
/******************************************определение браузера и версии********************************************************/
//		echo $_SERVER['HTTP_USER_AGENT'];
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Opera") !==false)
		   {
		     $ua="Opera";
		     $uaVers = substr($_SERVER['HTTP_USER_AGENT'],strpos($_SERVER['HTTP_USER_AGENT'],"Opera")+6,4);
		   }
		elseif (strpos($_SERVER['HTTP_USER_AGENT'],"Gecko") !==false)
		   {
		     $ua="Netscape";
		     $uaVers = substr($_SERVER['HTTP_USER_AGENT'],strpos($_SERVER['HTTP_USER_AGENT'],"Mozilla")+8,3);
		   }
		elseif (strpos($_SERVER['HTTP_USER_AGENT'],"Windows") !==false)
		   {
		     $ua="Explorer";
		     $uaVers = substr($_SERVER['HTTP_USER_AGENT'],strpos($_SERVER['HTTP_USER_AGENT'],"MSIE")+5,3);
		   }
		else
		   {
		     $ua=$_SERVER['HTTP_USER_AGENT'];
		     $uaVers=""; 
		   }
		$USER->appName = $ua;
		$USER->appVersion = $uaVers;		   
/**************************************************************************************************/
		
		return $ret_val;
		}
	}	
?>