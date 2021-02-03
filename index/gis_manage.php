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
//echo $post[$curURLLvl];
//echo '/'.$post[$curURLLvl+1];
$isMng = ($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)?true:false;	

//print_r($allMenu);
//echo $fuckedSymbolPosition.' - '.$act;	
$error = 0;
$menuActive = '';
$feedFile = '../htdocs/realty.xml';
//$feedFile = '../realty.xml';

switch ($act)
	{	
	case 'set': 
		{
//		echo 'fff - '.$post[$curURLLvl+1];
		$error=0;
		$HISTORY_SKIP = 1;		
		if(($post[$curURLLvl+1] == 'layerAdd')&&($isMng))
			{
			require_once("../classes/gis/manage_class.php");	
			$MNG = new Manage;			
			$templates = array();			
			$newLayer = $MNG->newLayer($_POST);
			if($newLayer['error'])
				{
				echo $newLayer['errorMsg'];
				}	
			else
				{
//				$newLayer['last_id']
				//print_r($newLayer);
				}
			}
		elseif(($post[$curURLLvl+1] == 'objAdd')&&($isMng))
			{
			require_once("../classes/gis/manage_class.php");	
			$MNG = new Manage;			
			$templates = array();
			if($name = trim($_POST['oName']))
				$param['oName'] = htmlspecialchars($name, ENT_QUOTES);  
			else
				$param['oName'] = '';
/*возможно некорректное изменение если тип фигуры == 0  !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/			
			$param['oType'] = ($_POST['oType'])?intval($_POST['oType']):1; 
			$param['oSpecParam'] = (trim($_POST['oSpecParam']))?trim($_POST['oSpecParam']):''; 			  
			$param['oAbout'] = (get_magic_quotes_gpc())?trim($_POST['oAbout']):addslashes(trim($_POST['oAbout'])); 			  
			$param['oTemplate'] = ($_POST['oTemplate'])?intval($_POST['oTemplate']):1;  
			$param['lId'] = ($_POST['lId'])?intval($_POST['lId']):1;  
//			print_r($param);
			$newObj = $MNG->newObject($param);
			if($newObj['error'])
				{
				echo '_<error!/>'.$newObj['errorMsg'];
				}	
			else
				{
				$pointsRes = $MNG->newObjectPoints($_POST['points'], $newObj['last_id']);
				if($pointsRes['error'])
					{
					echo '_<error!/>'.$pointsRes['errorMsg'];
					}
				else
					{
					$ret = $newObj['last_id'];
					if(!$name)
						{
						$paramUp = array();
						$p['name'] = 	'oName';
						$p['val'] = 	'Объект №'.$newObj['last_id'];
						$paramUp[] = $p;
						$pointsUp = $MNG->updateObject($paramUp, $newObj['last_id']);
						$ret .= '<delim!/>'.$p['val'];
						}							
					echo $ret;
					}
				}
			}
		elseif((($post[$curURLLvl+1] == 'fileUpload')||($needExplode = strpos($post[$curURLLvl+1], 'fileUpload?') !== false ))&&($isMng))
			{
			$templates = array();			
			$uploadTmpPath = 'src/upload/tmp/';
			require_once("../classes/fileUploader.php");			
			$allowedExtensions = array();
			// max file size in bytes
			$sizeLimit = 10 * 1024 * 1024;
			$uploader = new qqFileUploader($allowedExtensions, $sizeLimit, array(225, 0));
			$result = $uploader->handleUpload($CONST['relPathPref'].'/'.$uploadTmpPath);
			$templates[0] = '';	
			$typeArr = array('point' => 0, 'linestring' => 1, 'polygon' => 2, 'multipoint' => 3, 'multilinestring' => 4, 'multipolygon' => 5 );
			if($result['success'])
				{
				require_once("../classes/gis/manage_class.php");	
				$MNG = new Manage;			
				$file = $result['uploadDirectory'].$result['filename'].'.'.$result['ext'];
				$fileCont =  file_get_contents($file);
				$geoObj = json_decode($fileCont);
				$layer = $MNG->getSingleLayer($_GET['layerId']);
//				var_dump($geoObj);
				if(strtolower($geoObj->type) == 'featurecollection')
					{
					$geoFeatures = array();
					foreach ($geoObj->features as $feature) {
						$param = array();
						if($feature->type == 'Feature'){
//						foreach ($features->Feature as $feature) 
//							{
							$coordArr = array();
							$param['oName'] = $feature->properties->NAME;
							$param['type'] = $feature->geometry->type;
							$param['oAbout'] = (isset($feature->properties->AREA)) ? 'Площадь равна '.number_format($feature->properties->AREA, 1, '.', ' ').' кв. км.':'';
//							$param['oAREA'] = $feature->properties->AREA;
							$param['oType'] = $typeArr[strtolower($feature->geometry->type)];
							$param['lStr'] =  '_'.$_GET['layerId'];	
							$param['lParentStr'] =  $layer['l_parStr'];	
							$param['layerId'] = $_GET['layerId'];
							
							$newObj = $MNG->newObject($param);
							if($newObj['error'])
								{
								echo '_<error!/>'.$newObj['errorMsg'];
								}	
							else{
								$param['oId'] = $newObj['last_id'];									
								$counter = $featureCnt = $holeCnt = 0;
								switch($param['oType']){
									case 0:	{ //single point	
										$coordArr[0]['lat'] = $feature->geometry->coordinates[1];
										$coordArr[0]['lng'] = $feature->geometry->coordinates[0];
									} break;								
									case 1:	{//single line 
										foreach ($feature->geometry->coordinates as $objCoordArr){														
											$coordArr[$counter]['lat'] = $objCoordArr[1];
											$coordArr[$counter]['lng'] = $objCoordArr[0];
											$counter ++;
										}
									} break;
									case 2:	{//single polygon 
										foreach ($feature->geometry->coordinates as $objCoordArr1){
											$counter = 0;
											foreach ($objCoordArr1 as $objCoordArr){														
												$coordArr[$counter]['lat'] = $objCoordArr[1];
												$coordArr[$counter]['lng'] = $objCoordArr[0];
												$coordArr[$counter]['hId'] = $holeCnt;
												$counter ++;
											}
											$holeCnt ++;											
										}
									} break;
									case 3:	{//multipoint
										foreach ($feature->geometry->coordinates as $objCoordArr){														
											$objCoord  = $objCoordArr;
											$coordArr[$counter]['lat'] = $objCoordArr[1];
											$coordArr[$counter]['lng'] = $objCoordArr[0];
											$coordArr[$counter]['fId'] = $featureCnt;
											$featureCnt ++;											
											$counter ++;
										}
									} break;
									case 4:	{//multilinestring 
										foreach ($feature->geometry->coordinates as $objCoordArr1){														
											foreach ($objCoordArr1 as $objCoordArr){														
												$objCoord  = $objCoordArr;
												$coordArr[$counter]['lat'] = $objCoordArr[1];
												$coordArr[$counter]['lng'] = $objCoordArr[0];
												$coordArr[$counter]['fId'] = $featureCnt;
												$counter ++;
											}
											$featureCnt ++;											
										}
									} break;
									case 5:	{//multipolygon 
										foreach ($feature->geometry->coordinates as $objCoordArr1){
											$holeCnt = 0;											
											foreach ($objCoordArr1 as $objCoordArr2){														
												foreach ($objCoordArr2 as $objCoordArr){														
													$objCoord  = $objCoordArr;
													$coordArr[$counter]['lat'] = $objCoordArr[1];
													$coordArr[$counter]['lng'] = $objCoordArr[0];
													$coordArr[$counter]['hId'] = $holeCnt;
													$coordArr[$counter]['fId'] = $featureCnt;
													$counter ++;
												}
											$holeCnt ++;											
											}
										$featureCnt ++;											
										}
									} break;
									
								}
								if(isset($coordArr[0])){
//									$iteratonNum = ceil(count($coordArr)/500);
									$insArray = array_chunk($coordArr, 500);
									foreach ($insArray as $insItem){																								
										$pointsRes = $MNG->newObjectPoints($insItem, $param['oId']);
									}
									if($pointsRes['error'])
										{
										echo '_<error!/>'.$pointsRes['errorMsg'];
										}										
								}
								else
									echo '_<error!/>There are no points in file!';
							}
							
//						print_r($param);
//						print_r($coordArr);
//						echo $param['geo']
//						var_dump($param);
							$geoFeatures[] = $param;
							}
						}
					$result['GeoCollection'] = $geoFeatures;
					}
				else
					echo '_<error!/>Файл, возможно, не соответствует спецификации GEOJSON';
					
//				$_SESSION['newsImg']['img'][] = $uploadTmpPath.$uploader->filenameOut['basename'];
//				$_SESSION['newsImg']['img'][] = $uploadTmpPath.$result['filename'].'.'.$result['ext'];
				}
//			echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);			
			echo json_encode($result);			
			}		
		else
			{
			$error ++;
			}
		if($error)
			{
			$errMess = 'Не удалось изменить запись! ';
			$errMess .= ($CONST['debugMode'])?': System Message - '.$res['errorMsg']:'';
			$MESS = new Message('Error', 'Изменение записи', $errMess, $NAV->GetPrewURI());														
			}
		
			
		} break;
	case 'main': 
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
	$templates = array();	
/*	$_SESSION['MESSAGE'] = $MESS;
	header('Location: /');	*/
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