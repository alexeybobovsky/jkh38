<?php
/*****************************************************************************************************************************************/
//1-  права на объект наследуются сверху вниз: право назначенное уровнем ниже перекрывает все родительские права (если они пересекаются)
//2-  права заданные явно перекрывают права заданные при создании объекта - создателем - владельцем (owner)
//3-  права назначенные пользователю перекрывают права назначенные группе, в которой этот пользователь состоит
//4-  если пользователь входит в более чем одну группу, на которую назначены права, то приоритетнее права запрета
//т.е. сначала смотрим - назначены ли права на объект, если да, то выясняем, относится ли это к данному пользователю, если да -то останавливаемся - 
// это самые приоритетные права. Если права назначены на группу, то проверяем - состоит ли пользователь в данной группе (группах) 
//если на пользователя или его группы в данном объекте права не указаны, то проверяем права создателя - если в первых трёх битах (права для всех) 
//не все нули, то присваем это право данному объекту. если же права создателя не указаны - то обращаемся к родителю и проверяем аналогично его.
//в итоге если мы нигде не встретим прав, то объект унаследует права данные на корень
/*****************************************************************************************************************************************/
class ACL_class
	{
	function SetOwnerRight($obj_id, $user_id, $table_name, $col_name, $right, $type, $access_mode) //Устанавливает права владельца на объект
		{
		global $USER;
//		$ret = 0;
		$AM = array(0 => '000', 1 => '001', 3 => '011', 7 => '111');
		for($i=0; $i<(strlen($access_mode)/3); $i++)
			{
			$tmp_am[$i] =  substr($access_mode, $i*(strlen($access_mode)/3), 3);
			}
		if($type==0)
			{
			$accessMode = $AM[$right].$tmp_am[1].$tmp_am[2];
			}
		elseif($type==1)
			{
			$accessMode = $tmp_am[0].$tmp_am[1].$AM[$right];
			}
		$query = 'insert into ACL SET user_id = 0, table_name = \'SiteCatalog\',  col_name = \'sc_id\', obj_id = \''.$obj_id.'\', access_mode = \''.$accessMode.'\'';
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if($LNK->error)
			{
			$ret['error'] = 1;
			$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
			}
		else
			{
			$ret['error'] = 0;
			}
		return $ret;				
		}
	function EditRight($right_id, $user_id, $table_name, $col_name,  $right, $obj_id) //редактирует права на объект
		{
		global $USER;
		$accessMode = '';
		$queryUpdate = '';
//		$ret = 0;
		$query = 'SELECT * FROM ACL WHERE right_id = '.$right_id;
		$LNK= new DBLink;
		$ret_arr  = array('access_mode', 'user_id', 'right_id', 'obj_id');
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData($ret_arr, false);
		if ($LNK->GetNumRows())
			{
			if($ret_tmp['obj_id'] == $obj_id)
				{
				$AM = array(0 => '000', 1 => '001', 3 => '011', 7 => '111');
				$owner = ($user_id == $this->GetOwner($ret_tmp['obj_id'], $table_name, $col_name))?1:0;
				$group = ($USER->isGroup($user_id))?1:0;				
				if(($ret_tmp['user_id']!=0)&&($ret_tmp['user_id'] == $user_id))
					{
					//echo '1';
					if($group)
						{
						$accessMode = '000'.$AM[$right].'000';
						}
					elseif(!$group)
						{
						$accessMode = $AM[$right].'000'.'000';
						}
					}
				elseif(($ret_tmp['user_id']==0))
					{
					//echo '2';					
					for($i=0; $i<(strlen($ret_tmp['access_mode'])/3); $i++)
						{
						$tmp_am[$i] =  substr($ret_tmp['access_mode'], $i*(strlen($ret_tmp['access_mode'])/3), 3);
						}
					if($owner)
						{
						$accessMode = $AM[$right].$tmp_am[1].$tmp_am[2];
						}
					elseif(!$owner)
						{
						$accessMode = $tmp_am[0].$tmp_am[1].$AM[$right];					
						}
					}
				if($accessMode)
					{
					$queryUpdate = 'UPDATE ACL SET access_mode = \''.$accessMode.'\' WHERE right_id = '.$right_id;
					$LNK->Query($queryUpdate);
					}
				if($LNK->error)
					{
					$ret['error'] = 1;
					$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
					}
				elseif((!$LNK->error)&&($queryUpdate))
					{
					$ret['error'] = 0;
					}
				elseif(!$queryUpdate)
					{
					$ret['error'] = 1;
					$ret['errorMsg'] = 'no objects for update';// = $LNK->error_string;			
					}
				}
			else
				{
				$owner = ($user_id == $this->GetOwner($obj_id, $table_name, $col_name))?1:0;
//				$group = ($USER->isGroup($user_id))?1:0;
				$sysGroup = (($USER->getUserParam('user_id', $user_id, 'is_system'))&&($USER->getUserParam('user_id', $user_id, 'is_group')))?1:0;
				if((!$owner)&&(!$sysGroup))
					{
//					echo '(!owner)&&(!sysGroup)';
					$ret = $this->SetRight($obj_id, $user_id, $table_name, $col_name, $right);			
					
					}
				elseif($owner)
					{
					$ret = $this->SetOwnerRight($obj_id, $user_id, $table_name, $col_name, $right, 0,  $ret_tmp['access_mode']);									
//					echo '(owner)';
					}
				elseif((!$owner)&&($sysGroup))
					{
					$ret = $this->SetOwnerRight($obj_id, $user_id, $table_name, $col_name, $right, 1,  $ret_tmp['access_mode']);									
//					echo '(!owner)&&(sysGroup)';
					}
				if(!$ret['error'])
					$ret['update'] = 1;
				}
			}
		return $ret;				
		}
	function GetRightFromId($right_id, $user_id) //получить текущие права на элемент таблицы SiteCatalog для конкретного пользователя
		{
		global $USER;
		$ret = 0;
		$query = 'SELECT * FROM ACL WHERE right_id = '.$right_id;
		$LNK= new DBLink;
		$ret_arr  = array('access_mode', 'user_id', 'right_id', 'obj_id');
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData($ret_arr, false);
		if ($LNK->GetNumRows())
			{
			if(($ret_tmp['user_id']!=0)&&($ret_tmp['user_id'] == $user_id))
				{
				$right = $this->GetRightArr($ret_tmp['access_mode']);	
				$ret = ($USER->isGroup($user_id))?$right['group']:$right['owner'];
//				echo 'cur - '.$ret;
				}
			elseif($ret_tmp['user_id']==0)
				{
				if($this->isOwner($ret_tmp['obj_id'], 'SiteCatalog', 'sc_id', $user_id))
					{
					$right = $this->GetRightArr($ret_tmp['access_mode']);	
					$ret = $right['owner'];
//					echo 'owner';
					}
				elseif(($USER->getUserParam('user_id', $user_id, 'is_system'))&&($USER->getUserParam('user_id', $user_id, 'is_group')))
					{
					$right = $this->GetRightArr($ret_tmp['access_mode']);	
					$ret = $right['all'];
//					echo 'all - '.$ret;
					}
				elseif($ret_tmp['obj_id'] == 0)
					{
					$right = $this->GetRightArr($ret_tmp['access_mode']);	
					$ret = $right['owner'];
					}				
				}
			}
		return $ret;				
		}
	function DeleteRight($right_id) //удаляет права на объект
		{
			$query = 'delete from ACL where right_id = '.$right_id.'';
			$LNK= new DBLink;
			$LNK->Query($query);
			if($LNK->error)
				{
				$ret['error'] = 1;
				$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
				}
			else
				{
				$ret['error'] = 0;
				}
		return $ret;				
		}
		
	function SetRight($obj_id, $user_id, $table_name, $col_name, $right) //Устанавливает права на объект
		{
		global $USER, $CNT;
		
//		$ret = 0;
//		echo $obj_id;
		$AM = array(0 => '000', 1 => '001', 3 => '011', 7 => '111');
		$owner = ($user_id == $this->GetOwner($obj_id, $table_name, $col_name))?1:0;
		$group = ($USER->isGroup($user_id))?1:0;
//		echo $sysGroup = (($USER->getUserParam('user_id', $user_id, 'is_system'))&&($USER->getUserParam('user_id', $user_id, 'is_group')))?1:0;
		if($owner)
			{
			$accessMode = $AM[$right].'000'.'000';
			}
		elseif($group)
			{
			$accessMode = '000'.$AM[$right].'000';
			}
		elseif(!$group)
			{
			$accessMode = $AM[$right].'000'.'000';
			}		
		if(/*(!$owner)&&*/(!$sysGroup))
			{
//			echo '(!owner)&&(!sysGroup)';
			$query = 'insert into ACL SET user_id = '.$user_id.', table_name = \'SiteCatalog\',  col_name = \'sc_id\', obj_id = \''.$obj_id.'\', access_mode = \''.$accessMode.'\'';
			$LNK= new DBLink;
//			$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
			$LNK->Query($query);
			if($LNK->error)
				{
				$ret['error'] = 1;
				$ret['errorMsg'] = $LNK->error_string;// = $LNK->error_string;
				}
			else
				{
				$ret['error'] = 0;
				$idNeed = $CNT->GetCurNode($obj_id, -1);
				$CNT->GetChildren($idNeed['catalog']['sc_parId'], 0, 0, 0, 0, -1, $idNeed['catalog']['sc_thread'], 0);
//				$CNT->GetChildren($obj_id, 0, 0, 0, 0, -1, $idNeed['catalog']['sc_thread'], 1);
				$tree = $CNT->child;
				$CNT->Reset();
				for($i=0; $i<count($tree); $i++)					
					{
					if($this->GetClosedParentRight($tree[$i]['catalog']['sc_id'])<=3)
						{
						/*echo '<br>'.$tree[$i]['catalog']['sc_id'].' - '.$tree[$i]['catalog']['sc_name'].'  -  ';
						echo $this->GetRightFromId($tree[$i]['catalog']['sc_id'], $user_id);*/
						$curRight = $this->GetRightFromId($tree[$i]['catalog']['sc_id'], $user_id);
						$query = 'insert into ACL SET user_id = '.$user_id.', 
						table_name = \'SiteCatalog\',  col_name = \'sc_id\', obj_id = \''.$tree[$i]['catalog']['sc_id'].'\', 
						access_mode = \''.$AM[$curRight].'000'.'000'.'\'';						
//						$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
						$LNK->Query($query);						
						}															
					}
//					print_r($tree);
				}
			}
		return $ret;				
		}
		
	function GetAllRight($obj_id, $table_name, $col_name) //ВСЕ права на объект
		{
		global $USER;
		$CNT = new  GetContent;
		$right['owner'] = $this->GetAllOwnerRight($obj_id, 'SiteCatalog', 'sc_id');
		$right['current'] = $this->GetAllCurRight($obj_id, 'SiteCatalog', 'sc_id');
		$right['parent'] = $this->GetAllParentRight( $CNT->GetCurNodePar($obj_id, 'sc_parId'), 'SiteCatalog', 'sc_id');		
		$right['root'] = $this->GetAllRootRight();
		
		$rCount = 0;		
		if(is_array($right['current']))			
			{
			for($i=0; $i<count($right['current']); $i++)
				{
				$r_tmp[$rCount]['rightId'] = $right['current'][$i]['rightId'];
				$r_tmp[$rCount]['rightType'] = 'current';
				$r_tmp[$rCount]['userId'] = $right['current'][$i]['userId'];
				$r_tmp[$rCount]['userName'] = $USER->getUserParam('user_id', $right['current'][$i]['userId'] , 'user_name');
				$isGroup = $USER->getUserParam('user_id', $right['current'][$i]['userId'] , 'is_group');
				$r_tmp[$rCount]['userType'] = ($isGroup)?'group':'user';
				$r_tmp[$rCount]['right'] = $right['current'][$i]['AM']['owner'].$right['current'][$i]['AM']['group'].$right['current'][$i]['AM']['all'];
				$r_tmp[$rCount]['inherit'] = '';
/*				if($right['current'][$i]['userId'] == 5)
					print_r($r_tmp[$rCount]);*/
				$rCount++;
				}
			}					
		if(is_array($right['owner']))
			{
			$r_tmp[$rCount]['rightId'] = $right['owner']['rightId'];
			$r_tmp[$rCount]['rightType'] = 'owner';
			$r_tmp[$rCount]['userId'] = $right['owner']['userId'];
			$r_tmp[$rCount]['userName'] = $USER->getUserParam('user_id', $right['owner']['userId'] , 'user_name');
			$r_tmp[$rCount]['userType'] = 'user';
			$r_tmp[$rCount]['right'] = $right['owner']['AM']['owner'].$right['owner']['AM']['group'].$right['owner']['AM']['all'];
			$r_tmp[$rCount]['inherit'] = '';
			$rCount++;
			
			}		
		if(is_array($right['parent']))			
			{
			for($i=0; $i<count($right['parent']); $i++)
				{
				if(is_array($right['parent'][$i]['list']))
					{
/*					print_r($right['parent'][$i]['list']);
					echo 'list<hr>';*/
					for($l=0; $l<count($right['parent'][$i]['list']); $l++)
						{
						$r_tmp[$rCount]['rightId'] = $right['parent'][$i]['list'][$l]['rightId'];
						$r_tmp[$rCount]['rightType'] = 'parent';
						$r_tmp[$rCount]['userId'] = $right['parent'][$i]['list'][$l]['userId'];
						$r_tmp[$rCount]['userName'] = $USER->getUserParam('user_id', $right['parent'][$i]['list'][$l]['userId'] , 'user_name');
						$isGroup = $USER->getUserParam('user_id', $right['parent'][$i]['list'][$l]['userId'] , 'is_group');
						$r_tmp[$rCount]['userType'] = ($isGroup)?'group':'user';
						$r_tmp[$rCount]['right'] = $right['parent'][$i]['list'][$l]['AM']['owner'].$right['parent'][$i]['list'][$l]['AM']['group'].$right['parent'][$i]['list'][$l]['AM']['all'];
						$r_tmp[$rCount]['inherit'] = $CNT->GetNodePath($right['parent'][$i]['objId'], 0);//$right['parent'][$i]['objId'];
/*						print_r($r_tmp[$rCount]);
						echo 'cur list<hr>';*/						
						$rCount++;
						}
					}
				if(is_array($right['parent'][$i]['owner']))
					{
//					print_r($right['parent'][$i]['owner']);
/*					print_r($right['parent'][$i]['owner']);
					echo 'owner<hr>';*/
					$r_tmp[$rCount]['rightId'] = $right['parent'][$i]['owner']['rightId'];
					$r_tmp[$rCount]['rightType'] = 'owner';
					$r_tmp[$rCount]['userId'] = $right['parent'][$i]['owner']['userId'];
					$r_tmp[$rCount]['userName'] = $USER->getUserParam('user_id', $right['parent'][$i]['owner']['userId'] , 'user_name');
					$r_tmp[$rCount]['userType'] = ($isGroup)?'group':'user';
					$r_tmp[$rCount]['right'] = $right['parent'][$i]['owner']['AM']['owner'].$right['parent'][$i]['owner']['AM']['group'].$right['parent'][$i]['owner']['AM']['all'];
					$r_tmp[$rCount]['inherit'] = $CNT->GetNodePath($right['parent'][$i]['objId'], 0);//$right['parent'][$i]['objId'];
/*					print_r($r_tmp[$rCount]);
					echo 'cur owner<hr>';*/
					$rCount++;
					}
					
				}
			}
		$r_tmp[$rCount]['rightType'] = 'root';
		$r_tmp[$rCount]['userId'] = $USER->getUserParam('user_name', 'root' , 'user_id');
		$r_tmp[$rCount]['userName'] = 'root';
		$r_tmp[$rCount]['userType'] = 'user';
		$r_tmp[$rCount]['right'] = $right['root']['am']['owner'].$right['root']['am']['group'].$right['root']['am']['all'];
		$r_tmp[$rCount]['inherit'] = '/';
		$r_tmp[$rCount]['rightId'] = $right['root']['right_id'];
		
		return $r_tmp ;				
		}
	
	function GetAllRootRight() //все права на корень сайта
		{
		$rootId = 1;
		$ret = array();
		$query = 'SELECT * FROM ACL WHERE obj_id = 0 AND user_id = 0 AND table_name = \'SiteCatalog\' ';
		$LNK= new DBLink;
		$ret_arr  = array('access_mode', 'right_id');
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData($ret_arr, false);
		if ($LNK->GetNumRows())
			{
			$right['am'] = $this->GetRightArr($ret_tmp['access_mode']);
			$right['right_id'] = $ret_tmp['right_id'];
			}
		return $right ;				
		}
	function GetAllParentRight($obj_id /*ближайший предок*/, $table_name, $col_name) //собирает все наследуемы права на объект
		{
		$right = array();
		$query = 'SELECT * FROM SiteCatalog';
		$ret_arr  = array('sc_id', 'sc_parId', 'sc_handler', 'sc_name', 'user_id' ,'sc_order');
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData($ret_arr, true);
		$rightCnt = 0;
		if ($LNK->GetNumRows())
			{
			$stop=0;
			$m = 0;
			//$right = -1;
			$curId = $obj_id;
			do
				{
				$l = 0;
				$stop1=0;
				do
					{
					if($ret_tmp[$l]['sc_id']==$curId)
						{
						$right[$rightCnt]['list'] = $this->GetAllCurRight($curId, 'SiteCatalog', 'sc_id');						
						$right[$rightCnt]['owner'] = $this->GetAllOwnerRight($curId, 'SiteCatalog', 'sc_id');						
						$right[$rightCnt]['objId'] = $curId;
						$curId = $ret_tmp[$l]['sc_parId'];
						$stop1++;
						$rightCnt++; 
						}
					$l++;
					}
				while(($l<count($ret_tmp))&&(!$stop1));
				}
			while(($curId)&&(!$stop));
			}
		if(count($right))
			{
//			arsort($right);
//			print_r($right);
			return $right;
			}
		else
			return -1;			
		}
	
	function GetAllCurRight($obj_id, $table_name, $col_name) //все назначенные права на оъект
		{
		$ret = array();
		$query = 'SELECT * FROM ACL WHERE obj_id = '.$obj_id.' AND user_id != 0 AND table_name = \''.$table_name.'\' ';
		$LNK= new DBLink;
		$ret_arr  = array('access_mode', 'user_id', 'right_id' );
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData($ret_arr, true);
		$numRows = $LNK->GetNumRows();
		if ($numRows)
			{
			for($i=0; $i<$numRows; $i++)
				{
				$right[$i]['AM'] = $this->GetRightArr($ret_tmp[$i]['access_mode']);
				$right[$i]['userId'] = $ret_tmp[$i]['user_id'];
				$right[$i]['rightId'] = $ret_tmp[$i]['right_id'];
/*				if($ret_tmp[$i]['user_id'] == 5)
					print_r($right[$i]);*/
				}
			}
//		print_r($right);
		if(count($right))
			{
//			print_r($right);
			return $right;
			}
		else
			return -1;			
		}
	function GetAllOwnerRight($obj_id, $table_name, $col_name) //права на объект  назначенные создателем 
		{
		$ownerId = $this->GetOwner($obj_id, $table_name, $col_name);
		$ret = -1;
		$query = 'SELECT * FROM ACL WHERE obj_id = '.$obj_id.' AND user_id = 0 AND table_name = \''.$table_name.'\' ';
		$LNK= new DBLink;
		$ret_arr  = array('access_mode', 'right_id');
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData($ret_arr, false);
		if ($LNK->GetNumRows())
			{
//			print_r($ret_tmp);
			$right['AM'] = $this->GetRightArr($ret_tmp['access_mode']);
			$right['userId'] = $ownerId;
			$right['rightId'] = $ret_tmp['right_id'];
			}
		if(count($right))		
			return $right;				
		else
			return -1;							
		}
	function GetClosedParentRight($obj_id) //получить права на элемент таблицы SiteCatalog с поиском прав у предков
		{
//		echo 'GetClosedParentRight '.$obj_id.'<hr>';
		global $USER;
		$right = -1;
		$right = $this->GetCurRight($obj_id, 'SiteCatalog', 'sc_id', $right, -1);
//		echo $right;
		if($right<0)
			{
//			$ret = array();
			$query = 'SELECT * FROM SiteCatalog WHERE (Lang_id = '.$USER->language.' OR Lang_id = 0)';
			$ret_arr  = array('sc_id', 'sc_parId', 'sc_handler', 'sc_name', 'user_id' ,'sc_order');
			$LNK= new DBLink;
///		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
			$LNK->Query($query);
			$ret_tmp = $LNK->GetData($ret_arr, true);
			if ($LNK->GetNumRows())
				{
				$stop=0;
				$m = 0;
				$right = -1;
				$curId = $obj_id;
				do
					{
					$l = 0;
					$stop1=0;
					do
						{
						if($ret_tmp[$l]['sc_id']==$curId)
							{
//							echo "BEFORE: cur_id = ".$curId."; right = ".$right;
							$right = $this->GetCurRight($curId, 'SiteCatalog', 'sc_id', $right, -1);						
							if ($right>=0)
								{
								$stop ++; 
								}
							else
								{
								$curId = $ret_tmp[$l]['sc_parId'];
								}								
							$stop1++;
//							echo "<br>AFTER: cur_id = ".$curId."; right = ".$right."<br>";
							}
						$l++;
						}
					while(($l<count($ret_tmp))&&(!$stop1));
					}
				while(($curId)&&(!$stop));
				}
			if($right<0)
				$right = $this->GetRootRight();
			}		
		$ret = $right;
		return $ret;				
		}
	function GetOwnerRight($obj_id, $table_name, $col_name) //права на объект данному юзеру,  назначенные создателем объекта
		{
		global $USER;
		$ownerId = $this->GetOwner($obj_id, $table_name, $col_name);
		$ret = -1;
		$query = 'SELECT * FROM ACL WHERE obj_id = '.$obj_id.' AND user_id = 0 AND table_name = \''.$table_name.'\' ';
		$LNK= new DBLink;
		$ret_arr  = array('access_mode');
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData($ret_arr, false);
		if ($LNK->GetNumRows())
			{
			$right = $this->GetRightArr($ret_tmp['access_mode']);
			//print_r($right);
			if($USER->id == $ownerId)
				$ret = $right['owner'];
			elseif($USER->isConfederate($USER->id, $ownerId))
				$ret = $right['group'];
			else
				$ret = $right['all'];
			}
//		echo '<b>'.__FUNCTION__.', '.$ret.'</b>';
		return $ret;				
		}
	function GetOwner($obj_id, $table_name, $col_name)  //дает пользователя -  владельца-создателя объекта
		{
		$ret = 0;
		$query = 'SELECT * FROM '.$table_name.' WHERE '.$col_name.' = '.$obj_id;
		$ret_arr  = array('user_id');
		$LNK= new DBLink;
//		if ($obj_id == 86)
//			$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if ($LNK->GetNumRows())
			{
			$ret_tmp = $LNK->GetData($ret_arr, false);
			$ret = $ret_tmp['user_id'];
//			if ($obj_id == 86)
//				echo $ret;
			
			}
		return $ret;
		}
	function isOwner($obj_id, $table_name, $col_name, $user_id)  //является ли пользователь владельцем объекта
		{
		$ret = 0;
		$query = 'SELECT * FROM '.$table_name.' WHERE '.$col_name.' = '.$obj_id.' AND user_id = '.$user_id;
		$LNK= new DBLink;
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		if ($LNK->GetNumRows())
			{
			$ret ++;
			}
		return $ret;
		}
	function GetCurRight($obj_id, $table_name, $col_name, $parRight, $user) //права на данный элемент у текущего или другого пользователя при построении дерева
		{
		global $USER;
		$ownerRight = $this->GetOwnerRight($obj_id, $table_name, $col_name);
		$curRight['owner'] = ($ownerRight>=0)?$ownerRight:$parRight;
		$userId = ($user>=0)?$user:$USER->id;
//		echo '<br><i>'.__FUNCTION__.', '.$ownerRight.'</i>';
//		$rootId = 1;
		$ret = array();
		$query = 'SELECT * FROM ACL WHERE obj_id = '.$obj_id.' AND user_id != 0 AND table_name = \''.$table_name.'\' ';
		$LNK= new DBLink;
		$ret_arr  = array('access_mode', 'user_id', 'right_id');
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData($ret_arr, true);
		$numRows = $LNK->GetNumRows();
		if ($numRows)
			{
			$fixCnt = 0;
			for($i=0; $i<$numRows; $i++)
				{
				$right = $this->GetRightArr($ret_tmp[$i]['access_mode']);
//				echo $ret_tmp[$i]['access_mode'].'-'.$ret_tmp[$i]['user_id'];
//				print_r($right);
				if ($USER->isGroup($ret_tmp[$i]['user_id']))
					{
					if($USER->userInGroup($userId, $ret_tmp[$i]['user_id']))
						{
						$curRight['fix'][$fixCnt]['group'] = $right['group'];
						$fixCnt ++;
						}
					}
				else
					{
					if($userId == $ret_tmp[$i]['user_id'])
						{
						$curRight['fix'][$fixCnt]['user'] = $right['owner'];					
						$fixCnt ++;
						}
					}
				}
//			print_r($curRight);			
			}
/*		if ($obj_id == 16)
			print_r($curRight);	*/
		$owner = $curRight['owner'];
		$user = -1;
		$group = -1;
//		echo 'count - '.count($curRight['fix']);
		for($i=0; $i<count($curRight['fix']); $i++)
			{
//			echo '<br>owner : '.$owner.'; group : '.$group.';user : '.$user.'<hr>';
			if(isset($curRight['fix'][$i]['user']))
				{
				$user = $curRight['fix'][$i]['user'];
				}
			if(isset($curRight['fix'][$i]['group']))
				{
//				echo '<br>Cur group : '.$group.' new group: '.$curRight['fix'][$i]['group'];
				if(($group<0)||($group > $curRight['fix'][$i]['group']))
					$group = $curRight['fix'][$i]['group'];
				}
			}
/*		if(($user>=0)||($group>=0))*/
//			echo '<br>owner : '.$owner.'; group : '.$group.';user : '.$user.'<hr>';
		if($user>=0)
			return $user;
		elseif($group>=0)
			return $group;
		else
			return $owner;
//		echo $curRight ;
//		return $curRight ;				
		}
	function GetRightArr($accessMode) //вычисляет значение в Access Mode-е
		{
		$ret = array();
		$ret_name = array('owner', 'group', 'all');
		$ret_val = array(7, 3, 1);
		for($i=0; $i<(strlen($accessMode)/3); $i++)
			{
			$tmp_am[$i] =  substr($accessMode, $i*(strlen($accessMode)/3), 3);
			for($l=0; $l<(3); $l++)
				{
				$AM[$i][$l] = substr($tmp_am[$i], $l, 1);				
				if(($AM[$i][$l])&&(!$ret[$ret_name[$i]]))
					{
					$ret[$ret_name[$i]] = $ret_val[$l];
					}
				}
			if(!isset($ret[$ret_name[$i]]))
				$ret[$ret_name[$i]] = 0;
			}
//		print_r($ret);
		return $ret;				
		}
	function GetRootRight() //права на корень сайта - заданные создателем
		{
		global $USER;
		$rootId = 1;
		$ret = array();
		$query = 'SELECT * FROM ACL WHERE obj_id = 0 AND user_id = 0 AND table_name = \'SiteCatalog\' ';
		$LNK= new DBLink;
		$ret_arr  = array('access_mode');
//		$LNK->SetDebug(__FILE__, __FUNCTION__, __LINE__);
		$LNK->Query($query);
		$ret_tmp = $LNK->GetData($ret_arr, false);
		if ($LNK->GetNumRows())
			{
			$right = $this->GetRightArr($ret_tmp['access_mode']);
//			print_r($right);
			if($USER->id == $rootId)
				$ret = $right['owner'];
			elseif($USER->isConfederate($USER->id, $rootId))
				$ret = $right['group'];
			else
				$ret = $right['all'];
			}
//		echo $ret;
		return $ret;				
		}
	}
?>