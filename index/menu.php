<?php
$startYear = 2019;
$headerPar = $CNT->GetCurChild(0, 'header', 0);
$headerPar['userRegistered'] = $USER->registered;
$headerPar['userName'] = ($USER->registered)?$USER->name:'Гость';
$headerPar['userDisplayName'] = (($USER->displayName != 'NULL')&&($USER->displayName))? $USER->displayName : $headerPar['userName'];
$headerPar['providerName'] = $USER->providerName;
$headerPar['providerTitle'] = $USER->providerTitle;
$headerPar['userCity'] = $USER->curCity;
$headerPar['userId'] = 	 $USER->id;
$headerPar['curYear'] = (date("Y",time())!=$startYear)?$startYear.'&nbsp;&mdash; '.date("Y",time()).'&nbsp;г.г.':$startYear.'&nbsp;г.';
$headerPar['client'] =  array('name' => $USER->appName, 'version'=>$USER->appVersion);																						
$headerPar['startLink'] =  'http://'.$_SERVER['SERVER_NAME'].'';		
$headerPar['curURI'] =  $_SERVER['REQUEST_URI'].'';		
$headerPar['siteName'] =  'Портал ЖКХ Иркутской области';

/*временное меню*/
/*
$SMRT['modules'][] =  array('name' => 'menuList', 	'body' => array(
		array('title' => 'Начало', 'link' => 'http://'.$_SERVER['SERVER_NAME'].'/', 'id'=>'menuMain'),
		array('title' => 'Рубрики организаций', 'link' => '/catalog', 'id'=>'menuCatalog', 'more' => true),
		array('title' => 'Карта', 'link' => '/map', 'id'=>'menuMap'),
		array('title' => 'Новости', 'link' => '/news', 'id'=>'menuNews'),
		array('title' => 'Статьи', 'link' => '/doc', 'id'=>'menuDoc')
		));		
*/		
/*временное меню*/

$templates[] = $tplDir.'header.tpl';
$change_position = false;
$cur_sub_menu = 0;
$cur_menu = -1;
$CNT->GetMenuItems(0, 0, 1, 0);
$menuItems = $CNT->child;
$CNT->Reset();
$menu_cnt = 0;
$subMenu_cnt = 0;

$alt_cur_menu = -1;
$change_position = true;
if($uri)
	{
	$altCnt=0;
	for($k=0; $k<count($menuItems); $k++)
		{
		if(($ROUT->UriCompare($uri, $menuItems[$k]['body']['link'])))
			{
			$curMenu[$menuItems[$k]['level']] = $k;
			$m = 0;
			$curMenuNum = $k;
			if($menuItems[$k]['level'])
				{
				while($m<$menuItems[$k]['level'])
					{
					for($l=0; $l<count($menuItems); $l++)
						{
						if($menuItems[$curMenuNum]['catalog']['sc_parId'] == $menuItems[$l]['catalog']['sc_id'])
							{
							$curMenu[$menuItems[$l]['level']] = $l;
							$curMenuNum = $l;
							}
						}
					$m++;
					}
				}
			}	
		else
			{
			$weight =  $ROUT->UriFirstCompare($uri, $menuItems[$k]['body']['link']);
			if($weight)
				{
				$curMenuAlt[$altCnt]['weight'] = $weight;			
				$curMenuAlt[$altCnt]['lvl'] = $menuItems[$k]['level'];
				$curMenuAlt[$altCnt]['cnt'] = $k;
				$altCnt++;
				}
			}
		}	
	}
else
	{
	$curMenu[] = 0;
	}
if($change_position)
	{
	if(is_array($curMenu))
		{
		$NAV->SetPos($curMenu, $history);
		}
	elseif(is_array($curMenuAlt))
		{
		$curMenuIndx_tmp = 0;
		$equal = 0;
		for($i=1; $i<count($curMenuAlt); $i++)
			{
			if($curMenuAlt[$i]['weight']>$curMenuAlt[$curMenuIndx_tmp]['weight'])
				{
				$curMenuIndx_tmp = $i;
				}
			elseif($curMenuAlt[$i]['weight']==$curMenuAlt[$curMenuIndx_tmp]['weight'])
				{
				$equal ++;
				}
			}
		if(($equal==count($curMenuAlt)-1)&&(count($curMenuAlt)>1))
			{
			}
		else
			{
			$curMenuIndx = $curMenuIndx_tmp;
			$m = 0;
			$curMenuAltNum = $curMenuAlt[$curMenuIndx]['cnt'];
			$curMenuAltFin[$curMenuAlt[$curMenuIndx]['lvl']] = $curMenuAlt[$curMenuIndx]['cnt'];
			while($m<=$curMenuAlt[$curMenuIndx]['lvl'])
				{
				for($l=0; $l<count($menuItems); $l++)
					{
					if($menuItems[$curMenuAltNum]['catalog']['sc_parId'] == $menuItems[$l]['catalog']['sc_id'])
						{
						$curMenuAltFin[$menuItems[$l]['level']] = $l;
						$curMenuAltNum = $l;
						}
					}
				$m++;
				}
			$NAV->SetPos($curMenuAltFin, $history);
			$curMenu = $curMenuAltFin;
			}
		}
	else
		{
		$curMenu[] = 0;
		$NAV->SetPos($curMenuAlt, $history);		
		}
	}
$allMenu = $TPL->TPL_GetMenu($menuItems);
?>