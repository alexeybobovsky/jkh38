<?php
//require_once("main.inc.php");
class ZhKH
	{
	var $last_query;
	var $child = array();
	var $idInUse = array();
	var $childNum;
	var $childLevel;
	var $rekCount;
/******************TEMPLATE***********************************************************************/	
/******************GETTERS***********************************************************************/	
	function getSpecKlsMetriks($id) /*2019_04_01 забрать все  оперативные сообщения*/
		{
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$where = ($id) ? ' where   ID_SINGLE  = '.$id.' ' : '';
		$query = 'SELECT * FROM `kls_metriks` '.$where;//' where  ID_SINGLE = '.$id.'';
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
		return $ret;						
		}	
	function getSpecKlsFactor($id) /*2019_04_01 забрать все  оперативные сообщения*/
		{
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$where = ($id) ? ' where   ID_FACTOR  = '.$id.' ' : '';
		$query = 'SELECT * FROM `kls_factor` '.$where;// ' where   ID_FACTOR  = '.$id.' ';
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
		return $ret;						
		}		
	function getSpecKlsSingle($id) /*2019_04_01 забрать все  оперативные сообщения*/
		{
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$where = ($id) ? ' where   ID_SINGLE  = '.$id.' ' : '';
		$query = 'SELECT * FROM `kls_single` '.$where;//' where  ID_SINGLE = '.$id.'';
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
		return $ret;						
		}		
	function getSpecHrFactor($id) /*2019_04_01 забрать все  оперативные сообщения*/
		{
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$where = ($id) ? ' where   ID_OBJECT  = '.$id.' ' : '';

		$query = 'SELECT * FROM `hr_factor` '.$where;//'where  ID_OBJECT = '.$id.'';
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
		return $ret;						
		}	
	function getRep_pmo9($start, $distr, $sendDate) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_pasport9.sendDate  = '.$sendDate : '';
 		$startAdd = ($start) ? ' AND zhkhform_pasport9.id  > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_pasport9.distrId = '.$distr : '';
		$LNK= new DBLink;
		$query = 'SELECT * FROM `zhkhform_pasport9`, zhkh_type_data_pasport9,  gis_layers where zhkhform_pasport9.id_type_data  = zhkh_type_data_pasport9.id  AND zhkhform_pasport9.distrId = gis_layers.l_id  		
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  gis_layers.l_id,  sendDate,  zhkhform_pasport9.id_type_data DESC';		 
		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;	
			}
		else 
			$ret = 0;
		return $ret;
		}		
	function getRep_pmo8($start, $distr, $sendDate) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_pasport8.sendDate  = '.$sendDate : '';
 		$startAdd = ($start) ? ' AND zhkhform_pasport8.id  > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_pasport8.distrId = '.$distr : '';
		$LNK= new DBLink;
		$query = 'SELECT * FROM `zhkhform_pasport8`, zhkh_type_data_pasport8,  gis_layers where zhkhform_pasport8.id_type_data  = zhkh_type_data_pasport8.id  AND zhkhform_pasport8.distrId = gis_layers.l_id  		
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  gis_layers.l_id,  sendDate,  zhkhform_pasport8.id_type_data DESC';		 
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;	
			}
		else 
			$ret = 0;
		return $ret;
		}		
	function getRep_pmo7($start, $distr, $sendDate = 0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_pasport7.sendDate  = '.$sendDate : '';
 		$startAdd = ($start) ? ' AND zhkhform_pasport7.id  > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_pasport7.distrId = '.$distr : '';
		$LNK= new DBLink;
		$query = 'SELECT * FROM `zhkhform_pasport7`, zhkh_type_data_pasport7,  gis_layers where zhkhform_pasport7.id_type_data  = zhkh_type_data_pasport7.id  AND zhkhform_pasport7.distrId = gis_layers.l_id  		
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  gis_layers.l_id,  sendDate,  zhkhform_pasport7.id_type_data';		 
		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, 1);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;	
			}
		else 
			$ret = 0;
		return $ret;
		}		
	function checkRep_pmo6($distr, $objName) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_pasport6.sendDate  = '.$sendDate : '';
 		$startAdd = ($start) ? ' AND zhkhform_pasport6.id  > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_pasport6.distrId = '.$distr : '';
		$LNK= new DBLink;
		$query = 'SELECT * FROM `zhkhform_pasport6`,  gis_layers where zhkhform_pasport6.distrId = gis_layers.l_id  		
			AND  	cell_1 = \''.$objName.'\'	AND  	 	distrId = \''.$distr.'\'		
			ORDER BY  gis_layers.l_id,  sendDate DESC';		 
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;	
			}
		else 
			$ret = 0;
		return $ret;
		} 		
	function getRep_pmo6($start, $distr, $sendDate = 0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_pasport6.sendDate  = '.$sendDate : '';
  		$startAdd = ($start) ? ' AND zhkhform_pasport6.id > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_pasport6.distrId = '.$distr : '';
		$LNK= new DBLink;	 
		$query = 'SELECT * FROM `zhkhform_pasport6`, gis_layers where zhkhform_pasport6.distrId = gis_layers.l_id 			
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  gis_layers.l_id,  sendDate DESC,  zhkhform_pasport6.date DESC ';		
//		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		 
			$msgList = $LNK->GetData(0, true);
			foreach ($msgList as $msg) {					
				$ret[] = $msg;
				}			
			}
		else 
			$ret = 0;
		return $ret;
		}		
	function getRep_pmo6_single($recId, $sendDate = 0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_pasport6.sendDate  = '.$sendDate : '';  		
		$LNK= new DBLink;	 
		$query = 'SELECT * FROM `zhkhform_pasport6` where id = '.$recId.$sendDateAdd.' ';		
//		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, false);
			}
		else 
			$ret = 0;
		return $ret;
		}		
	function getRep_pmo5($start, $distr, $sendDate = 0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_pasport5.sendDate  = '.$sendDate : '';
 		$startAdd = ($start) ? ' AND zhkhform_pasport5.id  > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_pasport5.distrId = '.$distr : '';
		$LNK= new DBLink;
		$query = 'SELECT * FROM `zhkhform_pasport5`, zhkh_type_data_pasport5,  gis_layers where zhkhform_pasport5.id_type_data  = zhkh_type_data_pasport5.id  AND zhkhform_pasport5.distrId = gis_layers.l_id  		
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  gis_layers.l_id,  sendDate,  zhkhform_pasport5.id_type_data';		 
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;	
			}
		else 
			$ret = 0;
		return $ret;
		}		
	function getRep_pmo4($start, $distr, $sendDate = 0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_pasport4.sendDate  = '.$sendDate : '';
 		$startAdd = ($start) ? ' AND zhkhform_pasport4.id  > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_pasport4.distrId = '.$distr : '';
		$LNK= new DBLink;
		$query = 'SELECT * FROM `zhkhform_pasport4`, zhkh_type_data_pasport4,  gis_layers where zhkhform_pasport4.id_type_data  = zhkh_type_data_pasport4.id  AND zhkhform_pasport4.distrId = gis_layers.l_id  		
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  gis_layers.l_id,  sendDate,  zhkhform_pasport4.id_type_data';		 
//		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;	
			}
		else 
			$ret = 0;
		return $ret;
		}		
	function getRep_pmo3($start, $distr, $sendDate = 0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_pasport3.sendDate  = '.$sendDate : '';
 		$startAdd = ($start) ? ' AND zhkhform_pasport3.id  > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_pasport3.distrId = '.$distr : '';
		$LNK= new DBLink;
		$query = 'SELECT * FROM `zhkhform_pasport3`, zhkh_type_data_pasport3,  gis_layers where zhkhform_pasport3.id_type_data  = zhkh_type_data_pasport3.id  AND zhkhform_pasport3.distrId = gis_layers.l_id  		
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  gis_layers.l_id,  sendDate,  zhkhform_pasport3.id_type_data ';		 
//		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;	
			}
		else 
			$ret = 0;
		return $ret;
		}			
	function getRep_pmo2($start, $distr, $sendDate = 0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_pasport2.sendDate  = '.$sendDate : '';
  		$startAdd = ($start) ? ' AND zhkhform_pasport2.id > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_pasport2.distrId = '.$distr : '';
		$LNK= new DBLink;	 
		$query = 'SELECT * FROM `zhkhform_pasport2`, gis_layers where zhkhform_pasport2.distrId = gis_layers.l_id 			
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  gis_layers.l_id,  sendDate DESC,  zhkhform_pasport2.date DESC ';		
		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			foreach ($msgList as $msg) {					
//				$ret[] = (object) $msg;
				$ret[] = $msg;
				}			
			}
		else 
			$ret = 0;
		return $ret;
		}		
	function getRep_pmo2_single($recId, $sendDate = 0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_pasport2.sendDate  = '.$sendDate : '';  		
		$LNK= new DBLink;	 
		$query = 'SELECT * FROM `zhkhform_pasport2` where id = '.$recId.$sendDateAdd.' ';		
//		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, false);
			}
		else 
			$ret = 0;
		return $ret;
		}		
	function getRep_pmo1($start, $distr, $sendDate = 0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_pasport1.sendDate  = '.$sendDate : '';
 		$startAdd = ($start) ? ' AND zhkhform_pasport1.id  > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_pasport1.distrId = '.$distr : '';
		$LNK= new DBLink;
		$query = 'SELECT * FROM `zhkhform_pasport1`, zhkh_type_data_pasport1,  gis_layers where zhkhform_pasport1.id_type_data  = zhkh_type_data_pasport1.id  AND zhkhform_pasport1.distrId = gis_layers.l_id  		
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  gis_layers.l_id,  sendDate,  zhkhform_pasport1.id_type_data DESC';		 
//		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;	
			}
		else 
			$ret = 0;
		return $ret;
		}		
	function getOrgList($layerId) /*2019_04_01 забрать все  оперативные сообщения*/
		{
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 

		$query = 'SELECT * FROM `zhkh_org` where   layerId = '.$layerId.' order by name';
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
		return $ret;						
		}
	function getMesComments($messId) /*2019_04_01 забрать все  оперативные сообщения*/
		{
		$LNK= new DBLink;
		$query = 'SELECT user_name, zhkh_mesComment.id as commId, text, date, type, mesId,  userId, type
			FROM `zhkh_mesComment`, users  
			where userId = users.user_id  AND  mesId = '.$messId.' order by date desc';
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, true);
			}
		else 
			$ret = 0;
		return $ret;						
		}
	function getRep_toplPril1($start, $distr, $sendDate = 0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_toplPr1.sendDate  = '.$sendDate : '';
  		$startAdd = ($start) ? ' AND zhkhform_toplPr1.id > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_toplPr1.distrId = '.$distr : '';
		//$retList = 'zhkh_message.id as messId, userId, user_name, distId, npId, l_name, zhkhSectorId, orgId, orgName, zhkh_sector.name as zhkhSectorName, initiator, dateCreate, incidentDate, reason, hardware, consumerNum, bossOfWork, bossOfCity, goodNewsMen, tempereture, badNewsMen, status, closeDate 	  ';	
		$LNK= new DBLink;	 
		$query = 'SELECT * FROM `zhkhform_toplPr1`, gis_layers where zhkhform_toplPr1.distrId = gis_layers.l_id 			
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  gis_layers.l_id,  sendDate DESC,  zhkhform_toplPr1.id ';		
		echo $queryAdd ;
		//$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			foreach ($msgList as $msg) {					
//				$ret[] = (object) $msg;
				$ret[] = $msg;
				}			
			}
		else 
			$ret = 0;
		return $ret;
		}
	function getRep_zhkh24($start, $distr, $sendDate = 0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_zhkh24.sendDate  = '.$sendDate : '';
 		$startAdd = ($start) ? ' AND zhkhform_zhkh24.id  > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_zhkh24.distrId = '.$distr : '';
		$LNK= new DBLink;
		$query = 'SELECT * FROM `zhkhform_zhkh24`, zhkh_factor,  gis_layers where zhkhform_zhkh24.id_factor  = zhkh_factor.id_factor  AND zhkhform_zhkh24.distrId = gis_layers.l_id  		
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  gis_layers.l_id,  sendDate DESC,  zhkhform_zhkh24.id_factor';		 
//		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;	
			}
		else 
			$ret = 0;
		return $ret;
		}		
	function getRep_zhkh2($start, $distr, $sendDate=0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_zhkh2.sendDate  = '.$sendDate : '';
 		$startAdd = ($start) ? ' AND zhkhform_zhkh2.id  > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_zhkh2.distrId = '.$distr : '';
		$LNK= new DBLink;
		$query = 'SELECT * FROM `zhkhform_zhkh2`, zhkh_type_data2,  gis_layers where zhkhform_zhkh2.id_type_data  = zhkh_type_data2.id  AND zhkhform_zhkh2.distrId = gis_layers.l_id  		
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  gis_layers.l_id,  sendDate DESC,  zhkhform_zhkh2.id_type_data ';		 
//		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;
			/*foreach ($msgList as $msg) {					
				$ret[] = (object) $msg;
				}	*/		
			}
		else 
			$ret = 0;
		return $ret;
		}		
	function getRep_toplPril2($start, $distr, $sendDate = 0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND fact_fuel_use.sendDate  = '.$sendDate : '';
 		$startAdd = ($start) ? ' AND fact_fuel_use.id  > '.$start : '';
 		$distrAdd = ($distr) ? ' AND fact_fuel_use.distrId = '.$distr : '';
		$LNK= new DBLink;
		$query = 'SELECT * FROM `fact_fuel_use`, kls_fuel,  gis_layers where fact_fuel_use.id_fuel  = kls_fuel.ID_FUEL  AND fact_fuel_use.distrId = gis_layers.l_id  		
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  gis_layers.l_id,  sendDate DESC,  kls_fuel.ID_FUEL';		 
		//echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;
			/*foreach ($msgList as $msg) {					
				$ret[] = (object) $msg;
				}	*/		
			}
		else 
			$ret = 0;
		return $ret;
		}		
	function getRep_zhkh3($start, $distr, $sendDate = 0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND zhkhform_zhkh3.sendDate  = '.$sendDate : '';
 		$startAdd = ($start) ? ' AND zhkhform_zhkh3.id  > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_zhkh3.distrId = '.$distr : '';
		$LNK= new DBLink;
		$query = 'SELECT * FROM `zhkhform_zhkh3`, `zhkh_type_data3`, gis_layers where zhkhform_zhkh3.id_type_data = zhkh_type_data3.id AND zhkhform_zhkh3.distrId = gis_layers.l_id  		
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY   gis_layers.l_id, sendDate DESC, zhkhform_zhkh3.id_type_data';		 
		//echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;
			/*foreach ($msgList as $msg) {					
				$ret[] = (object) $msg;
				}	*/		
			}
		else 
			$ret = 0;
		return $ret;
		}	
	function getRep_sfo($start, $distr, $sendDate = 0) /*2019_04_01 забрать */
		{		
 		$sendDateAdd = ($sendDate) ? ' AND operation_info_sfo.sendDate  = '.$sendDate : '';
 		$startAdd = ($start) ? ' AND operation_info_sfo.id  > '.$start : '';
 		$distrAdd = ($distr) ? ' AND operation_info_sfo.distrId = '.$distr : '';
		$LNK= new DBLink;
		$query = 'SELECT * FROM `operation_info_sfo`, gis_layers where operation_info_sfo.distrId = gis_layers.l_id  		
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  operation_info_sfo.id DESC';		 
		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;	
			}
		else 
			$ret = 0;
		return $ret;
		}	
	function getRep_1zhkh_single($start, $distr, $sendDate) /*2019_04_01 забрать */
		{		
  		$sendDateAdd = ($sendDate) ? ' AND zhkhform_zhkh1.sendDate  = '.$sendDate : '';
		$startAdd = ($start) ? ' AND zhkhform_zhkh1.id > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_zhkh1.distrId = '.$distr : '';
		//$retList = 'zhkh_message.id as messId, userId, user_name, distId, npId, l_name, zhkhSectorId, orgId, orgName, zhkh_sector.name as zhkhSectorName, initiator, dateCreate, incidentDate, reason, hardware, consumerNum, bossOfWork, bossOfCity, goodNewsMen, tempereture, badNewsMen, status, closeDate 	  ';	
		$LNK= new DBLink;
		$query = 'SELECT * FROM `zhkhform_zhkh1`, zhkh_type_data, gis_layers where zhkhform_zhkh1.distrId = gis_layers.l_id  AND zhkh_type_data.id = zhkhform_zhkh1.id_type_data 			
			'.$startAdd.$distrAdd.$sendDateAdd.'		
			ORDER BY  zhkhform_zhkh1.id ';		
		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;
			/*foreach ($msgList as $msg) {					
				$ret[] = (object) $msg;
				}	*/		
			}
		else 
			$ret = 0;
		return $ret;
		}
	function getRep_1zhkh($start, $distr, $light=false) /*2019_04_01 забрать */
		{		
 		$startAdd = ($start) ? ' AND zhkhform_zhkh1.id > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkhform_zhkh1.distrId = '.$distr : '';
		//$retList = 'zhkh_message.id as messId, userId, user_name, distId, npId, l_name, zhkhSectorId, orgId, orgName, zhkh_sector.name as zhkhSectorName, initiator, dateCreate, incidentDate, reason, hardware, consumerNum, bossOfWork, bossOfCity, goodNewsMen, tempereture, badNewsMen, status, closeDate 	  ';	
		$LNK= new DBLink;
		$query = 'SELECT *, zhkhform_zhkh1.id AS formId FROM `zhkhform_zhkh1`, zhkh_type_data, gis_layers where zhkhform_zhkh1.distrId = gis_layers.l_id  AND zhkh_type_data.id = zhkhform_zhkh1.id_type_data 			
			'.$startAdd.$distrAdd.'		
			ORDER BY zhkhform_zhkh1.sendDate DESC, zhkhform_zhkh1.id ';		
		$queryLight = 'SELECT zhkhform_zhkh1.id AS formId, zhkhform_zhkh1.userId as userId, user_name, zhkhform_zhkh1.distrId as distrId, zhkhform_zhkh1.date as date, sendDate 
			FROM `zhkhform_zhkh1`, zhkh_type_data, gis_layers, users 
			WHERE zhkhform_zhkh1.distrId = gis_layers.l_id AND users.user_id = zhkhform_zhkh1.userId AND zhkh_type_data.id = zhkhform_zhkh1.id_type_data 			
			'.$startAdd.$distrAdd.'		
			ORDER BY zhkhform_zhkh1.sendDate DESC, zhkhform_zhkh1.id ';		
//		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query(($light)?$queryLight:$query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			$ret = $msgList;
			/*foreach ($msgList as $msg) {					
				$ret[] = (object) $msg;
				}	*/		
			}
		else 
			$ret = 0;
		return $ret;
		}
	function getNP($npId) /*2019_05_18 населённый пункт*/
		{		
 		$startAdd = ($start) ? ' AND zhkh_message.id > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkh_message.distId = '.$distr : '';
		$retList = 'zhkh_message.id as messId, userId, user_name, distId, npId, l_name, zhkhSectorId, orgId, orgName,  orgUsedForce, zhkh_sector.name as zhkhSectorName, initiator, dateCreate, incidentDate, reason, hardware, hardwareType, consumerNum, bossOfWork, bossOfCity, goodNewsMen, tempereture, badNewsMen, status, closeDate 	  ';	
		$LNK= new DBLink;	 
		$query = 'SELECT * FROM `gis_layers` WHERE `l_id` = '.$npId.'';		
		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$ret = $LNK->GetData(0, false);
			}
		else 
			$ret = 0;
		return $ret;
		}
	function getReasons($sectorId = 0) /*2019_12_18 забрать все  причины*/
		{		
			$LNK= new DBLink;	 
			$query = 'SELECT * FROM `zhkh_reason` ORDER BY sectorId, lvl, parentId, id 				
				 ';			 
			$LNK->Query($query);
			if($LNK->GetNumRows()){	
				$ret = $LNK->GetData(0, true);	
			}
			else 
				$ret = 0;
		return $ret;
		}
	function getMessage($mId, $param='') /*2019_04_01 забрать все  оперативные сообщения*/
		{		
		if($mId){
			$startAdd = ' AND zhkh_message.id =  '.$mId ;
			$retList = 'zhkh_message.id as messId, userId, user_name, distId, npId, l_name, zhkhSectorId, sphereJkh, orgId, orgName,  orgUsedForce, zhkh_sector.name as zhkhSectorName, initiator, dateCreate, incidentDate, reason, hardware, hardwareType, consumerNum, bossOfWork, bossOfCity, goodNewsMen, tempereture, badNewsMen, status, closeDate 	  ';	
			$LNK= new DBLink;	 
			$query = 'SELECT '.$retList.' FROM `zhkh_message` , users, zhkh_sector, gis_layers  
				where zhkh_message.userId = users.user_id 
				and  zhkh_message.zhkhSectorId = zhkh_sector.id 
				and zhkh_message.distId = gis_layers.l_id			
				'.$startAdd.'		
				 ';		
	//		echo $queryAdd ;
	//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__, 1);	 
			$LNK->Query($query);
			if($LNK->GetNumRows()){		
				$msg = $LNK->GetData(0, false);
				if(!$param){
					$msg['comments'] = $this->getMesComments($msg['messId']);
					$msg['npObj'] =  $this->getNP($msg['npId']);
					$msg['npName'] =  $msg['npObj']['l_name'];
					$ret = (object) $msg;	
				}else 	
					$ret = $msg[$param];	
			}
			else 
				$ret = 0;
		} else 
			$ret = 0;
		return $ret;
		}
	function getMessageAll($start, $distr ) /*2019_04_01 забрать все  оперативные сообщения*/
		{		
 		$startAdd = ($start) ? ' AND zhkh_message.id > '.$start : '';
 		$distrAdd = ($distr) ? ' AND zhkh_message.distId = '.$distr : '';
		$retList = 'zhkh_message.id as messId, userId, user_name, distId, npId, l_name, zhkhSectorId, sphereJkh, orgId, orgName,  orgUsedForce, zhkh_sector.name as zhkhSectorName, initiator, dateCreate, incidentDate, reason, hardwareId, hardware, hardwareType, consumerNum, bossOfWork, bossOfCity, goodNewsMen, tempereture, badNewsMen, status, closeDate 	  ';	
		$LNK= new DBLink;	 
		$query = 'SELECT '.$retList.' FROM `zhkh_message` , users, zhkh_sector, gis_layers  
			where zhkh_message.userId = users.user_id 
			and  zhkh_message.zhkhSectorId = zhkh_sector.id 
			and zhkh_message.distId = gis_layers.l_id			
			'.$startAdd.$distrAdd.'		
			ORDER BY status DESC, incidentDate DESC';		
		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($query);
		if($LNK->GetNumRows())
			{		
			$msgList = $LNK->GetData(0, true);
			foreach ($msgList as $msg) {
					$msg['comments'] = $this->getMesComments($msg['messId']);
					$msg['npObj'] =  $this->getNP($msg['npId']);
					$msg['npName'] =  $msg['npObj']['l_name'];
				$ret[] = (object) $msg;
				}			
			}
		else 
			$ret = 0;
		return $ret;
		}


/******************SETTERS***********************************************************************/	
	function newRep_pmo9($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = $curCell = $rowCnt = 0 ;
		$queryAdd = 'INSERT into `zhkhform_pasport9` (distrId , userId,  sendDate, cell_1, cell_2, cell_3, cell_4, cell_5, cell_6, cell_7,'. 
														'cell_8, cell_9, cell_10, cell_11, cell_12, cell_13,cell_14, cell_15, '.
														'cell_16, cell_17, cell_18, cell_19, cell_20, cell_21, cell_22, cell_23, cell_24, cell_25, cell_26, cell_27, cell_28, '.
					'  id_type_data)   VALUES ';
		$row = array();
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
				if(!isset($row[$tmpArr[2]]))
					$row[$tmpArr[2]] = '( \''.$param->frm_distId.'\' ,\''.$param->frm_userId.'\' ,\''.$sendDate.'\' , ';
				else 
					$row[$tmpArr[2]] .= ', ';
				$row[$tmpArr[2]] .= '\''.floatval($val).'\'';
				$counter ++;
			}
		}
		$counter = 0;
		foreach ($row as $item) {			
			$queryAdd .= ($counter)? ', ' : '';
			$queryAdd .= $item.', \''.($counter+1).'\')';
			$counter ++;
		}
//		echo $queryAdd;
		$ret = array();
		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($queryAdd);
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
	function newRep_pmo8($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = $curCell = $rowCnt = 0 ;
		$queryAdd = 'INSERT into `zhkhform_pasport8` (distrId , userId,  sendDate, cell_1, cell_2, cell_3, cell_4, cell_5, cell_6, cell_7,'. 
														'cell_8, cell_9, cell_10, cell_11, cell_12, cell_13,cell_14, cell_15, '.
					'  id_type_data)   VALUES ';
		$row = array();
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
				if(!isset($row[$tmpArr[2]]))
					$row[$tmpArr[2]] = '( \''.$param->frm_distId.'\' ,\''.$param->frm_userId.'\' ,\''.$sendDate.'\' , ';
				else 
					$row[$tmpArr[2]] .= ', ';
				$row[$tmpArr[2]] .= '\''.floatval($val).'\'';
				$counter ++;
			}
		}
		$counter = 0;
		foreach ($row as $item) {			
			$queryAdd .= ($counter)? ', ' : '';
			$queryAdd .= $item.', \''.($counter+1).'\')';
			$counter ++;
		}
//		echo $queryAdd;
		$ret = array();
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($queryAdd);
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
	function newRep_pmo7($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = $curCell = $rowCnt = 0 ;
		$queryAdd = 'INSERT into `zhkhform_pasport7` (distrId , userId,  sendDate, cell_1, cell_2, cell_3, cell_4, cell_5, cell_6, cell_7, cell_8, cell_9, cell_10,'.
					'  id_type_data)   VALUES ';
		$row = array();
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
				if(!isset($row[$tmpArr[2]]))
					$row[$tmpArr[2]] = '( \''.$param->frm_distId.'\' ,\''.$param->frm_userId.'\' ,\''.$sendDate.'\' , ';
				else 
					$row[$tmpArr[2]] .= ', ';
				$row[$tmpArr[2]] .= '\''.floatval($val).'\'';
				$counter ++;
			}
		}
		$counter = 0;
		foreach ($row as $item) {			
			$queryAdd .= ($counter)? ', ' : '';
			$queryAdd .= $item.', \''.($counter+1).'\')';
			$counter ++;
		}
//		echo $queryAdd;
		$ret = array();
		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($queryAdd);
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
	function upRep_pmo6($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = 'UPDATE  `zhkhform_pasport6` SET  userId = \''.$param->frm_userId.'\' ';
		$queryIfAdd .= ' WHERE id = \''.$param->recId.'\' AND sendDate = \''.$sendDate.'\' ';	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);

				$valNew = ((!$val)&&($tmpArr[2] == 'f'))?'0':$val;
				$queryAdd .= ', cell_'.$tmpArr[1].' = \''.$valNew.'\' ';
				$counter ++;
			}
		}
		$LNK->Query($queryAdd.' '.$queryIfAdd);
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
	function newRep_pmo6($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = 'INSERT into `zhkhform_pasport6` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\' ';
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
//				echo $tmpArr[2].'     '.', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
//				$valNew = ()
				$valNew = ((!$val)&&($tmpArr[2] == 'f'))?'0':$val;
				$queryAdd .= ', cell_'.$tmpArr[1].' = \''.$valNew.'\' ';
				$counter ++;
			}
		}
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
/*		$LNK= new DBLink;	
		$counter = 0;
		$rows = $values ='';
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = 'INSERT into `zhkhform_pasport6` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\'0\' ';	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
//				echo $tmpArr[2].'     '.', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
				switch($tmpArr[2]){
					case 'f':{
						$transValue = floatval($val);
					} break;
					case 'i':{
						$transValue = intval($val);
					} break;
					case 't':{
					}
					default :{
						$transValue = htmlspecialchars(trim($val),ENT_QUOTES);
					}
				}
				$queryAdd .= ', cell_'.$tmpArr[1].' = \''.$transValue.'\' ';
				$counter ++;
			}
		}
		$ret = array();
//		echo $queryAdd;
		$LNK->Query($queryAdd);
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
	function newRep_pmo5($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = $curCell = $rowCnt = 0 ;
		$queryAdd = 'INSERT into `zhkhform_pasport5` (distrId , userId,  sendDate, cell_1, cell_2, cell_3, cell_4, cell_5, cell_6, cell_7, cell_8, cell_9, cell_10,'.
					' cell_11, cell_12, cell_13, cell_14, cell_15, cell_16,  id_type_data)   VALUES ';
		$row = array();
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
				if(!isset($row[$tmpArr[2]]))
					$row[$tmpArr[2]] = '( \''.$param->frm_distId.'\' ,\''.$param->frm_userId.'\' ,\''.$sendDate.'\' , ';
				else 
					$row[$tmpArr[2]] .= ', ';
				$row[$tmpArr[2]] .= '\''.floatval($val).'\'';
				$counter ++;
			}
		}
		$counter = 0;
		foreach ($row as $item) {			
			$queryAdd .= ($counter)? ', ' : '';
			$queryAdd .= $item.', \''.($counter+1).'\')';
			$counter ++;
		}
//		echo $queryAdd;
		$ret = array();
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($queryAdd);
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
	function newRep_pmo4($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = $curCell = $rowCnt = 0 ;
		$queryAdd = 'INSERT into `zhkhform_pasport4` (distrId , userId,  sendDate, cell_1, cell_2, cell_3, cell_4, cell_5, cell_6, cell_7, cell_8, cell_9, cell_10,'.
					' cell_11, cell_12, cell_13, cell_14, cell_15, cell_16, cell_17, cell_18, cell_19, cell_20, id_type_data)   VALUES ';
		$row = array();
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
				if(!isset($row[$tmpArr[2]]))
					$row[$tmpArr[2]] = '( \''.$param->frm_distId.'\' ,\''.$param->frm_userId.'\' ,\''.$sendDate.'\' , ';
				else 
					$row[$tmpArr[2]] .= ', ';
				$row[$tmpArr[2]] .= '\''.floatval($val).'\'';
				$counter ++;
			}
		}
		$counter = 0;
		foreach ($row as $item) {			
			$queryAdd .= ($counter)? ', ' : '';
			$queryAdd .= $item.', \''.($counter+1).'\')';
			$counter ++;
		}
//		echo $queryAdd;
		$ret = array();
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($queryAdd);
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
	function newRep_pmo3($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = $curCell = $rowCnt = 0 ;
		$queryAdd = 'INSERT into `zhkhform_pasport3` (distrId , userId,  sendDate, cell_1, cell_2, cell_3, cell_4, cell_5, cell_6, id_type_data)   VALUES ';
		$row = '';
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
				if($tmpArr[2] != $curCell ){
					if($counter){
						$queryAdd .= ($rowCnt)?', ':'';
						$queryAdd .= '( \''.$param->frm_distId.'\' ,\''.$param->frm_userId.'\' ,\''.$sendDate.'\' , '.$row.'  \''.$curCell.'\' )';
						$row = '';
						$curCell = $tmpArr[2];
						$rowCnt++;
					}
					else{
						$curCell = $tmpArr[2];					
					}
				}
				$row .= '\''.floatval($val).'\', ';
				$counter ++;
			}
		}
		$queryAdd .= ', ( \''.$param->frm_distId.'\' ,\''.$param->frm_userId.'\' ,\''.$sendDate.'\' , '.$row.'  \''.$tmpArr[2].'\' )';
//		echo $queryAdd;
		$ret = array();
		$LNK->Query($queryAdd);
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
	function upRep_pmo2($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = 'UPDATE  `zhkhform_pasport2` SET  userId = \''.$param->frm_userId.'\' ';
		$queryIfAdd .= ' WHERE id = \''.$param->recId.'\' AND sendDate = \''.$sendDate.'\' ';	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);

				$valNew = ((!$val)&&($tmpArr[2] == 'f'))?'0':$val;
				$queryAdd .= ', cell_'.$tmpArr[1].' = \''.$valNew.'\' ';
				$counter ++;
			}
		}
		$LNK->Query($queryAdd.' '.$queryIfAdd);
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
	function newRep_pmo2($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = 'INSERT into `zhkhform_pasport2` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\' ';
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
//				echo $tmpArr[2].'     '.', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
//				$valNew = ()
				$valNew = ((!$val)&&($tmpArr[2] == 'f'))?'0':$val;
				$queryAdd .= ', cell_'.$tmpArr[1].' = \''.$valNew.'\' ';
				$counter ++;
			}
		}
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
	function newRep_pmo1($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = 0;
		$rows = $values ='';
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd[1] = 'INSERT into `zhkhform_pasport1` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\', id_type_data=1 ';	
		$queryAdd[2] = 'INSERT into `zhkhform_pasport1` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\', id_type_data=2 ';	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
//				echo $tmpArr[2].'     '.', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
				$queryAdd[$tmpArr[2]] .= ', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
				$counter ++;
			}
		}
		$ret = array();
		foreach ($queryAdd as $query) {
			if(!$ret['error']){
				$LNK->Query($query);
				if($LNK->error){
					$ret['error'] = 1;
					$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
					}
				else{
					$ret['error'] = 0;
					$ret['last_id'][] = $LNK->last_id;// = $LNK->error_string;
				}					
			}
		}
		return $ret;
		}
	function upRep_zhkh3($param, $sendDate, $insert7even = false) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = 0;
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = array();
		$queryAdd[1] = $queryAdd[2] = $queryAdd[3] = $queryAdd[4] = $queryAdd[5] = $queryAdd[6] = 'UPDATE `zhkhform_zhkh3` SET  userId = \''.$param->frm_userId.'\' ';	
		$queryAdd[7] = ($insert7even) ? 'INSERT into `zhkhform_zhkh3` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\', id_type_data=7 ': $queryAdd[6];	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
				$queryAdd[$tmpArr[2]] .= ', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
				$counter ++;
			}
		}
		$queryAdd[1] .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_type_data=1 ';
		$queryAdd[2] .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_type_data=2 ';
		$queryAdd[3] .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_type_data=3 ';
		$queryAdd[4] .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_type_data=4 ';
		$queryAdd[5] .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_type_data=5 ';
		$queryAdd[6] .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_type_data=6 ';
		$queryAdd[7] .= ($insert7even) ? '' : ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_type_data=7 ';
		$LNK->Query($queryAdd[1]);
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$LNK->Query($queryAdd[2]);
			if($LNK->error)
				{
				$ret['error'] = 1;
				$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
				}
			else
				{
				$LNK->Query($queryAdd[3]);
				if($LNK->error)
					{
					$ret['error'] = 1;
					$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
					}
				else
					{
					$LNK->Query($queryAdd[4]);
					if($LNK->error)
						{
						$ret['error'] = 1;
						$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
						}
					else
						{
						$LNK->Query($queryAdd[5]);
						if($LNK->error)
							{
							$ret['error'] = 1;
							$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
							}
						else
							{
							$LNK->Query($queryAdd[6]);
							if($LNK->error)
								{
								$ret['error'] = 1;
								$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
								}
							else
								{
								$LNK->Query($queryAdd[7]);
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
								}
							}
						}
					}
				}
			}
		return $ret;
		}
	function upRep_sfo($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = 0;
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = 'UPDATE `operation_info_sfo` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\', sendDate=\''.$sendDate.'\'';	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$addVal = floatval($val);
				$queryAdd .= ', '.$name.' = \''.$addVal.'\' ';
				$counter ++;
				}
			}
		$queryAdd .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\'';
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
	function upRep_1zhkh($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = 0;
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
//		$sendDate = time();
		$queryAdd = array();
		$queryAdd[1] = 'UPDATE `zhkhform_zhkh1` SET  userId = \''.$param->frm_userId.'\' ';	
		$queryAdd[2] = 'UPDATE `zhkhform_zhkh1` SET  userId = \''.$param->frm_userId.'\' ';	
		$queryAdd[3] = 'UPDATE `zhkhform_zhkh1` SET  userId = \''.$param->frm_userId.'\' ';	
		$queryAdd[4] = 'UPDATE `zhkhform_zhkh1` SET  userId = \''.$param->frm_userId.'\' ';	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
				$queryAdd[$tmpArr[2]] .= ', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
				$counter ++;
			}
		}
//		print_r($queryAdd);
		$queryAdd[1] .= ' WHERE distrId = \''.$param->frm_distId.'\' AND sendDate = \''.$sendDate.'\'AND id_type_data = 1 ';	
		$queryAdd[2] .= ' WHERE distrId = \''.$param->frm_distId.'\' AND sendDate = \''.$sendDate.'\'AND id_type_data = 2 ';	
		$queryAdd[3] .= ' WHERE distrId = \''.$param->frm_distId.'\' AND sendDate = \''.$sendDate.'\'AND id_type_data = 3 ';	
		$queryAdd[4] .= ' WHERE distrId = \''.$param->frm_distId.'\' AND sendDate = \''.$sendDate.'\'AND id_type_data = 4 ';	
		$LNK->Query($queryAdd[1]);
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$LNK->Query($queryAdd[2]);
			if($LNK->error)
				{
				$ret['error'] = 1;
				$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
				}
			else
				{
				$LNK->Query($queryAdd[3]);
				if($LNK->error)
					{
					$ret['error'] = 1;
					$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
					}
				else
					{
					$LNK->Query($queryAdd[4]);
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
			}}}
		return $ret;
		}
	function newRep_1zhkh($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$format = array ('i', 'f', 'f', 'i', 'f' , 'f' , 'f' , 'f' , 'f' , 'f' , 'f' , 'f' , 'f' , 'f' , 'f' , 'f' , 'f' , 'f' , 'f' , 'f' , 'f' , 'f' , 't'  );
		$counter = 0;
		$LNK= new DBLink;	
//		$sendDate = time();
		$queryAdd = array();
		$queryAdd[1] = 'INSERT into `zhkhform_zhkh1` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\', sendDate = \''.$sendDate.'\', id_type_data = 1 ';	
		$queryAdd[2] = 'INSERT into `zhkhform_zhkh1` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\', sendDate = \''.$sendDate.'\', id_type_data = 2 ';	
		$queryAdd[3] = 'INSERT into `zhkhform_zhkh1` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\', sendDate = \''.$sendDate.'\', id_type_data = 3 ';	
		$queryAdd[4] = 'INSERT into `zhkhform_zhkh1` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\', sendDate = \''.$sendDate.'\', id_type_data = 4 ';	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
				$queryAdd[$tmpArr[2]] .= ', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
				$counter ++;
			}
		}
//		print_r($queryAdd);
		$LNK->Query($queryAdd[1]);
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$LNK->Query($queryAdd[2]);
			if($LNK->error)
				{
				$ret['error'] = 1;
				$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
				}
			else
				{
				$LNK->Query($queryAdd[3]);
				if($LNK->error)
					{
					$ret['error'] = 1;
					$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
					}
				else
					{
					$LNK->Query($queryAdd[4]);
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
			}}}
		return $ret;
		}
	function upRep_toplPril2($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = 0;
		$LNK= new DBLink;	
		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = array();
		$queryAdd[1] =  $queryAdd[3] = $queryAdd[4] = $queryAdd[5] = $queryAdd[6] =  $queryAdd[7] =  $queryAdd[8] =  $queryAdd[9] =
					'UPDATE `fact_fuel_use` SET  userId = \''.$param->frm_userId.'\' ';	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
				$queryAdd[$tmpArr[2]] .= ', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
				$counter ++;
			}
		}
		$queryAdd[1] .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_fuel=1 ';
		$queryAdd[3] .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_fuel=3 ';
		$queryAdd[4] .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_fuel=4 ';
		$queryAdd[5] .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_fuel=5 ';
		$queryAdd[6] .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_fuel=6 ';
		$queryAdd[7] .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_fuel=7 ';
		$queryAdd[8] .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_fuel=8 ';
		$queryAdd[9] .= ' WHERE  distrId = \''.$param->frm_distId.'\' AND sendDate=\''.$sendDate.'\' AND id_fuel=9 ';
		$LNK->Query($queryAdd[1]);
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$LNK->Query($queryAdd[3]);
			if($LNK->error)
				{
				$ret['error'] = 1;
				$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
				}
			else
				{
				$LNK->Query($queryAdd[4]);
				if($LNK->error)
					{
					$ret['error'] = 1;
					$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
					}
				else
					{
					$LNK->Query($queryAdd[5]);
					if($LNK->error)
						{
						$ret['error'] = 1;
						$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
						}
					else
						{
						$LNK->Query($queryAdd[6]);
						if($LNK->error)
							{
							$ret['error'] = 1;
							$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
							}
						else
							{
							$LNK->Query($queryAdd[7]);
							if($LNK->error)
								{
								$ret['error'] = 1;
								$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
								}
							else
								{
								$LNK->Query($queryAdd[8]);
								if($LNK->error)
									{
									$ret['error'] = 1;
									$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
									}
								else
									{
									$LNK->Query($queryAdd[9]);
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
									}
								}
							}
						}
					}
				}
			}
		return $ret;
		}
	function newRep_toplPril2($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = 0;
		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = array();
		$queryAdd[1] = 'INSERT into `fact_fuel_use` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\',  	id_fuel=1 ';	
		$queryAdd[3] = 'INSERT into `fact_fuel_use` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\',  	id_fuel=3 ';	
		$queryAdd[4] = 'INSERT into `fact_fuel_use` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\',  	id_fuel=4 ';	
		$queryAdd[5] = 'INSERT into `fact_fuel_use` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\',  	id_fuel=5 ';	
		$queryAdd[6] = 'INSERT into `fact_fuel_use` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\',  	id_fuel=6 ';	
		$queryAdd[7] = 'INSERT into `fact_fuel_use` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\',  	id_fuel=7 ';	
		$queryAdd[8] = 'INSERT into `fact_fuel_use` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\',  	id_fuel=8 ';	
		$queryAdd[9] = 'INSERT into `fact_fuel_use` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\',  	id_fuel=9 ';	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
//				echo $tmpArr[2].'     '.', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
				$queryAdd[$tmpArr[2]] .= ', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
				$counter ++;
			}
		}
		$ret = array();
		foreach ($queryAdd as $query) {
			if(!$ret['error']){
				$LNK->Query($query);
				if($LNK->error){
					$ret['error'] = 1;
					$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
					}
				else{
					$ret['error'] = 0;
					$ret['last_id'][] = $LNK->last_id;// = $LNK->error_string;
				}					
			}
		}
	}
	function upRep_zhkh2($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		//print_r($param);
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$counter = $curCell = $rowCnt = 0 ;
		$row = array();
		$queryIfAdd .= ' WHERE distrId = \''.$param->frm_distId.'\' AND sendDate = \''.$sendDate.'\' ';	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
				$row[$tmpArr[2]] .= ', cell_'.$tmpArr[1].' = \''.floatval($val).'\'';
				$counter ++;
			}
		}
		$ret = 0;
		foreach ($row as $name => $val ) {			
			$queryUp = 'UPDATE `zhkhform_zhkh2` SET  userId = \''.$param->frm_userId.'\' '.$val.' '.$queryIfAdd.' AND id_type_data = '.$name;
			$LNK->Query($queryUp);
			if($LNK->error){
				$ret ++;
				}
		}		
		return $ret;
	}
	function newRep_zhkh2($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
//		print_r($param);
		$LNK= new DBLink;	
		$counter = $curCell = $rowCnt = 0 ;
		$queryAdd = 'INSERT into `zhkhform_zhkh2` (distrId , userId,  sendDate, cell_1, cell_2, cell_3, cell_4, cell_5, cell_6, cell_7,'. 
														'cell_8, cell_9, cell_10, cell_11, cell_12, '.
					'  id_type_data)   VALUES ';
		$row = array();
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
				if(!isset($row[$tmpArr[2]]))
					$row[$tmpArr[2]] = '( \''.$param->frm_distId.'\' ,\''.$param->frm_userId.'\' ,\''.$sendDate.'\' , ';
				else 
					$row[$tmpArr[2]] .= ', ';
				$row[$tmpArr[2]] .= '\''.floatval($val).'\'';
				$counter ++;
			}
		}
		$counter = 0;
		foreach ($row as $item) {			
			$queryAdd .= ($counter)? ', ' : '';
			$queryAdd .= $item.', \''.($counter+1).'\')';
			$counter ++;
		}
//		echo $queryAdd;
		$ret = array();
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$LNK->Query($queryAdd);
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
	function upRep_zhkh24($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = 0;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = 'UPDATE `zhkhform_zhkh24` SET  userId = \''.$param->frm_userId.'\' ';
//		print_r($param);
		$counter = $curCell = $rowCnt = 0 ;
		$row = array();
		$queryIfAdd .= ' WHERE distrId = \''.$param->frm_distId.'\' AND sendDate = \''.$sendDate.'\' ';	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
				$row[$tmpArr[2]] .= ', cell_'.$tmpArr[1].' = \''.floatval($val).'\'';
				$counter ++;
			}
		}
		$ret = 0;
		foreach ($row as $name => $val ) {			
			$queryUp = 'UPDATE `zhkhform_zhkh24` SET  userId = \''.$param->frm_userId.'\' '.$val.' '.$queryIfAdd.' AND id_factor = '.$name;
			$LNK->Query($queryUp);
			if($LNK->error){
				$ret ++;
				}
			}	 				
		return $ret;
		}	
	function newRep_zhkh24($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = 0;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = 'INSERT into `zhkhform_zhkh24` (distrId , userId,  sendDate, cell_1, id_factor)   VALUES ';
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				if($counter)
					$queryAdd .= ', ';
				$tmpArr = explode('_',$name);
				$queryAdd .= '( \''.$param->frm_distId.'\' ,\''.$param->frm_userId.'\' ,\''.$sendDate.'\' , \''.floatval($val).'\',  \''.$tmpArr[2].'\' )';
				$counter ++;
			}
		}
//		echo $queryAdd;
		$ret = array();
		$LNK->Query($queryAdd);
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
	function newRep_zhkh3($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = 0;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = array();
		$queryAdd[1] = 'INSERT into `zhkhform_zhkh3` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\', id_type_data=1 ';	
		$queryAdd[2] = 'INSERT into `zhkhform_zhkh3` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\', id_type_data=2 ';	
		$queryAdd[3] = 'INSERT into `zhkhform_zhkh3` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\', id_type_data=3 ';	
		$queryAdd[4] = 'INSERT into `zhkhform_zhkh3` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\', id_type_data=4 ';	
		$queryAdd[5] = 'INSERT into `zhkhform_zhkh3` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\', id_type_data=5 ';	
		$queryAdd[6] = 'INSERT into `zhkhform_zhkh3` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\', id_type_data=6 ';	
		$queryAdd[7] = 'INSERT into `zhkhform_zhkh3` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\', id_type_data=7 ';	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
//				echo $tmpArr[2].'     '.', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
				$queryAdd[$tmpArr[2]] .= ', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
				$counter ++;
			}
		}
		$LNK->Query($queryAdd[1]);
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$LNK->Query($queryAdd[2]);
			if($LNK->error)
				{
				$ret['error'] = 1;
				$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
				}
			else
				{
				$LNK->Query($queryAdd[3]);
				if($LNK->error)
					{
					$ret['error'] = 1;
					$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
					}
				else
					{
					$LNK->Query($queryAdd[4]);
					if($LNK->error)
						{
						$ret['error'] = 1;
						$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
						}
					else
						{
						$LNK->Query($queryAdd[5]);
						if($LNK->error)
							{
							$ret['error'] = 1;
							$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
							}
						else
							{
							$LNK->Query($queryAdd[6]);
							if($LNK->error)
								{
								$ret['error'] = 1;
								$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
								}						
							else
								{
								$LNK->Query($queryAdd[7]);
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
								}
							}
						}
					}
				}
			}
		return $ret;
		}
	function newRep_sfo($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$counter = 0;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = '';
		$queryAdd = 'INSERT into `operation_info_sfo` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\'';	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$addVal = floatval($val);
				$queryAdd .= ', '.$name.' = \''.$addVal.'\' ';
				$counter ++;
				}
			}
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
	function upRep_toplPril1($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		//$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = 'UPDATE `zhkhform_toplPr1` SET  userId = \''.$param->frm_userId.'\' ';
		$queryIfAdd .= ' WHERE distrId = \''.$param->frm_distId.'\' AND sendDate = \''.$sendDate.'\' ';	
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
//				echo $tmpArr[2].'     '.', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
				$queryAdd .= ', cell_'.$tmpArr[1].' = \''.$val.'\' ';
				$counter ++;
			}
		}
		$LNK->Query($queryAdd.' '.$queryIfAdd);
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
	function newRep_toplPril1($param, $sendDate) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
		$queryAdd = 'INSERT into `zhkhform_toplPr1` SET  userId = \''.$param->frm_userId.'\', distrId = \''.$param->frm_distId.'\',  sendDate=\''.$sendDate.'\' ';
		foreach ($param as $name => $val ) {
			if(strpos($name , 'cell_')!== false){
				$tmpArr = explode('_',$name);
//				echo $tmpArr[2].'     '.', cell_'.$tmpArr[1].' = \''.floatval($val).'\' ';
				$queryAdd .= ', cell_'.$tmpArr[1].' = \''.$val.'\' ';
				$counter ++;
			}
		}
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
	function newOrg($param) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$typeAdd = (isset($param->commClose))?', type = 2 ':', type = 1 ';
		$queryAdd = 'INSERT into `zhkh_org` SET  name = \''.$param['name'].'\'  , 
						 	layerId = \''.$param['layerId'].'\'  , 
							info = \''.$param['info'].'\'  ';
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
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
	function closeMessage($messId) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$typeAdd = (isset($param->commClose))?', type = 2 ':', type = 1 ';
		$queryAdd = 'UPDATE  `zhkh_message` SET   	status = 0  ,  closeDate = FROM_UNIXTIME( \''.time().'\') WHERE id = '.$messId.'';
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
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
	function upMessage($messId) /*2019_10_31 обновление оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$queryAdd = 'UPDATE  `zhkh_message` SET   	 	dateUp = FROM_UNIXTIME( \''.time().'\') WHERE id = '.$messId.'';
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
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
	function newComment($param) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$typeAdd = (isset($param->commClose))?', type = 2 ':', type = 1 ';
		$queryAdd = 'INSERT into `zhkh_mesComment` SET  userId = \''.$param->userId.'\'  ,  mesId = \''.$param->messId.'\'  ,  text = \''.trim($param->commBody).'\'  '.$typeAdd;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
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
			$this->upMessage($param->messId);
			}
		return $ret;
		}	
		
	function newMessage($param) /*2019_04_01 добавление нового оперативного сообщения*/
		{
		$LNK= new DBLink;	
		$queryAdd = '';
		$queryAdd = 'INSERT into `zhkh_message` SET  userId = \''.$param->userId.'\'  ';		
		$queryAdd .= ($param->type)?', zhkhSectorId = \''.$param->type.'\' ': '';
		$queryAdd .= ($param->distId)?', distId = \''.$param->distId.'\' ': '';
		$queryAdd .=  ', npId = \''.$param->npId.'\' ' ;
		$queryAdd .= ($param->incidentDateTS)?', incidentDate  = FROM_UNIXTIME( \''.$param->incidentDateTS.'\') ': '';
		$queryAdd .= ($param->reason)?', reason = \''.trim($param->reason).'\' ': '';
		$queryAdd .= ($param->hardwareId)?', hardwareId = \''.$param->hardwareId.'\' ': '';
		$queryAdd .= ($param->hardware)?', hardware = \''.trim($param->hardware).'\' ': '';
		$queryAdd .= ($param->potrebiteli)?', consumerNum = \''.$param->potrebiteli.'\' ': '';
		$queryAdd .=  ', orgId = \''.$param->orgId.'\' ';
		$queryAdd .= ($param->org)?', orgName = \''.$param->org.'\' ': '';
		$queryAdd .= ($param->orgUsedForce)?', orgUsedForce = \''.$param->orgUsedForce.'\' ': '';
		$queryAdd .= ($param->hardwareType)?', hardwareType = \''.$param->hardwareType.'\' ': '';
		$queryAdd .= ($param->initiator)?', initiator = \''.$param->initiator.'\' ': '';
		$queryAdd .= ($param->bossOfWork)?', bossOfWork = \''.$param->bossOfWork.'\' ': '';
		$queryAdd .= ($param->bossOfCity)?', bossOfCity  = \''.$param->bossOfCity.'\' ': '';
		$queryAdd .= ($param->goodNewsMen)?', goodNewsMen  = \''.$param->goodNewsMen.'\' ': '';
		$queryAdd .= ($param->tempereture)?', tempereture   = \''.$param->tempereture.'\' ': '';
		$queryAdd .= ($param->badNewsMen)?', badNewsMen   = \''.$param->badNewsMen.'\' ': '';		
//		echo $queryAdd ;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);	 
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


	}
?>