<?
if($oldSupport)
	{
//	chdir('..//');
	require_once('../classes/config.inc.php');
	require_once("../classes/MySQL_class.php");
	require_once("../classes/GetContent_class.php");
	require_once("../classes/fs/GetOrg_class.php");	
	require_once("../classes/User_class.php");
	require_once("../classes/ACL_class.php");
	require_once("../classes/Rout_class.php");
	$ROUT = new Routine;
	$getOrg = new GetOrganization;
	$CNT = 		new GetContent;
	$ROUT = new Routine;
	$CONST = $CNT->GetAllConfig();
	$REDIRECT = '';
/*	
option=com_comprofiler&    =		всякая хрень - завязана на пользователе - обрабатывать аналогично option=com_resource&
option=com_resource&			основной тип ссылок 
	Itemid=63 				фирмы
		category_id=61		старый id фирмы. если не указан, то редирект на список фирм
	Itemid=55				Весь город
	Itemid=18				Октябрьский
	Itemid=29				Правобережный
	Itemid=56				Свердловский
	Itemid=57				Ленинский
		&article=263		старый id  стройки. если стройка не указана или не найдена, показываем либо /construction  либо фильтруем по районам - если указано что-то вместо Itemid=55


*/	
//	print_r($_GET);
/*	$itemStr = "&Itemid";
	$itemDel = '&';
	$pos = strpos($uri, $itemStr);
	if($pos === false)
		{
		$pos = strpos($uri, $itemStr);	
		$itemStr = "?Itemid";
		$itemDel = '?';
		}
	if($pos !== false)
		{
		if(strlen($uri) > $pos+12)
			{
			$str0   = $ROUT->getStrPrt($uri, $itemStr, 1);
			$itemId   = $ROUT->getStrPrt($str0, $itemDel, 0);		
			}
		else
			$itemId   = intval($ROUT->getStrPrt($uri, $itemStr, 1));					
		}
	else
		$itemId = '';*/
	if($itemId = $_GET['Itemid'])
		{
		$filterStr = '';
		switch ($itemId)
			{
			case '18':
				{
				if (!$filterStr)
					$filterStr = 'filter_state~1-district~2';
				}
			case '29':
				{
				if (!$filterStr)
					$filterStr = 'filter_state~1-district~1';
				}
			case '56':
				{
				if (!$filterStr)
					$filterStr = 'filter_state~1-district~3';
				}
			case '57':
				{
				if (!$filterStr)
					$filterStr = 'filter_state~1-district~4';
				}
			case '55':
				{
				if (!$filterStr)
					$filterStr = 'filter_state~1';
/*				$str1   = $ROUT->getStrPrt($uri, '&article=', 1);
				$oldId   = $ROUT->getStrPrt($str1, '&', 0);*/
				$oldId   = $_GET['article'];
				if($oldId)
					{
					$objSingle = 	$getOrg->getObjectByOldId($oldId);
					$REDIRECT = '/list/construction/'.$objSingle['obj_id'];
					}
				else
					{
					$REDIRECT = '/list/construction/'.$filterStr;
					}				
				} break;
			case '59':
				{
/*http://www.fotostroek.ru/index.php?option=com_content&view=article&id=84:214-&catid=40:2010-07-17-02-30-51&Itemid=59
http://www.fotostroek.ru/index.php?option=com_content&view=article&id=99:2014-01-26-04-22-04&catid=40:2010-07-17-02-30-51&Itemid=59	
http://www.fotostroek.ru/index.php?option=com_content&view=article&id=78:2010-07-30-02-50-41&catid=40:2010-07-17-02-30-51&Itemid=59
http://www.fotostroek.ru/index.php?option=com_content&view=article&id=79:2010-07-30-03-13-08&catid=40:2010-07-17-02-30-51&Itemid=59	
http://www.fotostroek.ru/doc/2014/04/14/kak_pravilno_prinimat_kva
http://www.fotostroek.ru/doc/2014/04/14/ssyilki_na_internet-resur*/
				$REDIRECT = '/';
				if($_GET['view']=='article')
					{
//					echo $_GET['id'];
					if($_GET['id'] == '84:214-')
						$REDIRECT = '/doc/2014/04/14/214-fz_ob_uchastii_v_dole';
					elseif($_GET['id'] == '99:2014-01-26-04-22-04')
						$REDIRECT = '/doc/2014/04/14/chto_takoe_apartamentyi_i';
					elseif($_GET['id'] == '78:2010-07-30-02-50-41')
						$REDIRECT = '/doc/2014/04/14/kak_pravilno_prinimat_kva';
					elseif($_GET['id'] == '79:2010-07-30-03-13-08')
						$REDIRECT = '/doc/2014/04/14/ssyilki_na_internet-resur';
					}
//					echo $REDIRECT;
				} break;
			case '63':
				{
/*				$str1   = $ROUT->getStrPrt($uri, '&category_id=', 1);
				$oldId   = $ROUT->getStrPrt($str1, '&', 0);*/
				$oldId   = $_GET['category_id'];				
				if($oldId)
					{
					$firm = 	$getOrg->getCurFirmByOldId($oldId);
					$REDIRECT = '/list/firm/'.$firm['firm_id'];
					}
				else
					{
					$REDIRECT = '/list/firm';
					}				
				} break;
			default:
				{
				$REDIRECT = '/';				
				}
			}
			
		
		}
	else
		{
/*		$messBodyNews=($CONST['debugMode'])?'Запрошенная Вами страница не найдена':'Запрошенная Вами страница не найдена';
		$MESS = new Message('Error', 'ERROR 404', $messBodyNews, $NAV->GetPrewURI());													*/
		$REDIRECT = '/';				
		}
//	echo $REDIRECT;
	header("HTTP/1.1 301 Moved Permanently");
	header('Location: '.$REDIRECT);		
	}
?>