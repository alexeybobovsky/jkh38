<?
//echo __FILE__	;
//print_r($allMenu);
//echo $uri;
$realPath = $ROUT->GetStartedUrlLvl($allMenu['curLabel']['link'], $allMenu['activeLink'], $uri);
$curURL = $realPath['url'];
$curURLLvl = $realPath['lvl'];
//print_r($realPath);
//echo '1: '.$realPath.' - '.$allMenu['curNodeId'];
$isMng = false;
if($allMenu['curNodeId']){
	$isMng = ($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)?true:false;	
}
if((!trim($post[$curURLLvl])))
	$act = $allMenu['curNodeName'];
else
	$act = trim($post[$curURLLvl]);
//print_r($allMenu);	
//	echo $act;	
$error = 0;
$menuActive = '';
$feedFile = '../htdocs/realty.xml';
//$feedFile = '../realty.xml';

switch ($act)
	{	
	case 'kaznaXML': 
		{
			echo 'kaznaXML';
		} 
	break;	
	case '404': 
		{
		if(isset($_SESSION['MESSAGE_404']))
			{
/*			$mess =  $_SESSION['MESSAGE_404'];
			$SMRT['modules'][] = array('name' => 'MESS', 'body' => $_SESSION['MESSAGE_404']);
			$title = $mess->Header;*/
			$mess =  $_SESSION['MESSAGE_404'];
			$SMRT['modules'][] = array('name' => 'MESS', 'body' => $_SESSION['MESSAGE_404']);
			$title = $_SESSION['MESSAGE_404']->Header;
			unset($_SESSION['MESSAGE_404']);			
			}
		else
			{
			$title = 'Ошибка';
			$SMRT['modules'][] = array('name' => 'MESS', 'body' => new Message('Error', 'Страницы не найдено', 'Этой страницы не существует. Нам очень жаль. <br />Возможно, мы просто не успели её нарисовать. Но мы будем стараться, чтобы в следующий раз всё было на месте.', $NAV->GetPrewURI()));			
			}
		$SMRT['modules'][] =  array('name' => 'pageType', 'body' => '404');
		$SMRT['modules'][] =  array('name' => 'menuItems', 'body' => array('startLink' => 'list', 'curentItem' => 'none'));
		$SMRT['modules'][] =  array('name' => 'title', 	'body' => $title);		
		$templates = array();
		$templates[] = $tplDir.'headerSimple.tpl';
		$templates[] = $tplDir.'Message.tpl';				
		$templates[] = $tplDir.'footer.tpl';				
//		$templates[] = $tplDir.'footerSimple.tpl';				
		} break;
	case 'nav':  //Режим навигатора - слежение за автотранспортом
		{
//			echo 'nav';
		}
	case 'main': 
		{
//		print_r($_SESSION['filter']);
//		$title = 'Мониторинг новостроек Иркутска';	
		require_once("../classes/gis/manage_class.php");	
		$MNG = new Manage;			
		$mngAct = array(
				'ao' => '/manage/set/objAdd',
				'al' => '/manage/set/layerAdd',
				'uf' => '/manage/set/fileUpload',
				);
		$title = 'Портал ЖКХ Иркутской области';	
		$metaDescription = 'Официальный портал ЖКХ Иркутской области';			
//		print_r($MNG->getAllLayers($isMng));
		$SMRT['modules'][] =  array('name' => 'mapType', 'body' => $act);
		$SMRT['modules'][] =  array('name' => 'menuItems', 'body' => array('startLink' => 'list', 'curentItem' => 'none'));
		$SMRT['modules'][] =  array('name' => 'title', 	'body' => $title);		
		$SMRT['modules'][] =  array('name' => 'links', 'body' => array('start' => 'http://'.$_SERVER['HTTP_HOST'].''));
		$templates = array();
//		print_r($USER);
		if(!$USER->registered){
				
				$templates[] = $tplDir.'headerSimple.tpl';
//				$templates[] = $tplDir.'login.tpl';				
				$templates[] = $tplDir.'footer.tpl';				
				$SMRT['modules'][] =  array('name' => 'pageType', 'body' => 'login');
//				echo 'login!';
		}
		else {	
			$usr2distr = $MNG->getUsrToDistr($USER->id, 1);
			if($usr2distr['distr_id']==1){
				//echo 'superUser';
				$templates[] = $tplDir.'header.tpl';
				$SMRT['modules'][] =  array('name' => 'user', 'body' => $usr2distr);								
				$SMRT['modules'][] =  array('name' => 'layerList', 'body' => $MNG->getAllLayers($isMng));
				$usrStr = 'Диспетчер ';
//				print_r($usr2distr);
			}
			elseif($usr2distr['distr_id']>1000){
				require_once("../classes/gis/zhkh.php");	
				$ZhKH = new ZhKH;	
/*				$orgList = $ZhKH->getOrgList($usr2distr['distr_id']);
				print_r($orgList);*/
				$SMRT['modules'][] =  array('name' => 'homeDistr', 'body' => $MNG->getSingleLayer($usr2distr['distr_id']));				
				$SMRT['modules'][] =  array('name' => 'npList', 'body' => 	$MNG->getNPListOfDistr($usr2distr['distr_id']));
				$templates[] = $tplDir.'headerSimple.tpl';
				if($usr2distr['role'] == 2 ){
					$usrStr = 'Диспетчер ';
					$SMRT['modules'][] =  array('name' => 'sectors', 'body' => $MNG->getAllSectors());
					$SMRT['modules'][] =  array('name' => 'orgList', 'body' => $ZhKH->getOrgList($usr2distr['distr_id']));
					$templates[] = $tplDir.'operator.tpl';				
					$SMRT['modules'][] =  array('name' => 'pageType', 'body' => 'operator');
					$SMRT['modules'][] =  array('name' => 'messStr', 'body' => $messStrReady); 
				} elseif($usr2distr['role'] == 3 ){
					$usrStr = 'Сотрудник администрации  ';
					$SMRT['modules'][] =  array('name' => 'pageType', 'body' => 'adm');
					$templates[] = $tplDir.'adm.tpl';									
				}
				$templates[] = $tplDir.'footer.tpl';
				$SMRT['modules'][] =  array('name' => 'user', 'body' => $usr2distr);								
			}
			else
				echo 'bad user';
		}
		}
	break;	
	case 'popular': 
		{
		}
	break;	
	case 'siteMap': 
		{

		}
	break;

	default :
		{
		$error=404;
		}
	};
if($error==404)	
	{
	$messBodyNews=($CONST['debugMode'])?'Запрошенная Вами страница не найдена (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')':'Запрошенная Вами страница не найдена';
	$MESS = new Message('Error', 'ERROR 404', $messBodyNews, $NAV->GetPrewURI());											
	}
if(isset($MESS))
	{		
	$_SESSION['MESSAGE'] = $MESS;
	header('Location: /');	
	}
elseif($REDIRECT)
	{
	header('Location: '.$REDIRECT);	
	}
else
	{
	$SMRT['modules'][] =  array('name' => 'menuActive', 	'body' => ($menuActive) ? $menuActive : $act);		
	
	if(isset($titleForShare))
		$SMRT['modules'][] =  array('name' => 'shareButton', 'body' => array('link' => 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'],
																		'title' => $titleForShare.' на сайте ГОРОД-ДЕТЯМ.РФ'));		
	if(isset($metaDescription))
		{
		$SMRT['modules'][] =  array('name' => 'meta', 'body' => array(	'keywords' => $metaKewords,
																		'description' => $metaDescription));	
		}
	}																		
?>