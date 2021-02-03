<?php
//require_once("main.inc.php");
class Simplight
	{
	var $last_query; 
	var $_token;
	var $_url = "http://83.234.93.231:8080/sse/live";
	var $_headers = [
			"Host: 83.234.93.231:8080",
			"User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:70.0) Gecko/20100101 Firefox/70.0",
			"Accept: */*",
			"Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
			"Accept-Encoding: gzip, deflate",
			"Connection: keep-alive",
			"Referer: http://83.234.93.231:8080/live",
			"Pragma: no-cache",
			"Cache-Control: no-cache" ];
	var $_metrics;	
	var $_dvcId;	
	var $_counter;	
	var $_counterNext;	
    function __construct($dvcId){
		$this->_dvcId = $dvcId;
		$this->_counter = $this->_getCounter();		
		$this->_counterNext = $this->_counter + 1;		
		error_log('[INFO] - dvcId = : "'.$this->_dvcId.'";  counter is: "'.$this->_counter.'";  counterNext is: "'.$this->_counterNext.'; ', 0);
		}
	private function _getCounter(){ /*2019_07_03 выбор  объекта */
		$sql = "SELECT * FROM `pu_devices` WHERE id = '".$this->_dvcId."'";
		$count = 0;
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$LNK->Query($sql);
		$res = $LNK->GetData(0, false);
		return($res['counter']);
	}
	private function _upDvcCounter(){ /*2019_07_03 выбор  объекта */
		$sql = "UPDATE pu_devices SET counter = '".$this->_counterNext."' WHERE id = '".$this->_dvcId."'";
		$count = 0;
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$LNK->Query($sql);
		/*$res = $LNK->GetData(0, true);
		if($LNK->error){
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else{
			$ret['error'] = 0;
			$ret['last_id'][] = $LNK->last_id;// = $LNK->error_string;
		}					
		return $ret;*/
	}	
	private function _upMetricCounter($metrikId){ /*2019_07_03 выбор  объекта */
		$sql = "UPDATE pu_metrics` SET counter = '".$this->_counterNext."'  WHERE id = '".$metrikId."'";
		$count = 0;
		$LNK= new DBLink;	
		$LNK->Query($sql);
		$res = $LNK->GetData(0, true);
		if($LNK->error){
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else{
			$ret['error'] = 0;
			$ret['last_id'][] = $LNK->last_id;// = $LNK->error_string;
		}					
		return $ret;
	}
	public function getValuesFromDvc(){ /*2019_07_03 выбор  объекта */
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_headers);
		curl_setopt($ch, CURLOPT_URL, $this->_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
//		error_log('[INFO] CURL_EXEC	', 0); 
		$server_output = curl_exec ($ch);
//		error_log('[INFO] CURL_OUT	'.$server_output, 0); 
		$pattern = '/data:(.+)/';
		preg_match($pattern, $server_output, $out);
		return json_decode($out[1]);		
	}
	public function getDvcMetriks($assoc = false){ /*2019_07_03 выбор  объекта */	
		$LNK= new DBLink;	
		$retArr = true;
		$query = 'SELECT * FROM `pu_metrics` WHERE dvcId = "'.$this->_dvcId.'"';	
//		$query .= ' ORDER BY dateUp DESC ';	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$devList = $LNK->GetData(0, $retArr);
			if($assoc){
				foreach ($devList as $dvc){
					$ret[$dvc['nativeId']]  = $dvc;
				}
			} else 			
				$ret = $devList;
		}
		else 
			$ret = 0;
		$this->_metrics = $ret;
		return $ret;		
	}	
	public function getValuesFromDB($start=0){ /*2019_07_03 выбор  объекта */
	
		$LNK= new DBLink;	
		$retArr = true;
		$startAdd = ($start)?' AND pu_metrikValues.dateUp > '.$start.' ':'';
		$query = 'SELECT * FROM `pu_metrics`, `pu_metrikValues` WHERE 
			pu_metrics.id = pu_metrikValues.metrikId AND pu_metrics.dvcId = '.$this->_dvcId.' '.$startAdd.' ORDER BY pu_metrikValues.dateUp DESC , pu_metrics.id ';	
//		$query .= ' ORDER BY dateUp DESC ';	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$metrikList = array();
			$devList = $LNK->GetData(0, true);
			$cnt = $rowCnt = 0;
			$out = $outHeader='';
			foreach ($devList as $dvc){
				if(in_array($dvc['metrikId'], $metrikList)){
					$out .= PHP_EOL;
					$cnt = 0;
					$metrikList = array();
					$rowCnt++;
				}
				if(!$rowCnt){
					$outHeader .= ($outHeader=='')?'Дата измерения;':';';
					$outHeader .= $dvc['name'].' ('.$dvc['measureUnit'].')';
				}
				$out .= ($cnt)?';':$dvc['dateUp'].';';
				$out .= $dvc['value'];
//				$ret[$dvc['nativeId']]  = $dvc;
				$metrikList[] = $dvc['metrikId'];
				$cnt ++;
			}
			$ret = $outHeader.PHP_EOL.$out;
		}
		else 
			$ret = 0;
		$this->_metrics = $ret;
		return $ret;		
	}
	public function getValuesFromDBList($startDate = 0, $startCounter = 0){ /*2019_07_03 выбор  объекта */
	
		$LNK= new DBLink;	
		$retArr = true;
		$startDateAdd = ($startDate)?' AND `pu_metrikValues`.`dateUp` > "'.$startDate.'" ' : '';
		$startCounterAdd = ($startCounter)?' AND `pu_metrikValues`.`counter` > "'.$startCounter.'" ' : '';
//		$query = 'SELECT * FROM `pu_metrics`, `pu_metrikValues` WHERE pu_metrics.id = pu_metrikValues.metrikId AND pu_metrics.dvcId = '.$this->_dvcId.$startDateAdd.$startCounterAdd' ORDER BY pu_metrikValues.counter,  pu_metrics.id ASC ';	
		
		$query = 'SELECT *, `pu_metrikValues`.`counter` as recordId, `pu_metrikValues`.`dateUp` as dateOfValue FROM `pu_metrics`, `pu_metrikValues` 
					WHERE pu_metrics.id = pu_metrikValues.metrikId AND pu_metrics.dvcId = '.$this->_dvcId.$startDateAdd.$startCounterAdd.
					' ORDER BY `pu_metrikValues`.`counter`, pu_metrics.id ASC ';
//		$query .= ' ORDER BY dateUp DESC ';	
		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$mList = $LNK->GetData(0, true);
			$recId = 0;
			$metrikRows = array();
			$metrik= array();
			foreach ($mList as $element){
//				print_r($element);
				if($element['recordId']>$recId){
					if(count($metrik)>1)
						$metrikRows[] = $metrik;						
					$metrik= array();
					$recId = $element['recordId'];
				}
				$metrik[]  = $element;
			}
			$metrikRows[] = $metrik;						
			$ret = $metrikRows;
		}
		else 
			$ret = 0;
		return $ret;		
	}		
	public function getValuesFromDBLast($assoc = 0){ /*2019_07_03 выбор  объекта */
	
		$LNK= new DBLink;	
		$retArr = true;
		$query = 'SELECT * FROM `pu_metrics`, `pu_metrikValues` WHERE pu_metrics.id = pu_metrikValues.metrikId AND pu_metrics.dvcId = '.$this->_dvcId.' AND pu_metrikValues.counter = '.$this->_counter.' ORDER BY pu_metrics.id ASC ';	
//		$query .= ' ORDER BY dateUp DESC ';	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$devList = $LNK->GetData(0, $retArr);
			if($assoc){
				foreach ($devList as $dvc){
					$ret[$dvc['nativeId']]  = $dvc;
				}
			} else 			
				$ret = $devList;
		}
		else 
			$ret = 0;
		$this->_metrics = $ret;
		return $ret;		
	}	
/*	public function getValuesFromDBLastParam($dvcId, $start){
	
		$LNK= new DBLink;	
		$retArr = true;
		$query = 'SELECT * FROM `pu_metrics`, `pu_metrikValues` WHERE pu_metrics.id = pu_metrikValues.metrikId AND pu_metrics.dvcId = '.$dvcId.' AND pu_metrikValues.counter = '.$this->_counter.' ORDER BY `pu_metrics.id` ASC ';	
//		$query .= ' ORDER BY dateUp DESC ';	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$devList = $LNK->GetData(0, $retArr);
			if($assoc){
				foreach ($devList as $dvc){
					$ret[$dvc['nativeId']]  = $dvc;
				}
			} else 			
				$ret = $devList;
		}
		else 
			$ret = 0;
		$this->_metrics = $ret;
		return $ret;		
	}*/
	public function saveValues($valList, $metrList){ /*2019_07_03 выбор  объекта */
		$this->_dvcId;
		$sql = "INSERT INTO `pu_metrikValues` (`puId`, `metrikId`, `value`, `counter`)  VALUES ";
		$sqlUp = "UPDATE  `pu_metrics` SET counter = '".$this->_counterNext."' WHERE ";
		$count = 0;
		foreach ($valList as $val) {
			if($count) {
				$sql .= ', ';	
				$sqlUp .= ' OR ';	
			}
			$sql .= '(\'1\', \''.$metrList[$val->ID]['id'].'\', \''.$val->value.'\', \''.$this->_counterNext.'\')';
			$sqlUp .= ' id = '.$metrList[$val->ID]['id'].' ';
			$count ++;
		}
		if($count){
			$LNK= new DBLink;	
//			$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
			$LNK->Query($sql);
			$LNK->Query($sqlUp);
			$this->_upDvcCounter();	
		}
		
	}
	public function getDevices($start = false){ /*2019_07_03 выбор  объекта */
		$startAdd = ($start)?' AND dateUp >= "'.$start.'" ' : '';
		$LNK= new DBLink;	
		$retArr = true;
		$query = 'SELECT *, pu_devices.id AS deviceId, gis_jkhObj.id as objId FROM `pu_devices`, `gis_jkhObj` where pu_devices.id_objectJkh = gis_jkhObj.id AND pu_devices.id = '.$this->_dvcId.' ';	
		$queryMetr = 'SELECT *, pu_metrics.id AS metrId, pu_metrikValues.value AS valueId FROM `pu_metrics`, `pu_metrikValues` 
				WHERE pu_metrics.id = pu_metrikValues.metrikId AND pu_metrics.dvcId = '.$this->_dvcId.' AND pu_metrikValues.counter = '.$this->_counter.' ORDER BY pu_metrics.id ASC ';	
//		$query .= ' ORDER BY dateUp DESC ';	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$dvc = $LNK->GetData(0, $retArr);
			$LNK->Query($queryMetr);
			$dvc['metriks'] = $LNK->GetData(0, $retArr);
			error_log('[INFO] - dvc = : "'.print_r($dvc, true).'" ', 0);
			
			$ret  = (object) $dvc;
		}
		else 
			$ret = 0;
		return $ret;

	}
	public function getAllDevices($start = false){ /*2019_07_03 выбор  объекта */
		$startAdd = ($start)?' AND dateUp >= "'.$start.'" ' : '';
		$LNK= new DBLink;	
		$ROUT = new Routine;
		$retArr = true;
		$query = 'SELECT *, pu_devices.id AS deviceId, gis_jkhObj.id as objId FROM `pu_devices`, `gis_jkhObj` where pu_devices.id_objectJkh = gis_jkhObj.id ';	
//		$query .= ' ORDER BY dateUp DESC ';	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$dvc = $LNK->GetData(0, $retArr);
			$cnt = 0;
			foreach ($dvc as $val) {
				$dvc[$cnt]['dateUp_ru'] = $ROUT->GetRusDataStr($val['dateUp'], false, true, true);
				
				$queryMetr = 'SELECT *, pu_metrics.id AS metrId, pu_metrikValues.value AS valueId FROM `pu_metrics`, `pu_metrikValues` 
						WHERE pu_metrics.id = pu_metrikValues.metrikId AND pu_metrics.dvcId = '.$val['deviceId'].' AND pu_metrikValues.counter = '.$val['counter'].' ORDER BY pu_metrics.id ASC ';	
				$LNK->Query($queryMetr);
				$dvc[$cnt]['metriks'] = $LNK->GetData(0, true);
				$cnt ++;
			}
//		error_log('[INFO] - dvc = : "'.print_r($dvc, true).'" ', 0);
//		$ret  = (object) $dvc;
		$ret  =  $dvc;
		}
		else 
			$ret = 0;
		return $ret;

	}	
	public function getDevicesList($start = false){ /*2019_07_03 выбор  объекта */
		$startAdd = ($start)?' AND dateUp >= "'.$start.'" ' : '';
		$LNK= new DBLink;	
		$ROUT = new Routine;
		$retArr = true;
		$query = 'SELECT *, pu_devices.id AS deviceId, gis_jkhObj.id as objId FROM `pu_devices`, `gis_jkhObj` where pu_devices.id_objectJkh = gis_jkhObj.id ';	
//		$query .= ' ORDER BY dateUp DESC ';	
		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$dvc = $LNK->GetData(0, true);
			
//		error_log('[INFO] - dvc = : "'.print_r($dvc, true).'" ', 0);
//		$ret  = (object) $dvc;
			$ret  =  $dvc;
		}
		else 
			$ret = 0;
		return $ret;

	}
	
/******************GETTERS***********************************************************************/	
/****tmp func    */	
	}
?>