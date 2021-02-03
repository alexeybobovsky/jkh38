<?php
//require_once("main.inc.php");
class GetContent
	{
	var $last_query;
	var $child = array();
	var $idInUse = array();
	var $childNum;
	var $childLevel;
	var $rekCount;
	var $curRight;
	function GetConfigValueById($id) /*получает значение константы */
		{
		$query = 'SELECT * FROM config  WHERE conf_id = \''.intval($id).'\'';
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if ($LNK->GetNumRows())
			{
			$ret = $LNK->GetData(array('conf_name', 'conf_value', 'conf_id', 'conf_type', 'conf_comment'), false);
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		}
	function GetAllConfigTable() /*получает все константы */
		{
		$query = 'SELECT * FROM config  ';
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$ret_arr  = array('conf_name', 'conf_value', 'conf_id', 'conf_type', 'conf_comment');
		$LNK->Query($query);
		if ($LNK->GetNumRows())
			{
			$ret = $LNK->GetData($ret_arr, true);
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		}
	function GetContent()
		{
		$this->childNum = 0;
		$this->childLevel = 0;
		$this->rekCount = 0;
		$this->curRight = 0;
		}		
	function Reset()
		{
		$this->child = array();		
		$this->idInUse = array();		
		$this->childNum = 0;
		$this->childLevel = 0;
		$this->rekCount = 0;
		$this->curRight = 0;
		}
	function GetCurBlockBody($id, $param) /*указанные  блоки из таблицы SiteBody через sb_id  14_08_2007 */
		{
		$ret = array();
		$query = 'SELECT * FROM SiteBody WHERE sc_id = '.$id.' ORDER BY sb_name, sb_order';
		$LNK= new DBLink;
		$ret_arr  = array('sc_id', 'sb_id','sb_name', 'sb_type', 'sb_value');
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData($ret_arr, true);
		if ($LNK->GetNumRows())
			{
			for($i=0; $i<count($ret_tmp); $i++)
				{
				for($k=0; $k<count($param); $k++)
					{
					if($ret_tmp[$i]['sb_name']==$param[$k])
						{
						$ret[$param[$k]]['name'] = 	$ret_tmp[$i]['sb_name'];
						$ret[$param[$k]]['id'] = 	$ret_tmp[$i]['sb_id'];
						$ret[$param[$k]]['value'] = $ret_tmp[$i]['sb_value'];				
						$ret[$param[$k]]['order'] = $ret_tmp[$i]['sb_order'];
						$ret[$param[$k]]['type'] = 	$ret_tmp[$i]['sb_type'];				
						$ret[$param[$k]]['sc_id'] = $ret_tmp[$i]['sc_id'];
						}
					}
				}
			}
/*		print_r($ret_tmp);
		print_r($ret);*/
		//print_r($ret);					
		return $ret;		
		}
	function GetCurLvl($treeTmp) /*получает уровень вызова текущего обработчика (handler) согласно URL-а (09_08_2007)*/
		{
		$tree = array();
		for($i=1; $i<count($treeTmp); $i++)
			{
			$tree[$i-1] = $treeTmp[$i];
			$tree[$i-1]['level'] --; 
			}
		return $tree;				
		}
	function MoveFirstElementFromTree($treeTmp) /*Удаляет корень дерева с пересчетом уровней(03_06_2007)*/
		{
		$tree = array();
		for($i=1; $i<count($treeTmp); $i++)
			{
			$tree[$i-1] = $treeTmp[$i];
			$tree[$i-1]['level'] --; 
			}
		return $tree;				
		}
	function GetThread($id) /*получает всю ветку (01_03_07)*/
		{
		$query = 'SELECT * FROM SiteCatalog where sc_thread = '.$id;
		$ret_arr  = array('sc_id', 'sc_parId', 'sc_name');
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret = $LNK->GetData($ret_arr, true);
		return $ret;
		}
	function GetNodePath($id, $useLink) /*получает путь к узлу от корня ()поднимается от родителя к родителю) 31_05_2007  $useLink - использовать ли link*/
		{
		$query = 'SELECT * FROM SiteCatalog';
		
		$ret_arr  = array('sc_id', 'sc_parId', 'sc_name');
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData($ret_arr, true);
		if ($LNK->GetNumRows())
			{
			$stop=0;
			$path = array();
			$parCnt = 0;
			$curId = $id;
			do
				{
				$l = 0;
				$stop1=0;
				do
					{
					if($ret_tmp[$l]['sc_id']==$curId)
						{
						if($useLink)
							{
							$body = $this->GetCurNode($ret_tmp[$l]['sc_id'], 0);
							}
						$path[] = ($body['link'])?$body['link']:$ret_tmp[$l]['sc_name'];
						$curId = $ret_tmp[$l]['sc_parId'];
						$stop1++;
						$parCnt++; 
						}
					$l++;
					}
				while(($l<count($ret_tmp))&&(!$stop1));
				}
			while($curId);
			asort($path);
			reset($path);
			$retPath = '';
			while (list ($key, $val) = each ($path)) 
				{
				$retPath .= '/'.$val;
				}			
			$ret = $retPath;
			}
		else
			{
			$ret = '';
			}
		return $ret;		
		}	
	function GetCurNodePar($id, $param) /*получает параметр узла*/
		{
		$query = 'SELECT * FROM SiteCatalog WHERE sc_id = '.$id;
		$ret_arr  = $param; //array('sc_id','sc_handler', 'sc_bodyTable', 'sc_bodyIdName', 'sc_name', 'sc_menu', 'lang_id', 'sc_order');
		$LNK= new DBLink;
//		$ret=array();
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if ($LNK->GetNumRows())
			{
			$ret = $LNK->GetData($ret_arr, false);
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		}	
	function GetBlockBodyViaId($id) /*простые блоки из таблицы SiteBody через sb_id - ассоциативный массив*/
		{
		$ret = array();
		$query = 'SELECT * FROM SiteBody WHERE sb_id = '.$id.' ORDER BY sb_name, sb_order';
		$LNK= new DBLink;
		$ret_arr  = array('sc_id', 'sb_id','sb_name', 'sb_type', 'sb_value');
		//$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData($ret_arr, false);
		if ($LNK->GetNumRows())
			{
			$ret['name'] = 	$ret_tmp['sb_name'];
			$ret['id'] = 	$ret_tmp['sb_id'];
			$ret['value'] = $ret_tmp['sb_value'];				
			$ret['order'] = $ret_tmp['sb_order'];
			$ret['type'] = 	$ret_tmp['sb_type'];				
			$ret['sc_id'] = $ret_tmp['sc_id'];
			}
/*		print_r($ret_tmp);
		print_r($ret);*/
		//print_r($ret);					
		return $ret;		
		}
	function GetChildren1Level($parId, $curLang, $body_type) /*получает всех детей первого уровня*/
		{
		global $USER;
//		print_r($_SESSION);
		if($curLang)
			$query = 'SELECT * FROM SiteCatalog WHERE sc_parId = '.$parId.' And (Lang_id = '.$USER->language.' OR Lang_id = 0)  ORDER BY sc_parId, sc_order';
		else
			$query = 'SELECT * FROM SiteCatalog WHERE sc_parId = '.$parId.'  ORDER BY sc_parId, sc_order';			
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);		
		$ret_arr  = array('sc_id', 'sc_parId', 'sc_thread', 'sc_order', 'sc_menu' ,'sc_handler', 'sc_bodyTable', 'sc_bodyIdName', 'sc_name');
		if(!$curLang)
			{
			$ret_arr[] =  'lang_id'; 
			}
		$LNK->Query($query);
//		$ret['child'] /*_tmp*/ = $LNK->GetData($ret_arr, false);
		$numRows = $LNK->GetNumRows();
		if ($numRows)
			{
			$tmp_cat = $LNK->GetData($ret_arr, true);
			for ($i=0; $i<$numRows; $i++)
				{
				$tmp_child['haveBrother'] = ($i<($numRows-1))?1:0;					
				$tmp_child['catalog'] = $tmp_cat[$i];
				if($body_type == 1)
					$tmp_child['body'] = $this->GetBlockBody($tmp_cat[$i]['sc_id']);
				elseif($body_type == 2)
					$tmp_child['body'] = $this->GetBlockBody_asis($tmp_cat[$i]['sc_id']);
				elseif($body_type == 0)
					{
					/*no body return*/
					}
				$ret[] = $tmp_child;
				}
			return $ret;
			}
		else
			{
			return 0;
			}
		}	
	function GetBrothers($id) /*получает всех братьев данного узла*/
		{
		$query1 = 'SELECT sc_parId FROM SiteCatalog WHERE sc_id =  '.$id;
		$ret_arr_tmp  = array('sc_parId');
		
		$ret_arr  = array('sc_id','sc_handler', 'sc_bodyTable', 'sc_bodyIdName', 'sc_name', 'sc_menu', 'lang_id', 'sc_order');
		$LNK= new DBLink;
//		$ret=array();
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query1);
		if ($LNK->GetNumRows())
			{
			$parId = $LNK->GetData($ret_arr_tmp, false);
			$query = 'SELECT * FROM SiteCatalog WHERE sc_parId ='.$parId['sc_parId'].' ORDER BY sc_order';
			$LNK->Query($query);
			if ($LNK->GetNumRows())
				{
				$retAr['catalog'] = $LNK->GetData($ret_arr, true);
//			$ret['catalog'] = $LNK->GetData($ret_arr, true);
				for($i=0; $i<count($retAr['catalog']); $i++)
					{
						$ret[$i]['catalog'] = $retAr['catalog'][$i];
						$ret[$i]['body'] = $this->GetBlockBody($retAr['catalog'][$i]['sc_id']);
					}
//			$ret['body'] = $this->GetBlockBody($ret['catalog']['sc_id']);
				}
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		}	
	function GetMenuItems($parId, $level, $body_type, $parRight) /*построение меню*/
		{
		$ACL = new ACL_class;
		if((!$this->rekCount)&&(!$parRight))
			{
			$parRight = $ACL->GetRootRight();
			}
		$this->rekCount ++;
		global $USER;		
		if(!$parId)
			$query = 'SELECT * FROM SiteCatalog WHERE sc_menu = 1 And sc_published = 1 And (Lang_id = '.$USER->language.' OR Lang_id = 0) ORDER BY sc_parId, sc_order';
		else
			$query = 'SELECT * FROM SiteCatalog WHERE sc_menu = 1 And sc_published = 1 And sc_parId = '.$parId.' And (Lang_id = '.$USER->language.' OR Lang_id = 0) ORDER BY sc_parId, sc_order';		
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$ret_arr  = array('sc_id', 'sc_parId', 'sc_thread', 'sc_handler', 'sc_name', 'user_id' ,'sc_order', 'sc_published');
		$LNK->Query($query);
		$numRows = $LNK->GetNumRows();
		if ($numRows)
			{			
			$tmp_cat = $LNK->GetData($ret_arr, true);
				{
				for ($i=0; $i<$numRows; $i++)
					{
					if(!in_array( $tmp_cat[$i]['sc_id'], $this->idInUse))
						{
						$tmp_child['right'] = $ACL->GetCurRight($tmp_cat[$i]['sc_id'], 'SiteCatalog', 'sc_id', $parRight, -1);
//						echo '<hr>level ='.$level.'; count = '.$this->rekCount.': '.$query;
						if(($this->rekCount > 1)&&($tmp_child['right']>0))
							$this->childNum++;
/*						else
							$this->curRight = $ACL->GetRootRight();*/
						$curChildNum = $this->childNum;
						$tmp_child['haveBrother'] = ($i<($numRows-1))?1:0;					
						$this->idInUse[] = $tmp_cat[$i]['sc_id'];
						$tmp_child['catalog'] = $tmp_cat[$i];				
						if($body_type == 1)
							$tmp_child['body'] = $this->GetBlockBody($tmp_cat[$i]['sc_id']);
						elseif($body_type == 2)
							$tmp_child['body'] = $this->GetBlockBody_asis($tmp_cat[$i]['sc_id']);					
						$tmp_child['level'] = $level;
						if ($tmp_child['right']>0)
							{
							$this->child[] = $tmp_child;				
							}
						$res = $this->GetMenuItems($tmp_cat[$i]['sc_id'], $level+1, $body_type, $tmp_child['right']);
						if ($tmp_child['right']>0)
							{						
							$this->child[$curChildNum]['numChild'] = $res;
							}
/*						else
							{
							echo $tmp_cat[$i]['sc_id'].' - '.$numRows.' - '.$query;
							}*/
/*						if($parId == 85)
							echo '<hr>'.$query;*/
						}
					}
				}
//				print_r( $this->idInUse);
			return $numRows;
			}			
		else
			{
			return 0;
			}
		//return $ret;		
		}
	function GetAllLanguages($ret_type) /*получает все языки */
		{
		global $USER;
		$query = 'SELECT * FROM  Lang order by lang_id';
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$ret_arr  = array('lang_id','lang_name');
		$LNK->Query($query);
		if ($LNK->GetNumRows())
			{
			$ret_tmp = $LNK->GetData($ret_arr, true);
			for($i=0; $i<count($ret_tmp); $i++)
				{
				if($ret_type==0)
					{
					$ret[$ret_tmp[$i]['lang_name']] = $ret_tmp[$i]['lang_id'];
					}
				elseif($ret_type == 1)
					{
					$ret['value'][] = $ret_tmp[$i]['lang_id'];
					$ret['caption'][] = $ret_tmp[$i]['lang_name'];
					}				
				elseif($ret_type == 2)
					{
					$ret[$ret_tmp[$i]['lang_id']] = $ret_tmp[$i]['lang_name'];
					}				
				}
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		}
	function GetCurNode($id, $body) /*получает данный узел и все его свойства*/
		{
		$query = 'SELECT * FROM SiteCatalog WHERE sc_id = '.$id.' ORDER BY sc_order';
		$ret_arr  = array('sc_id','sc_handler', 'sc_published',  'sc_name', 'sc_menu', 'sc_system', 'lang_id', 'sc_order', 'sc_parId', 'sc_thread', 'user_id');
		$LNK= new DBLink;
//		$ret=array();
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if ($LNK->GetNumRows())
			{
			$ret['catalog'] = $LNK->GetData($ret_arr, false);
			if($body>=0)
				{
				if(!$body)
					$ret['body'] = $this->GetBlockBody($ret['catalog']['sc_id']);
				else
					$ret['body'] = $this->GetBlockBody_asis($ret['catalog']['sc_id']);			
				}
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		}	
	function GetAllConfig() /*получает все константы */
		{
		global $USER;
		$query = 'SELECT * FROM config  ';
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$ret_arr  = array('conf_name','conf_value');
		$LNK->Query($query);
		if ($LNK->GetNumRows())
			{
			$ret_tmp = $LNK->GetData($ret_arr, true);
			for($i=0; $i<count($ret_tmp); $i++)
				{
				$ret[$ret_tmp[$i]['conf_name']] = $ret_tmp[$i]['conf_value'];
				}
			/********************************Задел на будующее - когда будут другие города - удалить*****************************************/
			$ret['curCity'] = 1;
			/*****************************************************************************************************************************/
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		}
	function GetConfigValue($name) /*получает значение константы */
		{
		global $USER;
		$query = 'SELECT * FROM config  WHERE conf_name = '.$name;
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
//		$ret_arr  = array('sc_id','sc_handler', 'sc_bodyTable', 'sc_bodyIdName', 'sc_name');
		$LNK->Query($query);
		if ($LNK->GetNumRows())
			{
			$ret = $LNK->GetData('conf_value', false);
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		}
	function GetFirstChild($parId, $inMenu, $body) /*поиск первого наследника */
		{
		global $USER;
		$menuInp = ($inMenu)?' AND sc_menu = 1 ':'';			
		$query = 'SELECT * FROM SiteCatalog WHERE sc_parId = '.$parId.$menuInp.' And (Lang_id = '.$USER->language.' OR Lang_id = 0) ORDER BY sc_order';
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$ret_arr  = array('sc_id','sc_handler', 'sc_bodyTable', 'sc_bodyIdName', 'sc_name');
		$LNK->Query($query);
		if ($LNK->GetNumRows())
			{
			$ret['catalog'] = $LNK->GetData($ret_arr, false);
			if($body>=0)
				{
				if(!$body)
					$ret['body'] = $this->GetBlockBody($ret['catalog']['sc_id']);
				else
					$ret['body'] = $this->GetBlockBody_asis($ret['catalog']['sc_id']);			
				}
//			$ret['body'] = $this->GetBlockBody($ret['catalog']['sc_id']);
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		}
	function GetChildrenNum($parId) /*количество потомков */
		{
		global $USER;
		$query = 'SELECT * FROM SiteCatalog WHERE sc_parId = '.$parId.' And (Lang_id = '.$USER->language.' OR Lang_id = 0)  ORDER BY sc_parId, sc_order';
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$ret_arr  = array('sc_id', 'sc_parId', 'sc_order' ,'sc_handler', 'sc_bodyTable', 'sc_bodyIdName', 'sc_name');
		$LNK->Query($query);
		if ($LNK->GetNumRows())
			{
			$tmp_cat = $LNK->GetData($ret_arr, true);
			for ($i=0; $i<$LNK->GetNumRows(); $i++)
				{
				$this->childNum ++;
				$this->GetChildrenNum($tmp_cat[$i]['sc_id']);
				}
			}		
		}	
	function GetChildren($parId, $level, $body_type, $curLang, $parRight, $rightLimit, $thread, $system) /*поиск потомков $body_type - в каком виде возвращать свойства mod 29_03_2007 - выдергивание отдельных веток*/
		{
		$ACL = new ACL_class;
		if((!$this->rekCount)&&(!$parRight))
			{
			$parRight = $ACL->GetRootRight();
			}			
		if(!$system) // 0 - все, 1 - системные, 2 - не системные
			$threadStr = '';
		elseif($system == 1)
			$threadStr = ' AND sc_system = 1 ';
		elseif($system == 2)
			$threadStr = ' AND sc_system = 0 ';
		$threadStr .= ($thread)?' AND sc_thread = '.$thread:'';
		$this->rekCount ++;
		global $USER;
		if($curLang)
			$query = 'SELECT * FROM SiteCatalog WHERE sc_parId = '.$parId.$threadStr.' And (Lang_id = '.$USER->language.' OR Lang_id = 0)  ORDER BY sc_parId, sc_order';
		else
			$query = 'SELECT * FROM SiteCatalog WHERE sc_parId = '.$parId.$threadStr.'  ORDER BY sc_parId, sc_order';			
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);		
		$ret_arr  = array('sc_id', 'sc_parId', 'sc_thread', 'sc_order', 'sc_menu' ,'sc_handler', 'sc_published', 'user_id', 'sc_name');
		if(!$curLang)
			{
			$ret_arr[] =  'lang_id'; 
			}
		$LNK->Query($query);
//		$ret['child'] /*_tmp*/ = $LNK->GetData($ret_arr, false);
		$numRows = $LNK->GetNumRows();
		if ($numRows)
			{
			
			$tmp_cat = $LNK->GetData($ret_arr, true);
//			print_r($tmp_cat);
			for ($i=0; $i<$numRows; $i++)
				{
				if($i<($numRows-1))
					{
					$next = 0;
					for($k=$i+1; $k<$numRows; $k++)
						{
						$tmpRight = $ACL->GetCurRight($tmp_cat[$k]['sc_id'], 'SiteCatalog', 'sc_id', $parRight, -1);				
						if (($rightLimit)&&($tmpRight>$rightLimit))
							{
//							echo '<hr>name = '.$tmp_cat[$k]['sc_name'].'; rightLimit = '.$rightLimit.'; tmpRight = '.$tmpRight.';';
							
							$next++;	
							}
						}
/*					do
						{
						$tmpRight = $ACL->GetCurRight($tmp_cat[$cnt]['sc_id'], 'SiteCatalog', 'sc_id', $parRight, -1);				
						if (($rightLimit)&&($tmpRight>$rightLimit))
							$next++;	
						else
							$cnt++;
						}
					while((!$next)||($cnt<$numRows));			*/		
					$tmp_child['haveBrother'] = ($next)?1:0;					
//					echo '<br>name = '.$tmp_cat[$i]['sc_name'].'; haveBrother = '.$tmp_child['haveBrother'] .'<hr>';
					}
				else
					{
					$tmp_child['haveBrother'] = 0;	
					}
//				$tmp_child['haveBrother'] = ($i<($numRows-1))?1:0;					
				$tmp_child['catalog'] = $tmp_cat[$i];
				if($body_type == 1)
					$tmp_child['body'] = $this->GetBlockBody($tmp_cat[$i]['sc_id']);
				elseif($body_type == 2)
					$tmp_child['body'] = $this->GetBlockBody_asis($tmp_cat[$i]['sc_id']);
				elseif($body_type == 0)
					{
					/*no body return*/
					}
				$tmp_child['level'] = $level;
				if(!$level)
					$tmp_child['path'] = '/'.$tmp_cat[$i]['sc_name'].'/';
				else
					{
					$tmp_child['path'] = '/';
					$parId =  $tmp_cat[$i]['sc_parId'];
					$path = $tmp_cat[$i]['sc_name'];
					$f = 0;
					while(!$f)
						{
						$path.= '|'.$this->GetCurNodePar($parId, 'sc_name');
						$parId =  $this->GetCurNodePar($parId, 'sc_parId');
						if(!$parId)
							$f ++;
						}				
					
					$tmp_path = explode('|', $path);
					for($f=1; $f<= count($tmp_path); $f++)
					    $tmp_child['path'] .=$tmp_path[count($tmp_path)-$f].'/';
					}
				$tmp_child['right'] = $ACL->GetCurRight($tmp_cat[$i]['sc_id'], 'SiteCatalog', 'sc_id', $parRight, -1);				
				if (($rightLimit)&&($tmp_child['right']>$rightLimit))
					{
//					$tmp_child['haveBrother'] = ($i<($numRows-1))?1:0;					
					if($this->rekCount > 1)
						$this->childNum++;
					$curChildNum = $this->childNum;
					$this->child[$curChildNum] = $tmp_child;				
					$res = $this->GetChildren($tmp_cat[$i]['sc_id'], $level+1, $body_type, $curLang, $tmp_child['right'], $rightLimit, $thread, $system);				
					$this->child[$curChildNum]['numChild'] = $res;
/*					echo '<hr>name = '.$tmp_cat[$i]['sc_name'].'; rightLimit = '.$rightLimit.'; 
							tmp_child[right] = '.$tmp_child['right'].'; numChild = '.$res.'; curChildNum = '.$curChildNum.';<hr>';*/
					}
/*				if ($tmp_child['right']>$rightLimit)
					{*/
//					}
//					}
//				$this->child[] = $tmp_child;
//				if($res)
//				$this->child[$curChildNum]['numChild'] = $res;
				}
			return $numRows;
			}
		else
			{
			return 0;
			}
		}
	function GetCurChild($parId, $keyword, $body) /*поиск наследника по ключевому слову*/
		{
		global $USER;
//		$query = 'SELECT * FROM SiteCatalog WHERE sc_parId = '.$parId.' AND sc_name = \''.$keyword.'\' And (Lang_id = '.$USER->language.' OR Lang_id = 0) ';
		$query = 'SELECT * FROM SiteCatalog WHERE sc_parId = '.$parId.' AND sc_name = \''.$keyword.'\' ';
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$ret_arr  = array('sc_id','sc_handler', 'sc_bodyTable', 'sc_bodyIdName', 'sc_name');
		$LNK->Query($query);
		$ret = array();
		if ($LNK->GetNumRows())
			{
			$ret['catalog'] = $LNK->GetData($ret_arr, false);
//			echo  PHP_EOL .__LINE__ .'ret is: ';
//			print_r($ret);
			if(!$body)
				$ret['body'] = $this->GetBlockBody($ret['catalog']['sc_id']);
			else
				$ret['body'] = $this->GetBlockBody_asis($ret['catalog']['sc_id']);			
//			$ret['body'] = $this->GetBlockBody($ret['catalog']['sc_id']);
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		}
	function GetCurNodeKeyword($keyWord, $body) /*получает данный узел и все его свойства*/
		{
		global $USER;
		$query = 'SELECT * FROM SiteCatalog WHERE sc_name = \''.$keyWord.'\'  And (Lang_id = '.$USER->language.' OR Lang_id = 0) ';
		$ret_arr  = array('sc_id','sc_handler', 'sc_bodyTable', 'sc_bodyIdName', 'sc_name', 'sc_menu', 'lang_id', 'sc_order', 'sc_thread', 'sc_parId', 'sc_published');
		$LNK= new DBLink;
//		$ret=array();
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if ($LNK->GetNumRows())
			{
			$ret['catalog'] = $LNK->GetData($ret_arr, false);
			if($body>=0)
				{
				if(!$body)
					$ret['body'] = $this->GetBlockBody($ret['catalog']['sc_id']);
				else
					$ret['body'] = $this->GetBlockBody_asis($ret['catalog']['sc_id']);			
				}
			}
		else
			{
			$ret = 0;
			}
		return $ret;		
		}	
	function GetHandler($keyword, $parId) /*поиск обработчика*/
		{
		global $USER;
		if($parId<0)
			$inp = '';
		else			
			$inp = 'AND sc_parId = '.$parId.' ';
		$query = 'SELECT * FROM SiteCatalog WHERE sc_name = \''.$keyword.'\' '.$inp.' And (Lang_id = '.$USER->language.' OR Lang_id = 0) ';
		$LNK= new DBLink;
		$ret_arr  = array('sc_id','sc_handler', 'sc_name');
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret /*_tmp*/ = $LNK->GetData($ret_arr, false);
		return $ret;		
		}
	function GetBlockBody_asis($id) /*простые блоки из таблицы SiteBody - сырые данные КАК ЕСТЬ*/
		{
		$query = 'SELECT * FROM SiteBody WHERE sc_id = '.$id.' ORDER BY sb_order, sb_name, sb_id';
		$LNK= new DBLink;
		$ret_arr  = array('sb_id','sb_name', 'sb_order', 'sb_type', 'sb_value');
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData($ret_arr, true);
		if ($LNK->GetNumRows())
			{
			for ($i=0; $i<count($ret_tmp); $i++)
				{
				$ret[$i]['id'] = $ret_tmp[$i]['sb_id'];
				$ret[$i]['name'] = $ret_tmp[$i]['sb_name'];
				$ret[$i]['value'] = $ret_tmp[$i]['sb_value'];				
				$ret[$i]['order'] = $ret_tmp[$i]['sb_order'];
				$ret[$i]['type'] = $ret_tmp[$i]['sb_type'];				
				}
			}
/*		print_r($ret_tmp);
		print_r($ret);*/
		return $ret;		
		}
	function GetBlockBody($id) /*простые блоки из таблицы SiteBody - ассоциативный массив*/
		{
		$ret = array();
		$query = 'SELECT * FROM SiteBody WHERE sc_id = '.$id.' ORDER BY sb_name, sb_order';
		$LNK= new DBLink;
		$ret_arr  = array('sb_id','sb_name', 'sb_type', 'sb_value');
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData($ret_arr, true);
		if ($LNK->GetNumRows())
			{
			for ($i=0; $i<count($ret_tmp); $i++)
				{
				if (array_key_exists($ret_tmp[$i]['sb_name'], $ret))
					{
//					echo count($ret[$ret_tmp[$i]['sb_name']]);
					if(count($ret[$ret_tmp[$i]['sb_name']])==1)
						{
						//echo $tmp ;
						$tmp = $ret[$ret_tmp[$i]['sb_name']];
						unset($ret[$ret_tmp[$i]['sb_name']]);
						$ret[$ret_tmp[$i]['sb_name']][0] = $tmp;
						$ret[$ret_tmp[$i]['sb_name']][1] = $ret_tmp[$i]['sb_value'];
						}
					else
						{
						//echo '2';
						$indx = count($ret[$ret_tmp[$i]['sb_name']]);
						$ret[$ret_tmp[$i]['sb_name']][$indx] = $ret_tmp[$i]['sb_value'];						
						}					
					}
				else
					{
					$ret[$ret_tmp[$i]['sb_name']] = $ret_tmp[$i]['sb_value'];
					}
				}
			}
/*		print_r($ret_tmp);
		print_r($ret);*/
		//print_r($ret);					
		return $ret;		
		}
	function GetLangDefault()
		{
		$query = 'SELECT * FROM Lang WHERE lang_IsDefault = 1';
		$LNK= new DBLink;
		$ret=array();
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData('lang_id', false);
//		print_r($ret_tmp);
		return $ret_tmp;		
		}

	}
?>