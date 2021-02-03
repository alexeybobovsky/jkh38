<?
/*print_r($_POST);
echo 'ddddd';
/*if($USER->id>1)
	Error_Reporting(0);	
	*/
$pathBeg = (($_SERVER['SERVER_NAME'] == '84.201.157.24')||($_SERVER['SERVER_NAME'] == 'jkh38.ru')||($_SERVER['SERVER_NAME'] == 'www.jkh38.ru')) ? '/opt/web' : '/var';
if(trim($_POST['type'])) //подгрузка HTML кода посредством АЯКС
//if((trim($_POST['type']))||(trim($_GET['type']))) //подгрузка HTML кода посредством АЯКС
	{
//					$charset = ($CONST['dinamicUTF8Convert'])?'windows-1251':'UTF-8';
//				header('Content-type: text/html; charset='.$charset);

	$type = trim($_POST['type']);
/*	$type = trim($_GET['type']);*/
	switch ($type)
		{
		case 'deleteFromReport': /*2019_03_07 */
			{
			chdir('..//');	
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/zhkh.php");	
			$CNT = 		new GetContent;
//			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$ZhKH = new ZhKH;	
//			echo $_POST['itemType'].' - '.$_POST['itemIndex'];
			if(($_POST['itemIndex'])&&($_POST['itemType'])){
				$ret = $ZhKH->deleteFromReport($_POST['itemType'], $_POST['itemIndex']); 
				if($ret['error'])
					{
					$strOut =  ' Возникла непредвиденная ошибка в процессе удаления позиции. Попробуйте снова или обратитесь к администрации ('.$ret['errorMsg'].')';
					}
				else{
					$strOut =  1;
				}
				
			} else {
				$strOut =  ' Возникла непредвиденная ошибка в процессе удаления позиции. Попробуйте снова или обратитесь к администрации ( "Неизвестный тип данных!" )';
			}
			echo $strOut;
		} break;
		case 'getDevices': /*2019_04_01 */
			{				
			$vihDevId = 1; //контроллер в Вихоревке
			chdir('..//');	
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/eldis.php");	
			require_once("../classes/gis/simplight.php");	
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$ELD = new Eldis;
			if(isset($_POST['cmd']))
				$cmd = trim($_POST['cmd']);
			else 
				$cmd = 'firstLoad';
			$start = ($_POST['start']) ? trim($_POST['start']) : 0;
			$full = ($_POST['full']) ? trim($_POST['full']) : 0;
			$ret = 0;
//			echo $cmd;
			switch($cmd)
				{
				case 'objectsMetriks': {
					$dvcList = trim($_POST['option']);
					if(strlen($optionStr = trim($_POST['option']))>10){
						$option =  json_decode($optionStr);
//						print_r($option);
						foreach ($option->list as $dvc) {
							if($dvc->collectorType == 'native'){
								$SL = new Simplight($dvc->deviceId);
								if($dvc->full){
									$dvc->metriks = $SL->getValuesFromDBList(); 
//									echo 'full is '.$dvc->full;
									
								} elseif($dvc->startDate){
									$dvc->metriks = $SL->getValuesFromDBList($dvc->startDate);
//									echo 'startDate is '.$dvc->startDate;
									
								} elseif($dvc->startCounter){
//									echo 'startCounter is '.$dvc->startCounter;
									$dvc->metriks = $SL->getValuesFromDBList(0, $dvc->startCounter); 
									
								} else {
									$dvc->metriks = $SL->getValuesFromDBLast(); 
								}
							} elseif ($dvc->collectorType == 'eldis'){
								if($dvc->full){
									$dvc->metriks = $ELD->getMetricList($dvc->deviceId); 																		
//									echo 'full is '.$dvc->full;
								} elseif($dvc->startDate){
//									echo 'startDate is '.$dvc->startDate;
									$dvc->metriks = $ELD->getMetricList($dvc->deviceId, $dvc->startDate); 									
								} elseif($dvc->startCounter){
//									echo 'startCounter is '.$dvc->startCounter;
									$dvc->metriks = $ELD->getMetricList($dvc->deviceId, 0, $dvc->startCounter); 									
								} else {
//									echo 'noth';
									$dvc->metriks = $ELD->getMetricLast($dvc->deviceId);	
								}									
							}
//							print_r($dvc);
						}
//					print_r($option);
					//$ret = 1;
					$ret = json_encode($option);
					} else {
						$ret = 0;
					}						
				} break;				
				case 'objectList': {
					$SL = new Simplight(1);
					$cnt = 0;
					$retArr = array();
					$retEld =   	$ELD->getDevicesList($start);
					foreach ($retEld as $val) {
						$val['dateUp_ru'] = $ROUT->GetRusDataStr($val['dateUp'], false, true, true);
						$val['collectorType'] = 'eldis';
						$retArr[] = $val;
						$cnt ++;					
					}
					$retSimple = 	$SL->getDevicesList();
					foreach ($retSimple as $val) {
						$val['dateUp_ru'] = $ROUT->GetRusDataStr($val['dateUp'], false, true, true);
						$val['collectorType'] = 'native';
						$retArr[] = $val;
						$cnt ++;
					}
//					$ret =  json_encode(array_merge($retEld, $retSimple));
					$ret =  json_encode($retArr);
				} break;				
				case 'firstLoad': {
					$SL = new Simplight(1);
					$retEld =   $ELD->getDevicesAnalitic($start);
					$retSimple = $SL->getAllDevices();
					$ret =  json_encode(array_merge($retEld, $retSimple));
				} break;
				case 'metriksLabel': {
					$ret =  json_encode($ELD->getMetriksLabels($start));
				} break;
				default: {
					$ret = '{"geoObj": "default"}'; 
				} break;				
			}
			//print_r($ret);	
			$strout = print_r($ret,true);
			echo '{"SomeJsonObject": {"Items": '.$strout.'}}';
			}break;		
		case 'geoObj': /*2019_06_19 комманды для работы с геообъектами */
			{
			$result	= '{"success": false}';			
//			if($cmd = trim($_POST['cmd'])){	
			if(isset($_POST['cmd'])){
				$cmd = trim($_POST['cmd']);
				chdir('..//');	
				require_once('../classes/config.inc.php');
				require_once("../classes/MySQLi_class.php");
				require_once("../classes/GetContent_class.php");
//				require_once("../classes/User_class.php");
				require_once("../classes/ACL_class.php");
				require_once("../classes/Rout_class.php");
				require_once("../classes/gis/manage_class.php");	
				$CNT = 		new GetContent;
				$ROUT = new Routine;
				$CONST = $CNT->GetAllConfig();
//				$usr = new CurrentUser ($_SERVER[REMOTE_ADDR], 0);
				$MNG = new Manage;	
				$ret = 0;
				switch($cmd)
					{
					case 'getObjectsOfNpSector': /*получить объекты для нас. пункта*/
						{
//						print_r($_POST);
						if((isset($_POST['npId']))&&(isset($_POST['jkhSector']))){
							$npId = (trim($_POST['npId']))?trim($_POST['npId']):0; 
							$jkhSector = (trim($_POST['jkhSector']))?trim($_POST['jkhSector']):0; 
							$geoObjList = $MNG->getObjOfNpSector($npId, $jkhSector);	
							//print_r($geoObjList);
							if($geoObjList!=0){
	//							print_r(json_encode($geoObjList), true);
								$result = json_encode($geoObjList);			
							} else {
								$result =  0;
							}
						}
					} break;					
					case 'getObjects': /*получить файлы для толстого клиента*/
						{				
						$geoObjList = $MNG->getAllObjJSON(0);	
						//print_r($geoObjList);
						if($geoObjList!=0){
//							print_r(json_encode($geoObjList), true);
							$result = json_encode($geoObjList);			
						} else {
							echo '0';
						}
					} break;					
					case 'getFiles': /*получить файлы для толстого клиента*/
						{				
						$upDir = $pathBeg.'/zhkh/htdocs';
						$zipFileName = $pathBeg.'/zhkh/tmp/geoFiles.zip';						
						$start = isset($_POST['start']) ? trim($_POST['start']):0;
//						error_log(print_r($_POST, true));
						$geoObjList = $MNG->getAllObjJSON($start);	
						//print_r($geoObjList);
						if($geoObjList!=0){
							$zip = new ZipArchive();
							$rslt = $zip->open($zipFileName, ZipArchive::CREATE);
//							var_dump($zip);
							$objArr = array();
							if ($rslt === TRUE) {
								foreach ($geoObjList as $obj) {
									$objArr[] = 
									$fnArr = $ROUT->getFileName($obj['geoFile']);
									$shortFName = $fnArr['name'].'.'.$fnArr['ext'];
									if(!$obj['isDeleted']){
										$zip->addFile($upDir.$obj['geoFile'], $shortFName);
									} else
										$zip->addFromString($shortFName, '');									
								}
							$zip->addFromString('index.json', print_r(json_encode(/*(object) */array('index' => $geoObjList) , JSON_UNESCAPED_UNICODE), true));
							$zip->close();
							header('Content-Type: application/zip');
							header('Content-Length: ' . filesize($zipFileName));
							header('Content-Disposition: attachment; filename="geoFiles.zip"');
							readfile($zipFileName);
							unlink($zipFileName); 
							}
						} else {
							echo '0';
						}
					} break;					
					case 'add': /*загрузить*/
						{
/*						print_r($_POST);
						print_r($_FILES);*/
						$upDir = $pathBeg.'/zhkh/htdocs/download/geoObj/';
						$urlDir = '/download/geoObj/';

						if((isset($_POST['obj']))){
							$name = isset($_POST['name'])?trim($_POST['name']):0;
							$npId = isset($_POST['npId'])?trim($_POST['npId']):0;
							$dist = isset($_POST['dist'])?trim($_POST['dist']):0;
							$jkhSectorId = isset($_POST['jkhSectorId'])?trim($_POST['jkhSectorId']):0;
//							$res = $MNG->addObjJSON($dist, json_encode($_POST['obj']), $name, $npId, $jkhSectorId);	
//							$geoObj = json_decode($_POST['obj'], true);
							if(is_string($_POST['obj'])){
								$geoObj =  $_POST['obj']; 
							} else {
								$geoObj = json_encode($_POST['obj']);
							}
//							var_dump($geoObj);
//							echo json_last_error();
							if(($_FILES['geoObjFile']['size'])){
								$fileNameUp = $upDir.$_FILES['geoObjFile']['name'];
								$fileName = $urlDir.$_FILES['geoObjFile']['name'];
								$res = $MNG->addObjJSON($dist, $geoObj, $fileName, $name, $npId, $jkhSectorId);	
								if(!$res['error']){
									if (move_uploaded_file($_FILES['geoObjFile']['tmp_name'], $fileNameUp)) {
										$res['geoFile'] = array('uploaded' => 'success', 'newPath' => $fileName);
									} else {
										$res['geoFile'] = array('uploaded' => 'error');
									}
								}
							} else {
								$res['error'] = true;
								$res['errorMsg'] = 'error with geoFile';
							}
							$result = genJSONResponse('geoObj', $cmd,  $res, 'set');					
//							$result = '{"geoObj": : {"cmd": '.$cmd.', "success": '.($res['error'])?'false':'true'.', "result": '.($res['error'])?'0':$res['last_id'].'}}'; 							
						}
					} break;
					case 'up': /*обновление*/
						{
						if((isset($_POST['obj']))&&(isset($_POST['id']))){
							$id = trim($_POST['id']);
							$name = isset($_POST['name'])?trim($_POST['name']):0;
							$npId = isset($_POST['npId'])?trim($_POST['npId']):0;
							$dist = isset($_POST['dist'])?trim($_POST['dist']):0;
							$jkhSectorId = isset($_POST['jkhSectorId'])?trim($_POST['jkhSectorId']):0;
							if(is_string($_POST['obj'])){
								$geoObj =  $_POST['obj']; 
							} else {
								$geoObj = json_encode($_POST['obj']);
							}
//							var_dump($geoObj);
//							echo json_last_error();
							$res = $MNG->upObjJSON($id, $dist, $geoObj, $name, $npId, $jkhSectorId);	
							$result = genJSONResponse('geoObj', $cmd,  $res, 'up');					
//							$result = '{"geoObj": : {"cmd": '.$cmd.', "success": '.($res['error'])?'false':'true'.', "result": '.($res['error'])?'0':$res['last_id'].'}}'; 							
						}
					} break;
					case 'del': /*удаление*/
						{
						if((isset($_POST['id']))){
							$id = trim($_POST['id']);
							$res = $MNG->delObjJSON($id);	
							$result = genJSONResponse('geoObj', $cmd,  $res, 'del');					
//							$result = '{"geoObj": : {"cmd": '.$cmd.', "success": '.($res['error'])?'false':'true'.', "result": '.($res['error'])?'0':$res['last_id'].'}}'; 							
						}
					} break;
					case 'getSingle': /*загрузить*/
						{
						if(isset($_POST['objId'])){
/*							$objStr = print_r($_POST['obj'],true);
							$objStr = json_encode($_POST['obj']);*/
							$res = $MNG->getObjJSON($_POST['objId']);
							$result = genJSONResponse('geoObj', $cmd,  $res, 'get');					
							
						}
					} break;
					case 'getObjJSONOfSector': /*загрузить*/
						{
						if(isset($_POST['objId'])){
							$npId = isset($_POST['npId'])?trim($_POST['npId']):0;
							$jkhSectorId = isset($_POST['jkhSectorId'])?trim($_POST['jkhSectorId']):0;
							$res = $MNG->getObjJSONOfSector($npId, $jkhSectorId);
							$result = genJSONResponse('geoObj', $cmd,  $res, 'get');					
							
						}
					} break;
					default: /*инфо о данных чата, проверка на статус*/
						{			
						$result = '{"geoObj": "default"}'; 
						} break;
					} 
			}
//			error_log('[INFO] geoObj: result is '.$result, 0); 			
//			$strout = print_r($result,true);
//			echo '{"SomeJsonObject": {"Items": '.$strout.'}}';
//			echo '{"SomeJsonObject": '.$strout.'}';
//			if (($cmd != 'getFiles')&&($cmd != 'getObjectsOfNpSector'))
			if (($cmd != 'getFiles')){
				if($result!= 0)
					echo print_r($result,true);
				else 
					echo $result;
			}
		} break;
		case 'bot': /*2019_05_21 комманды для работы с ботами */
			{
			$result	= '{"success": false}';			
//			if($cmd = trim($_POST['cmd'])){	
			if(isset($_POST['cmd'])){
				$cmd = trim($_POST['cmd']);
				chdir('..//');	
				require_once('../classes/config.inc.php');
				require_once("../classes/MySQLi_class.php");
				require_once("../classes/GetContent_class.php");
//				require_once("../classes/User_class.php");
				require_once("../classes/ACL_class.php");
				require_once("../classes/Rout_class.php");
				require_once("../classes/gis/manage_class.php");	
				require_once("../classes/messRoutine.php");	
				$CNT = 		new GetContent;
				$ROUT = new Routine;
				$CONST = $CNT->GetAllConfig();
//				$usr = new CurrentUser ($_SERVER[REMOTE_ADDR], 0);
				$MNG = new Manage;	
				$MR = new messengerRoutine;	
				$ret = 0;
				switch($cmd)
					{
					case 'start': /*старт работы - запись данных чата, запрос креденшиналов */
						{				
						$result = '{"chat": "start"}'; 
					} break;
					case 'getUsr': /*инфо о данных чата, проверка на статус*/
						{
						if((isset($_POST['id']))&&(isset($_POST['platform']))){							
							$cli = $MR->getClient(trim($_POST['platform']), trim($_POST['id']));	
							if(!$cli)							
								$cli = $MR->addClient(trim($_POST['platform']), trim($_POST['id']));
							else{
								$usr2distr = $MNG->getUsrToDistr($cli['userId']);
								if($usr2distr['distr_id']==1){
									$cli['distr']	= 'Иркутская область';							
								}
								elseif($usr2distr['distr_id']>1000){
									$cli['distr']	= $MNG->getSingleLayer($usr2distr['distr_id']);	
								}
							}
							$result = $ret = json_encode($cli); 
						}
					} break;
					case 'linkUsr': /**/
						{
						if((isset($_POST['cliUserId']))&&(isset($_POST['sysUserName']))&&(isset($_POST['platform']))){							
							$cli = $MR->linkUsr(trim($_POST['cliUserId']), trim($_POST['sysUserName']), trim($_POST['platform']));	
/*							if(!$cli)							
								$cli = $MR->addClient(trim($_POST['platform']), trim($_POST['id']));	*/
							$result = $ret = json_encode($cli); 
						}
					} break;					
					case 'exit': /**/
						{
						if((isset($_POST['cliUserId']))&&(isset($_POST['platform']))){							
							$cli = $MR->unlinkUsr(trim($_POST['cliUserId']), trim($_POST['platform']));	
/*							if(!$cli)							
								$cli = $MR->addClient(trim($_POST['platform']), trim($_POST['id']));	*/
							$result = $ret = json_encode($cli); 
						}
					} break;
					default: /*инфо о данных чата, проверка на статус*/
						{			
						$result = '{"default": "default"}'; 
						} break;
					} 
			}
			error_log('[INFO] APP: result is '.$result, 0); 			
			$strout = print_r($ret,true);
//			echo '{"SomeJsonObject": {"Items": '.$strout.'}}';
//			echo '{"SomeJsonObject": '.$strout.'}';
			echo $strout;
		} break;
		case 'checkUsr': /*2019_04_01 */
			{				
			chdir('..//');	
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			require_once("../classes/gis/manage_class.php");	
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$usr = new CurrentUser ($_SERVER[REMOTE_ADDR], 0);
			$MNG = new Manage;			
			$ret = 0;
			$roleStr = '';
			if(($user=$usr->CheckUser($_POST['uName'], $_POST['uPass']))&&($user['user_name'] == $_POST['uName'])){
				$usr2distr = $MNG->getUsrToDistr($user['user_id']);
				$roleStr = ', "role": "'.$usr2distr['role'].'"';
				$ret = 1;
			}
				
			$retRes = ($ret)?'true':'false';
			echo '{"SomeJsonObject": {"success": "'.$retRes.'"'.$roleStr.'}}';			
			} break;
		case 'getUp': /*2019_04_01 */
			{
			$ret = 0;
			if(($_POST['cliName'])){		
				chdir('..//');	
				Error_Reporting(E_ALL & ~E_NOTICE);			
				$fileName = strpos(trim($_POST['cliName']), '.')? '../htdocs/download/client/'.$_POST['cliName'] : '../htdocs/download/client/'.$_POST['cliName'].'.exe';
				  if (file_exists($fileName)) {
					if (ob_get_level()) {
					  ob_end_clean();
					}
					// заставляем браузер показать окно сохранения файла
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename=' . basename($fileName));
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					header('Content-Length: ' . filesize($fileName));
					// читаем файл и отправляем его пользователю
					if ($fd = fopen($fileName, 'rb')) {
					  while (!feof($fd)) {
						print fread($fd, 1024);
					  }
					  fclose($fd);
					}
					exit;
				  }
				}				

			} break;
		case 'checkUp': /*2019_04_01 */
			{
			$ret = 0;
			$verAdd = '';
			if(($_POST['cliName'])&&($_POST['cliVer'])){		
				chdir('..//');	
				Error_Reporting(E_ALL & ~E_NOTICE);			
				$fileName = '../htdocs/download/client/'.$_POST['cliName'].'.version';
				$version = file_get_contents($fileName);
				if($version !== false){
				//$version = fread($fpr, filesize($fileName));
				//echo 'file is '.$fileName.'; new ver is  ' . $version . '; old is '.$_POST['cliVer'];
					$verAdd = ', "versionClient": "'.$_POST['cliVer'].'", "versionServer": "'.$version.'"';
					if($version!=$_POST['cliVer'])
						$ret = 1;
				}
			}
			$retRes = ($ret)?'true':'false';
			echo '{"SomeJsonObject": {"update": "'.$retRes.'"'.$verAdd.'}}';
			
//			echo $ret;
			} break;
		case 'getRepAll': /*2019_04_01 */
			{				
			chdir('..//');	
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/zhkh.php");	
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$ZhKH = new ZhKH;	

			$distr = ($_POST['distr']) ? trim($_POST['distr']) : 0;
			$start = ($_POST['start']) ? trim($_POST['start']) : 0;
			switch($_POST['formName']){
				case 'start':{
//--------------------------------------------------------------------------------------------get zhkh1
					$retTmp = $ZhKH->getRep_1zhkh($start, $distr, false);
					//print_r($retTmp);
					$startTime = 0;
					$cnt = -1;
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
							$item['archieved'] = ($item['sendDate']>time())?false:true;
	//						$item['sendDate_ru_arr'] = print_r($ROUT->GetRusData($item['sendDate']), true);
							$retArr[$cnt][] = $item;												
						}
					}
					if(count($retArr)>0)
						$retStart['zhkh1']  =  $retArr;
					else
						$retStart['zhkh1'] = 0;
					
//--------------------------------------------------------------------------------------------get zhkh2					
					$retTmp =  $ZhKH->getRep_zhkh2($start, $distr);
					$startTime = 0;
					$cnt = -1;
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
							$item['archieved'] = ($item['sendDate']>time())?false:true;
							$retArr[$cnt][] = $item;	
									
						}
					}
					if(count($retArr)>0)
						$retStart['zhkh2']  =  $retArr;
					else
						$retStart['zhkh2'] = 0;
					
//--------------------------------------------------------------------------------------------get zhkh24	
				
					$retTmp =  $ZhKH->getRep_zhkh24($start, $distr);
					$startTime = 0;
					$cnt = -1;
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
							$item['archieved'] = ($item['sendDate']>time())?false:true;
							$retArr[$cnt][] = $item;	
									
						}
					}
					if(count($retArr)>0)
						$retStart['zhkh24']  =  $retArr;
					else
						$retStart['zhkh24'] = 0;
					
					
//--------------------------------------------------------------------------------------------get zhkh3	
				
					$retTmp =  $ZhKH->getRep_zhkh3($start, $distr);
					
					$startTime = 0;
					$cnt = -1;
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
							$nextDateTmp= mktime(0, 0, 0, date("m")  , 1, date("Y"));
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
	//						$item['archieved'] = ($item['sendDate']>time())?false:true;
							$item['archieved'] = ($item['sendDate']>$nextDateTmp)?false:true;
							$retArr[$cnt][] = $item;									
						}
					}
					if(count($retArr)>0)
						$retStart['zhkh3']  =  $retArr;
					else
						$retStart['zhkh3'] = 0;
					
//--------------------------------------------------------------------------------------------get SFO	
				
					$retTmp =  $ZhKH->getRep_sfo($start, $distr);					
					$startTime = 0;
					$cnt = -1;
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
	//						$nextDateTmp= mktime(0, 0, 0, date("m"), date("d")+1, date("Y"));
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
	//						$item['archieved'] = ($item['sendDate']>=$nextDateTmp)?false:true;
							$item['archieved'] = ($item['sendDate']>time())?false:true;
	//						$item['nextDateTmp'] = $nextDateTmp;
							$retArr[$cnt][] = $item;									
						}
					}
					if(count($retArr)>0)
						$retStart['SFO']  =  $retArr;
					else
						$retStart['SFO'] = 0;
//--------------------------------------------------------------------------------------------get toplPril1	
				
					$retTmp =  $ZhKH->getRep_toplPril1($start, $distr);					
					$startTime = 0;
					$cnt = -1;
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
							$nextDateTmp= mktime(0, 0, 0, date("m"), date("d")+1, date("Y"));
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
							$item['archieved'] = ($item['sendDate']>=$nextDateTmp)?false:true;
	//						$item['nextDateTmp'] = $nextDateTmp;
							$retArr[$cnt][] = $item;									
						}
					}
					if(count($retArr)>0)
						$retStart['toplPril1']  =  $retArr;
					else
						$retStart['toplPril1'] = 0;
//--------------------------------------------------------------------------------------------get toplPril2					
					$retTmp =  $ZhKH->getRep_toplPril2($start, $distr);					
					$startTime = 0;
					$cnt = -1;
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
							$nextDateTmp = (time() >= mktime(0, 0, 0, date("m"),  5, date("Y")))?mktime(0, 0, 0, date("m")+1,  5, date("Y")): mktime(0, 0, 0, date("m"),  5, date("Y"));
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
							$item['archieved'] = ($item['sendDate']>=$nextDateTmp)?false:true;
	//						$item['nextDateTmp'] = $nextDateTmp;
							$retArr[$cnt][] = $item;									
						}
					}
					if(count($retArr)>0)
						$retStart['toplPril2']  =  $retArr;
					else
						$retStart['toplPril2'] = 0;
//--------------------------------------------------------------------------------------------get PMO 1					
					$retTmp =  $ZhKH->getRep_pmo1($start, $distr);					
					$startTime = 0;
					$cnt = -1;
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
							$lastDate= mktime(0, 0, 0, 8  , 1, date("Y"));				
							$nextDate= mktime(0, 0, 0, 8  , 1, date("Y")+1);
							$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
							
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
							$item['upDate_ru'] = $ROUT->GetRusDataStr($item['date'], false, true);
							$item['archieved'] = ($item['sendDate']>=$nextDateTmp)?false:true;
	//						$item['nextDateTmp'] = $nextDateTmp;
							$retArr[$cnt][] = $item;									
						}
					}
					if(count($retArr)>0)
						$retStart['PMO1']  =  $retArr;
					else
						$retStart['PMO1'] = 0;
//--------------------------------------------------------------------------------------------get PMO 2				
					$retTmp =  $ZhKH->getRep_pmo2($start, $distr);					
					$startTime = 0;
					$cnt = -1;
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							/*if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}*/
							$lastDate= mktime(0, 0, 0, 8  , 1, date("Y"));				
							$nextDate= mktime(0, 0, 0, 8  , 1, date("Y")+1);
							$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
							
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
							$item['upDate_ru'] = $ROUT->GetRusDataStr($item['date'], false, true);
							$item['archieved'] = ($item['sendDate']>=$nextDateTmp)?false:true;
	//						$item['nextDateTmp'] = $nextDateTmp;
							$retArr[] = $item;									
						}
					}
					if(count($retArr)>0)
						$retStart['PMO2']  =  $retArr;
					else
						$retStart['PMO2'] = 0; 
//--------------------------------------------------------------------------------------------get PMO 3					
					$retTmp =  $ZhKH->getRep_pmo3($start, $distr);					
					$startTime = 0;
					$cnt = -1;
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
							$lastDate= mktime(0, 0, 0, 8  , 1, date("Y"));				
							$nextDate= mktime(0, 0, 0, 8  , 1, date("Y")+1);
							$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
							
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
							$item['upDate_ru'] = $ROUT->GetRusDataStr($item['date'], false, true);
							$item['archieved'] = ($item['sendDate']>=$nextDateTmp)?false:true;
	//						$item['nextDateTmp'] = $nextDateTmp;
							$retArr[$cnt][] = $item;									
						}
					}
					if(count($retArr)>0)
						$retStart['PMO3']  =  $retArr;
					else
						$retStart['PMO3'] = 0;
//--------------------------------------------------------------------------------------------get PMO 4					
					$retTmp =  $ZhKH->getRep_pmo4($start, $distr);					
					$startTime = 0;
					$cnt = -1;
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
							$lastDate= mktime(0, 0, 0, 8  , 1, date("Y"));				
							$nextDate= mktime(0, 0, 0, 8  , 1, date("Y")+1);
							$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
							
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
							$item['upDate_ru'] = $ROUT->GetRusDataStr($item['date'], false, true);
							$item['archieved'] = ($item['sendDate']>=$nextDateTmp)?false:true;
	//						$item['nextDateTmp'] = $nextDateTmp;
							$retArr[$cnt][] = $item;									
						}
					}
					if(count($retArr)>0)
						$retStart['PMO4']  =  $retArr;
					else
						$retStart['PMO4'] = 0;
//--------------------------------------------------------------------------------------------get PMO 5					
					$retTmp =  $ZhKH->getRep_pmo5($start, $distr);					
					$startTime = 0;
					$cnt = -1;
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
							$lastDate= mktime(0, 0, 0, 8  , 1, date("Y"));				
							$nextDate= mktime(0, 0, 0, 8  , 1, date("Y")+1);
							$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
							
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
							$item['upDate_ru'] = $ROUT->GetRusDataStr($item['date'], false, true);
							$item['archieved'] = ($item['sendDate']>=$nextDateTmp)?false:true;
	//						$item['nextDateTmp'] = $nextDateTmp;
							$retArr[$cnt][] = $item;									
						}
					}
					if(count($retArr)>0)
						$retStart['PMO5']  =  $retArr;
					else
						$retStart['PMO5'] = 0;
//--------------------------------------------------------------------------------------------get PMO 6					
					$retTmp =  $ZhKH->getRep_pmo6($start, $distr);					
					$startTime = 0;
					$cnt = -1;
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							$lastDate= mktime(0, 0, 0, 8  , 1, date("Y"));				
							$nextDate= mktime(0, 0, 0, 8  , 1, date("Y")+1);
							$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
							
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
							$item['upDate_ru'] = $ROUT->GetRusDataStr($item['date'], false, true);
							$item['archieved'] = ($item['sendDate']>=$nextDateTmp)?false:true;

							$retArr[] = $item;									
						}
					}
					if(count($retArr)>0)
						$retStart['PMO6']  =  $retArr;
					else
						$retStart['PMO6'] = 0;
//--------------------------------------------------------------------------------------------get PMO 7					
					$retTmp =  $ZhKH->getRep_pmo7($start, $distr);					
					$startTime = 0;
					$cnt = -1; 
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
							$lastDate= mktime(0, 0, 0, 8  , 1, date("Y"));				
							$nextDate= mktime(0, 0, 0, 8  , 1, date("Y")+1);
							$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
							
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
							$item['upDate_ru'] = $ROUT->GetRusDataStr($item['date'], false, true);
							$item['archieved'] = ($item['sendDate']>=$nextDateTmp)?false:true;
	//						$item['nextDateTmp'] = $nextDateTmp;
							$retArr[$cnt][] = $item;									
						}
					}
					if(count($retArr)>0)
						$retStart['PMO7']  =  $retArr;
					else
						$retStart['PMO7'] = 0;	
//--------------------------------------------------------------------------------------------get PMO 8				
					$retTmp =  $ZhKH->getRep_pmo8($start, $distr);					
					$startTime = 0;
					$cnt = -1; 
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
							$lastDate= mktime(0, 0, 0, 8  , 1, date("Y"));				
							$nextDate= mktime(0, 0, 0, 8  , 1, date("Y")+1);
							$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
							
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
							$item['upDate_ru'] = $ROUT->GetRusDataStr($item['date'], false, true);
							$item['archieved'] = ($item['sendDate']>=$nextDateTmp)?false:true;
	//						$item['nextDateTmp'] = $nextDateTmp;
							$retArr[$cnt][] = $item;									
						}
					}
					if(count($retArr)>0)
						$retStart['PMO8']  =  $retArr;
					else
						$retStart['PMO8'] = 0;		
//--------------------------------------------------------------------------------------------get PMO 9				
					$retTmp =  $ZhKH->getRep_pmo9($start, $distr);					
					$startTime = 0; 
					$cnt = -1; 
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
							$lastDate= mktime(0, 0, 0, 8  , 1, date("Y"));				
							$nextDate= mktime(0, 0, 0, 8  , 1, date("Y")+1);
							$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
							
							$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
							$item['upDate_ru'] = $ROUT->GetRusDataStr($item['date'], false, true);
							$item['archieved'] = ($item['sendDate']>=$nextDateTmp)?false:true;
	//						$item['nextDateTmp'] = $nextDateTmp;
							$retArr[$cnt][] = $item;									
						}
					}
					if(count($retArr)>0)
						$retStart['PMO9']  =  $retArr;
					else
						$retStart['PMO9'] = 0;					
					/*
					$retStart['toplPril1'] =  $ZhKH->getRep_toplPril1($start, $distr);*/
					$ret = json_encode($retStart);
				}break;
				case 'toplPril1':{
					$ret =  json_encode($ZhKH->getRep_toplPril1($start, $distr));
				}break;
				case '1zhkh':{
					$retTmp = $ZhKH->getRep_1zhkh($start, $distr, isset($_POST['light'])?$_POST['light']:false);
					//print_r($retTmp);
					$startTime = 0;
					$cnt = -1;
					$retArr = array();
					if($retTmp){
						foreach ($retTmp as $item) {
							if($item['sendDate']!=$startTime){
								$startTime = $item['sendDate'];
								$cnt++;
							}
							if(isset($_POST['light'])){
								$itemTmp['id'] = $item['formId'];
								$itemTmp['user'] = $item['user_name'];
								$itemTmp['upDate'] = $item['date'];
								$itemTmp['distrId'] = $item['distrId'];
								$itemTmp['sendDate'] = $item['sendDate'];
								$itemTmp['sendDate_human'] = date('d.m.Y', $item['sendDate']);
								$itemTmp['archieved'] = ($item['sendDate']>time())?false:true;
								$retArr[$cnt][] = $itemTmp;	
							} else {
								$item['sendDate_ru'] = $ROUT->GetRusDataStr($item['sendDate'], false, false);
								$item['archieved'] = ($item['sendDate']>time())?false:true;
		//						$item['sendDate_ru_arr'] = print_r($ROUT->GetRusData($item['sendDate']), true);
								$retArr[$cnt][] = $item;	
							}					
						}
					}
					if(is_array($retArr))
						$ret =  json_encode($retArr);
					else
						$ret = json_encode(0);
				}break;
			}
			print_r( $ret);
			}break;			
		case 'getReport': /*2019_04_01 */
			{				
			chdir('..//');	
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/zhkh.php");	
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$ZhKH = new ZhKH;	
			$distr = ($_POST['distr']) ? trim($_POST['distr']) : 0;
			$start = ($_POST['start']) ? trim($_POST['start']) : 0;
			switch($_POST['formName']){
				case 'toplPril1':{
					$ret =  json_encode($ZhKH->getRep_toplPril1($start, $distr));
					//print_r($ret);
					}break;
				case 'toplPril2':{	
					$sendDate = 0;
					$retTmp = $ZhKH->getRep_toplPril2($start, $distr, $sendDate);
					foreach ($retTmp as $item) {
						$item['sendDateHuman'] = date('d.m.Y', $item['sendDate']);
						$newRet[] = $item;
					}
					$ret =  json_encode($newRet);
					}break;
				case 'sfo':{	
					$sendDate = 0;
					$retTmp = $ZhKH->getRep_sfo($start, $distr, $sendDate);
					foreach ($retTmp as $item) {
						$item['sendDateHuman'] = date('d.m.Y', $item['sendDate']);
						$newRet[] = $item;
					}
					$ret =  json_encode($newRet);
					}break;
			}
			
			//$ret =  json_encode($ZhKH->getMessageAll($start, $distr));
			//print_r($ret);
			$strout = print_r($ret,true);
			echo '{"SomeJsonObject": {"Items": '.$strout.'}}';
			}break;			
		case 'getMessReport': /*2019_04_01 */
			{				
			chdir('..//');	
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/zhkh.php");	
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$ZhKH = new ZhKH;	
			$distr = ($_POST['distr']) ? trim($_POST['distr']) : 0;
			$start = ($_POST['start']) ? trim($_POST['start']) : 0;
			$ret =  json_encode($ZhKH->getMessageAll($start, $distr));
			//print_r($ret);
			$strout = print_r($ret,true);
			echo '{"SomeJsonObject": {"Items": '.$strout.'}}';
			}break;			
		case 'getMessSingle': /*2019_04_01 */
			{
			chdir('..//');	
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/zhkh.php");	
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$ZhKH = new ZhKH;	
			$mId = ($_POST['mId']) ? trim($_POST['mId']) : 0;
			$ret =  json_encode($ZhKH->getMessage($mId));
			//print_r($ret);
			$strout = print_r($ret,true);
			echo '{"SomeJsonObject": {"Items": '.$strout.'}}';
			}break;			
		case 'specQuery': /*2019_05_22 запросы от Паши к его служебным таблицам */
			{
			$ret = '{"success": false}';		
			if(isset($_POST['cmd'])){
				$cmd = trim($_POST['cmd']);
				chdir('..//');	
				require_once('../classes/config.inc.php');
				require_once("../classes/MySQLi_class.php");
				require_once("../classes/GetContent_class.php");
				require_once("../classes/gis/zhkh.php");	
				require_once("../classes/User_class.php");
				require_once("../classes/ACL_class.php");
				require_once("../classes/Rout_class.php");
				$CNT = 		new GetContent;
				$ROUT = new Routine;
				$CONST = $CNT->GetAllConfig();
				$ZhKH = new ZhKH;	
				switch($cmd)
					{
					case 'hr_factor': /*возврат одноимённой таблички*/
						{
						$oId = (isset($_POST['id']))?trim($_POST['id']):0;
						$ret = json_encode($ZhKH->getSpecHrFactor($oId));
					} break;
					case 'kls_metriks': /*возврат одноимённой таблички*/
						{
						$oId = (isset($_POST['id']))?trim($_POST['id']):0;
						$ret = json_encode($ZhKH->getSpecKlsMetriks($oId));
/*						if(isset($_POST['id'])){
							$ret = json_encode($ZhKH->getSpecKlsSingle(trim($_POST['id'])));
						}*/
					} break;					
					case 'kls_single': /*возврат одноимённой таблички*/
						{
						$oId = (isset($_POST['id']))?trim($_POST['id']):0;
						$ret = json_encode($ZhKH->getSpecKlsSingle($oId));
/*						if(isset($_POST['id'])){
							$ret = json_encode($ZhKH->getSpecKlsSingle(trim($_POST['id'])));
						}*/
					} break;
					case 'kls_factor': /*возврат одноимённой таблички*/
						{
						$oId = (isset($_POST['id']))?trim($_POST['id']):0;
						$ret = json_encode($ZhKH->getSpecKlsFactor($oId));
/*						if(isset($_POST['id'])){
							$ret = json_encode($ZhKH->getSpecKlsFactor(trim($_POST['id'])));
						}*/
					} break;
				} 
			}
			$strout = print_r($ret,true);
			echo '{"SomeJsonObject": {"Items": '.$strout.'}}';
			}break;			
		case 'getMessAll': /*2019_04_01 */
			{				
			chdir('..//');	
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/zhkh.php");	
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$ZhKH = new ZhKH;	

			$distr = ($_POST['distr']) ? trim($_POST['distr']) : 0;
			$start = ($_POST['start']) ? trim($_POST['start']) : 0;
			
			$retTmp =  $ZhKH->getMessageAll($start, $distr);
				foreach ($retTmp as $item) {
//					print_r($item);
//					$item['IncDateHuman'] = date('d.m.Y', $item['incidentDate']);
					$item->incidentDate_ts = strtotime($item->incidentDate);
					$item->incidentDate_ru = $ROUT->GetRusDataStr($item->incidentDate_ts, false, false, true);
					$item->incidentDate_ts *= 1000;
					$newRet[] = $item;
				}
//			$ret =  json_encode($ZhKH->getMessageAll($start, $distr));
			print_r( json_encode($newRet));
			}break;				
		case 'getODLists': /*2019_04_01 */
			{				
			chdir('..//');	
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/zhkh.php");	
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			require_once("../classes/gis/manage_class.php");
			$MNG = new Manage;	
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$ZhKH = new ZhKH;	
			$distr = ($_POST['distr']) ? trim($_POST['distr']) : 0;
			$retTmp = 	$ZhKH->getMessageAll(0, $distr);
			foreach ($retTmp as $item) {
//					print_r($item);
//					$item['IncDateHuman'] = date('d.m.Y', $item['incidentDate']);
				$item->incidentDate_ts = strtotime($item->incidentDate);
				$item->incidentDate_ru = $ROUT->GetRusDataStr($item->incidentDate_ts, false, false, true);
				$item->incidentDate_ts *= 1000;
				$retArr['messList'][] = $item;
			}
			
//			$retArr['messList'] = 	$ZhKH->getMessageAll(0, $distr);
			$retArr['orgList'] = 	$ZhKH->getOrgList($distr);
			if ($npList = $MNG->getNPListOfDistr($distr)){
				$retArr['npList'] = 	$npList;
				foreach ($retArr['npList'] as $item) {
	//				$retArr['listNP'][] = array('n_'.$item['l_id'] => $item['l_name']);
					$retArr['listNP']['n_'.$item['l_id']] = $item['l_name'];
				}
			} else {
				$retArr['npList'] = array();
				$retArr['listNP'] = 0;				
			}
//			$retArr['listNP'] = 	$MNG->getNPListOfDistr($distr);
			
			
			$ret =  json_encode($retArr);
			print_r( $ret);
			}break;			
		case 'getOrgList': /*2019_04_01 */
			{				
			chdir('..//');	
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/zhkh.php");	
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$ZhKH = new ZhKH;	

			$distr = ($_POST['distr']) ? trim($_POST['distr']) : 0;
			$ret =  json_encode($ZhKH->getOrgList($distr));
			print_r( $ret);
			}break;			
		case 'getNPList': /*2019_04_01 */
			{				
			chdir('..//');	
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			require_once("../classes/gis/manage_class.php");
			$MNG = new Manage;	
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();

			$distr = ($_POST['distr']) ? trim($_POST['distr']) : 0;
			$ret =  json_encode($MNG->getNPListOfDistr($distr));
			print_r( $ret);
			}break;			
		case 'getMess': /*2019_03_07 рутинная работа с оперативными донесениями*/
			{				
			chdir('..//');	
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/Rout_class.php");
			require_once("../classes/gis/zhkh.php");		
			$ret = 0;
			if(isset($_POST['cmd']))
				$cmd = trim($_POST['cmd']);
			else 
				$cmd = 'getReasons';
			$ZhKH = new ZhKH;	
			$ret = 0;
//			echo $cmd;
			switch($cmd)
				{
				case 'getReasons': {
					$ret = $ZhKH->getReasons();
				} break;
			}
			if($ret){
				$ret =  json_encode($ret);
				print_r( $ret);			
			} else 
				echo $ret;			
			}break;			
	
			case 'addComment': /*2019_03_07 */
				{
				chdir('..//');
				require_once('../classes/config.inc.php');
				require_once("../classes/MySQLi_class.php");
				require_once("../classes/GetContent_class.php");
				require_once("../classes/gis/zhkh.php");
				require_once("../classes/User_class.php");
				require_once("../classes/ACL_class.php");
				require_once("../classes/Rout_class.php");
				$CNT =          new GetContent;
				$ROUT = new Routine;
				$CONST = $CNT->GetAllConfig();
				$ZhKH = new ZhKH;
				$data = json_decode($_POST['data']);
				//print_r($data);
				$ret = $ZhKH->newComment($data);
				if($ret['error'])
						{
						$strOut =  ' Возникла непредвиденная ошибка в процессе добавления сообщения. Попробуйте снова или обратитесь к администрации ('.$ret['errorMsg'].')';
						}
				else{
						if (isset($data->commClose)){
								$ret = $ZhKH->closeMessage($data->messId);
								if($ret['error'])
										{
										$strOut =  ' Возникла непредвиденная ошибка в процессе добавления сообщения. Попробуйте снова или обратитесь к администрации ('.$ret['errorMsg'].')';
										}
								else
										$strOut =  1;
						} else {
								$strOut =  1;
						}
				}
//                      echo $_SERVER['SERVER_NAME'].'; position = '.strpos($_SERVER['SERVER_NAME'],  'jkh38.ru').';';
				if(($strOut ==  1)&&(strpos($_SERVER['SERVER_NAME'],  'jkh38.ru')!== false)){

						require_once("../classes/messRoutine.php");
						$MR = new messengerRoutine;
						$MR->sendAlarm('comment', $data->messId, $ZhKH->getMessage($data->messId, 'distId'));
				}
				echo $strOut;/**/
			} break;
		case 'addReport': /*2019_03_07 */
			{				
			chdir('..//');	
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/zhkh.php");	
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			$fname = '../htdocs/operMess.txt';
			$fp = fopen($fname, "r");
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$ZhKH = new ZhKH;	
			$data = json_decode($_POST['data']);
			switch($_POST['formName']){
				case 'sfo_1':{};
				case 'sfo_2':{};
				case 'sfo_3':{};
				case 'sfo_4':{};
				case 'sfo_5':{
					$start = 0; $distr = $data->frm_distId;
					$thisDate15= mktime(0, 0, 0, date("m")  , 15, date("Y"));
					$nextDate1= mktime(0, 0, 0, date("m")+1  , 1, date("Y"));
					$sendDate = (time()<$thisDate15) ? $thisDate15 : $nextDate1;
					if($ZhKH->getRep_sfo($start, $distr, $sendDate)){
						$ret = $ZhKH->upRep_sfo($data, $sendDate);
					}
					else{
						$ret = $ZhKH->newRep_sfo($data, $sendDate);
					}
					}break;
				case 'toplPril1':{
					$start = 0; $distr = $data->frm_distId;
					//$thisDate15= mktime(0, 0, 0, date("m")  , 15, date("Y"));
					$sendDate= mktime(0, 0, 0, date("m") , date("d")+1, date("Y"));
//					print_r($data);
					if($ZhKH->getRep_toplPril1($start, $distr, $sendDate)){
					//	echo 'update!!!';
						$ret = $ZhKH->upRep_toplPril1($data, $sendDate);
					}
					else{
					//	echo 'insert!!!';
						$ret = $ZhKH->newRep_toplPril1($data, $sendDate);
					}					
//					print_r($ret);
				//	$ret = $ZhKH->newRep_toplPril1($data);
				}break;
				case 'zhkh1':{
				}
				case '1zhkh':{
					$start = 0; $distr = $data->frm_distId;
					$thisDate15= mktime(0, 0, 0, date("m")  , 15, date("Y"));
					$nextDate1= mktime(0, 0, 0, date("m")+1  , 1, date("Y"));
					$sendDate = (time()<$thisDate15) ? $thisDate15 : $nextDate1;
					if($ZhKH->getRep_1zhkh_single($start, $distr, $sendDate)){
						$ret = $ZhKH->upRep_1zhkh($data, $sendDate);
					}else{				
						$ret = $ZhKH->newRep_1zhkh($data, $sendDate);
					}
				}break;
				case 'toplPril2':{
					$start = 0; $distr = $data->frm_distId;
					//$thisDate15= mktime(0, 0, 0, date("m")  , 15, date("Y"));
					$thisDate5= mktime(0, 0, 0, date("m")  , 5, date("Y"));
					$nextDate5= mktime(0, 0, 0, date("m")+1  , 5, date("Y"));
					$sendDate = (time()<$thisDate5) ? $thisDate5 : $nextDate5;
					//print_r($data);
					if($ZhKH->getRep_toplPril2($start, $distr, $sendDate)){
//						echo 'update!!!';
						$ret = $ZhKH->upRep_toplPril2($data, $sendDate);
					}
					else{
//						echo 'insert!!!';
						$ret = $ZhKH->newRep_toplPril2($data, $sendDate);
					}						
//					print_r($ret);
				}break;				
				case 'zhkh2':{
					$start = 0; $distr = $data->frm_distId;
					$lastDate= mktime(0, 0, 0, 12  , 1, date("Y"));
					$nextDate= mktime(0, 0, 0, 12  , 1, date("Y")+1);
					$sendDate = $nextDate;
					$sendDate = (time()<$lastDate) ? $lastDate : $nextDate;
					if($ZhKH->getRep_zhkh2($start, $distr, $sendDate)){
						//echo 'update!!!';
						$ret = $ZhKH->upRep_zhkh2($data, $sendDate);
					}
					else{
						//echo 'insert!!!';
						$ret = $ZhKH->newRep_zhkh2($data, $sendDate);
					}
					}break;
				case 'zhkh24':{
					$start = 0; $distr = $data->frm_distId;
					$lastDate= mktime(0, 0, 0, 12  , 7, date("Y"));				/*********************************************вернуть после 7 декабря 19**********************************************/
					$nextDate= mktime(0, 0, 0, 12  , 1, date("Y")+1);
					$sendDate = $nextDate;
					//print_r($data);
					$sendDate = (time()<$lastDate) ? $lastDate : $nextDate;
					if($ZhKH->getRep_zhkh24($start, $distr, $sendDate)){
						//echo 'update!!!';
						$ret = $ZhKH->upRep_zhkh24($data, $sendDate);
					}
					else{
						//echo 'insert!!!';
						$ret = $ZhKH->newRep_zhkh24($data, $sendDate);
					}
					}break;
				case 'zhkh3':{
					$start = 0; $distr = $data->frm_distId;
					$sendDate =  mktime(0, 0, 0, date("m")+1  , 1, date("Y")); //$nextDate;					
					//print_r($data);
					if($repList = $ZhKH->getRep_zhkh3($start, $distr, $sendDate)){
						//echo 'update!!!';
						$insert7even = false;
						if(time() < '1575129600'){ //удалить после декабря 2019
							$insert7even = true;							
							foreach ($repList as $rep) {					
								if ( $rep['id_type_data'] == 7)
									$insert7even = false;
								}	
							}
						$ret = $ZhKH->upRep_zhkh3($data, $sendDate, $insert7even);
					}
					else{
//						echo 'insert!!!';
						$ret = $ZhKH->newRep_zhkh3($data, $sendDate);
					}
					}break;
				case 'pmo1':{};
				case 'pmo_1':{
					$start = 0; $distr = $data->frm_distId;
					$lastDate= mktime(0, 0, 0,8  , 1, date("Y"));				
					$nextDate= mktime(0, 0, 0,8  , 1, date("Y")+1);
					$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
					$sendDate = $nextDateTmp;
//					print_r($data);
					if($ZhKH->getRep_pmo1($start, $distr, $sendDate)){
//						echo 'update!!!';
						$ret = $ZhKH->upRep_pmo1($data, $sendDate);
						}
					else{
//						echo 'insert!!!';
						$ret = $ZhKH->newRep_pmo1($data, $sendDate);
						}					
					} break;
				case 'pmo3':{};
				case 'pmo_3':{
					$start = 0; $distr = $data->frm_distId;
					$lastDate= mktime(0, 0, 0,8  , 1, date("Y"));				
					$nextDate= mktime(0, 0, 0,8  , 1, date("Y")+1);
					$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
					$sendDate = $nextDateTmp;
//					print_r($data);
					if($ZhKH->getRep_pmo3($start, $distr, $sendDate)){
//						echo 'update!!!';
						$ret = $ZhKH->upRep_pmo3($data, $sendDate);
					}
					else{
//						echo 'insert!!!';
						$ret = $ZhKH->newRep_pmo3($data, $sendDate);
					}
					} break;				
				case 'pmo4':{};
				case 'pmo_4':{
					$start = 0; $distr = $data->frm_distId;
					$lastDate= mktime(0, 0, 0,8  , 1, date("Y"));				
					$nextDate= mktime(0, 0, 0,8  , 1, date("Y")+1);
					$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
					$sendDate = $nextDateTmp;
//					print_r($data);
					if($ZhKH->getRep_pmo4($start, $distr, $sendDate)){
//						echo 'update!!!';
						$ret = $ZhKH->upRep_pmo4($data, $sendDate);
					}
					else{
//						echo 'insert!!!';
						$ret = $ZhKH->newRep_pmo4($data, $sendDate);
					}
					} break;
				case 'pmo5':{};
				case 'pmo_5':{
					$start = 0; $distr = $data->frm_distId;
					$lastDate= mktime(0, 0, 0,8  , 1, date("Y"));				
					$nextDate= mktime(0, 0, 0,8  , 1, date("Y")+1);
					$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
					$sendDate = $nextDateTmp;
//					print_r($data);
					if($ZhKH->getRep_pmo5($start, $distr, $sendDate)){
//						echo 'update!!!';
						$ret = $ZhKH->upRep_pmo5($data, $sendDate);
					}
					else{
//						echo 'insert!!!';
						$ret = $ZhKH->newRep_pmo5($data, $sendDate);
					}
				} break;
				case 'pmo6':{
				}
				case 'pmo_6':{
					$start = 0; $distr = $data->frm_distId;
					//$thisDate15= mktime(0, 0, 0, date("m")  , 15, date("Y"));
					$recId = $data->recId;
					$lastDate= mktime(0, 0, 0,8  , 1, date("Y"));				
					$nextDate= mktime(0, 0, 0,8  , 1, date("Y")+1);
					$sendDate = (time()<$lastDate) ? $lastDate : $nextDate;
					if($ZhKH->getRep_pmo6_single($recId, $sendDate)){
//						echo 'update!!!';
						$ret = $ZhKH->upRep_pmo6($data, $sendDate);
					}
					else{
//						echo 'insert!!!';
						$ret = $ZhKH->newRep_pmo6($data, $sendDate);
					}
				}break;
					
				case 'pmo2':{
				}
				case 'pmo_2':{
					$start = 0; $distr = $data->frm_distId;
					//$thisDate15= mktime(0, 0, 0, date("m")  , 15, date("Y"));
					$recId = $data->recId;
					$lastDate= mktime(0, 0, 0,8  , 1, date("Y"));				
					$nextDate= mktime(0, 0, 0,8  , 1, date("Y")+1);
					$sendDate = (time()<$lastDate) ? $lastDate : $nextDate;
//					print_r($data);
					if($ZhKH->getRep_pmo2_single($recId, $sendDate)){
//						echo 'update!!!';
						$ret = $ZhKH->upRep_pmo2($data, $sendDate);
					}
					else{
//						echo 'insert!!!';
						$ret = $ZhKH->newRep_pmo2($data, $sendDate);
					}
				}break;


				case 'pmo7':{};
				case 'pmo_7':{
					$start = 0; $distr = $data->frm_distId;
					$lastDate= mktime(0, 0, 0,8  , 1, date("Y"));				
					$nextDate= mktime(0, 0, 0,8  , 1, date("Y")+1);
					$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
					$sendDate = $nextDateTmp;
//					print_r($data);
					if($ZhKH->getRep_pmo7($start, $distr, $sendDate)){
//						echo 'update!!!';
						$ret = $ZhKH->upRep_pmo7($data, $sendDate);
					}
					else{
//						echo 'insert!!!';
						$ret = $ZhKH->newRep_pmo7($data, $sendDate);
					}
				}break;
				case 'pmo8':{};
				case 'pmo_8':{
					$start = 0; $distr = $data->frm_distId;
					$lastDate= mktime(0, 0, 0,8  , 1, date("Y"));				
					$nextDate= mktime(0, 0, 0,8  , 1, date("Y")+1);
					$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
					$sendDate = $nextDateTmp;
//					print_r($data);
					if($ZhKH->getRep_pmo8($start, $distr, $sendDate)){
//						echo 'update!!!';
						$ret = $ZhKH->upRep_pmo8($data, $sendDate);
					}
					else{
//						echo 'insert!!!';
						$ret = $ZhKH->newRep_pmo8($data, $sendDate);
					}
				}break;
				case 'pmo9':{};
				case 'pmo_9':{
					$start = 0; $distr = $data->frm_distId;
					$lastDate= mktime(0, 0, 0,8  , 1, date("Y"));				
					$nextDate= mktime(0, 0, 0,8  , 1, date("Y")+1);
					$nextDateTmp = (time()<$lastDate) ? $lastDate : $nextDate;
					$sendDate = $nextDateTmp;
//					print_r($data);
					if($ZhKH->getRep_pmo9($start, $distr, $sendDate)){
//						echo 'update!!!';
						$ret = $ZhKH->upRep_pmo9($data, $sendDate);
					}
					else{
//						echo 'insert!!!';
						$ret = $ZhKH->newRep_pmo9($data, $sendDate);
					}				
				}break;
					
			} 
			if($ret['error'])
				{
				$strOut =  ' Возникла непредвиденная ошибка в процессе добавления сообщения. Попробуйте снова или обратитесь к администрации ('.$ret['errorMsg'].')';
				}
			else
				$strOut =  1;
			echo $strOut;
			} break;	
		case 'addForm': /*2019_03_07 */
			{				
			chdir('..//');	
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/zhkh.php");	
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			$fname = '../htdocs/operMess.txt';
			$fp = fopen($fname, "r");
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$ZhKH = new ZhKH;	

			
			$fileName = '../htdocs/operMess.txt';
			$data = json_decode($_POST['data']);
			if(!$data->orgId) $data->orgId = 0;
			if(!$data->npId)
				$data->npId = 0; 
			elseif(strpos($data->npId, '_')!== false) {
				$data->npId = $ROUT->getStrPrt($data->npId, '_', 1);
			} 
				
			if((!$data->hardwareId)&&(trim($data->hardwareNew))) {
				$data->hardware = trim($data->hardwareNew);
			} elseif(($data->hardwareId)){
				$data->hardware = trim($data->hardwareName);				
			}
			$data->incidentDateTS =  strtotime ($data->incidentDate);
			$data->number = $ZhKH->genMessageNumNext();
//			print_r($data);  
			$ret = $ZhKH->newMessage($data); 
			if($ret['error'])
				{
				$strOut =  ' Возникла непредвиденная ошибка в процессе добавления сообщения. Попробуйте снова или обратитесь к администрации ('.$ret['errorMsg'].')';
				}
			else
				$strOut =  1;
			if(($strOut ==  1)&&(strpos($_SERVER['SERVER_NAME'],  'jkh38.ru')!== false)){
				require_once("../classes/messRoutine.php");	
				$MR = new messengerRoutine;					
				$MR->sendAlarm('message', $ret['last_id'], $data->distId);
			}
			echo $strOut;
			} break;	
		case 'addForm2File': /*2019_03_07 */
			{				
			chdir('..//');			
			$fileName = '../htdocs/operMess.txt';
			$fpw = fopen($fileName,  "a");
//			$w1 = fwrite($fpw, $content);
			$w2 = fwrite($fpw, $_POST['data']);
			fclose($fpw);
/*			foreach ($data as $item) {
				print_r($item);
				}			
*/
//			fwrite( $fp, $_POST['data']);
			echo 1;
			} break;	
		case 'addForm_': /*2019_03_07 */
			{				
			$wreckType = array('waterOut'=>1, 'waterIn'=>2, 'electricity'=>3, 'heat'=>4);
			chdir('..//');			
			$fileName = '../htdocs/operMess.txt';
			$fpr = fopen($fileName,  "r");
			$content = fread($fpr, filesize($fileName));
			fclose($fpr);
//			$dataExist = json_encode($content);
//			$dataExist = json_decode($content);
//			print_r($dataExist);
/*			$cnt = 0;
			foreach ($dataExist as $item) {
				print_r($item);
				}		*/	
			$nextIndex = 1;
			$data = json_decode($_POST['data']);
//			$data = $_POST['data'];
			//print_r($data);
			$dataSave = array('dateCreate' => date('d m Y H:i'),
							'distId' => $data->distId, 
							'npId' => $data->distId, 
							'incidentDate' => $data->incidentDate, 
							'wreckType' => $wreckType[$data->type], 
							'reason' => $data->reason, 
							'hardware' => $data->hardware, 
							'potrebiteli' => $data->potrebiteli, 
							'org' => $data->org, 
							'bossOfWork' => $data->bossOfWork, 
							'bossOfCity' => $data->bossOfCity, 
							'goodNewsMen' => $data->goodNewsMen, 
							'tempereture' => $data->tempereture, 
							'incidentInfoDate' => $data->incidentInfoDate, 
							'badNewsMen' => $data->badNewsMen, 
							'status' => 1, 
							'id' => $nextIndex
							);
//			print_r(json_encode((object) $dataSave )) ;
			$fpw = fopen($fileName,  "w");
			$w1 = fwrite($fpw, $content);
			$w2 = fwrite($fpw, json_encode((object) $dataSave ));
			fclose($fpw);
/*			foreach ($data as $item) {
				print_r($item);
				}			
*/
//			fwrite( $fp, $_POST['data']);
//			echo 1;
			} break;	
		case 'navForestSingle': /*2018_04_19 получить трек по леснику  - по адресу и времени*/
			{
			chdir('..//');			
			$context = stream_context_create(array(
				'http' => array(
						'header' => implode("\r\n", array(
							"dataType: json"
						))
			)));			
//			print_r($_POST);
//			$JSStr = $_POST;
//			$JSStr = '';
//http://91.229.154.8/les_json/les_data.php?id=868204003130886&start_timestamp=1520697894&finish_timestamp=1520755494
			$path = 'http://91.229.154.8/les_json/les_data.php?id='.trim($_POST['obj']).'&start_timestamp='.trim($_POST['date'][0]).'&finish_timestamp='.trim($_POST['date'][1]).'';
			$JSStr = file_get_contents($path);
/*			$obj = json_decode(file_get_contents($path));
			echo json_last_error_msg ();
			print_r($obj);*/
			echo $JSStr;
			} break;		
		case 'navForest': /*2018_03_25 получить данные о лесниках */
			{
			chdir('..//');
			$context = stream_context_create(array(
				'http' => array(
						/*'method'=>"GET",
						'content' => $reqdata = http_build_query(array(
						)),*/
						'header' => implode("\r\n", array(
							"dataType: json"
								/*"Content-Length: " . strlen($reqdata),
								"User-Agent: SyberiaGisBot/0.1",
								"Connection: Close",
								""*/
						))
			)));			

			$JSStr = '';
//			$JSStr = file_get_contents('http://91.229.154.8/les_json/obj_les.php', false, $context);
			$JSStr = file_get_contents('http://91.229.154.8/les_json/obj_les.php');
			echo $JSStr;
			} break;
		case 'getPKK': /*2017_02_09 получить объекты слоёв*/
			{
			ini_set('memory_limit', '256M');
			chdir('..//');
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/manage_class.php");	
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			require_once("../classes/gis/manage_class.php");
			require_once("../classes/gis/pkkSprav.php");
			
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$MNG = new Manage;	
			$valueDelim = '::';
			$paramDelim = '##';
			$objDelim = '||';
			$JSStr = 	'';
			$ctx = stream_context_create(array(
				'http' => array(        'timeout' => 1        )
					)
					);
//			$isMng = ($_POST['startStr'] == '*69F727D4F74061CDEB44032B0A7FBE5EE6453E76') ? 1 : 0;					
			$latM = number_format($_POST['lat'], 6, ',', '');
			$lngM = number_format($_POST['lng'], 6, ',', '');
			$url1 = 'http://pkk5.rosreestr.ru/api/features/1?text='.$latM.'%20'.$lngM.'&tolerance=4&limit=11';
			$obj1 = json_decode(file_get_contents($url1, 0, $ctx)); 
//			var_dump($obj1->features);
			if(is_array($obj1->features))
				{
				foreach ($obj1->features as $feature) {
					$url2 = 'http://pkk5.rosreestr.ru/api/features/1/'.$feature->attrs->id;
					$obj2 = json_decode(file_get_contents($url2, 0, $ctx)); 
					$JSStr .= 	'Кадастровый номер'.$valueDelim.$obj2->feature->attrs->cn.$paramDelim.
								'Адрес'.$valueDelim.$obj2->feature->attrs->address.$paramDelim.
								'Кадастровый округ'.$valueDelim.$obj2->feature->attrs->okrug.$paramDelim.
								'Кадастровый район'.$valueDelim.$obj2->feature->attrs->rayon.$paramDelim.
								'Кадастровый квартал'.$valueDelim.$obj2->feature->attrs->kvartal.$paramDelim;
								
					$JSStr .= 	($obj2->feature->attrs->adate)	? 'Дата обновления границ'.$valueDelim.$ROUT->GetRusDataStr($obj2->feature->attrs->adate, 1).$paramDelim : '';
					$JSStr .= 	$areaArr[$obj2->feature->attrs->area_type].$valueDelim.$obj2->feature->attrs->area_value.' '.$unitArr[$obj2->feature->attrs->area_unit].$paramDelim.
							'Кадастровая стоимость'.$valueDelim.number_format($obj2->feature->attrs->cad_cost, 2, '.', ' ').' '.$unitArr[$obj2->feature->attrs->cad_unit].$paramDelim;
					$JSStr .= 	($obj2->feature->attrs->date_cost)?'Дата внесения кадастровой стоимости'.$valueDelim.$ROUT->GetRusDataStr($obj2->feature->attrs->date_cost, 1).$paramDelim : '';
					if($obj2->feature->attrs->cad_eng_data)			
						{
						$JSStr .= 	'Дата обновления атрибутов'.$valueDelim.$ROUT->GetRusDataStr($obj2->feature->attrs->cad_eng_data->actual_date, 1).$paramDelim;
						$JSStr .= (isset($obj2->feature->attrs->cad_eng_data->co_name)) ?
							 'Кадастровый инженер (организация)'.$valueDelim.$obj2->feature->attrs->cad_eng_data->co_name.$paramDelim : 
							 'Кадастровый инженер '.$valueDelim.$obj2->feature->attrs->cad_eng_data->ci_surname.' '.$obj2->feature->attrs->cad_eng_data->ci_first.' '.$obj2->feature->attrs->cad_eng_data->ci_patronymic.
								' (№ свидетельства '.$obj2->feature->attrs->cad_eng_data->ci_n_certificate.')'.$paramDelim;
						}
					if(($obj2->feature->attrs->util_code != '')||($obj2->feature->attrs->util_by_doc != ''))
						{
						$JSStr .= 'Разрешенное использование'.$valueDelim;
						$JSStr .= ($obj2->feature->attrs->util_code != '') ? $areaUtilArray[$obj2->feature->attrs->util_code] : $obj2->feature->attrs->util_by_doc;
						$JSStr .= (($obj2->feature->attrs->util_code != '')&&($obj2->feature->attrs->util_by_doc != '')) ? ' ('.$obj2->feature->attrs->util_by_doc.')':'';
						$JSStr .= $paramDelim;
									'';
						}
					$JSStr .= ($JSStr) ? $objDelim :'';
					}
				$JSStr .= (!$JSStr) ? '0' : '';
				
				}
			else
				$JSStr .= '0';
/*			$JSStr  .=$_POST['lat'].'  -  '.$_POST['lng'];                                    */
			//var_dump($obj2);
//			var_dump($JSStr);
			echo $JSStr;
			} break;				
		case 'ULObjectGeo': /*2017_10_15 получить геометрию объекта */
			{
			chdir('..//');
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/manage_class.php");	
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			require_once("../classes/gis/manage_class.php");
//			ini_set('max_input_vars', '5000');
			$JSStr = '';
			$layerDelim = '~~';
			$paramDelim = '##';
			$objStart = '<<obj>>';
			$BoundsStart = '<<bounds>>';
			$objDelim = '||';
			$geoDelim = '^^';
			$paramDelimObj = '%%';
			$paramDelimGeo = '$$';
			$holeDelimGeo = '**';
			$featureDelimGeo = '++';
			
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$MNG = new Manage;
//			print_r($_POST);
			if(is_array($_POST['oId'])){
				$cObj = 0;
				$JSStr = '<mlt>';
//				$JSStr .= '__array: '.print_r($_POST['oId']);
				$objGeo =  $MNG->getGeoOfObjectArr($_POST['oId']);
				foreach ($objGeo as $obj) {
					$JSStr .= (($cObj != $obj['o_id']) && ($cObj )) ? $objDelim : '';
					$JSStr .= 	$obj['c_lat'].$paramDelim.				//0
								$obj['c_lng'].$paramDelim.				//1
								$obj['hole_id'].$paramDelim.			//2
								$obj['feature_id'].$paramDelim.			//3
								$obj['o_id'].$paramDelim.$layerDelim;			//4
					$cObj =  $obj['o_id'];
				}
			}
			else{
				
				$objGeo =  $MNG->getGeoOfObject($_POST['oId']);
				foreach ($objGeo as $obj) {
					$JSStr .= 	$obj['c_lat'].$paramDelim.				//0
								$obj['c_lng'].$paramDelim.				//1
								$obj['hole_id'].$paramDelim.			//2
								$obj['feature_id'].$paramDelim.$layerDelim;			//3
								
					}
			}
			echo $JSStr;
			} break;
		case 'ULObjectsZHKH': /*2019_03_17 получить объекты слоёв ЖКХ*/
			{
			ini_set('memory_limit', '512M');
			chdir('..//');
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/manage_class.php");	
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			require_once("../classes/gis/manage_class.php");
			require_once("../classes/gis/zhkh.php");	
			echo 'ULObjects';
/*			$fname = '../htdocs/operMess.txt';
			$fp = fopen($fname, "r");*/
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$MNG = new Manage;	
			$ZhKH = new ZhKH;	

			$latMax = $latMin = $lngMax = $lngMin = 0;
			
			$isMng = ($_POST['startStr'] == '*69F727D4F74061CDEB44032B0A7FBE5EE6453E76') ? 1 : 0;					
			
			$JSStr = '';
			$layerDelim = '~~';
			$paramDelim = '##';
			$objStart = '<<obj>>';
			$BoundsStart = '<<bounds>>';
			$messStart = '<<mess>>';
			$objDelim = '||';
			$geoDelim = '^^';
			$paramDelimObj = '%%';
			$paramDelimGeo = '$$';
			$holeDelimGeo = '**';
			$featureDelimGeo = '++';
			$layerList = $MNG->getAllLayers($isMng);
//			$objList = $MNG->getAllObjStart();
			$objList = $MNG->getAllObjStartZhKH();
//			$messStr = fread($fp, filesize($fname));
			
/*			$distr = ($_POST['distr']) ? trim($_POST['distr']) : 0;
			$start = ($_POST['start']) ? trim($_POST['start']) : 0;*/
			$messStr =  json_encode($ZhKH->getMessageAll(0, 0));
//			print_r( $ret);

			foreach ($layerList as $layer) {
				$JSStr .= 	$layer['l_id'].$paramDelim.							//0
//					 str_replace('&quot;', '"', $layer['l_name']).$paramDelim.	//1
						$layer['l_name'].$paramDelim.							//1
						$layer['l_info'].$paramDelim.							//2
						$layer['l_public'].$paramDelim.							//3
						$layer['l_system'].$paramDelim.							//4
						$layer['l_parId'].$paramDelim.							//5
						$layer['l_childCnt'].$paramDelim.						//6
						$layer['l_objCnt'].$paramDelim.							//7	
						$layer['l_parStr'].$paramDelim;							//8
				$JSStr .= $layerDelim;
			}
			$JSStr .= $objStart;
			usort($objList, 'sortObjNameAsc');
//			print_r($objList);
			foreach ($objList as $obj) {
				$polyType = 0;
//				$MNG->addObj2Layer($obj['l_id'], $obj['o_id']);
				if((!$obj['o_name'])||(strlen($obj['o_name'])<3))
					$obj['o_name'] = strip_tags(substr($obj['o_info'], strpos($obj['o_info'], '<h2>'), strpos($obj['o_info'], '</h2>')));
				$JSStr 			.= 	$obj['o_id'].$paramDelim.			//0
									$obj['o_name'].$paramDelim.  		
//									str_replace('&quot;', '"', $obj['o_name']).$paramDelim.//1
									$obj['o_type'].$paramDelim.  		//2
									$obj['o_info'].$paramDelim.  		//3
									$obj['o_specParam'].$paramDelim.	//4
									$obj['t_id'].$paramDelim.			//5
//									$obj['o2l_str'].$paramDelim.  		//6
									$obj['o_lStr'].$paramDelim.  		//6
									$obj['o_lParentStr'].$paramDelim;  	//7
						if(is_array($obj['geo'])){						//8
							$hole = $feature = $polyType =  0;
							foreach ($obj['geo']  as $geo) {
								
								if($geo['hole_id']!=$hole){
									$hole = $geo['hole_id'];
									$JSStr .= $holeDelimGeo;
								}
								if($geo['feature_id']!=$feature){
									$feature = $geo['feature_id'];
									$JSStr .= $featureDelimGeo;
								}
/*								$JSStr .= 	$geo['c_id'].$paramDelimGeo.		//0
											$geo['c_lng'].$paramDelimGeo.       //2
											$geo['c_lat'].$paramDelimGeo.       //1
											$geo['o_id'].$geoDelim;            	//3*/
											
								$JSStr .= 	$geo['c_lng'].$paramDelimGeo.       //1
											$geo['c_lat'].$geoDelim;            //0
							/*	if(($geo['c_lat'] > $latMax) && ($geo['c_lat'] < 180 ))
									$latMax = floatval ($geo['c_lat']);
								if(($geo['c_lat'] < $latMin) && ($geo['c_lat'] > -180 ))
									$latMin = floatval ($geo['c_lat']);
								if(($geo['c_lng'] > $lngMax) && ($geo['c_lng'] < 90 ))
									$lngMax = floatval ($geo['c_lng']);
								if(($geo['c_lng'] < $lngMin) && ($geo['c_lng'] > -90 ))
									$lngMin = floatval ($geo['c_lng']);*/
							}
							if(($feature>0)&&($hole>0))
								$polyType = 3;
							elseif(($feature>0)&&($hole==0))
								$polyType = 2;
							elseif(($feature==0)&&($hole>0))
								$polyType = 1;
//							echo 'feature = '.$feature.'; hole = '.$hole.'; polyType = '.$polyType;
							$JSStr .= 	$paramDelim.$polyType;				//9
						}
						elseif($obj['geo']=='remote'){
							$JSStr .= 	'remote';							//8
						}
				$JSStr .= $objDelim;                                    
/*				$boundsObj = $MNG->getObjBounds($obj['o_id']);*/
				if($obj['o_latMin']){
					$bounds['minLat'] = (($obj['o_latMin']<$bounds['minLat'])||(!isset($bounds['minLat'])))?$obj['o_latMin']:$bounds['minLat'];
					$bounds['maxLng'] = (($obj['o_lngMax']>$bounds['maxLng'])||(!isset($bounds['maxLng'])))?$obj['o_lngMax']:$bounds['maxLng'];
					$bounds['maxLat'] = (($obj['o_latMax']>$bounds['maxLat'])||(!isset($bounds['maxLat'])))?$obj['o_latMax']:$bounds['maxLat'];
					$bounds['minLng'] = (($obj['o_lngMin']<$bounds['minLng'])||(!isset($bounds['minLng'])))?$obj['o_lngMin']:$bounds['minLng'];
					}
				}
			$JSStr .= $BoundsStart.$bounds['minLat'].$paramDelim.$bounds['maxLng'].$paramDelim.$bounds['maxLat'].$paramDelim.$bounds['minLng'];                                    
			$JSStr .= $messStart.'['.str_replace("}{", "},{", $messStr).']';                                    
//			$JSStr .= $BoundsStart.$latMin.$paramDelim.$lngMax.$paramDelim.$latMax.$paramDelim.$lngMin;                                    
			echo $JSStr;
			} break;	
		case 'ULObjects': /*2017_02_09 получить объекты слоёв*/
			{
			ini_set('memory_limit', '512M');
			chdir('..//');
			require_once('../classes/config.inc.php');
			require_once("../classes/MySQLi_class.php");
			require_once("../classes/GetContent_class.php");
			require_once("../classes/gis/manage_class.php");	
			require_once("../classes/User_class.php");
			require_once("../classes/ACL_class.php");
			require_once("../classes/Rout_class.php");
			require_once("../classes/gis/manage_class.php");
//			echo 'ULObjects';
//			$fname = '../htdocs/operMess.txt';
//			$fp = fopen($fname, "r");
			$CNT = 		new GetContent;
			$ROUT = new Routine;
			$CONST = $CNT->GetAllConfig();
			$MNG = new Manage;	
			$latMax = $latMin = $lngMax = $lngMin = 0;
			
			$isMng = ($_POST['startStr'] == '*69F727D4F74061CDEB44032B0A7FBE5EE6453E76') ? 1 : 0;					
			
			$JSStr = '';
			$layerDelim = '~~';
			$paramDelim = '##';
			$objStart = '<<obj>>';
			$BoundsStart = '<<bounds>>';
			$messStart = '<<mess>>';
			$objDelim = '||';
			$geoDelim = '^^';
			$paramDelimObj = '%%';
			$paramDelimGeo = '$$';
			$holeDelimGeo = '**';
			$featureDelimGeo = '++';
			$layerList = $MNG->getAllLayersZ($isMng);
			$objList = $MNG->getAllObjStart();
//			$messStr = fread($fp, filesize($fname));
			foreach ($layerList as $layer) {
				$JSStr .= 	$layer['l_id'].$paramDelim.							//0
//					 str_replace('&quot;', '"', $layer['l_name']).$paramDelim.	//1
						$layer['l_name'].$paramDelim.							//1
						$layer['l_info'].$paramDelim.							//2
						$layer['l_public'].$paramDelim.							//3
						$layer['l_system'].$paramDelim.							//4
						$layer['l_parId'].$paramDelim.							//5
						$layer['l_childCnt'].$paramDelim.						//6
						$layer['l_objCnt'].$paramDelim.							//7	
						$layer['l_parStr'].$paramDelim;							//8
				$JSStr .= $layerDelim;
			}
			$JSStr .= $objStart;
			usort($objList, 'sortObjNameAsc');
//			print_r($objList);
			foreach ($objList as $obj) {
				$polyType = 0;
//				$MNG->addObj2Layer($obj['l_id'], $obj['o_id']);
				if((!$obj['o_name'])||(strlen($obj['o_name'])<3))
					$obj['o_name'] = strip_tags(substr($obj['o_info'], strpos($obj['o_info'], '<h2>'), strpos($obj['o_info'], '</h2>')));
				$JSStr 			.= 	$obj['o_id'].$paramDelim.			//0
									$obj['o_name'].$paramDelim.  		
//									str_replace('&quot;', '"', $obj['o_name']).$paramDelim.//1
									$obj['o_type'].$paramDelim.  		//2
									$obj['o_info'].$paramDelim.  		//3
									$obj['o_specParam'].$paramDelim.	//4
									$obj['t_id'].$paramDelim.			//5
//									$obj['o2l_str'].$paramDelim.  		//6
									$obj['o_lStr'].$paramDelim.  		//6
									$obj['o_lParentStr'].$paramDelim;  	//7
						if(is_array($obj['geo'])){						//8
							$hole = $feature = $polyType =  0;
							foreach ($obj['geo']  as $geo) {
								
								if($geo['hole_id']!=$hole){
									$hole = $geo['hole_id'];
									$JSStr .= $holeDelimGeo;
								}
								if($geo['feature_id']!=$feature){
									$feature = $geo['feature_id'];
									$JSStr .= $featureDelimGeo;
								}
/*								$JSStr .= 	$geo['c_id'].$paramDelimGeo.		//0
											$geo['c_lng'].$paramDelimGeo.       //2
											$geo['c_lat'].$paramDelimGeo.       //1
											$geo['o_id'].$geoDelim;            	//3*/
											
								$JSStr .= 	$geo['c_lng'].$paramDelimGeo.       //1
											$geo['c_lat'].$geoDelim;            //0
							/*	if(($geo['c_lat'] > $latMax) && ($geo['c_lat'] < 180 ))
									$latMax = floatval ($geo['c_lat']);
								if(($geo['c_lat'] < $latMin) && ($geo['c_lat'] > -180 ))
									$latMin = floatval ($geo['c_lat']);
								if(($geo['c_lng'] > $lngMax) && ($geo['c_lng'] < 90 ))
									$lngMax = floatval ($geo['c_lng']);
								if(($geo['c_lng'] < $lngMin) && ($geo['c_lng'] > -90 ))
									$lngMin = floatval ($geo['c_lng']);*/
							}
							if(($feature>0)&&($hole>0))
								$polyType = 3;
							elseif(($feature>0)&&($hole==0))
								$polyType = 2;
							elseif(($feature==0)&&($hole>0))
								$polyType = 1;
//							echo 'feature = '.$feature.'; hole = '.$hole.'; polyType = '.$polyType;
							$JSStr .= 	$paramDelim.$polyType;				//9
						}
						elseif($obj['geo']=='remote'){
							$JSStr .= 	'remote';							//8
						}
				$JSStr .= $objDelim;                                    
/*				$boundsObj = $MNG->getObjBounds($obj['o_id']);*/
				if($obj['o_latMin']){
					$bounds['minLat'] = (($obj['o_latMin']<$bounds['minLat'])||(!isset($bounds['minLat'])))?$obj['o_latMin']:$bounds['minLat'];
					$bounds['maxLng'] = (($obj['o_lngMax']>$bounds['maxLng'])||(!isset($bounds['maxLng'])))?$obj['o_lngMax']:$bounds['maxLng'];
					$bounds['maxLat'] = (($obj['o_latMax']>$bounds['maxLat'])||(!isset($bounds['maxLat'])))?$obj['o_latMax']:$bounds['maxLat'];
					$bounds['minLng'] = (($obj['o_lngMin']<$bounds['minLng'])||(!isset($bounds['minLng'])))?$obj['o_lngMin']:$bounds['minLng'];
					}
				}
			$JSStr .= $BoundsStart.$bounds['minLat'].$paramDelim.$bounds['maxLng'].$paramDelim.$bounds['maxLat'].$paramDelim.$bounds['minLng'];                                    
			$JSStr .= $messStart.'['.str_replace("}{", "},{", $messStr).']';                                    
//			$JSStr .= $BoundsStart.$latMin.$paramDelim.$lngMax.$paramDelim.$latMax.$paramDelim.$lngMin;                                    
			echo $JSStr;
			} break;	
	
		case 'emailExist': /*21_05_2008 список улиц начинающихся с...*/
			{
			if(($_SERVER['HTTP_REFERER'] == 'http://'.$_SERVER['SERVER_NAME'].'/registration')&&(trim($_POST['email'])))
				{
				chdir('..//');
//				$login = iconv("UTF-8","windows-1251",trim($_POST['login']));
				require_once('../classes/config.inc.php');			
				require_once("../classes/MySQL_class.php");
				require_once("../classes/User_class.php");
//				require_once("../../classes/ga/TPL_Obj_class.php");	
				$usr = new CurrentUser ($_SERVER[REMOTE_ADDR], 0);
				if(!preg_match("/^([a-zA-Z0-9\.\-_])+@([a-zA-Z0-9\.\-_])+(\.([a-zA-Z0-9])+)+$/", trim($_POST['email'])))
					{
					$res = 0;
					}		
				elseif($usr->getUserParam('user_email', trim($_POST['email']), 'user_id'))
					{
					$res = 2;
					}
				else
					$res = 1;
				echo $res;
				}
			else
				{
				echo -1;
				}
			}
		break;		
		case 'loginExist': /*21_05_2008 список улиц начинающихся с...*/
			{
			if(($_SERVER['HTTP_REFERER'] == 'http://'.$_SERVER['SERVER_NAME'].'/registration')&&(strlen(trim($_POST['login']))>2))
				{
				chdir('..//');
//				$login = iconv("UTF-8","windows-1251",trim($_POST['login']));
				$login = trim($_POST['login']);
				require_once('../classes/config.inc.php');			
				require_once("../classes/MySQL_class.php");
				require_once("../classes/User_class.php");
//				require_once("../../classes/ga/TPL_Obj_class.php");	
				$usr = new CurrentUser ($_SERVER[REMOTE_ADDR], 0);
				if(!$usr->getUserParam('user_name', $login, 'user_id'))
					$res = 1;
				else
					$res = 0;
				echo $res;
				}
			else
				{
				echo -1;
				}
			}
		break;		
		case 'usedAvtoSelect': /**14_05_2012  */
			{
			} break;
		}
	}
elseif((isset($_GET['q']))&&(isset($_GET['type']))) //Подгрузка списков для автоподстановки
	{
//	print_r($_GET);
	$type = trim($_GET['type']);
	$qStr = trim($_GET['q']);
	switch ($type)
		{
		case 'fastSearch': /*2013_09_19 поиск в текстовом индексе*/
			{	

			}
		break;		
			
	
		default: {}
		}
	}
elseif($imgType = trim($_GET['imgType']))	
	{
	switch ($imgType)
		{
		case 'cam': /*15_02_2012 рисуем картинку с камеры*/
			{
			} break;
		}
	}
function genJSONResponse($name, $cmd, $res, $type) 
	{	
//		$resArr = array($name => array())
		$result = '{"'.$name.'" : {"cmd": "'.$cmd.'", "success": ';
		$result .= ($res['error']==1)?'"false"':'"true"';
		$result .= ', "result": ';
		if($res['error']){
			$result .= '"error": {"message" : "'.$res['errorMsg'].'"}';
		} elseif($type == 'set') {
			$result .= '{"id" : "'.$res['last_id'].'"';
			if($res['geoFile']['uploaded'] == 'success')
				$result .= ', "geoFile": "'.$res['geoFile']['newPath'].'"';	
			else 
				$result .= ', "geoFile": "error"';	
			$result .= '}';	
//			$res['geoFile']
//			$res['geoFile'] = array('uploaded' => 'success', 'newPath' => $fileName);
		} elseif($type == 'get') {
			$result .= '"result": '.json_encode($res).'';			
		}
		$result .= '}}'; 
		return $result;
	}	
function sortObjNameAsc($a, $b) 
	{	
//		$aa = trim(htmlspecialchars_decode($a['o_name'], ENT_QUOTES));
		$aa = str_replace(array("'",'"'),'',trim(htmlspecialchars_decode($a['o_name'], ENT_QUOTES)));
		$bb = str_replace(array("'",'"'),'',trim(htmlspecialchars_decode($b['o_name'], ENT_QUOTES)));
//		$bb = trim(htmlspecialchars_decode($b['o_name'], ENT_QUOTES));
/*		echo $aa.' = ';
		echo $bb; 
		echo '
';	*/	
		return strnatcmp($aa, $bb);
	}	
?>	