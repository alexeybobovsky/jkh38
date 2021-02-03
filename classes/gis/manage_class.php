<?php
//require_once("main.inc.php");
class Manage
	{
	var $last_query;
	var $child = array();
	var $idInUse = array();
	var $childNum;
	var $childLevel;
	var $rekCount;
/******************TEMPLATE***********************************************************************/	
/******************GETTERS***********************************************************************/	
/****tmp func    */	
	
	function getObjOfLayerNameZ($lName) /*2019_03_17 получить все объекты при старте */
		{
		$query = 'SELECT * FROM `gis_objects` WHERE `o_lParentStr` LIKE \'0_997\' and o_name like \''.$lName.'\' ';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, false);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $objList;				
		}		
	function getAllLayersZCities() /*2019_03_17 получить все объекты при старте */
		{
		$query = 'SELECT * FROM `gis_layers` WHERE l_parId = \'997\' AND l_parStr like \'%_997_%\' ';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $objList;				
		}		
	function getDistrBorderObj($lId) /*2019_03_17 получить все объекты при старте */
		{
		$query = 'SELECT * FROM `gis_objects` WHERE `o_lStr` LIKE \'%_'.$lId.'_1007%\' ';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, fale);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $objList;				
		}	

	function getNPListAllNew() /*2019_03_17 получить все объекты при старте */
		{
		$query = 'SELECT * FROM `gis_layers` WHERE `l_parStr` LIKE \'%0_997_%\' and l_id>\'8000\' ORDER BY `gis_layers`.`l_parStr` ASC';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $objList;				
		}
	function getDistrListCities() /*2019_03_17 получить все объекты при старте */
		{
		$query = 'SELECT * FROM `gis_layers` WHERE `l_parStr` LIKE \'%0_999_1002_1005_3403%\' and l_childCnt=0 ORDER BY `gis_layers`.`l_parStr` ASC';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $objList;				
		}
	function getNPListOld($lpar) /*2019_03_17 получить все объекты при старте */
		{
		$query = 'SELECT *  FROM `gis_layers` WHERE `l_parStr` LIKE \'%0_999_1002_1005_1349_'.$lpar.'%\' and l_childCnt=0	 				
					ORDER BY `l_name` ASC';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $objList;				
		}
		

		
		/****tmp func    */		
		
		
		
	function getObjJSONFactors() /*2019_07_03 выбор  объекта */
		{
		$LNK= new DBLink;	

		$query = 'SELECT * FROM `zhkh_factor_kls`  order by id_factor';		
//		$query = 'SELECT * from  `gis_jkhObj` '.$queryAdd.' order by date_up ASC';		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
		return $ret;
		}		
	function getAllObjJSON($start) /*2019_07_03 выбор  объекта */
		{
		$LNK= new DBLink;	
		$queryAdd = ($start) ? ' WHERE date_up > \''.$start.'\' ' : '';
//		$queryAdd = ($start) ? ' AND date_up > \''.$start.'\' ' : '';
		$queryFactors = 'SELECT * FROM `zhkh_factor_kls`  order by id_factor';		
		$query = 'SELECT *, gis_jkhObj.id as idObj  FROM  `gis_jkhObj` LEFT JOIN `zhkh_factor_values` on `gis_jkhObj`.`id_object`=`zhkh_factor_values`.`id_object` '.$queryAdd.' order by gis_jkhObj.id';		
//		$query = 'SELECT * FROM `zhkh_factor_values`, `gis_jkhObj` WHERE `gis_jkhObj`.`id_object`=`zhkh_factor_values`.`id_object` '.$queryAdd.' order by edate DESC ';		
//		$query = 'SELECT * from  `gis_jkhObj` '.$queryAdd.' order by date_up ASC';		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, true);	 
		$LNK->Query($queryFactors);
			$factors = $LNK->GetData(0, true);		
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, true);
			foreach ($objList as $obj) {				
				foreach ($obj as $name => $val ) {
//					$obj['attr'] = array();
					if((strpos($name , 'cell_')!== false)&&($val!= null)){
						$tmpArr = explode('_',$name);
						foreach ($factors as $elem) {
							if($elem['number_cell'] == $tmpArr[1]){
//								echo $elem['name_factor'];								
								$obj['attr'][] = array('id'=>$elem['id_factor'], 'name' =>  $elem['name_factor'], 'value' => $val, 'is_dec' =>  $elem['is_dec']);
							}
						}
					}
				}			
			$ret[] = $obj;
			
//			$ret = $objList;
			}
			
		}
		else 
			$ret = 0;
		return $ret;
		}	
	function getObjOfNpSector($npId, $sectorId) /*2019_07_03 выбор  объекта */
		{
		$LNK= new DBLink;	
		$query = '';
		$query = 'SELECT * from  `gis_jkhObj` where    np_id  = \''.$npId.'\' and jkhSector_id =\''.$sectorId.'\' ';		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, 1);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
		return $ret;
		}

	function getObjJSONOfNP($npId) /*2019_07_03 выбор  объекта */
		{
		$LNK= new DBLink;	
		$queryAdd = '';
		$queryAdd = 'SELECT * from  `gis_jkhObj` where    np_id  = \''.$npId.'\' and jkhSector_id != \'0\' ';		
		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, 1);	 
		$LNK->Query($queryAdd);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(1, true);
			}
		else 
			$ret = 0;
		return $ret;
		}
	function getObjJSON($objId) /*2019_07_03 выбор  объекта */
		{
		$LNK= new DBLink;	
		$query = 'SELECT * from  `gis_jkhObj` where    id  = \''.$objId.'\' ';		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, 1);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, false);
			}
		else 
			$ret = 0;
		return $ret;
		}
	function getAllSectors() /*2019_04_01 получить все отрасли ЖКХ */
		{
		$query = 'SELECT * FROM `zhkh_sector`  ORDER BY `name` ';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $objList;				
		}
	function getNPListOfDistr($lId) /*2019_03_17 получить все объекты при старте */
		{
		$query = 'SELECT *  FROM `gis_layers` WHERE `l_parStr` LIKE \'%0_997_'.trim($lId).'%\' ORDER BY `l_name` ';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $objList;				
		}
	function getUsrToDistr($uid, $full = 0) /*2019_03_17 получить все объекты при старте */
		{
			
		$query = ($full)?'SELECT * FROM `gis_usr2distr`, users, gis_layers WHERE gis_usr2distr.user_id = users.user_id AND gis_layers.l_id = gis_usr2distr.distr_id AND users.user_id = \''.$uid.'\' ' :
			'SELECT * FROM `gis_usr2distr` WHERE `user_id` = \''.$uid.'\'';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, false);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $objList;				
		}
	function getNPListAll() /*2019_03_17 получить все объекты при старте */
		{
		$query = 'SELECT *  FROM `gis_layers` WHERE `l_parStr` LIKE \'%0_997_%\' AND `l_childCnt` = 0  
		ORDER BY l_parId,  `gis_layers`.`l_name`  DESC';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $objList;				
		}
	function getDistrListMain() /*2019_03_17 получить все объекты при старте */
		{
		$query = 'SELECT * FROM `gis_layers` WHERE `l_parId` = 997 					
					ORDER BY `l_name` ASC';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $objList;				
		}
	function getDistrListOKTMO() /*2019_03_17 получить все объекты при старте */
		{
		$query = 'SELECT * FROM `gis_layers` WHERE `l_parId` = 1349					
					ORDER BY `l_name` ASC';
		$LNK= new DBLink;	
		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $objList;				
		}
	function getAllObjStartZhKH() /*2019_03_17 получить все объекты при старте */
		{
		$query = 'SELECT * FROM `gis_objects` where `o_lStr` LIKE \'%_1007%\' OR  `o_lStr` LIKE \'%_7000%\'					
					ORDER BY `o_name` ASC';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, true);
			foreach ($objList as $obj) {
				$obj['geo'] = $this->getGeoOfObject($obj['o_id']);
				$ret[] = $obj;
				}			
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;				
		}
	function getAllLayersOfParent($parId = 0) /*2017_04_19 получить список ВСЕХ дочерних слоёв */
		{
		$query = 'SELECT * 
					FROM `gis_layers` 
					WHERE l_parStr like \'%_'.intval($parId).'%\'
					ORDER BY l_parStr, l_name, l_objCnt';

		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{
			$ret = $LNK->GetData(/*$ret_arr*/0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;				

		}
	function getLayersOfParent($parId = 0) /*2017_03_28 получить список дочерних слоёв */
		{
		$query = 'SELECT * 
					FROM `gis_layers` 
					WHERE l_parId = '.intval($parId).'
					ORDER BY l_name, l_objCnt';

		$LNK= new DBLink;	
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{
			$ret = $LNK->GetData(/*$ret_arr*/0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;				

		}
	function getObjBounds($id = 0) /*2017_05_16 получить границы координат */
		{
		$suf = ($id)?' and o_id = '.$id.' ':'';
		$query = 'SELECT MIN(`c_lat`) as minLat, MAX(`c_lat`) as maxLat, MIN(`c_lng`)as minLng, MAX(`c_lng`)as maxLng FROM `gis_coord` WHERE
				(c_lat < 90 and c_lat > -90 ) AND (c_lng < 180 and c_lat > -180 )'.$suf;

		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{			
			$ret = $LNK->GetData(/*$ret_arr*/0, false);
			if(($ret['minLat'] === $ret['maxLat']) && ($ret['minLng'] === $ret['maxLng'])) //1 point object
				$ret = 0;
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;				

		}	
	function getObj2Layers() /*2017_03_28 получить все привязки объектов к слоям */
		{
		$query = 'SELECT * 
					FROM `gis_obj2layer` 
					ORDER BY l_id';

		$LNK= new DBLink;	
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{
			$ret = $LNK->GetData(/*$ret_arr*/0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;				

		}

	function getSingleObject($id, $noGeo = 1) /*2017_12_13 получить конкретный объект */
		{
		$query = 'SELECT * FROM `gis_objects`  
					WHERE `o_id` = '.$id.' ';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			if($noGeo)
				{
				$ret = $LNK->GetData(0, false);
				}
			else
				{
				$objList = $LNK->GetData(0, true);
				foreach ($objList as $obj) {
					$obj['geo'] = $this->getGeoOfObject($obj['o_id']);
					$ret[] = $obj;
					}				
				}
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;				
		}	
	function getSingleLayer($id) /*2017_04_26 получить конкретный слой */
		{
		$query = 'SELECT * 
					FROM `gis_layers` 
					where l_id = \''.$id.'\'';

		$LNK= new DBLink;	
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{
			$ret = $LNK->GetData(/*$ret_arr*/0, false);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;				

		}	
	function getAllLayersZ($isMng = 0) /*2019_03_27 получить список всех слоёв для ЖКХ*/
		{
		$query = 'SELECT * FROM `gis_layers` WHERE l_parStr like \'%_997%\' ';
		if(!$isMng)
			$query .= '	WHERE l_public > 0';
		
		$query .= '	ORDER BY  l_name,l_parId, l_objCnt';

		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{
			$ret = $LNK->GetData(/*$ret_arr*/0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;				

		}
	function getAllLayers($isMng = 0) /*2017_03_27 получить список всех слоёв */
		{
		$query = 'SELECT * 
					FROM `gis_layers` ';
		if(!$isMng)
			$query .= '	WHERE l_public > 0';
		
		$query .= '	ORDER BY l_parId, l_name, l_objCnt';

		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{
			$ret = $LNK->GetData(/*$ret_arr*/0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;				

		}
	function getAllObj() /*2017_03_28 получить все объекты */
		{
		$query = 'SELECT * FROM `gis_objects`  					
					ORDER BY `o_name` ASC';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, true);
			foreach ($objList as $obj) {
//				$obj['o2l_str'] = $this->getObj2layerStr($obj['o_id']);
				$obj['geo'] = $this->getGeoOfObject($obj['o_id']);
				$ret[] = $obj;
				}				
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;				
		}	
	function getAllObjStart() /*2017_07_04 получить все объекты при старте */
		{
		$query = 'SELECT * FROM `gis_objects`  					
					ORDER BY `o_name` ASC';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$objList = $LNK->GetData(0, true);
			foreach ($objList as $obj) {
//				$obj['o2l_str'] = $this->getObj2layerStr($obj['o_id']);
				if($obj['o_autoLoad']==1)
					$obj['geo'] = $this->getGeoOfObject($obj['o_id']);
				else
					$obj['geo'] = 'remote';
				$ret[] = $obj;
				}			
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;				
		}
	function getObj2layerStr($o_id) /*2017_02_10 получить все точки с координатами (геометрию) объекта */
		{
		$query = 'SELECT * FROM `gis_obj2layer` 
					WHERE `o_id` = '.$o_id.' ORDER BY `l_id` ';
		$LNK= new DBLink;	
		$retStr = '';
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$list = $LNK->GetData(0, true);
			foreach ($list as $obj) {
//				$retStr .= (!$retStr) ? '' : '_';
				$retStr .= '_'.$obj['l_id'];
				}							
			}
		return $retStr;				
		}
	function getGeoOfObjectArr($list) /*2017_10_24 получить все точки с координатами (геометрию) списка объектов */
		{
		$where = '';
		foreach ($list as $obj) {
			$where .= ($where)? ' OR ': '';
			$where .= '`o_id` = '.$obj.' ';
			}				
		
		$query = 'SELECT c_id, c_lat, c_lng, o_id , hole_id , feature_id FROM `gis_coord` 
					WHERE '.$where.' ORDER BY o_id, `hole_id`, `feature_id`, `c_id` ';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
//		print_r($ret);
		return $ret;				
		}
	function getGeoOfObject($o_id) /*2017_02_10 получить все точки с координатами (геометрию) объекта */
		{
		$query = 'SELECT c_id, c_lat, c_lng, o_id , hole_id , feature_id FROM `gis_coord` 
					WHERE `o_id` = '.$o_id.' ORDER BY `hole_id`, `feature_id`, `c_id` ';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
//		print_r($ret);
		return $ret;				
		}
	function getObjListOfLayer($lId, $onlyNames = 0) /*2017_12_11  получить все объекты слоя */
		{
		$query = 'SELECT * FROM `gis_objects`  
					WHERE `o_lStr` like \'%_'.$lId.'%\' ORDER BY `o_name` ASC';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			if($onlyNames)
				{
				$ret = $LNK->GetData(0, true);
				}
			else
				{
				$objList = $LNK->GetData(0, true);
				foreach ($objList as $obj) {
					$obj['geo'] = $this->getGeoOfObject($obj['o_id']);
					$ret[] = $obj;
					}				
				}
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;
		}	
	function getObjListOfLayerOld($lId, $onlyNames = 0) /*2017_02_09 УСТАРЕВШАЯ!!!! получить все объекты слоя */
		{
		$query = 'SELECT * FROM `gis_objects`  
					WHERE `l_id` = '.$lId.' ORDER BY `o_name` ASC';
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			if($onlyNames)
				{
				$ret = $LNK->GetData(0, true);
				}
			else
				{
				$objList = $LNK->GetData(0, true);
				foreach ($objList as $obj) {
					$obj['geo'] = $this->getGeoOfObject($obj['o_id']);
					$ret[] = $obj;
					}				
				}
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;				
		}
	function _getAllLayers($onlyNames = 0) /*2017_01_08 получить список всех слоёв*/
		{
		$query = 'SELECT * 
					FROM `gis_layers` 
					ORDER BY l_name';
		$ret_arr  = array(	'l_id',
							'l_name',
							'l_dateAdd',
							'l_dateModify',
							'l_info',
							'l_public',
							'l_system');
		$LNK= new DBLink;	
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{
			if($onlyNames)
				{
				$ret = $LNK->GetData(/*$ret_arr*/0, true);
				}
			else
				{
				$layerList = $LNK->GetData(/*$ret_arr*/0, true);
				foreach ($layerList as $layer) {
					$layer['obj'] = $this->getObjListOfLayer($layer['l_id']);
					$ret[] = $layer;
					}				
				}
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;				

		}
		
		
		
	function getApConstrYandex($monthNum)  /*2015_08_07 получение полного списка квартир для яндекса */
		{
//		$lastmonth = date("Y-m-d\Th:i:s\+06:00", mktime(0, 0, 0, date("m")-2, date("d"),   date("Y")));

		$limitDate = date("Y-m-d h:i:s", mktime(0, 0, 0, date("m")-$monthNum, date("d"),   date("Y")));
		$query = 'SELECT DISTINCT ap_objId, 
					obj_name, obj_adrString, district_name, obj_state, obj_dateEnd, firm_name, material_value, obj_material,  obj_sales, 
					obj_geoX, obj_geoY
					FROM fs_apartment, gd_object, gd_firm,  fs_materials, fs_district 
					WHERE ap_objId = obj_id 
					AND gd_firm.firm_id = obj_firmZakaz 
					AND fs_materials.material_id = obj_material 
					AND fs_district.district_id = obj_district 
					AND ap_dateModify >= \''.$limitDate.'\'
					AND (ap_price != 0 AND ap_price IS NOT NULL)
					AND  (ap_sold != 1 OR  ap_sold IS NULL) 
					ORDER BY obj_name,  ap_floor, ap_roomNum, ap_area,  ap_price';
		$ret_arr  = array(	'ap_objId', 
							'obj_name',
							'obj_adrString',
							'obj_dateEnd',
							'obj_state',
							'district_name',
							'obj_material',
							'material_value',
							'firm_name',
							'obj_geoX',
							'obj_geoY',
							'obj_sales'	);
		$LNK= new DBLink;		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$finishReplace = 	array(1 => 'черновая', 2 => 'получистовая', 3 => 'чистовая');
			$ret = $LNK->GetData($ret_arr, true);
			}
		else 
			$ret = 0;
//		$LNK->Close_link();
		return $ret;				
		}


/******************SETTERS***********************************************************************/	

	function upObjJSON($id, $distr, $obj, $fileName, $name, $np=0, $jkhSector=0) /*2019_04_01 добавление нового объекта в json формате сообщения*/
		{
		$LNK= new DBLink;	
		$queryAdd = '';
		$queryAdd = 'update `gis_jkhObj` SET   distr_id  = \''.$distr.'\' , 
			geoData  = \''.$obj.'\' , 
			np_id  = \''.$np.'\' , 
			name  = \''.$name.'\' , 
			jkhSector_id  = \''.$jkhSector.'\' , 
			date_add = FROM_UNIXTIME( \''.time().'\')';		
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
	function delObjJSON($id) /*2019_04_01 добавление нового объекта в json формате сообщения*/
		{
		$LNK= new DBLink;	
		$queryAdd = '';
		$queryAdd = 'update `gis_jkhObj` SET isDeleted   = 1 , 
			WHERE id  = \''.$id.'\' ';		
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, 1);	 
		$LNK->Query($queryAdd);
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$ret = 0;
			}
		return $ret;
		}	
	function addObjJSON($distr, $obj, $fileName, $name, $np=0, $jkhSector=0) /*2019_04_01 добавление нового объекта в json формате сообщения*/
		{
		$LNK= new DBLink;	
		$queryAdd = '';
		$queryAdd = 'INSERT into `gis_jkhObj` SET   distr_id  = \''.$distr.'\' , 
			geoData  = \''.$obj.'\' , 
			np_id  = \''.$np.'\' , 
			name  = \''.$name.'\' , 
			geoFile  = \''.$fileName.'\' , 
			jkhSector_id  = \''.$jkhSector.'\' , 
			date_add = FROM_UNIXTIME( \''.time().'\')';		
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
	function addUsr2Distr($uId, $distr, $role) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$queryAdd = '';
		$queryAdd = 'INSERT into `gis_usr2distr` SET   	user_id  = \''.$uId.'\' ,  	distr_id  = \''.$distr.'\' ,	role  = \''.$role.'\' ';		
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

	function incObj2LyerCnt($lId) /*2017_03_28 увеличение количества объектов в слое */
		{
		$LNK= new DBLink;				
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	
		$queryInsert = 'UPDATE `gis_layers` SET l_objCnt = l_objCnt + 1  WHERE l_id = \''.$lId.'\'';
		$LNK->Query($queryInsert);
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		}
	function incLyerChildCnt($lId) /*2017_05_03 увеличение количества потомков в слое */
		{
		$LNK= new DBLink;				
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	
		$queryInsert = 'UPDATE `gis_layers` SET l_ChildCnt = l_ChildCnt + 1  WHERE l_id = \''.intval($lId).'\'';
		$LNK->Query($queryInsert);
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		}
	function addObj2Layer($lid, $oId) /*2017_12_13 добавление объекта в слой - !!!новая функция!!!*/
		{
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$layer = $this->getSingleLayer($lid);
		$obect = $this->getSingleObject($oId);
//		print_r($obect);
		$olStr_arr = explode('_', $obect['o_lStr']);
		$olParentStr_arr = explode('_', $obect['o_lParentStr']);
		$lparStr_arr = explode('_', 	$layer['l_parStr']);
		$addStrLayer = $addStr = '';
		foreach ($lparStr_arr as $layerPar) {
			if((!in_array($layerPar, $olParentStr_arr))&&($layerPar > 0 ))
				$addStr .= '_'.$layerPar;						
			}
		if(!in_array($lid, $olStr_arr))
			$addStrLayer .= '_'.$lid;				
		if(($addStr) || ($addStrLayer)){
/*			echo '<br>alid = '.$lid.'; oId = '.$oId;
			echo '<br>addStrLayer = '.$addStrLayer.'; addStr = '.$addStr;*/
/*			print_r(array(	0=>array('name' => 'o_lParentStr', 'val' => $obect['o_lParentStr'].$addStr),
										1=>array('name' => 'o_lStr', 'val' => $obect['o_lStr'].$addStrLayer)));*/
			
			$this->updateObject(array(	0=>array('name' => 'o_lParentStr', 'val' => $obect['o_lParentStr'].$addStr),
										1=>array('name' => 'o_lStr', 'val' => $obect['o_lStr'].$addStrLayer)), $oId);			
		}
		}
	function addObj2LayerOld($lId, $oId) /*2017_03_28 добавление объекта в слой*/
		{
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		if(is_array($lId))
			{
			$layerAdd = '';	
			$queryUp = 'insert into `gis_obj2layer` ( l_id, o_id ) values ';
			foreach ($lId as $layer) {
				if($layerAdd) $layerAdd .= ', ';	
				$layerAdd .= '('.$layer.', '.$oId.')';
				
				}				
			$queryUp .= $layerAdd;
			}
		else
			$queryUp = 'insert into `gis_obj2layer` SET 
								l_id = '.$lId.', 
								o_id = '.$oId.' ';
		$LNK->Query($queryUp);
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;
			}
		else
			{
			$this->incObj2LyerCnt($lId);
			$ret = 0;
			}
		return $ret;						
		}
	function updateLayer($param, $id) /*2017_05_03 редактирование параметров слоя*/
		{
		$LNK= new DBLink;	
		$queryUp = 'UPDATE `gis_layers` SET 									
									l_dateModify = FROM_UNIXTIME( \''.time().'\') ';
						
		foreach ($param as $item) {
			$queryUp .= ', '.$item['name'].' = \''.$item['val'].'\' ';

		}
		$queryUp .= ' WHERE l_id = '.$id;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	
		$LNK->Query($queryUp);
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;
			}
		else
			{
			$ret['error'] = 0;
			$ret['last_id'] = $LNK->last_id;
			}
		return $ret;						
		}	
	function updateObject($param, $id) /*2017_02_16 редактирование параметров объекта*/
		{
		$LNK= new DBLink;	
		$queryUp = 'UPDATE `gis_objects` SET 									
									o_dateModify = FROM_UNIXTIME( \''.time().'\') ';
						
		foreach ($param as $item) {
			$queryUp .= ', '.$item['name'].' = \''.$item['val'].'\' ';
/*			switch($item['name'])
				{
				case 'oName': $queryUp .= ', o_name = \''.$item['val'].'\' '; break;
				}*/
		}
		$queryUp .= ' WHERE o_id = '.$id;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	
		$LNK->Query($queryUp);
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;
			}
		else
			{
			$ret['error'] = 0;
			$ret['last_id'] = $LNK->last_id;
			}
		return $ret;						
		}	
	function newObjectPoints($list, $oId) /*2017_02_07 добавление точек для нового объекта*/
		{
		$LNK= new DBLink;	
		$queryInsertList = '';//`c_latFloat`, `c_lngFloat`, 
		$queryInsert = 'INSERT INTO `gis_coord` (`c_id`, `c_lat`, `c_lng`, `o_id`, `hole_id`, `feature_id`) VALUES ';
		foreach ($list as $point) {
/*			$point['latStr'] = number_format($point['lat'], 6, '.', '');
			$point['lngStr'] = number_format($point['lng'], 6, '.', '');*/
			$fId = (isset($point['fId'])) ? $point['fId'] : 0;
			$hId = (isset($point['hId'])) ? $point['hId'] : 0;
			$queryInsertList .= ($queryInsertList)? ', ': '';//.', '.$point['lat'].', '.$point['lng']
			$queryInsertList .= '(NULL, '.$point['lat'].', '.$point['lng'].', '.$oId.', '.$hId.', '.$fId.')';
			$queryInsertList .= '
';
//			print_r($point);
			}
	//	$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	
//		echo $queryInsert.$queryInsertList;
		$LNK->Query($queryInsert.$queryInsertList);
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
	function newObject($param) /*2017_02_07 добавление нового объекта*/
		{
		$LNK= new DBLink;	
		$queryInsert = 'INSERT into `gis_objects` SET 
									o_dateAdd  = 	FROM_UNIXTIME( \''.time().'\'), 
									o_dateModify = FROM_UNIXTIME( \''.time().'\')';
		$queryInsert .= ($param['lId'])?', l_id = \''.$param['lId'].'\' ': '';
		$queryInsert .= ($param['oName'])?', o_name = \''.$param['oName'].'\' ': '';
		$queryInsert .= (isset($param['oType']))?', o_type = \''.$param['oType'].'\' ': '';
		$queryInsert .= ($param['lStr'])?', o_lStr = \''.$param['lStr'].'\' ': '';
		$queryInsert .= ($param['lParentStr'])?', o_lParentStr = \''.$param['lParentStr'].'\' ': '';
		$queryInsert .= ($param['oAbout'])?', o_info = \''.$param['oAbout'].'\' ': '';
		$queryInsert .= (isset($param['oTemplate']))?', t_id = \''.$param['oTemplate'].'\' ': '';
		$queryInsert .= (isset($param['o_autoLoad']))?', o_autoLoad = \''.$param['o_autoLoad'].'\' ': '';
		$queryInsert .= (isset($param['specParam']))?', o_specParam = \''.$param['specParam'].'\' ': '';

//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	
		$LNK->Query($queryInsert);
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
	function newLayer($param) /*2017_01_04 добавление нового Слоя*/
		{
		$LNK= new DBLink;	
		$queryInsert = 'INSERT into `gis_layers` SET 
									l_dateAdd  = 	FROM_UNIXTIME( \''.time().'\'), 
									l_dateModify = FROM_UNIXTIME( \''.time().'\')';
		$queryInsert .= ($param['lId'])?', l_id = \''.$param['lId'].'\' ': '';
		$queryInsert .= ($param['lName'])?', l_name = \''.$param['lName'].'\' ': '';
		$queryInsert .= ($param['l_parStr'])?', l_parStr = \''.$param['l_parStr'].'\' ': '';
		$queryInsert .= ($param['l_parId'])?', l_parId = \''.$param['l_parId'].'\' ': '';
		$queryInsert .= ($param['l_objCnt'])?', 	l_objCnt = \''.$param['l_objCnt'].'\' ': '';
		$queryInsert .= ($param['lPublic'])?', 	l_public = \''.$param['lPublic'].'\' ': '';
		$queryInsert .= ($param['l_info'])?', 	l_info = \''.$param['l_info'].'\' ': '';

//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	
		$LNK->Query($queryInsert);
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


	}
?>