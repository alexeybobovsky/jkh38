<?php
//require_once("main.inc.php");
class messengerRoutine
	{
	private $uri;
	private $reqdata;
    function __construct(/*$, $request=0*/){
			$this->uri = 'https://jkh-38.club/tg/?bName=jkh&sender=1';
	}	
	private function _sendCommand(){
		$context_options = array (
				'http' => array (
					'method' => 'POST',
					 'header'=> "Content-type: application/x-www-form-urlencoded\r\n"
						. "Content-Length: " . strlen($this->reqdata) . "\r\n", 
					'content' => $this->reqdata
					)
				);				
		return json_decode(file_get_contents($this->uri, false, stream_context_create($context_options)));	
	}
	public function sendAlarm($type, $evntId,  $distId){
		$clientList = $this->getClientOfDistr('tg', $distId, true); 
		switch($type){
			case 'comment':{
				$textStr = 'Внимание! Отправлен комментарий к  оперативному донесению о технологическом нарушении в зоне Вашей  ответственности.';							
			} break;				
			case 'message':{
				$textStr = 'Внимание! Получено новое оперативное донесение о возникновении технологического нарушения в зоне Вашей  ответственности.';								
			} break;
			default: {	
				$textStr 	= 'Внимание! Что-то изменилось';
			}
		}		
		$this->reqdata = http_build_query(array('type' => $type, 'evntId' => $evntId, 'text' => $textStr, 'usrList' => $clientList));
		error_log('[INFO] messengerRoutine: reqdata is '.$this->reqdata, 0); 
		return $this->_sendCommand();
	}

/******************TEMPLATE***********************************************************************/	
/******************GETTERS***********************************************************************/	
	function getClientOfDistr($platform, $id, $simple=false) /*2019_04_01 забрать все  оперативные сообщения*/
		{
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, 1);	 

		$query = 'SELECT * FROM `gis_usr2distr`, `usersBot` where   usersBot.userId=gis_usr2distr.user_id AND	platform  = \''.$platform.'\' AND  (distr_id = '.$id.' OR distr_id=\'1\') ';
		$LNK->Query($query);
		if($LNK->GetNumRows())			
			{		
			if($simple){
				foreach ($LNK->GetData(0, true) as $msg) {
					$ret[] = $msg['clientId'];
				}					

			}else
				$ret = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
		return $ret;						
		}		
	function getClient($platform, $id) /*2019_04_01 забрать все  оперативные сообщения*/
		{
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, 1);	 

		$query = 'SELECT * FROM `usersBot` where   	platform  = \''.$platform.'\' AND  clientId = '.$id.'';
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, false);
			}
		else 
			$ret = 0;
		return $ret;						
		}		
	function getClientFull($platform, $id) /*2019_04_01 забрать все  оперативные сообщения*/
		{
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, 1);	 
		$query = 'SELECT * FROM `usersBot`, `users`, `gis_usr2distr`  where  usersBot.userId= users.user_id AND	usersBot.userId= gis_usr2distr.user_id AND platform  = \''.$platform.'\' AND  clientId = '.$id.'';
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, false);
			}
		else 
			$ret = 0;
		return $ret;						
		}		


/******************SETTERS***********************************************************************/	
	function addClient($platform, $id) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$queryAdd = '';
		$queryAdd = 'INSERT into `usersBot` SET   	clientId  = \''.$id.'\' ,  	platform  = \''.$platform.'\' , timeCreate = FROM_UNIXTIME( \''.time().'\')';		
//		error_log('[INFO SQL] QUERY: result is '.$queryAdd , 0); 			
//		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, 1);	 
		$LNK->Query($queryAdd);
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$ret['error'] = 0;
			$ret['last_id'] = $LNK->last_id;// = $LNK->error_string;
			}
		return $ret;
		}	
	function linkUsr($clientId, $userName, $platform) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$queryGet = 'SELECT * FROM users 
					WHERE user_name LIKE \''.$userName.'\'  ';
		$LNK= new DBLink;			
		$queryAdd = '';
//		error_log('[INFO SQL] QUERY: result is '.$queryAdd , 0); 			
//		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, 1);	 
		$LNK->Query($queryGet);
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else{
			$retUsr = $LNK->GetData(0, false);
			$querySet = 'update `usersBot` SET   userId= \''.$retUsr['user_id'].'\', status = 1   	where   clientId  = \''.$clientId.'\' ';		
			$LNK->Query($querySet);
			if($LNK->error){
				$ret['error'] = 1;
				$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}else{			
				$ret['error'] = 0;
				$ret['info'] = $this->getClientFull($platform, $retUsr['user_id']);// = $LNK->error_string;
			}
		}
		return $ret;
	}
	function unlinkUsr($clientId, $platform) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, 1);	 
		$LNK= new DBLink;			
		$querySet = 'update `usersBot` SET   userId= \'0\', status = 2   	where   clientId  = \''.$clientId.'\' AND  	platform   = \''.$platform.'\' ';		
		$LNK->Query($querySet);
		if($LNK->error){
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
		}else{			
			$ret['error'] = 0;
			$ret['info'] = $this->getClient($platform, $clientId);// = $LNK->error_string;
		}		
		return $ret;
	}


	}
?>