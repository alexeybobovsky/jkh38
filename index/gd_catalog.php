<?
$realPath = $ROUT->GetStartedUrlLvl($allMenu['curLabel']['link'], $allMenu['activeLink'], $uri);
$curURL = $realPath['url'];
$curURLLvl = $realPath['lvl'];
if($fuckedSymbolPosition = strpos($post[$curURLLvl], '?'))	//неверная обрезка строки запроса URL -  оставляет в параметрах знак "?" и все что за ним
	$post[$curURLLvl] = substr($post[$curURLLvl], 0, $fuckedSymbolPosition);		
if((!trim($post[$curURLLvl])))
	$act = $allMenu['curNodeName'];
else
	$act = trim($post[$curURLLvl]);
$isMng = ($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)?true:false;	
$error = 0;
$tplDir = 'main/';

switch ($act)
	{
	case 'set': 
		{
		if(($post[$curURLLvl+1] == 'message'))
			{
			$templates = array();				
			print_r($_POST);
			}
		elseif(($post[$curURLLvl+1] == 'updateLayerOrgCnt'))
			{
			if($isMng)
				{
				$templates = array();				
				require_once("../classes/gd/GetOrg_class.php");	
				require_once("../classes/gd/SetOrg_class.php");	
				$getOrg = new GetOrganization;
				$setOrg = new setOrganization;			
				$layers = $getOrg->getLayers2lvl();
				for($i=0; $i<sizeof($layers); $i++)
					{
					$setOrg->updateLayerOrgCnt($layers[$i]['layer_id'], $getOrg->getObjectsOfLayer($layers[$i]['layer_id'], $CONST['curCity'], 1));
					}				
				}			
			else
				$error = 404;
			}
		}
	break;	
	case 'updateOrgCnt': 
		{
		if($isMng)
			{
			$templates = array();				
			require_once("../classes/gd/GetOrg_class.php");	
			require_once("../classes/gd/SetOrg_class.php");	
			$getOrg = new GetOrganization;
			$setOrg = new setOrganization;			
			$firmList = $getOrg->getAllFirmListService();
			$up = $setOrg->clearFirmSpecCnt('firm_objCnt');
			for($i=0; $i< sizeof($firmList); $i++)
				{								
				$up2 = $setOrg->incFirmSpecCnt($firmList[$i]['firm_id'], 'firm_objCnt',  $getOrg->getObjectsOfOrg($firmList[$i]['firm_id'], 1));
				}			
			}
		else
			{
			$error = 404;
			}
		}break;
	case 'updateImgCnt': 
		{
		if($isMng)
			{
			$templates = array();				
			require_once("../classes/gd/GetOrg_class.php");	
			require_once("../classes/gd/SetOrg_class.php");	
			$getOrg = new GetOrganization;
			$setOrg = new setOrganization;			
			$objList = $getOrg->getObjListWithImgService();
			$up1 = $setOrg->clearFirmSpecCnt('firm_imgCnt');
			for($i=0; $i< sizeof($objList); $i++)
				{				
				$imgCnt = $getOrg->getImgForObjService($objList[$i]['objId'], 1);
				$up1 = $setOrg->incFirmSpecCnt($objList[$i]['firm_id'], 'firm_imgCnt', $imgCnt);
				}			
			}
		else
			{
			$error = 404;
			}
		}break;
	case 'updateIndex': 
		{
		if($isMng)
			{
			$objType = array(	
								'0' => 'catFirm', 
								'1' => 'firmName', 
								'2' => 'street', 
								'3' => 'phone', 
								'4' => 'firmInfo', 
								'5' => 'catDocs', 
								'6' => 'docName', 
								'7' => 'docBody', 
								'8' => 'catNews', 
								'9' => 'newsName', 
								'10' => 'newsBody' 
								);
			require_once("../classes/TimeMesure_class.php");
			$TM = new TimeMesure ('logIndex.txt');
			$TM->TimeCalc('start');			
			$startPacket = (trim($post[$curURLLvl+1])) ? intval($post[$curURLLvl+1]) : 0;
			$templates = array();				
			require_once("../classes/gd/GetOrg_class.php");	
			require_once("../classes/gd/SetOrg_class.php");	
			$getOrg = new GetOrganization;
			$setOrg = new setOrganization;			
			$setOrg->clearIndex();
			$orgList = $getOrg->getAllFirmListService();
			$orgLayersList = $getOrg->getLayers2lvl();
			
			$TM->TimeCalc('Total objects is '.sizeof($orgList));			
			$TM->TimeCalc('Start from '.$startPacket*$packetSize);			
			$TM->TimeCalc('Firms');
			$exluded = array('
', ' ', 'nbsp', '!','@','#','$','%','^','&','*','(',')','-','_','=','`','~',':',';','\'','"','\\','/','|','?','.',',','<','>', '№','%', '«', '»'); 
			for($i=0; $i< sizeof($orgList); $i++)
				{				
				$setOrg->updIndex(1, $orgList[$i]['firm_id'], str_replace($exluded, '', $orgList[$i]['firm_name']));
				if($data = trim($orgList[$i]['firm_info']))
					$setOrg->updIndex(4, $orgList[$i]['firm_id'],  str_replace($exluded, '', strip_tags($data)));					
				}
			$TM->TimeCalc('catFirm');				
			for($i=0; $i< sizeof($orgLayersList); $i++)
				{				
				if(!$orgLayersList[$i]['layer_disabled'])
					$setOrg->updIndex(0, $orgLayersList[$i]['layer_id'], str_replace($exluded, '', $orgLayersList[$i]['layer_name'].$orgLayersList[$i]['layer_title'] ));
				}
			$TM->TimeCalc('properties');				
			$propList = $getOrg->getAllPropListIndexService();
			for($i=0; $i< sizeof($propList); $i++)
				{	
				$type = ($propList[$i]['op_id'] == 1) ? 2 : 3;
				$setOrg->updIndex($type, $propList[$i]['obj_id'],  str_replace($exluded, '', $propList[$i]['prop_value']));
				}
			require_once("../classes/gd/news_class.php");			
			$NEWS = new news;	
			$newsLayersList = $NEWS->getCatListNoEmptyUser(1);
			$docsLayersList = $NEWS->getCatListNoEmptyUser(2);
			$newsList = $NEWS->getAllNewsListIndexService();
			$TM->TimeCalc('catDocs');				
			for($i=0; $i< sizeof($docsLayersList); $i++)
				{				
				if(!$docsLayersList[$i]['is_sys'])
					$setOrg->updIndex(5,  $docsLayersList[$i]['catId'],  str_replace($exluded, '', $docsLayersList[$i]['cat_name']));					
				}
			$TM->TimeCalc('catNews');				
			for($i=0; $i< sizeof($newsLayersList); $i++)
				{				
				if(!$newsLayersList[$i]['is_sys'])
					$setOrg->updIndex(8,  $newsLayersList[$i]['catId'],  str_replace($exluded, '', $newsLayersList[$i]['cat_name']));					
				}
			$TM->TimeCalc('news');				
			for($i=0; $i< sizeof($newsList); $i++)
				{
				
				$setOrg->updIndex(($newsList[$i]['news_parId'] == 1) ? 9 : 6, $newsList[$i]['news_id'],  str_replace($exluded, '', $newsList[$i]['news_name']));
				$setOrg->updIndex(($newsList[$i]['news_parId'] == 1) ? 10 : 7,  $newsList[$i]['news_id'],  str_replace($exluded, '', strip_tags($newsList[$i]['news_body'])));					
				}
			$TM->TimeCalc('end');
			$TM->TimeEnd();
			}	
		else
			{
			$error = 404;
			}
		}break;
	case 'updateImgService': 
		{
		if($isMng)
			{
			require_once("../classes/TimeMesure_class.php");
			$TM = new TimeMesure ('logImg.txt');
			$TM->TimeCalc('start');			
			$startPacket = (trim($post[$curURLLvl+1])) ? intval($post[$curURLLvl+1]) : 0;
			$templates = array();				
			require_once("../classes/gd/GetOrg_class.php");	
			require_once("../classes/gd/SetOrg_class.php");	
			$getOrg = new GetOrganization;
			$setOrg = new setOrganization;			
			$orgList = $getOrg->getObjListWithImgService();
			$orgListSize = sizeof($orgList);
			$packetSize = 10;
			echo 'Total objects is '.$orgListSize;
			$TM->TimeCalc('Total objects is '.$orgListSize);			
			$TM->TimeCalc('Start from '.$startPacket*$packetSize);			
			echo '</br>';
			$sizeArr = array(90, 45, 24);
			$sizeArrCnt = sizeOf($sizeArr);
//			for($i=0; $i< ($orgListSize); $i++)
//			for($i=0; $i< 3; $i++)
			for($i=$startPacket*$packetSize; $i< (($startPacket + 1)*$packetSize); $i++)
				{
				$urlPathRoot = 		'/src/upload/org/'.$orgList[$i]['objId'];						
				$urlPathSrc = 		$urlPathRoot.'/1024';
				$dir = opendir($CONST['relPathPref'].$urlPathSrc);
				while($file = readdir($dir))
					{
					if (($file!=".")&&($file!=".."))
						{     
						echo $srcFile = $CONST['relPathPref'].$urlPathSrc.'/'.$file;						
						$TM->TimeCalc($srcFile);			
						for($k=0; $k<$sizeArrCnt; $k++)
							{								
							$sizePath = $urlPathRoot.'/'.$sizeArr[$k];
							if(!is_dir($CONST['relPathPref'].$sizePath))
								{
								mkdir($CONST['relPathPref'].$sizePath);
								}
							$destFile = $CONST['relPathPref'].$sizePath.'/'.$file;
								$ROUT->imageResizeSqare($srcFile, $destFile, $sizeArr[$k], 1);								
/*							if(!is_file($destFile))
								{
								$ROUT->imageResizeSqare($srcFile, $destFile, $sizeArr[$k], 1);								
								}		*/					
							}
						echo '</br>';
						}
					}
				}
			$TM->TimeCalc('Last object number '.($startPacket + 1)*$packetSize);			
			$TM->TimeCalc('end');
			$TM->TimeEnd();
			}	
		}break;
		
	case 'catalog': 
		{
		$menuActive = 'menuCatalog';
		require_once("../classes/gd/GetOrg_class.php");	
		$getOrg = new GetOrganization;
		
		$title = 'Рубрики организаций';
		$layers = $getOrg->getLayersTree();
//		print_r($layers);
/*		for($i=0; $i<sizeof($layers); $i++)
			{
			if($layers[$i]['lvl'])
				$layers[$i]['item']['objCnt'] =  $getOrg->getObjectsOfLayer($layers[$i]['item']['layer_id'], $CONST['curCity'], 1);
			}*/
		$USER = $_SESSION['USER'];
		$SMRT['modules'][] =  array('name' => 'title', 	'body' => $title);		
		$SMRT['modules'][] =  array('name' => 'layers', 'body' => $layers);		
		$SMRT['modules'][] =  array('name' => 'client', 'body' => array('city' => $USER->curCity, 
																		'name' => $USER->appName, 
																		'version'=>$USER->appVersion));		

				
		$templates = array();
		$templates[] = $tplDir.'header.tpl';
		$templates[] = $tplDir.'catalog0p.tpl';		
//		$templates[] = $tplDir.'mapGlobal_tmp.tpl';						
		}
	break;
	default : 
		{
		if($par1 = trim($post[$curURLLvl]))
			{
//			echo $par1.'<br>';
//			echo $par2 = trim($post[$curURLLvl+1]).'<br>';
			$par2 = trim($post[$curURLLvl+1]);
			require_once("../classes/gd/GetOrg_class.php");	
			$getOrg = new GetOrganization;
//			print_r($post);
			if($urlObj = $getOrg->getCatalogObjByURL($USER->curCity['id'], urldecode($par1), urldecode($par2)))
				{
				$menuActive = 'menuCatalog';

				$layers = $getOrg->getLayersTree();
				$templates = array();
//				$templates[] = $tplDir.'headerNew.tpl';
				$templates[] = $tplDir.'header.tpl';
				if($urlObj['type'] == 'layer') 		//Рубрика
					{
					if(strstr($par2, 'sort'))
						{
						$sortStr = $par2;
						$sortArr = explode('_', $par2);
						$sortParam = 	$sortArr[1];
						$sortDir = 		$sortArr[2];
						if($limitStr = trim($post[$curURLLvl+2]))
							{
							$limitArr = 	explode('_', $limitStr);
							$limitNumOnPage = 	$limitArr[0];
							$limitCurPage = 	$limitArr[1];							
							}
						else
							{
							$limitNumOnPage = 	0;
							$limitCurPage = 	1;											
							}
//						echo 'sorting detected'.$par2;						
						}
					else
						{
						$sortParam = 	'Name';
						$sortDir = 		'Asc';
						$limitNumOnPage = 	0;
						$limitCurPage = 	1;																	
						$sortStr = $sortParam.'_'.$sortDir;
						$limitStr = $limitNumOnPage.'_'.$limitCurPage;
						}
					$sortFunctionNameList = array('sortOrgNameAsc', 'sortOrgNameDesc');
					$sortFunctionName = 	 (in_array('sortOrg'.$sortParam.$sortDir, $sortFunctionNameList)) ? 'sortOrg'.$sortParam.$sortDir : $sortFunctionNameList[0];					
					$urlObj['body'] = $getOrg->getCurLayerSimple($urlObj['obj']);
					$title = ($urlObj['body']['layer_title'])?$urlObj['body']['layer_title']:$urlObj['body']['layer_name'].' в Иркутске';
					$objectList = $getOrg->getOrgsOfLayer($urlObj['obj'], $USER->curCity['id'], ($isMng)?1:0);
					$firmListFullCnt = sizeof($objectList);
					$pagerFullCnt = ($limitNumOnPage) ? ceil($firmListFullCnt/$limitNumOnPage) : 1;
					if($objectList)
						{
						usort($objectList, $sortFunctionName); 
						$start 	= ($limitNumOnPage) ? ($limitCurPage - 1)*$limitNumOnPage : 0;
						$end 	= ($limitNumOnPage) ? ($limitCurPage)*$limitNumOnPage : $firmListFullCnt;
						$cnt = 0;
//						echo $start.' - '.$end;
						for($i=$start; $i<$end; $i++)
							{
//							$orgObjList = $getOrg->getObjectsOfOrg($objectList[$i]['firm_id'], 0);
							if(isset($objectList[$i]))
								{
								$objectListShown[$cnt] = $objectList[$i];
								$objectListShown[$cnt]['number'] =  $i+1;
//								echo $objectListShown[$cnt]['firm_name'][0] = $ROUT->ucfirst_ru($objectList[$i]['firm_name']);
//								echo '<br>';
								$objectListShown[$cnt]['firm_name'] = $ROUT->first_letter_up($objectList[$i]['firm_name']);
								$objectListShown[$cnt]['firm_rank'] = ($objectList[$i]['firm_rank']) ? $objectList[$i]['firm_rank'] : 0 ;
								$objectListShown[$cnt]['stars'] = $ROUT->getStars($objectListShown[$cnt]['firm_rank'], 5);							
								$orgObjList = $getOrg->getObjectsOfOrgAndLayer($objectList[$i]['firm_id'], $urlObj['body']['layer_id']);
								for($k=0; $k<sizeof($orgObjList); $k++)
									{
									$prop = array();
									$objProp = $getOrg->getObjectProperties($orgObjList[$k]['obj_id'], 1);					
									$prop['location'] = $objProp['street']['value'].' '.$objProp['building']['value'];
									$prop['location'] .= ($objProp['adrAdd']['value'])?' '.$objProp['adrAdd']['value']:'';
									$prop['phone'] = $ROUT->makeCityPhone($objProp['phone1']['value'], 0);
									$prop['phone'] .= ($objProp['phone2']['value'])?', '.$ROUT->makeCityPhone($objProp['phone2']['value'], 0):'';							
									if($objProp['img'])
										{
										$cnt1 = 0;
										while ((list ($key, $val) = each ($objProp['img']))&&($cnt1<1))
											{
											$img['path']  = $val['path'];
											$img['file']  = $val['file'];
											$img['about']  = $val['about'];
											$prop['img'] = $img;
											$cnt1 ++;
											}	
	//									print_r($prop['img']);
										}								
									$objectListShown[$cnt]['objProp'][$k] = $prop;					
									$objectListShown[$cnt]['objProp'][$k]['objId'] = $orgObjList[$k]['obj_id'];					
									}										
								$cnt ++;
								}
							}
	//					print_r($objectList);
						$SMRT['modules'][] =  array('name' => 'objNumStr', 'body' => ($firmListFullCnt)?$firmListFullCnt.' '.$ROUT->getCorrectDeclensionRu($firmListFullCnt, array('организация', 'организации', 'организаций')):'нет организаций');		
						$SMRT['modules'][] =  array('name' => 'obj', 'body' => $objectListShown);		
						$SMRT['modules'][] =  array('name' => 'sort', 'body' => array(	'curDir' => $sortDir, 
																						'nextPage' => (($pagerFullCnt>1)&&($limitCurPage < $pagerFullCnt)) ? $limitCurPage+1  : 0, 
																						'prewPage' => ($limitCurPage>1) ? $limitCurPage-1 : 0, 
																						'curPage' => $limitCurPage, 
																						'curNumOnPage' => $limitNumOnPage, 
//																						'sortStrPref' => 'sortOrg', 
																						'sortStr' => $sortStr, 
																						'limitStr' => $limitStr, 
																						'pagerFullCnt' => $pagerFullCnt, 
//																						'limitStr' => $limitStr, 
																						'paramStr' => 'sort_Name_' ));		
						
						require_once("../classes/gd/SetOrg_class.php");	
						$setOrg = new setOrganization;			
						$setOrg->incLayerViews($urlObj['body']['layer_id']);	
						
						}
					else
						{
						$SMRT['modules'][] =  array('name' => 'empty', 'body' => 1);								
						}
					$SMRT['modules'][] =  array('name' => 'curLayer', 'body' => $urlObj['body']);		
					if($par2 == 'table')
						$templates[] = $tplDir.'catalog2pTable.tpl';
					else
						$templates[] = $tplDir.'catalog1p.tpl';
					}
				elseif($urlObj['type'] == 'org') 		//Организация
					{					
					$catPrefString = '/catalog/';
					if(($_SERVER['HTTP_REFERER'])&&(strstr($_SERVER['HTTP_REFERER'], $catPrefString)))
						{
						$categName = urldecode($ROUT->getStrPrt($_SERVER['HTTP_REFERER'], $catPrefString, 1));
						$curLayerSimple = $getOrg->getLayersOfSearchStrStrong($categName);	
						$curLayerSimple['layerName'] = $curLayerSimple['layer_name'];
						}
					else
						{
						$curLayerSimple = 0;
						}				
					$object['body'] = 		$getOrg->getCurFirm($urlObj['obj']);					
					$object['body']['rate'] =  	$getOrg->getRating($object['body']['firm_id'], 'org'); 
					$object['body']['firm_name'] = $ROUT->first_letter_up($object['body']['firm_name']);					
					$object['body']['firm_rank'] = ($object['body']['firm_rank']) ? $object['body']['firm_rank'] : 0;
					$object['body']['stars'] = $ROUT->getStars($object['body']['firm_rank'], 5);
					$object['comments'] = 	$getOrg->getCommentsOfFirmSmart($urlObj['obj'], 0, ($isMng)?1:0, 'c', 0, 0);
					$object['userRate'] = 	$getOrg->getRatingOfObjUser($urlObj['obj'], $USER->id, 'org');
					$title = $object['body']['firm_name'];
					$orgObjList = $getOrg->getObjectsOfOrg($object['body']['firm_id'], 0);
					$objLayersGlobal = array(); 
					$object['objCnt'] = sizeof($orgObjList);	
					$layersDiff = 0;
					$maxLayer = $maxLayerTmp = array();
					for($k=0; $k<$object['objCnt']; $k++)
						{
						$prop = array();
						$objProp = $getOrg->getObjectProperties($orgObjList[$k]['obj_id'], 1);					
						$prop['location'] = $objProp['street']['value'].' '.$objProp['building']['value'];
						$prop['location'] .= ($objProp['adrAdd']['value'])?' '.$objProp['adrAdd']['value']:'';
						$prop['phone'] = $ROUT->makeCityPhone($objProp['phone1']['value'], 0);
						$prop['phone'] .= ($objProp['phone2']['value'])?', '.$ROUT->makeCityPhone($objProp['phone2']['value'], 0):'';							
						$prop['location'] = (trim($prop['location'])) ? $prop['location'] : '';
						$prop['phone'] = 	(trim($prop['phone'])) ? $prop['phone'] : '';
						$prop['objLayers'] = 	$getOrg->getObjectLayers($orgObjList[$k]['obj_id'], 0);
						if(!$curLayerSimple)
							$maxLayerTmp = 	$getOrg->getMaxViewedObjectLayer($orgObjList[$k]['obj_id']);						
						if(!$k)
							{
							if(!$curLayerSimple)
								$maxLayer = $maxLayerTmp;
							$objLayersGlobal = $prop['objLayers'];
							}
						else
							{
							if(($maxLayerTmp['layer_views'] > $maxLayer['layer_views'])&&(!$curLayerSimple))
								$maxLayer = $maxLayerTmp;
								
							$i=0;
							foreach($objLayersGlobal as $c1)
								if(in_array($c1, $prop['objLayers']))
									$i++;														
							if($i!=sizeof($objLayersGlobal))
								$layersDiff = 1;
							}
						if($objProp['img'])
							{
							while (list ($key, $val) = each ($objProp['img'])) 
								{
								$img['path']  = $val['path'];
								$img['file']  = $val['file'];
								$img['about']  = ($val['about']) ? $val['about'] : 'Фото '.$object['body']['firm_name'].' '.$USER->curCity['name'];
								$prop['img'][] = $img;
								}	
							}
						$object['objProp'][$k] = $prop;					
						$object['objProp'][$k]['objId'] = $orgObjList[$k]['obj_id'];					
						}
					if(!$curLayerSimple)
						$curLayerSimple = $maxLayer;
					if(!$layersDiff)					
						$SMRT['modules'][] =  array('name' => 'orgLayers', 'body' => $object['objProp'][0]['objLayers']);		
					$SMRT['modules'][] =  array('name' => 'obj', 'body' => $object);		
					$SMRT['modules'][] =  array('name' => 'curLayer', 'body' => $curLayerSimple);		
//					$templates[] = $tplDir.'catalog3p.tpl';									
					$templates[] = $tplDir.'catalog2p.tpl';		
					
//					print_r($curLayerSimple);
//					print_r($SMRT);
					}
				require_once("../classes/gd/SetOrg_class.php");	
				$setOrg = new setOrganization;			
				$setOrg->incFirmViews($object['body']['firm_id']);	
//				$USER = $_SESSION['USER'];
				$SMRT['modules'][] =  array('name' => 'title', 	'body' => $title);		
				$SMRT['modules'][] =  array('name' => 'layers', 'body' => $layers);		
				$SMRT['modules'][] =  array('name' => 'client', 'body' => array('city' => $USER->curCity, 
																				'name' => $USER->appName, 
																				'version'=>$USER->appVersion));
//				$templates[] = $tplDir.'footerNew.tpl';
				}
			else
				{
				$error = 404;
				}
				
			}
/*		if(!$autoLoadedObj)
			$REDIRECT = $allMenu['lastLink'];*/
		}
	};
if($error==404)	
	{
	$messBodyNews=($CONST['debugMode'])?'Запрошенная Вами страница не найдена (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')':'Запрошенная Вами страница не найдена';
	$MESS = new Message('Error', 'ERROR 404', $messBodyNews, $NAV->GetPrewURI());											
	}
if(isset($MESS))
	{		
/*	echo '<hr>';
	print_r($MESS);*/
	$_SESSION['MESSAGE'] = $MESS;
	header('Location: /');	
	}
elseif($REDIRECT)
	{
//	echo $REDIRECT;
	header('Location: '.$REDIRECT);	
	}
else
	{
	$SMRT['modules'][] =  array('name' => 'menuActive', 	'body' => $menuActive);		
	if(isset($metaDescription))
		$SMRT['modules'][] =  array('name' => 'meta', 'body' => array(	'keywords' => $metaKewords,
																		'description' => $metaDescription));	
	}
?>