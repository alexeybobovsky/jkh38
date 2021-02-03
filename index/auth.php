<?php
	$templates = array();
	if(!$post[2]) $action = "login";
	elseif($post[2] == 'logoff')  $action = "logoff";
	else $action = trim($post[2]);
			echo (__FILE__ .' - '. $action);
//			print_r($_SERVER);
/*    if(!isset($Username)) $Username = "anonymous";
    if(!isset($Password)) $Password = "111";*/
	$Username = (isset($_POST['Username'])) ? trim ($_POST['Username']) :  "anonymous";
	$Password = (isset($_POST['Password'])) ? $_POST['Password'] :  "111";
	$saveMe = 	(isset($_POST['saveMe'])) ? 1 :  0;
    switch ($action)
	    {
	    case "login":
	        {
			print_r($_POST);
	        if (!$usrChk = $USER->CheckUser($Username, $Password))
	            {	
				$regError = 1;
//				$USER->reg_error = true;
//				echo 'good';
	            }
	        else
	            {
				$res = $USER->GetAuthCookieValue($Username);
//				print_r($res);
				$uid = $res['value'];
				if($saveMe)
					{
					setcookie($CONST['cookieName'], $uid, time()+$CONST['cookieTime'], '/');
					}
				else
					{
					setcookie($CONST['cookieName'], $uid, time()-$CONST['cookieTime'], '/');
					}
	            $USER->UpdateUserStatus($Username, $usrChk);
				$regError = 0;
//				print_r($USER);
	            }
			if(!$regError)	
				{
//				$REDIRECT = ($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:$NAV->GetHistoryItem(2);
				$REDIRECT = 'http://'.$_SERVER['HTTP_HOST'];
				}
			else
				{
				$MESS = new Message('Error', 'Ошибка входа', 'Неверные логин или пароль! <a href=\'/entry\'>Попробуем снова</a>?', $NAV->GetPrewURI());											
/*				$_SESSION['MESSAGE'] = $MESS;				
				$REDIRECT = '/';*/
				}
	        }
	    break;
	    case "logoff":
	        {
	        if (!$USER->logoff())
	            {
				$USER->reg_error = true;
					$m = 'bad';
	            }
	        else
	            {
				$res = $USER->GetAuthCookieValue($USER->name);
				$uid = $res['value'];
				$USER = new CurrentUser ($_SERVER[REMOTE_ADDR], $CNT->GetLangDefault());
//				session_register('USER');
				$_SESSION['USER'] = $USER;
				setcookie($CONST['cookieName'], $uid, time()-$CONST['cookieTime'], '/');
				$m = 'good';
	            }
//			$REDIRECT = '/';//.$NAV->GetPrewURI();
//			$REDIRECT = ($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:$NAV->GetHistoryItem(2);
			$REDIRECT = 'http://'.$_SERVER['HTTP_HOST'];
//			echo $m;
	        }
	    break;
	   default:
	        {
//			Error_Reporting(E_ALL);	
			if($provider = $USER->getProvider($action))
				{
				$config =  		'../htdocs/includes/ha/config.php';
				require_once( "../htdocs/includes/ha/Hybrid/Auth.php" );
				try{
//					print_r($provider);
					$hybridauth = new Hybrid_Auth( $config );
					$prov = $hybridauth->authenticate( $provider['up_name'] );
					$is_user_logged_in = $prov->isUserConnected();
					$user_profile = $prov->getUserProfile();
//					echo 'r';
//					print_r($prov);
					if($prov->id)
						{
						//$saveMe = 1;
						$userName = $provider['up_id'].'_'.$user_profile->identifier;
						if (!$usrChk = $USER->checkProvUser($userName))  /*new user*/
							{
							//echo 'new extended user!!!';
							$usrRes = $USER->createExtendedUser($userName, $user_profile, $provider['up_id']);
							$resG = $ASET->AddUserToGroup($usrRes['last_id'], $CONST['groupIdUsers']);
							$usrChk = array('displayName' => $usrRes['displayName']);
							}
						else
							{
							$res = $USER->GetAuthCookieValue($userName);
							$uid = $res['value'];
							/*if($saveMe)
								{
								setcookie($CONST['cookieName'], $uid, time()+$CONST['cookieTime'], '/');
								}
							else
								{
								setcookie($CONST['cookieName'], $uid, time()-$CONST['cookieTime'], '/');
								}*/
							$regError = 0;
							//echo 'OLD extended user!!!';
							}
						$usrChk['providerName']  	= $provider['up_name'];
						$usrChk['providerTitle']  	= $provider['up_fullName'];
						$usrChk['providerId'] 		= $provider['up_id'];
						$USER->UpdateUserStatus($userName, $usrChk);
						setcookie($CONST['cookieName'], $uid, time()+$CONST['cookieTime'], '/');
						unset($_SESSION['HA::CONFIG']);
						unset($_SESSION['HA::STORE']);
						}
					
					// access user profile data
/*					echo "Ohai there! U are connected with: <b>{$prov->id}</b><br />";
					echo "As: <b>{$user_profile->displayName}</b><br />";
					echo "And your provider user identifier is: <b>{$user_profile->identifier}</b><br />";  */
					}
				catch( Exception $e )
					{
					$errorMessage = '';
					switch( $e->getCode() )
						{
						case 0 : $errorMessage =  "Unspecified error."; break;
						case 1 : $errorMessage =   "Hybridauth configuration error."; break;
						case 2 : $errorMessage =   "Provider not properly configured."; break;
						case 3 : $errorMessage =   "Unknown or disabled provider."; break;
						case 4 : $errorMessage =   "Missing provider application credentials."; break;
						case 5 : $errorMessage =   "Authentication failed. " 
								  . "The user has canceled the authentication or the provider refused the connection."; 
							   break;
						case 6 : $errorMessage =   "User profile request failed. Most likely the user is not connected "
								  . "to the provider and he should to authenticate again."; 
							   $prov->logout();
							   break;
						case 7 : $errorMessage =   "User not connected to the provider."; 
							   $prov->logout();
							   break;
						case 8 : $errorMessage =  "Provider does not support this feature."; break;
						} 
					
					echo "<br /><br /><b>Original error message:</b> " . $e->getMessage();
					echo "<hr /><h3>Trace</h3> <pre>" . $e->getTraceAsString() . "</pre>"; 
//					$MESS = new Message('Error', 'Ошибка входа', $errorMessage.' <a href=\'/entry\'>Попробуем снова</a>?', $NAV->GetPrewURI());											
					}
				$templates[] = 'manage/emtySelfClose.tpl';
//				$templates[] = 'emptyHeader.tpl';
				}
			else
				$MESS = new Message('Error', 'Ошибка входа', 'Неизвестный провайдер. <a href=\'/entry\'>Попробуем снова</a>?', $NAV->GetPrewURI());															
			}
	    break;
	    }
	if(isset($MESS))
		{
		$_SESSION['MESSAGE'] = $MESS;				
		$REDIRECT = 'http://'.$_SERVER['HTTP_HOST'];

		}
	echo $REDIRECT;
	print_r($MESS);
	header('Location: '.$REDIRECT);	
//	echo "<meta http-equiv=\"refresh\" CONTENT=\"0; url=http://".$NAV->GetPrewURI()."\">";
?>