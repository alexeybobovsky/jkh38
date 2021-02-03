<?
/***************************************************************************/
/*		формирование блоков для отображения и проверка кэша			*/
/***************************************************************************/
/*************************************Кэширование************************************************************/
	$smarty->caching = true;
	$caching[$tplDir.'mainPage.tpl']['lifetime'] = '3600';
	$caching[$tplDir.'rightColumn.tpl']['lifetime'] = '300';
/*	$caching[$tplDir.'apartmentStart.tpl']['lifetime'] = '7200';*/
/*************************************-Кэширование-************************************************************/
	$templateCnt=0;
	while(isset($templates[$templateCnt]))
		{
		if(($templates[$templateCnt] == $tplDir.'footer.tpl')&&(!$smarty->isCached($tplDir.'footer.tpl')))	//footer
			{
			require_once("../classes/gd/news_class.php");			
			$NEWS = new news;
			$lastEvents[] = $NEWS->getLastNews(6, 1);
			$lastEvents[] = $NEWS->getLastNews(6, 2);
			for($k=0; $k<2; $k++)
				for($i=0; $i<sizeof($lastEvents[$k]); $i++)
					{					
					$srcPref =  ($k) ? 'doc'  :  'news';
					$newsListPrew[$srcPref][$i]['link'] = 		'/'.$srcPref.'/'.date('Y/m/d/', strtotime($lastEvents[$k][$i]['news_date'])).$lastEvents[$k][$i]['news_nameTranslit'];
					$newsListPrew[$srcPref][$i]['name'] = 		$lastEvents[$k][$i]['news_name'];
					$newsListPrew[$srcPref][$i]['nameShort'] = 	$ROUT->getSmartCutedString($lastEvents[$k][$i]['news_name'], 90) ;
					$ruDate = $ROUT->GetRusData(strtotime($lastEvents[$k][$i]['news_date']));
					$newsListPrew[$srcPref][$i]['date'] 	= $ruDate['date'].' '.$ruDate['month'].' ';
					$newsListPrew[$srcPref][$i]['date'] 	.= (date("Y",time())!=$ruDate['year'])?' '.$ruDate['year'].' ':'';
					$newsListPrew[$srcPref][$i]['header'] = strip_tags($lastEvents[$k][$i]['news_header']);
					}					
			require_once("../classes/fs/GetOrg_class.php");	
			$getOrg = new GetOrganization;
			$kvartal = 	array(0 => 'I',  1 => 'II',  2 => 'III',  3 => 'IV');

			$intervalArray = $getOrg->getDateIntervalsConstr();			
			$start = 	explode('_', $intervalArray['monStartBegin']);
			$end = 		explode('_', $intervalArray['dateEndEnd']);
			$first['value'] = $intervalArray['monStartBegin'];
			$first['label'] = $start[1].' квартал '.$start[0].' года';
			$last['value'] = $intervalArray['dateEndEnd'];
			$last['label'] = $end[1].' квартал '.$end[0].' года';
			$dateInterval[] = $first;
			for($i=$start[0]; $i<=$end[0]; $i++)
				{
				for($k=0; $k<sizeof($kvartal); $k++)
					{
					$startTmp['value'] = $i.'_'.$kvartal[$k];
					if(($startTmp['value']>$first['value']) && ($startTmp['value']<$last['value']))
						{
						$startTmp['label'] = $kvartal[$k].' квартал '.$i.' года';	
						$dateInterval[] = $startTmp;
						}
					}
				}
			$dateInterval[] = $last;	

				
			
			$SMRT['modules'][] =  array('name' => 'newsPrew', 'body' => $newsListPrew);		
			$SMRT['modules'][] =  array('name' => 'interval', 'body' => $dateInterval);		
			$SMRT['modules'][] =  array('name' => 'mat', 'body' => $getOrg->getListOfMaterialsAll());		
//			print_r($messageLast);
//			print_r($dateInterval);
			}		
/*		elseif(($templates[$templateCnt] == $tplDir.'leftColumn.tpl')&&(!$smarty->isCached($tplDir.'leftColumn.tpl')))	//
			{
			}*/
		elseif(($templates[$templateCnt] == $tplDir.'rightColumn.tpl')&&(!$smarty->isCached($tplDir.'rightColumn.tpl')))	//
			{
			require_once("../classes/fs/message_class.php");	
			$mesObj = new messageBase;			
			$advert = array();
//			echo date("Y_m");
			
			if((date("Y_m") == '2015_08') && ((date("d") >=12 )&&(date("d") < 19 )))
				{
				$cnt = 0;
				$advert[$cnt]['link'] = 'http://xn--38-8kcxu4bh8h.xn--p1ai';
				$advert[$cnt]['width'] = '175px';
				$advert[$cnt]['height'] = '300px';
				$advert[$cnt]['src'] = 	'/src/design/main/img/advSmesi_175x300.gif';
				}
			else
				$cnt = -1;
			/*	
			$advert[$cnt+1]['link'] = 'http://vssdom.ru/node/419';
			$advert[$cnt+1]['width'] = '175px';
			$advert[$cnt+1]['height'] = '90px';
			$advert[$cnt+1]['src'] = 	'/src/design/main/img/vss_175x90_2-2_mln.gif';
			*/
			
			$advert[$cnt+1]['link'] = 'http://vssdom.ru/node/386';
			$advert[$cnt+1]['width'] = '175px';
			$advert[$cnt+1]['height'] = '90px';
			$advert[$cnt+1]['src'] = 	'/src/design/main/img/vss_175x90_2015_obmen.gif';
			
			$messageLast = 	$mesObj->getLastConstrMessageList($ROUT, 5);
			$SMRT['modules'][] =  array('name' => 'adv', 'body' => $advert);		
			$SMRT['modules'][] =  array('name' => 'mesList', 'body' => $messageLast);		
			}
		elseif(($templates[$templateCnt] == $tplDir.'constrMainPage.tpl')&&(!$smarty->isCached($tplDir.'constrMainPage.tpl')))	//
			{
			$cityId = $USER->curCity['id'];
			$limit = 6;
			require_once("../classes/fs/GetOrg_class.php");	
			$getOrg = new GetOrganization;
			}
		elseif(($templates[$templateCnt] == $tplDir.'mainPage.tpl')&&(!$smarty->isCached($tplDir.'mainPage.tpl')))	//footer
			{
			$cityId = $USER->curCity['id'];
			$limit = 6;
			require_once("../classes/fs/GetOrg_class.php");	
			$getOrg = new GetOrganization;
			require_once("../classes/fs/apartment_class.php");	
			$Apartments = new Apartments;
			
			$filter['city'] = $USER->curCity['id'];



/**************************************************Последние фото********************************************************************************/			
			$dateLimit = date('Y-m-d', (time()-(3600*24*30)));
			$objRet = $getOrg->getConstrListLastFotoDate($cityId, $dateLimit, 0);							
			$objList = $objRet['list'];	
			$listLastFotoCntNum = sizeof($objList);
			$listLastFotoCnt = $listLastFotoCntNum.' '.$ROUT->getCorrectDeclensionRu($listLastFotoCntNum, array('новостройки', 'новостроек', 'новостроек'));
			for($k=0; $k<$limit; $k++)
				{
				if(isset($objList[$k]))
					{
					$obj = array();
					$render	= '';				
					$objId = $objList[$k]['obj_id'];
					$objSingle = $objList[$k];
					$objProp = 		$getOrg->getObjectProperties($objId, 0);
					$objFoto = 		$getOrg->getFotoOfSet($objId,  $objSingle['obj_lastCapture']);
					$objApartmentCnt = 	$Apartments->getApListOfObj($objId, 1);
					
					if($objProp)
						{
						for($i = 0; $i< sizeof($objProp); $i++)
							{
/*							if($objProp[$i]['property_name'] == 'fotoRender')*/
							if(($objProp[$i]['property_name'] == 'fotoRender')&&(!$render))
								{
								$render =  $objProp[$i]['prop_value'];
								}
							}
						}
					if(!$render)
						{
						$obj['icon'] = $objFoto[0]['foto_src'];						
						}
					else
						$obj['icon'] = $render;		
					if($objFoto[0]['foto_date'])
						{
						$lastDateArr =  $ROUT->GetRusData(strtotime($objFoto[0]['foto_date']));
						$obj['fotoLastDate'] = $lastDateArr['date'].' '.$lastDateArr['month'];
						$obj['fotoLastDate'] .= (date("Y",time()) != $lastDateArr['year'])?' '.$lastDateArr['year']:'';
						}
					else
						{
						$obj['fotoLastDate'] = 'пока нет фото';
						}
					$obj['name'] =  $objSingle['obj_name'];
					$obj['id'] =  $objSingle['obj_id'];
					$obj['objApartmentCnt'] = $objApartmentCnt.' '.' '.$ROUT->getCorrectDeclensionRu($objApartmentCnt, array('квартира', 'квартиры', 'квартир'));
					$listLastFoto[] = $obj;
					}
				}	
			
/**************************************************Начато строительство ********************************************************************************/									

			$dateStart = date('Y-m-d', (time()-(3600*24*450)));
			$objRet = 	$getOrg->getConstrListByMonitoringStart($cityId, $dateStart, 0);
			$objList = $objRet['list'];	
			$objCount = sizeof($objList);
			$listNewObjCntNum = sizeof($objList);
			$listNewObjCnt = $listNewObjCntNum.' '.$ROUT->getCorrectDeclensionRu($listNewObjCntNum, array('новостройки', 'новостроек', 'новостроек'));
			for($k=0; $k<$limit; $k++)
				{
				if(isset($objList[$k]))
					{
					$obj = array();
					$render	= '';				
					$objId = $objList[$k]['obj_id'];
					$objSingle = $objList[$k];
					$objProp = 		$getOrg->getObjectProperties($objId, 0);
					$objFoto = 		$getOrg->getFotoOfSet($objId,  $objSingle['obj_lastCapture']);
					if($objProp)
						{
						for($i = 0; $i< sizeof($objProp); $i++)
							{
							if(($objProp[$i]['property_name'] == 'fotoRender')&&(!$render))
/*							if($objProp[$i]['property_name'] == 'fotoRender')*/
								{
								$render =  $objProp[$i]['prop_value'];
								}
							}
						}
					if(!$render)
						{
						$obj['icon'] = $objFoto[0]['foto_src'];						
						}
					else
						$obj['icon'] = $render;		
					if($objFoto[0]['foto_date'])
						{
						$lastDateArr =  $ROUT->GetRusData(strtotime($objFoto[0]['foto_date']));
						$obj['fotoLastDate'] = $lastDateArr['date'].' '.$lastDateArr['month'];
						$obj['fotoLastDate'] .= (date("Y",time()) != $lastDateArr['year'])?' '.$lastDateArr['year']:'';
						}
					else
						{
						$obj['fotoLastDate'] = 'пока нет фото';
						}
					$obj['name'] =  $objSingle['obj_name'];
					$obj['id'] =  $objSingle['obj_id'];
					$listNewObj[] = $obj;
					}
				}	
/**************************************************Закончено строительство  ********************************************************************************/									
			$dateStart = date('Y-m-d', (time()-(3600*24*180)));
			$objRet = 	$getOrg->getConstrListReady($cityId, $dateStart, 0);
			$objList = $objRet['list'];	
			$listRedyObjCntNum = 	sizeof($objList);	
			$listRedyObjCnt = $listRedyObjCntNum.' '.$ROUT->getCorrectDeclensionRu($listRedyObjCntNum, array('новостройки', 'новостроек', 'новостроек'));			
			for($k=0; $k<$limit; $k++)
				{
				if(isset($objList[$k]))
					{
					$obj = array();
					$render	= '';				
					$objId = $objList[$k]['obj_id'];
					$objSingle = $objList[$k];
					$objProp = 		$getOrg->getObjectProperties($objId, 0);
					$objFoto = 		$getOrg->getFotoOfSet($objId,  $objSingle['obj_lastCapture']);
					if($objProp)
						{
						for($i = 0; $i< sizeof($objProp); $i++)
							{
/*							if($objProp[$i]['property_name'] == 'fotoRender')*/
							if(($objProp[$i]['property_name'] == 'fotoRender')&&(!$render))
								{
								$render =  $objProp[$i]['prop_value'];
								}
							}
						}
					if(!$render)
						{
						$obj['icon'] = $objFoto[0]['foto_src'];						
						}
					else
						$obj['icon'] = $render;		
					if($objFoto[0]['foto_date'])
						{
						$lastDateArr =  $ROUT->GetRusData(strtotime($objFoto[0]['foto_date']));
						$obj['fotoLastDate'] = $lastDateArr['date'].' '.$lastDateArr['month'];
						$obj['fotoLastDate'] .= (date("Y",time()) != $lastDateArr['year'])?' '.$lastDateArr['year']:'';
						}
					else
						{
						$obj['fotoLastDate'] = 'пока нет фото';
						}
					$obj['name'] =  $objSingle['obj_name'];
					$obj['id'] =  $objSingle['obj_id'];
					$listRedyObj[] = $obj;
					}
				}	
			$objList = 	$getOrg->getConstrListAllMonitoring($cityId, 0);
			$listAllObjCntNum = 	sizeof($objList);	
			$listAllObjCnt = $listAllObjCntNum.' '.$ROUT->getCorrectDeclensionRu($listAllObjCntNum, array('новостройка', 'новостройки', 'новостроек'));			

/**************************************************Всего в базе новостроек  ********************************************************************************/									
			$rnd = array();
			for($k=0; $k<$limit; $k++)
				{
				$stop = 0;
				do
					{
					$ri = mt_rand(0, $listAllObjCntNum-1);
					$stop = (in_array($ri, $rnd)) ? 0 : 1;
					}
				while (!$stop);
				if(isset($objList[$ri]))
					{
					$rnd[] = $ri;
					$obj = array();
					$render	= '';				
					$objId = $objList[$ri]['obj_id'];
					$objSingle = $objList[$ri];
					$objProp = 		$getOrg->getObjectProperties($objId, 0);
					$objFoto = 		$getOrg->getFotoOfSet($objId,  $objSingle['obj_lastCapture']);
					if($objProp)
						{
						for($i = 0; $i< sizeof($objProp); $i++)
							{
							if(($objProp[$i]['property_name'] == 'fotoRender')&&(!$render))
/*							if($objProp[$i]['property_name'] == 'fotoRender')*/
								{
								$render =  $objProp[$i]['prop_value'];
								}
							}
						}
					if(!$render)
						{
						$obj['icon'] = $objFoto[0]['foto_src'];						
						}
					else
						$obj['icon'] = $render;		
					if($objFoto[0]['foto_date'])
						{
						$lastDateArr =  $ROUT->GetRusData(strtotime($objFoto[0]['foto_date']));
						$obj['fotoLastDate'] = $lastDateArr['date'].' '.$lastDateArr['month'];
						$obj['fotoLastDate'] .= (date("Y",time()) != $lastDateArr['year'])?' '.$lastDateArr['year']:'';
						}
					else
						{
						$obj['fotoLastDate'] = 'пока нет фото';
						}
					$obj['name'] =  $objSingle['obj_name'];
					$obj['id'] =  $objSingle['obj_id'];
					$listAllObj[] = $obj;
					}
				}	
				
			$objList = array();
			$objList = $getOrg->getFirmListPopular($cityId, $limit);
			$listFirmCntNum = 	sizeof($getOrg->getFirmListByOrder($cityId, trim('')));
			$listFirmCnt = $listFirmCntNum.' '.$ROUT->getCorrectDeclensionRu($listFirmCntNum, array('застройщик', 'застройщика', 'застройщиков'));			
			
//			print_r($objList);
			for($k=0; $k<$limit; $k++)
				{
				if(isset($objList[$k]))
					{
					$firm = array();
					$firm = $objList[$k];
					$firm['objCnt'] = $getOrg->getConstrListByFirm($objList[$k]['firm_id'], 1);
					$firm['objCnt'] = $firm['objCnt'].' '.$ROUT->getCorrectDeclensionRu($firm['objCnt'], array('объект', 'объекта', 'объектов'));
					$listFirmArr[] = $firm;
					}
				}

//			print_r($listFirmArr);

/**************************************************Квартиры в базе   ********************************************************************************/									

//			$statList = $Apartments->getApListByFilter(array('room' =>1, 'city'=> $filter['city']), 1);		
			$startFilterLink = '/list/apartment/filter_';
			$apList[0] = $Apartments->getApMainPaige(array('room' =>1, 'city'=> $filter['city']));
			$apList[0]['title'] = '1 - комн. квартиры';
			$apList[0]['cntStr'] = $apList[0]['cnt'].' '.$ROUT->getCorrectDeclensionRu($apList[0]['cnt'], array('вариант', 'варианта', 'вариантов'));
			$apList[0]['priceStr'] = 'от '.number_format($apList[0]['price'], 0, '.', ' ').' тыс. рублей';
			$apList[0]['url'] = $startFilterLink.'room~1'; 

			$apList[1] = $Apartments->getApMainPaige(array('room' =>2, 'city'=> $filter['city']));
			$apList[1]['title'] = '2 - комн. квартиры';
			$apList[1]['cntStr'] = $apList[1]['cnt'].' '.$ROUT->getCorrectDeclensionRu($apList[1]['cnt'], array('вариант', 'варианта', 'вариантов'));
			$apList[1]['priceStr'] = 'от '.number_format($apList[1]['price'], 0, '.', ' ').' тыс. рублей';
			$apList[1]['url'] = $startFilterLink.'room~2'; 

			$apList[2] = $Apartments->getApMainPaige(array('room' =>3, 'city'=> $filter['city']));
			$apList[2]['title'] = '3 - комн. квартиры';
			$apList[2]['cntStr'] = $apList[2]['cnt'].' '.$ROUT->getCorrectDeclensionRu($apList[2]['cnt'], array('вариант', 'варианта', 'вариантов'));
			$apList[2]['priceStr'] = 'от '.number_format($apList[2]['price'], 0, '.', ' ').' тыс. рублей';
			$apList[2]['url'] = $startFilterLink.'room~3'; 

			$apList[3] = $Apartments->getApMainPaige(array('district' =>1, 'city'=> $filter['city']));
			$apList[3]['title'] = 'Правобережный район';
			$apList[3]['cntStr'] = $apList[3]['cnt'].' '.$ROUT->getCorrectDeclensionRu($apList[3]['cnt'], array('вариант', 'варианта', 'вариантов'));
			$apList[3]['priceStr'] = 'квартиры от '.number_format($apList[3]['price'], 0, '.', ' ').' тыс. рублей';
			$apList[3]['url'] = $startFilterLink.'district~1'; 

			$apList[4] = $Apartments->getApMainPaige(array('district' =>2, 'city'=> $filter['city']));
			$apList[4]['title'] = 'Октябрьский район';
			$apList[4]['cntStr'] = $apList[4]['cnt'].' '.$ROUT->getCorrectDeclensionRu($apList[4]['cnt'], array('вариант', 'варианта', 'вариантов'));
			$apList[4]['priceStr'] = 'квартиры от '.number_format($apList[4]['price'], 0, '.', ' ').' тыс. рублей';
			$apList[4]['url'] = $startFilterLink.'district~2'; 

			$apList[5] = $Apartments->getApMainPaige(array('district' =>3, 'city'=> $filter['city']));
			$apList[5]['title'] = 'Свердловский район';
			$apList[5]['cntStr'] = $apList[5]['cnt'].' '.$ROUT->getCorrectDeclensionRu($apList[5]['cnt'], array('вариант', 'варианта', 'вариантов'));
			$apList[5]['priceStr'] = 'квартиры от '.number_format($apList[5]['price'], 0, '.', ' ').' тыс. рублей';
			$apList[5]['url'] = $startFilterLink.'district~3'; 
//			print_r($apList);
			$fullCnt = $Apartments->getApListByFilter(array('city'=> $filter['city']), 1);		



			$SMRT['modules'][] =  array('name' => 'apList', 'body' =>array('list' => $apList, 			'cnt' => $fullCnt.' '.$ROUT->getCorrectDeclensionRu($fullCnt, array('вариант', 'варианта', 'вариантов'))));	
			$SMRT['modules'][] =  array('name' => 'listFirm', 'body' =>array('list' => $listFirmArr, 			'cnt' => $listFirmCnt ));	
			$SMRT['modules'][] =  array('name' => 'listAllObj', 'body' => array('list' => $listAllObj, 		'cnt' => $listAllObjCnt));		
			$SMRT['modules'][] =  array('name' => 'listRedyObj', 'body' => array('list' => $listRedyObj, 	'cnt' => $listRedyObjCnt));		
			$SMRT['modules'][] =  array('name' => 'listLastFoto', 'body' => array('list' => $listLastFoto, 	'cnt' => $listLastFotoCnt));		
			$SMRT['modules'][] =  array('name' => 'listNewObj', 'body' =>  array('list' => $listNewObj, 	'cnt' => $listNewObjCnt));				
			}
		elseif(($templates[$templateCnt] == $tplDir.'rightArea.tpl')&&(!$smarty->isCached($tplDir.'rightArea.tpl')))	//rightArea
			{
/*			require_once("../classes/gd/GetOrg_class.php");	
			$getOrg = new GetOrganization;
			$layers = $getOrg->getLayersTree();
			$SMRT['modules'][] =  array('name' => 'layers', 'body' => $layers);		*/
//			print_r($layers);
			}
		elseif(($templates[$templateCnt] == $tplDir.'apartmentStart.tpl')&&(!$smarty->isCached($tplDir.'apartmentStart.tpl')))	//filter
			{
/*			require_once("../classes/fs/apartment_class.php");	
			$Apartments = new Apartments;
			$filter['city'] = $USER->curCity['id'];
			
			$startFilterLink = '/list/apartment/filter_';
			$statList['room_1']['value'] = $Apartments->getApListByFilter(array('room' =>1, 'city'=> $filter['city']), 1);		
			$statList['room_1']['title'] = '<span class="fontSize18">1-комнатные</span> квартиры в Иркутске (<span class="redDark">'.$statList['room_1']['value'].' '.$ROUT->getCorrectDeclensionRu($statList['room_1']['value'], array('вариант', 'варианта', 'вариантов')).'</span>)';		
			$statList['room_1']['link'] = $startFilterLink.'room~1';		
			
			$statList['room_2']['value'] = $Apartments->getApListByFilter(array('room' =>2, 'city'=> $filter['city']), 1);		
			$statList['room_2']['title'] = '<span class="fontSize18">2-комнатные</span> квартиры в Иркутске (<span class="redDark">'.$statList['room_2']['value'].' '.$ROUT->getCorrectDeclensionRu($statList['room_2']['value'], array('вариант', 'варианта', 'вариантов')).'</span>)';		
			$statList['room_2']['link'] = $startFilterLink.'room~2';		
			
			$statList['room_3']['value'] = $Apartments->getApListByFilter(array('room' =>3, 'city'=> $filter['city']), 1);		
			$statList['room_3']['title'] = '<span class="fontSize18">3-комнатные</span> квартиры в Иркутске (<span class="redDark">'.$statList['room_3']['value'].' '.$ROUT->getCorrectDeclensionRu($statList['room_3']['value'], array('вариант', 'варианта', 'вариантов')).'</span>)';		
			$statList['room_3']['link'] = $startFilterLink.'room~3';		
			
			$statList['room_4']['value'] = $Apartments->getApListByFilter(array('room' =>4, 'city'=> $filter['city']), 1);		
			$statList['room_4']['title'] = '<span class="fontSize18">Многокомн.</span> квартиры в Иркутске (<span class="redDark">'.$statList['room_4']['value'].' '.$ROUT->getCorrectDeclensionRu($statList['room_4']['value'], array('вариант', 'варианта', 'вариантов')).'</span>)';		
			$statList['room_4']['link'] = $startFilterLink.'room~4';		
						
			$statList['district_1']['value'] = $Apartments->getApListByFilter(array('district' =>1, 'city'=> $filter['city']), 1);		
			$statList['district_1']['title'] = 'Квартиры в <span class="fontSize18">Правобережном</span> районе Иркутска (<span class="redDark">'.$statList['district_1']['value'].' '.$ROUT->getCorrectDeclensionRu($statList['district_1']['value'], array('вариант', 'варианта', 'вариантов')).'</span>)';		;		
			$statList['district_1']['link'] = $startFilterLink.'district~1';		

			$statList['district_2']['value'] = $Apartments->getApListByFilter(array('district' =>2, 'city'=> $filter['city']), 1);		
			$statList['district_2']['title'] = 'Квартиры в <span class="fontSize18">Октябрьском</span> районе Иркутска (<span class="redDark">'.$statList['district_2']['value'].' '.$ROUT->getCorrectDeclensionRu($statList['district_2']['value'], array('вариант', 'варианта', 'вариантов')).'</span>)';		;		
			$statList['district_2']['link'] = $startFilterLink.'district~2';		

			$statList['district_3']['value'] = $Apartments->getApListByFilter(array('district' =>3, 'city'=> $filter['city']), 1);		
			$statList['district_3']['title'] = 'Квартиры в <span class="fontSize18">Свердловском</span> районе Иркутска (<span class="redDark">'.$statList['district_3']['value'].' '.$ROUT->getCorrectDeclensionRu($statList['district_3']['value'], array('вариант', 'варианта', 'вариантов')).'</span>)';		;		
			$statList['district_3']['link'] = $startFilterLink.'district~3';		

			$statList['district_4']['value'] = $Apartments->getApListByFilter(array('district' =>4, 'city'=> $filter['city']), 1);		
			$statList['district_4']['title'] = 'Квартиры в <span class="fontSize18">Ленинском</span> районе Иркутска (<span class="redDark">'.$statList['district_4']['value'].' '.$ROUT->getCorrectDeclensionRu($statList['district_4']['value'], array('вариант', 'варианта', 'вариантов')).'</span>)';		;		
			$statList['district_4']['link'] = $startFilterLink.'district~4';		

			$statList['state_1']['value'] = $Apartments->getApListByFilter(array('state' =>1, 'city'=> $filter['city']), 1);		
			$statList['state_1']['title'] = '<span class="fontSize18">Строящиеся</span> квартиры в новостройках Иркутска (<span class="redDark">'.$statList['state_1']['value'].' '.$ROUT->getCorrectDeclensionRu($statList['state_1']['value'], array('вариант', 'варианта', 'вариантов')).'</span>)';		;		
			$statList['state_1']['link'] = $startFilterLink.'state~1';		

			$statList['state_2']['value'] = $Apartments->getApListByFilter(array('state' =>2, 'city'=> $filter['city']), 1);		
			$statList['state_2']['title'] = '<span class="fontSize18">Готовые</span> квартиры в новостройках Иркутска (<span class="redDark">'.$statList['state_2']['value'].' '.$ROUT->getCorrectDeclensionRu($statList['state_2']['value'], array('вариант', 'варианта', 'вариантов')).'</span>)';		;		
			$statList['state_2']['link'] = $startFilterLink.'state~2';		

			$statList2[0]['value'] = $Apartments->getApListByFilter(array('room' =>1, 'priceEnd' =>1500, 'city'=> $filter['city']), 1);		
			$statList2[0]['title'] = '<span class="fontSize18">1 - комнатные</span> квартиры в новостройках Иркутска стоимостью до <span class="fontSize18">1.5 млн. рублей</span> (<span class="redDark">'.$statList2[0]['value'].' '.$ROUT->getCorrectDeclensionRu($statList2[0]['value'], array('вариант', 'варианта', 'вариантов')).'</span>)';		
			$statList2[0]['link'] = $startFilterLink.'room~1-priceEnd~1500';		

			$statList2[1]['value'] = $Apartments->getApListByFilter(array('district' =>1, 'areaEnd' =>60, 'city'=> $filter['city']), 1);		
			$statList2[1]['title'] = 'Квартиры в  <span class="fontSize18">Правобережном</span> районе Иркутска площадью до <span class="fontSize18">60 кв. метров</span> (<span class="redDark">'.$statList2[1]['value'].' '.$ROUT->getCorrectDeclensionRu($statList2[1]['value'], array('вариант', 'варианта', 'вариантов')).'</span>)';		
			$statList2[1]['link'] = $startFilterLink.'district~1-areaEnd~60';		

			$statList2[2]['value'] = $Apartments->getApListByFilter(array('room' =>2, 'district' =>3, 'city'=> $filter['city']), 1);		
			$statList2[2]['title'] = '<span class="fontSize18">2 - комнатные</span> квартиры в новостройках в <span class="fontSize18">Свердловском</span> районе Иркутска (<span class="redDark">'.$statList2[2]['value'].' '.$ROUT->getCorrectDeclensionRu($statList2[2]['value'], array('вариант', 'варианта', 'вариантов')).'</span>)';		
			$statList2[2]['link'] = $startFilterLink.'room~2-district~3';		
	
			$fullCnt = $Apartments->getApListByFilter(array('city'=> $filter['city']), 1);		
		

			$SMRT['modules'][] =  array('name' => 'stat', 'body' => $statList);		
			$SMRT['modules'][] =  array('name' => 'statMore', 'body' => $statList2);		
			$SMRT['modules'][] =  array('name' => 'fullCnt', 'body' =>$fullCnt.' '.$ROUT->getCorrectDeclensionRu($fullCnt, array('штука', 'штуки', 'штук')));		
			*/
			}
		elseif(($templates[$templateCnt] == $tplDir.'apartmentList.tpl')||($templates[$templateCnt] == $tplDir.'apartmentStart.tpl'))	//filter
			{
			$filterStr = '';
			if($filterAp = $_SESSION['filterAp'])
				{
				if(trim($_SESSION['filterAp']['state']))
					{				
					$filterStr .=  ($filterStr == '' ) ? '' : '-';			
					$filterStr .=  'state~'.$_SESSION['filterAp']['state'];			
					}
				if($_SESSION['filterAp']['district'])
					{				
					$filterStr .=  ($filterStr == '' ) ? '' : '-';			
					$filterStr .=  'district~'.$_SESSION['filterAp']['district'];			
					}
				if($_SESSION['filterAp']['room'])
					{				
					$filterStr .=  ($filterStr == '' ) ? '' : '-';			
					$filterStr .=  'room~'.$_SESSION['filterAp']['room'];			
					}
				if($_SESSION['filterAp']['finish'])
					{				
					$filterStr .=  ($filterStr == '' ) ? '' : '-';			
					$filterStr .=  'finish~'.$_SESSION['filterAp']['finish'];			
					}
				if($_SESSION['filterAp']['sold'])
					{				
					$filterStr .=  ($filterStr == '' ) ? '' : '-';			
					$filterStr .=  'sold~'.$_SESSION['filterAp']['sold'];			
					}
				if($_SESSION['filterAp']['areaStart'])
					{				
					$filterStr .=  ($filterStr == '' ) ? '' : '-';			
					$filterStr .=  'areaStart~'.$_SESSION['filterAp']['areaStart'];			
					}
				if($_SESSION['filterAp']['areaEnd'])
					{				
					$filterStr .=  ($filterStr == '' ) ? '' : '-';			
					$filterStr .=  'areaEnd~'.$_SESSION['filterAp']['areaEnd'];			
					}
				if($_SESSION['filterAp']['priceStart'])
					{				
					$filterStr .=  ($filterStr == '' ) ? '' : '-';			
					$filterStr .=  'priceStart~'.$_SESSION['filterAp']['priceStart'];			
					}
				if($_SESSION['filterAp']['priceEnd'])
					{				
					$filterStr .=  ($filterStr == '' ) ? '' : '-';			
					$filterStr .=  'priceEnd~'.$_SESSION['filterAp']['priceEnd'];			
					}
					
					
				$filterStr = ($filterStr == '') ? '' : 'filter_'.$filterStr ;
				}
//			echo '<br>:    ='.$filterStr;
			$SMRT['modules'][] =  array('name' => 'filterStringAp', 'body' => $filterStr);
			
			}
		elseif(($templates[$templateCnt] == $tplDir.'header.tpl')||($templates[$templateCnt] == $tplDir.'headerYM.tpl')||($templates[$templateCnt] == $tplDir.'headerPan.tpl'))	//filter
			{
			if(!isset($metaDescription))
				$metaDescription = 'На сайте FotoStroek.ru вы можете увидеть все новостройки Иркутска. Специально для вас мы занимаемся мониторингом всех новостроек и выкладываем самые свежие фото объектов.';				
			$SMRT['modules'][] =  array('name' => 'meta', 'body' => array(	'keywords' => $metaKewords,
																			'description' => $metaDescription));	
			
			}
		$templateCnt++;
		}
?>