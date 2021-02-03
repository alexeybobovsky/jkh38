<?
$act = ($post[2])?$post[2]:'';
$act = ($act)?$act:$post[1];
$ACNT = new GetAdmin;
$realPath = $ROUT->GetStartedUrlLvl($allMenu['curLabel']['link'], $allMenu['activeLink'], $uri);
$curURL = $realPath['url'];
$curURLLvl = $realPath['lvl'];
$REDIRECT = 0;
//$UCNT = new CurrentUser;
//$ATPL = new ADMIN_TEMPLATE;
require_once("../classes/TPL_reg_class.php");
$RTPL = new REG_TEMPLATE;
$ASET = new SetAdmin;
global $USER, $ROUT;
//echo $act;
switch ($act)
	{
	case 'entry': 
		{
//		echo 'ffff';
		$HISTORY_SKIP = 1;
		$allMenu['lastTitle'] = 'Вход на сайт';
		$contentHTML[] = $RTPL->showLogin();		
		$SMRT_TMP = array();
		$SMRT_TMP['name'] = 'contentHTML';
		$SMRT_TMP['body'] = $contentHTML;
		$SMRT['modules'][] = $SMRT_TMP;	
		$SMRT_TMP = array();
		$SMRT_TMP['name'] = 'helpString';
		$SMRT_TMP['body'] = '<P>Если Вы еще не зарегистрированы на нашем сайте, то это можно легко исправить на <a href=\'/registration\'>этой cтранице</a>.<br>
								Если же Вы были ранее зарегистрированы у нас на сайте, но забыли пароль, то воспользуйтесь <a href=\'/password\'>службой восстановления паролей</a>.
							</P>';
								
		$SMRT['modules'][] = $SMRT_TMP;	
		$SMRT['modules'][] =  array('name' => 'menuTopLvlUse', 'body' => array(
								'active' => '', 
								'title' => $allMenu['lastTitle'], 
								'counter' => ''));		
/*		$templates[] = 'default/firmCatalogCategoryList.tpl';*/
		$SMRT['modules'][] =  array('name' => 'title', 	'body' => $allMenu['lastTitle']);		
		$SMRT['modules'][] =  array('name' => 'menuItems', 'body' => array('startLink' => 'list', 'curentItem' => 'none'));
		$templates[] =  $tplDir.'registration.tpl';			
		$templates[] = $tplDir.'rightColumn.tpl';
		} break;	
	case 'password': 
		{
		$curURL = '/password/';
		$HISTORY_SKIP = 1;
		$allMenu['lastTitle'] = 'Восстановление пароля';
		$contentHTML[] = $RTPL->pasRemind($curURL);		
		$SMRT_TMP = array();
		$SMRT_TMP['name'] = 'contentHTML';
		$SMRT_TMP['body'] = $contentHTML;
		$SMRT['modules'][] = $SMRT_TMP;	
		$SMRT_TMP = array();
		$SMRT_TMP['name'] = 'helpString';
		$SMRT_TMP['body'] = '<P>Если Вы были ранее <a href="/registration">зарегистрированы</a> у нас на сайте, но забыли пароль и не можете сейчас войти, 
										   то мы по Вашему запросу сгенерируем новый пароль, 
										   который затем отправим на почтовый адрес, указанный Вами при регистрации. 
							</P>';
								
		$SMRT['modules'][] = $SMRT_TMP;	
		//$templates[] = 'ga/userRegForms.tpl';		
		$SMRT['modules'][] =  array('name' => 'menuTopLvlUse', 'body' => array(
								'active' => '', 
								'title' => $allMenu['lastTitle'], 
								'counter' => ''));		
		$SMRT['modules'][] =  array('name' => 'title', 	'body' => $allMenu['lastTitle']);		
		$SMRT['modules'][] =  array('name' => 'menuItems', 'body' => array('startLink' => 'list', 'curentItem' => 'none'));
		$templates[] =  $tplDir.'registration.tpl';			
		$templates[] = $tplDir.'rightColumn.tpl';
		} break;
	case 'set': 
		{
		if($post[3])
			{
			switch ($post[3])
				{
				case 'pasRemind':
					{
//					print_r($_POST);
					$curURL = '/password/';

					$HISTORY_SKIP = 1;
					
					if(trim($_POST['TYPE'])&&trim($_POST['VALUE']))
						{
						$param = (trim($_POST['TYPE'])=='user')?'user_name':'user_email';
						$uName = (trim($_POST['TYPE'])!='user')?$USER->getUserParam($param, trim($_POST['VALUE']), 'user_name'):trim($_POST['VALUE']);						
						$usr = $USER->getUserEmail($uName);
//						print_r($usr);
						if(is_array($usr))
							{
							require_once("../classes/Mail_class.php");	
							$MAIL = new SendMail;							
							$alphanum = "abcdefghigflmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
							$pswrd = substr(str_shuffle($alphanum), 0, 8);						
							$code = md5($usr['user_name'].time().$pswrd);
							$res = $ASET->CreatePasRemind($code, $pswrd,  $usr['user_id']);
							if(!$res['error'])
								{
								$success = $MAIL->sendRemindPassword($code, $pswrd, $usr, $curURL);
								}
							if ($success)
								{
								$MESS = new Message('Info', 'Восстановление пароля', 'На почтовый ящик пользователя, указаного Вами, отправлено письмо с новым паролем и ключом его активации.
													Для завершения процедуры восстановления, Вам необходимо в указанный срок пройти по ссылке, присланной в этом письме.', $NAV->GetHistoryItem(2));								
								}
							else
								{
								$MESS = new Message('Error', 'Ошибка восстановления пароля', 'Не удается восстановить пароль! Попробуйте позже.', $NAV->GetPrewURI());										
								}
							}
						else
							{
							$MESS = new Message('Error', 'Ошибка восстановления пароля', 'Пользователь с таким именем или адресом не найден!', $NAV->GetPrewURI());										
							}
						}
					else
						{
						$MESS = new Message('Error', 'Ошибка восстановления пароля', 'Не указано значение имени или адреса!', $NAV->GetPrewURI());										
						}
						
					}
				break;
				case 'info': 
					{
					$HISTORY_SKIP = 1;		
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
					if((($_POST['subsNews'])&&(!$_POST['CMP_subsNews']))||((!$_POST['subsNews'])&&($_POST['CMP_subsNews'])))
						{
						$param['subsNews'] = ($_POST['subsNews'])?'on':'off';
						}
					if(($UploadAvatar)&&($ROUT->CheckAndMoveUploadedFile('UploadAvatar', $_FILES,   $CONST['sizeUpAvatarMax'],$CONST['relPathPref'].$CONST['srcAvatarUsr'].$_FILES['UploadAvatar']['name'])))
						{
//						echo 'good!';
						$param['avatar'] = $CONST['srcAvatarUsr'].$_FILES['UploadAvatar']['name'];
						}	
					$res = $USER->setUserInfo($param);
					if((($_POST['subsNews'])&&(!$_POST['CMP_subsNews']))||((!$_POST['subsNews'])&&($_POST['CMP_subsNews'])))
						{
						$resSubs = ($_POST['subsNews'])?$ASET->AddUserToGroup($USER->id, $CONST['defSubscribersGroup']):$ASET->RemoveUserFromGroup($USER->id, $CONST['defSubscribersGroup']);
						}
					if(!$res['error'])						
						{}
//						$MESS = new Message('Info', 'Узменение персональной информации', 'Ваши данные успешно изменены! ', $NAV->GetPrewURI());
					else
						{
						$errorMsg = 'Причина ошибки неизвестна';
						if($CONST['debugMode'])
							$errorMsg = $res['errorMsg'];
						elseif($res['error']>1)
							$errorMsg = $res['errorMsg'];													
						$MESS = new Message('Error', 'Узменение персональной информации', 'В ходе изменения персональной информации произошла следующая ошибка: '.$errorMsg, $NAV->GetPrewURI());											
						}
					$REDIRECT = 'http://'.$NAV->GetPrewURI();
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
		} break;
	case 'user': 
		{
		} 
	case 'persInfo': 
		{
		$HISTORY_SKIP = 1;
		$curURL = '/user/';
		$addUser = $RTPL->persInfo($USER->getUserParamAll($USER->id), $curURL);		
		$SMRT_TMP['name'] = 'addUser';
		$SMRT_TMP['body'] = $addUser;
		$SMRT['modules'][] = $SMRT_TMP;			
		$templates[] = 'addUser.tpl';	
		} break;
	case 'confirmPR': //подтверждение о смене пароля
		{
		$HISTORY_SKIP = 1;		
		if($post[3])
			{
			$res = $USER->CheckUserPasRemind($post[3]);
			if(!$res['error'])
				{
/*//				print_r($res);
				$res = $USER->confirmPR($post[3]);
//				print_r($res);
				if(!$res['error'])	
					{*/
				$USER->UpdateUserStatus($res['user_id']);
				$MESS = new Message('Info', 'Восстановление пароля', 'Новый пароль успешно активирован! ', $NAV->GetPrewURI());								
				}				
			else
				{
				$confirmError = 1;
				}
			}
		else
			{
			$confirmError = 1;
			}				
		if($confirmError)
			{
			$MESS = new Message('Error', 'Ошибка регистрации', 'Введен неверный ключ активации аккаунта! ', $NAV->GetPrewURI());										
			}			
		} break;
	case 'confirm': 
		{
		$HISTORY_SKIP = 1;		
		if($post[3])
			{
			$res = $USER->CheckUserRegConfirm($post[3]);
			if(!$res['error'])
				{
				$userName = $res['user_name'];
				$userId = $res['user_id'];
//				print_r($res);
				$res = $USER->ConfirmUser($post[3]);
//				print_r($res);
				if(!$res['error'])	
					{
					$resG = $ASET->AddUserToGroup($userId, $CONST['groupIdUsers']);
					$USER->UpdateUserStatus($userName);
					$MESS = new Message('Info', 'Завершение регистрации', 'Поздравляем! Регистрация успешно завершена.</br> Добавить или изменить информацию о себе Вы можете в <a href=\'/user/'.$userName.'\'>своем профиле</a>.', /*$_SERVER['SERVER_NAME'].*/'/user/'.$userName /*$NAV->GetPrewURI()*/ );								
					}				
				else
					{
					$confirmError = 1;
					}
				}
			else
				{
				$confirmError = 1;
				}
			}
		else
			{
			$confirmError = 1;
			}
		if($confirmError)
			{
			$MESS = new Message('Error', 'Ошибка регистрации', 'Введен неверный ключ активации аккаунта! ', $NAV->GetPrewURI());										
			}
		} break;	
	case 'queryUser': 
		{
		$HISTORY_SKIP = 1;
		$success = 0;
//		print_r($_POST);

		if($USER->getUserParam('user_name', trim($_POST['USER_NAME']), 'user_id'))
			{
			$error = 1;
			$errorMsg = 'Пользователь с таким именем уже зарегистрирован! Если Вы уже регистрировались у нас ранее, но забыли пароль, то воспользуйтесь  <A href=\'/password\'>службой восстановления пароля</A>.';
			}		
		elseif(strlen(trim($_POST['USER_NAME']))<3)
			{
			$error = 1;
			$errorMsg = 'Логин не может быть менее 3 символов';
			}		
		elseif(!preg_match("/^([a-zA-Z0-9\.\-_])+@([a-zA-Z0-9\.\-_])+(\.([a-zA-Z0-9])+)+$/", trim($_POST['USER_EMAIL'])))
			{
			$error = 1;
			$errorMsg = 'Не верно указан E-mail! Пожалуйста, повторите попытку.';
			}		
		elseif($USER->getUserParam('user_email', trim($_POST['USER_EMAIL']), 'user_id'))
			{
			$error = 1;
			$errorMsg = 'Пользователь с таким почтовым ящиком уже зарегистрирован! На один почтовый ящик может быть зарегистрирован только один пользователь. Если Вы уже регистрировались у нас ранее, но забыли пароль, то воспользуйтесь  <A href=\'/password\'>службой восстановления пароля</A>.';
			}
		elseif(md5(trim($_POST['USER_CODE'])) != $_SESSION['image_random_value'])
			{
			$error = 1;
			$errorMsg = 'Введено неверное кодовое слово! Пожалуйста, повторите попытку.';
			}
		elseif($_POST['USER_PSW_1'] != $_POST['USER_PSW_2'])
			{
			$error = 1;
			$errorMsg = 'Пароли не совпадают! Пожалуйста, повторите попытку.';
			}

		unset($_SESSION['image_random_value']);
		if($error)
			{
			$_SESSION['regPost'] = $_POST;
			$_SESSION['errorMsg'] = $errorMsg;
			header('Location: /registration');
			}
		else
			{
			require_once("../classes/Mail_class.php");	
			$MAIL = new SendMail;			
			if(is_array($_SESSION['regPost']))
				unset($_SESSION['regPost']);
			if($_SESSION['errorMsg'])
				unset($_SESSION['errorMsg']);			
			$sid = md5($USER_NAME.$USER_EMAIL.$USER_PSW_1.time());
			$res = $ASET->CreateUserQuery($_POST, $sid);
			if(!$res['error'])
				{
				if($_POST['SUBSCRIBE'])
					$resSubs = $ASET->AddUserToGroup($res['last_id'], $CONST['defSubscribersGroup']);
				$success = $MAIL->sendRegistrationConfirm($USER_NAME, $USER_EMAIL, $USER_PSW_1, $sid);
				if ($success)
					{
					$MESS = new Message('Info', 'Подтверждение регистрации', 'На почтовый ящик, указаный Вами, отправлено письмо с ключом активации Вашего аккаунта.
										Для завершения регистрации вам необходимо пройти по ссылке, указанной в этом письме.', $NAV->GetPrewURI());								
					}	
				else
					{
					$ASET->DeleteUser($res['last_id']);
					$MESS = new Message('Error', 'Ошибка регистрации', 'Не удалось отправить письмо по адресу, введеному Вами. Попробуйте заново.', $NAV->GetPrewURI());															
					}
				}
			else
				{
				$MESS = new Message('Error', 'Ошибка регистрации', 'Возможно, пользователь с таким именем уже существует! Попробуйте поменять имя.', $NAV->GetPrewURI());										
				}
			}
		} break;
//	case 'showForm': 
	case 'registration': 
		{
//		echo time();
		$allMenu['lastTitle'] = 'Регистрация';
		$HISTORY_SKIP = 1;
		if(is_array($_SESSION['regPost']))
			{
			$defVal = $_SESSION['regPost'];
			unset($_SESSION['regPost']);			
			}
		else
			$defVal = 0;
		if($_SESSION['errorMsg'])			
			{
			$errorMsg = $_SESSION['errorMsg'];
			unset($_SESSION['errorMsg']);
			}
		else
			$errorMsg = '';
		$contentHTML[] = $RTPL->addUserRegNew($defVal);		
		$SMRT_TMP = array();
		$SMRT_TMP['name'] = 'helpString';
		$SMRT_TMP['body'] = 'Регистрация на нашем сайте сделает доступными для Вас такие  действия как написание отзывов, оценивание деятельности компаний, 				общение с другими посетителями<br /><br /><h3>Для регистрации необходимо указать:</h3>
<ul type=1>
	<LI><STRONG>Логин</STRONG> или имя учетной записи, которое будет вашим уникальным идентификатором на сайте (минимум 3 символа);</LI>
    './*<LI>Ваше <STRONG>Имя</STRONG> </LI>*/''.'
    <LI>Адрес Вашей <STRONG>электронной почты</STRONG></LI>    
    <LI><STRONG>Пароль</STRONG> для входа на сайт. </LI>
    <LI>Также необходимо написать символьное <STRONG>кодовое слово</STRONG>, показанное на картинке.</LI>';
								
		$SMRT['modules'][] = $SMRT_TMP;	
		$SMRT_TMP = array();
		$SMRT_TMP['name'] = 'contentHTML';
		$SMRT_TMP['body'] = $contentHTML;
		$SMRT['modules'][] = $SMRT_TMP;	
		$SMRT_TMP = array();
		if($errorMsg)
			{
			$SMRT_TMP['name'] = 'errorMsg';
			$SMRT_TMP['body']['text'] = $errorMsg;
			$SMRT_TMP['body']['img'] = 'src/design/message/error.gif';
			$SMRT['modules'][] = $SMRT_TMP;			
			}
		$SMRT['modules'][] =  array('name' => 'menuTopLvlUse', 'body' => array(
								'active' => '', 
								'title' => $allMenu['lastTitle'], 
								'counter' => ''));		
		$SMRT['modules'][] =  array('name' => 'title', 	'body' => $allMenu['lastTitle']);		
		$SMRT['modules'][] =  array('name' => 'menuItems', 'body' => array('startLink' => 'list', 'curentItem' => 'none'));
		$templates[] =  $tplDir.'registration.tpl';			
		$templates[] = $tplDir.'rightColumn.tpl';
		} break;
	case 'imgcode': 
		{
		$templates = array();
		$HISTORY_SKIP = 1;
		include('kcaptcha.php');
		$captcha = new KCAPTCHA();
		$_SESSION['image_random_value'] = md5($captcha->getKeyString());
		} break;
	default :
		{
		$MESS = new Message('Error', 'ERROR 404', 'Запрошенная Вами страница не найдена', $NAV->GetPrewURI());											
		}
	};
if(isset($MESS))
	{	
	$_SESSION['MESSAGE'] = $MESS;
	header('Location: /');	
	}
elseif($REDIRECT)
	{
	header('Location: '.$REDIRECT);	
	}

?>