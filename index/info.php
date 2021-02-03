<?
$keyword = ($allMenu['curNodeName'])?$allMenu['curNodeName']:$handler['sc_name'];
$content = $CNT->GetCurNodeKeyword($keyword , 1);
if($content['catalog']['sc_published'])
	{
	/*print_r($allMenu);*/
	//print_r($content);
	$textblock = 0;
	for($k=0;$k<count($content['body']); $k++)
		{
		if($content['body'][$k]['name'] == 'textblock')
			$textblock ++;
		}
	if(!$textblock)
		{
		$parId = $content['catalog']['sc_id'];
		$curEl = -1;
		for($i=0; $i<count($menuItems); $i++)
			{
			if(($menuItems[$i]['catalog']['sc_parId'] == $parId)&&(!$stop))
				{
	//				echo '<hr> 1 - '.$menuItems[$i]['catalog']['sc_name']; 
				$parId = $menuItems[$i]['catalog']['sc_id'];
				$curMenu[$menuItems[$i]['level']] = $i;
				if($menuItems[$i]['body']['textblock'])
					{					
	//					echo '<hr> 2 - '.$menuItems[$i]['catalog']['sc_name']; 
					$curId = $menuItems[$i]['catalog']['sc_id'];
					$curEl = $i;
					$stop++;
					}
				}
			}
		if($curEl>=0)
			{
	//		echo 'rrr';
			$content = $CNT->GetCurNodeKeyword($menuItems[$curEl]['catalog']['sc_name'], 1);
			$NAV->SetPos($curMenu, 1);
			$allMenu = $TPL->TPL_GetMenu($menuItems);
			}	
		}
		for($i=0; $i<count($content['body']); $i++)
			{
			if($content['body'][$i]['name'] == 'textblock')
				{
				$blockCount ++;
				$body[] = $content['body'][$i]['value'];
				}
			elseif($content['body'][$i]['name'] == 'template')
				{
				$template = $content['body'][$i]['value'];
				}
			elseif($content['body'][$i]['name'] == 'caption')
				{
				$caption = $content['body'][$i]['value'];
				}
			elseif($content['body'][$i]['name'] == 'title')
				{
				$title = $content['body'][$i]['value'];
				}
			}
	}
if ($blockCount)
	{
	if((!$allMenu['lastTitle'])&&(($title)||($caption)))
		{
		$allMenu['lastTitle'] = ($title)?$title:$caption;
		}
	$SMRT_TMP['name'] = 'BODY';
	$SMRT_TMP['body'] = $body;
	$SMRT['modules'][] = $SMRT_TMP;
	$titleMain = $allMenu['lastTitle'];
	$SMRT['modules'][] =  array('name' => 'menuItems', 'body' => array('startLink' => 'list', 'curentItem' => ''));
	$SMRT['modules'][] =  array('name' => 'menuTopLvlUse', 'body' => array(
							'active' => '', 
							'title' => $titleMain, 
							'counter' => ''));
	$SMRT['modules'][] =  array('name' => 'title', 	'body' => strip_tags($title));		
							
//	$templates[] = 'default/firmCatalogCategoryList.tpl';
	$templates[] = $tplDir.'info.tpl';
	$templates[] = $tplDir.'rightColumn.tpl';		
	
/*
	$templates[] = 'default/menuLeftStatic2011.tpl';			
	$templates[] = ($template)?$template:'default/infoBox.tpl';
	$templates[] = 'default/productCatalogFooter.tpl';*/
	}
else
	{
	$mess=($CONST['debugMode'])?'Запрошенная Вами страница не найдена (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')':'Запрошенная Вами страница не найдена';
	$MESS = new Message('Error', 'ERROR 404',  $mess, $NAV->GetPrewURI());								
	$SMRT_TMP['name'] = 'MESS';
	$SMRT_TMP['body'] = $MESS;
	$SMRT['modules'][] = $SMRT_TMP;
	$templates[] = 'Message.tpl';	
	}
?>