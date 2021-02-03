<?php
//require_once("main.inc.php");
class Eldis
	{
	var $last_query;
	var $_token;
	var $_url = "https://api.eldis24.ru/api/v2/";
	var $_key = 'ae9b677a902c42f2a013e114eefc719a';
/******************GETTERS***********************************************************************/	
/****tmp func    */	
	
	private function _connectAPI() /*2019_10_15 подключение к   API */
		{
//		$url = "https://api.eldis24.ru/api/v2/"; //Путь к API
//		$key = 'ae9b677a902c42f2a013e114eefc719a';
		$data = array ('login' => 'ushackovsckaya@yandex.ru', 'password' => '3a2c4a94');
		$data = http_build_query($data);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_url.'users/login');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  //Post Fields
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
		curl_setopt($ch, CURLOPT_HEADER, true);	
		$headers = [
			"key: ".$this->_key,
			"Content-Type: application/x-www-form-urlencoded",
			"Content-Length: ".strlen($data),
		];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$server_output = curl_exec ($ch);
//		print_r($server_output);
		curl_close ($ch);
		$pattern = '/access_token=(.+);\s+expires/';
		preg_match($pattern, $server_output, $out);
		$this->_token=$out[1];
		}	
	private function _getDevListAPI() /*2019_07_03 выбор  объекта */
		{
		$this->_connectAPI();
		$ch = curl_init();
		$data = array ();
		$data = http_build_query($data);
		curl_setopt($ch, CURLOPT_URL, $this->_url.'objects/list');
//		curl_setopt($ch, CURLOPT_URL, $url.'objectParameters/list');
//		curl_setopt($ch, CURLOPT_URL, $url.'tv/listForDevelopment');
//		curl_setopt($ch, CURLOPT_URL, $url.'objects/get?id=ec8f7176-5095-4392-86d8-00adaa4c9b58');
//		curl_setopt($ch, CURLOPT_URL, $url.'data/normalized?id=caa2654f-1973-4b9f-84bd-534c87d4f116&startDate=28.09.2019&endDate=30.09.2019&typeDataCode=30003&dateType=date');
//		curl_setopt($ch, CURLOPT_URL, $url.'data/normalized?id=c8213342-e8ea-432a-97dd-a58c56e7e969&startDate=29.09.2019&endDate=30.09.2019&typeDataCode=30003&dateType=date');
//		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  //Post Fields
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	 	
//		curl_setopt($ch, CURLOPT_HEADER, true);
	
		$headers2 = [
			"key: ".$this->_key,
			"Content-Type: application/x-www-form-urlencoded",
			"Content-Length: ".strlen($data),
			"Cookie: access_token=".$this->_token
		];
//		echo $this->_token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers2);
		$server_output = curl_exec ($ch);
//		echo 'server_output is '.$server_output;
//		print_r(curl_getinfo ($ch));
		curl_close ($ch);
//		print_r($headers2);
//		print_r($server_output);
		return($server_output);
		}
	private function _getDevMetricsAPI($urlPar) /*2019_07_03 выбор  объекта */
		{
		$this->_connectAPI();
		$ch = curl_init();
		$data = array ();
		$data = http_build_query($data);
//		curl_setopt($ch, CURLOPT_URL, $this->_url.'data/normalized?id='.$id.'&startDate='.$start.'&endDate='.$end.'&typeDataCode='.$typeDataCode.'&dateType='.$dateType.'');
		curl_setopt($ch, CURLOPT_URL, $this->_url.'data/normalized?'.$urlPar);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	 	
		$headers2 = [
			"key: ".$this->_key,
			"Content-Type: application/x-www-form-urlencoded",
			"Content-Length: ".strlen($data),
			"Cookie: access_token=".$this->_token
		];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers2);
		$server_output = curl_exec ($ch);
//		print_r(curl_getinfo ($ch));
		curl_close ($ch);
		return($server_output);
		}
	private function _getDevMetricsAPIRow($id, $start, $end, $typeDataCode, $dateType) /*2019_07_03 выбор  объекта */
		{
		$this->_connectAPI();
		$ch = curl_init();
		$data = array ();
		$data = http_build_query($data);
		curl_setopt($ch, CURLOPT_URL, $this->_url.'data/rowData?id='.$id.'&startDate='.$start.'&endDate='.$end.'&typeDataCode='.$typeDataCode.'');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	 	
		$headers2 = [
			"key: ".$this->_key,
			"Content-Type: application/x-www-form-urlencoded",
			"Content-Length: ".strlen($data),
			"Cookie: access_token=".$this->_token
		];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers2);
		$server_output = curl_exec ($ch);
//		print_r(curl_getinfo ($ch));
		curl_close ($ch);
		return($server_output);
		}
	public function getDevMetricsAPI($param) /*2019_07_03 выбор  объекта */
		{	
		$urlPar = http_build_query($param);
//		$list = $this->_getDevMetricsAPI($id, $start, $end, $typeDataCode, $dateType);
		$list = $this->_getDevMetricsAPI($urlPar);
		return $list;
		}
	public function getDevAPI() /*2019_07_03 выбор  объекта */
		{		
		$list = $this->_getDevListAPI();
		return $list;
		}	

	public function getMetricList($dvcId, $startDate = 0, $startCounter = 0) /*2019_07_03 выбор  объекта */
	{
		$ROUT = new Routine;
		$startDateAdd = ($startDate)?' AND dateUp >= "'.$startDate.'" ' : '';
		$startCounterAdd = ($startCounter)?' AND eldis_metriks.id >= "'.$startCounter.'" ' : '';
		$LNK= new DBLink;	
		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$queryMetr = 'SELECT *  FROM `eldis_metriks`  where device_id = '.$dvcId.$startDateAdd.$startCounterAdd.' ORDER BY dateUp ';	
		$LNK->Query($queryMetr);
		if($LNK->GetNumRows()){
			$metrikList = $LNK->GetData(0, true);
			$metrikRows= array();
			foreach ($metrikList as $metrikRow) {
				$metrik = array();
				foreach ($metrikRow as $name => $val ) {
					if(($val!='')&&($this->metrics[$name])){
						$metrik[] = array(	'name' => $this->metrics[$name], 
											'nameAPI' => $name, 
											'value' => $val, 
											'measureUnit' => $ROUT->getStrPrt($this->metrics[$name], ', ', 1),
											'recordId' => $metrikRow['id'] ,
											'dateUp' => $metrikRow['dateUp'] ,
											'dateUp_ru' => $ROUT->GetRusDataStr($metrikRow['dateUp'], false, true, true),
											'dvcId' => $metrikRow['device_id'] ,
											'alarmBoundsLow' => null,
											'alarmBoundsHigh' => null,
											'alarmDelay' => 0);
					}
//							print_r($dvc);
				}
				$metrikRows[] = $metrik;
			}
			$ret = $metrikRows;
		} else {
			$ret = 0;
		}
		return $ret;		
	}
	public function getMetricLast($dvcId) /*2019_07_03 выбор  объекта */
	{
		$ROUT = new Routine;
		$startAdd = ($start)?' AND dateUp >= "'.$start.'" ' : '';
		$LNK= new DBLink;	
		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$queryMetr = 'SELECT *  FROM `eldis_metriks`  where device_id = '.$dvcId.$startAdd.' ORDER BY dateUp DESC';	
		$LNK->Query($queryMetr);
		if($LNK->GetNumRows()){
			$metrikRow = $LNK->GetData(0, false);
			$metrik = array();
			foreach ($metrikRow as $name => $val ) {
				if(($val!='')&&($this->metrics[$name])){
					$metrik[] = array(	'name' => $this->metrics[$name], 
										'nameAPI' => $name, 
										'value' => $val, 
										'measureUnit' => $ROUT->getStrPrt($this->metrics[$name], ', ', 1),
										'dateUp' => $metrikRow['dateUp'] ,
										'dateUp_ru' => $ROUT->GetRusDataStr($metrikRow['dateUp'], false, true, true),
										'dvcId' => $metrikRow['device_id'] ,
										'alarmBoundsLow' => null,
										'alarmBoundsHigh' => null,
										'alarmDelay' => 0);
				}
//							print_r($dvc);
			}
			$ret = $metrik;
		} else {
			$ret = 0;
		}
		return $ret;		
	}
	public function getDevicesAnalitic($start) /*2019_07_03 выбор  объекта */
	{
		$startAdd = ($start)?' AND dateUp >= "'.$start.'" ' : '';
		$LNK= new DBLink;	
		$retArr = true;
		$query = 'SELECT *, eldis_devices.id AS deviceId, gis_jkhObj.id as objId FROM `eldis_devices`, `gis_jkhObj` where eldis_devices.id_objectJkh = gis_jkhObj.id ORDER BY eldis_devices.address';	
//		$query .= ' ORDER BY dateUp DESC ';	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$devList = $LNK->GetData(0, $retArr);
			foreach ($devList as $dvc){
				if($dvc['dateLastMetriks'] != ''){
					$queryMetr = 'SELECT *  FROM `eldis_metriks`  where device_id = '.$dvc['deviceId'].$startAdd.' ORDER BY dateUp';	
					$LNK->Query($queryMetr);
					$metriks = $LNK->GetData(0, $retArr);
				} else {
					$metriks = array();
				}
				$dvc['metriks'] = $metriks;
				$ret[]  = (object) $dvc;
			}						
//			$ret = $devList;
		}
		else 
			$ret = 0;
		return $ret;
/*		if($id){
			$query .= ' WHERE id = \''.$id.'\' ';	
			$retArr = false;
		}		*/
		
	}
	public function getDevicesList($start) /*2019_07_03 выбор  объекта */
	{
		$startAdd = ($start)?' AND dateUp >= "'.$start.'" ' : '';
		$LNK= new DBLink;	
		$retArr = true;
		$query = 'SELECT *, eldis_devices.id AS deviceId, gis_jkhObj.id as objId FROM `eldis_devices`, `gis_jkhObj` where eldis_devices.id_objectJkh = gis_jkhObj.id ORDER BY eldis_devices.address';	
//		$query .= ' ORDER BY dateUp DESC ';	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, true);
		}
		else 
			$ret = 0;
		return $ret;
/*		if($id){
			$query .= ' WHERE id = \''.$id.'\' ';	
			$retArr = false;
		}		*/
		
	}
	public function getDevice($id=0) /*2019_07_03 выбор  объекта */
		{		
		$LNK= new DBLink;	
		$retArr = true;
		$query = 'SELECT * FROM `eldis_devices` ';	
		if($id){
			$query .= ' WHERE id = \''.$id.'\' ';	
			$retArr = false;
		}
		$query .= ' ORDER BY dateUp DESC ';	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, $retArr);
			}
		else 
			$ret = 0;
		return $ret;
		}	
	public function getMetriksLabels($id=0) /*2019_07_03 выбор  объекта */
		{		

		return $this->metrics;
		}
/******************SETTERS***********************************************************************/	
	var $metrics = array(
		"dateUp" =>  "Дата получения показателей" , 
		"pointType" =>  "Тип точки учёта" , 
		"typeDataCode" =>  "Тип данных (30003 - накопленное за час)" , 
		"date" =>  "Дата архива, приборная" , 
		"dateOnEndOfArchive" =>  "Дата архива на конец часа" , 
		"dateWithTimeBias" =>  "Дата архива на конец часа с учётом времени на приборе и часового пояса" , 
		"V" =>  "Объём, м3" , 
		"V_Total" =>  "Накопленное значение объёма с момента сброса, м3" , 
		"M" =>  "Масса, т" , 
		"M_Total" =>  "Накопленное значение массы с момента сброса, т" , 
		"TGmax" =>  "Время, в течение которого фактический массовый расход был выше максимального нормированного значения для средства измерения, ч" , 
		"TGmin" =>  "Время, в течение которого фактический массовый расход был меньше допустимого минимального нормированного значения для средства измерения, ч" , 
		"TFault" =>  "Время функционального отказа, ч" , 
		"Toff" =>  "Время отключения питания, ч" , 
		"TOtherNS" =>  "Суммарное время других нештатных ситуаций, ч" , 
		"QntHIP" =>  "Время нормальной работы, ч" , 
		"QntHIP_Total" =>  "Накопленное значение времени нормальной работы с момента сброса, ч" , 
		"QntP" =>  "Время отсутствия счёта, ч" , 
		"QntP_Total" =>  "Накопленное значение времени отсутствия счёта с момента сброса, ч" , 
		"ns" =>  "Наличие нештатной ситуации" , 
		"empty" =>  "Указывает на пустой архив" , 
		"P" =>  "Давление, МПа" , 
		"Q1" =>  "Количество тепловой энергии на подающем трубопроводе, Гкал" , 
		"Q1_Total" =>  "Накопленное значение потребления тепловой энергии на подающем трубопроводе с момента сброса, Гкал" , 
		"Q2" =>  "Количество тепловой энергии на обратном трубопроводе, Гкал" , 
		"Q2_Total" =>  "Накопленное значение потребления тепловой энергии на обратном трубопроводе с момента сброса, Гкал" , 
		"t1" =>  "Температура на подающем трубопроводе, °C" , 
		"t2" =>  "Температура на обратном трубопроводе, °C" , 
		"V1" =>  "Объём на подающем трубопроводе, м3" , 
		"V1_Total" =>  "Накопленное значение объёма на подающем трубопроводе с момента сброса, м3" , 
		"V2" =>  "Объём на обратном трубопроводе, м3" , 
		"V2_Total" =>  "Накопленное значение объёма на обратном трубопроводе с момента сброса, м3" , 
		"M1" =>  "Масса на подающем трубопроводе, т" , 
		"M1_Total" =>  "Накопленное значение массы на подающем трубопроводе с момента сброса, т" , 
		"M2" =>  "Масса на обратном трубопроводе, т" , 
		"M2_Total" =>  "Накопленное значение массы на обратном трубопроводе с момента сброса, т" , 
		"P1" =>  "Давление на подающем трубопроводе, МПа" , 
		"P2" =>  "Давление на обратном трубопроводе, МПа" , 
		"Q" =>  "Количество тепловой энергии по всей системе, Гкал" , 
		"Q_Total" =>  "Накопленное значение потребления тепловой энергии по всей системе с момента сброса, Гкал" , 
		"dt" =>  "Разность температур, °C" , 
		"dV" =>  "Разность объёмов, м3" , 
		"dM" =>  "Разность масс, т" , 
		"tcw" =>  "Температура холодной воды, °C" , 
		"Pcw" =>  "Давление холодной воды, МПа" , 
		"Tdt" =>  "Время, в течение которого разность температур на подающем и обратном трубопроводах была меньше допустимой нормированной разности температур для теплосчетчика, ч" , 
		"t3" =>  "Температура на подпитке, °C" , 
		"V3" =>  "Объём на подпитке, м3" , 
		"V3_Total" =>  "Накопленное значение объёма на подпитке с момента сброса, м3" , 
		"M3" =>  "Масса на подпитке, т" , 
		"M3_Total" =>  "Накопленное значение массы на подпитке с момента сброса, т" , 
		"P3" =>  "Давление на подпитке, МПа" , 
		"ta" =>  "Температура наружного воздуха, °C" , 
		"Ap1" =>  "Активная энергия прямого направления по тарифу 1, кВт*ч" , 
		"Ap1_Total" =>  "Накопленное значение активной энергии прямого направления по тарифу 1 с момента сброса, кВт*ч" , 
		"Ap2" =>  "Активная энергия прямого направления по тарифу 2, кВт*ч" , 
		"Ap2_Total" =>  "Накопленное значение активной энергии прямого направления по тарифу 2 с момента сброса, кВт*ч" , 
		"Ap3" =>  "Активная энергия прямого направления по тарифу 3, кВт*ч" , 
		"Ap3_Total" =>  "Накопленное значение активной энергии прямого направления по тарифу 3 с момента сброса, кВт*ч" , 
		"Ap4" =>  "Активная энергия прямого направления по тарифу 4, кВт*ч" , 
		"Ap4_Total" =>  "Накопленное значение активной энергии прямого направления по тарифу 4 с момента сброса, кВт*ч" , 
		"Am1" =>  "Активная энергия обратного направления по тарифу 1, кВт*ч" , 
		"Am1_Total" =>  "Накопленное значение активной энергии обратного направления по тарифу 1 с момента сброса, кВт*ч" , 
		"Am2" =>  "Активная энергия обратного направления по тарифу 2, кВт*ч" , 
		"Am2_Total" =>  "Накопленное значение активной энергии обратного направления по тарифу 2 с момента сброса, кВт*ч" , 
		"Am3" =>  "Активная энергия обратного направления по тарифу 3, кВт*ч" , 
		"Am3_Total" =>  "Накопленное значение активной энергии обратного направления по тарифу 3 с момента сброса, кВт*ч" , 
		"Am4" =>  "Активная энергия обратного направления по тарифу 4, кВт*ч" , 
		"Am4_Total" =>  "Накопленное значение активной энергии обратного направления по тарифу 4 с момента сброса, кВт*ч" , 
		"Rp1" =>  "Реактивная энергия прямого направления по тарифу 1, кВар*ч" , 
		"Rp1_Total" =>  "Накопленное значение реактивной энергии прямого направления по тарифу 1 с момента сброса, кВар*ч" , 
		"Rp2" =>  "Реактивная энергия прямого направления по тарифу 2, кВар*ч" , 
		"Rp2_Total" =>  "Накопленное значение реактивной энергии прямого направления по тарифу 2 с момента сброса, кВар*ч" , 
		"Rp3" =>  "Реактивная энергия прямого направления по тарифу 3, кВар*ч" , 
		"Rp3_Total" =>  "Накопленное значение реактивной энергии прямого направления по тарифу 3 с момента сброса, кВар*ч" , 
		"Rp4" =>  "Реактивная энергия прямого направления по тарифу 4, кВар*ч" , 
		"Rp4_Total" =>  "Накопленное значение реактивной энергии прямого направления по тарифу 4 с момента сброса, кВар*ч" , 
		"Rm1" =>  "Реактивная энергия обратного направления по тарифу 1, кВар*ч" , 
		"Rm1_Total" =>  "Накопленное значение реактивной энергии обратного направления по тарифу 1 с момента сброса, кВар*ч" , 
		"Rm2" =>  "Реактивная энергия обратного направления по тарифу 2, кВар*ч" , 
		"Rm2_Total" =>  "Накопленное значение реактивной энергии обратного направления по тарифу 2 с момента сброса, кВар*ч" , 
		"Rm3" =>  "Реактивная энергия обратного направления по тарифу 3, кВар*ч" , 
		"Rm3_Total" =>  "Накопленное значение реактивной энергии обратного направления по тарифу 3 с момента сброса, кВар*ч" , 
		"Rm4" =>  "Реактивная энергия обратного направления по тарифу 4, кВар*ч" , 
		"Rm4_Total" =>  "Накопленное значение реактивной энергии обратного направления по тарифу 4 с момента сброса, кВар*ч" , 
		"Ap" =>  "Сумма тарифов активной энергии прямого направления, кВт*ч" , 
		"Ap_Total" =>  "Сумма тарифов накопленных значений активной энергии прямого направления с момента сброса, кВт*ч" , 
		"Am" =>  "Сумма тарифов активной энергии обратного направления, кВар*ч" , 
		"Am_Total" =>  "Сумма тарифов накопленных значений активной энергии обратного направления с момента сброса, кВт*ч" , 
		"Rp" =>  "Сумма тарифов реактивной энергии прямого направления, кВар*ч" , 
		"Rp_Total" =>  "Сумма тарифов накопленных значений реактивной энергии прямого направления с момента сброса, кВар*ч" , 
		"Rm" =>  "Сумма тарифов реактивной энергии обратного направления, кВар*ч" , 
		"Rm_Total" =>  "Сумма тарифов накопленных значений реактивной энергии обратного направления с момента сброса, кВар*ч" , 
		"Pp" =>  "Активная мощность прямого направления, кВт" , 
		"Pm" =>  "Активная мощность обратного направления, кВт" , 
		"Qp" =>  "Реактивная мощность прямого направления, кВар" , 
		"Qm" =>  "Реактивная мощность обратного направления, кВар" , 
		"S1" =>  "Полная мощность по фазе 1, кВА" , 
		"S2" =>  "Полная мощность по фазе 2, кВА" , 
		"S3" =>  "Полная мощность по фазе 3, кВА" , 
		"Q3" =>  "Реактивная мощность по фазе 3, кВар" , 
		"U1" =>  "Напряжение по фазе 1, В" , 
		"U2" =>  "Напряжение по фазе 2, В" , 
		"U3" =>  "Напряжение по фазе 3, В" , 
		"I1" =>  "Сила тока по фазе 1, А" , 
		"I2" =>  "Сила тока по фазе 2, А" , 
		"I3" =>  "Сила тока по фазе 3, А" , 
		"S" =>  "Сумма фаз полной мощности, кВА" , 
		"U" =>  "Сумма фаз напряжения, В" , 
		"I" =>  "Сумма фаз силы тока, А" , 
		"F" =>  "Частота тока, Гц" , 
		"CosPhi1" =>  "Коэффициент мощности по фазе 1" , 
		"CosPhi2" =>  "Коэффициент мощности по фазе 2" , 
		"CosPhi3" =>  "Коэффициент мощности по фазе 3" , 
		"PhiU12" =>  "Угол между фазными напряжениями фазы 1 и 2" , 
		"PhiU13" =>  "Угол между фазными напряжениями фазы 1 и 3" , 
		"PhiU23" =>  "Угол между фазными напряжениями фазы 2 и 3" , 
		"t" =>  "Температура, °C" , 
		"Vp" =>  "Рабочий объём, м3" , 
		"Vp_Total" =>  "Накопленное значение рабочего объёма с момента сброса, м3" , 
		"Vc" =>  "Стандартный объём, м3" , 
		"Vc_Total" =>  "Накопленное значение стандартного объёма с момента сброса, м3" );
/******************TEMPLATE***********************************************************************/	
	}
?>