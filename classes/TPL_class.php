<?php
//require_once("main.inc.php");
class TEMPLATE
	{
	var $parents = array();
	function ResetMenu($menu) //обнуление активных пунктов меню (14_08_2007)
		{
		if(is_array($menu['MenuItems']))
			for($i=0; $i< count($menu['MenuItems']); $i++)
				$menu['MenuItems'][$i]['selected'] = 0;
		if(is_array($menu['SubMenuItems']))
			for($i=0; $i< count($menu['SubMenuItems']); $i++)
				$menu['SubMenuItems'][$i]['selected'] = 0;
		if(is_array($menu['links']))
			for($i=0; $i< count($menu['links']); $i++)
				$menu['links'][$i]['selected'] = 0;
		return $menu;
		}
	function Reset()
		{
		$this->parents = array();		
		}
	function Create_Editor_Element($w, $h, $name, $value, $toolbar)		
		{		
		require_once('../htdocs/includes/FCKeditor/fckeditor.php');
		$oFCKeditor = new FCKeditor($name);
		$oFCKeditor->Height = $h;
		$oFCKeditor->Width = $w;
		$oFCKeditor->ToolbarSet = ($toolbar)?$toolbar:'Custom';
		$oFCKeditor->BasePath = '/includes/FCKeditor/';
		$oFCKeditor->Config['SkinPath'] = $oFCKeditor->BasePath. 'editor/skins/office2003/' ;
		$oFCKeditor->Value =  $value;
		$output = $oFCKeditor->CreateHtml() ;	
		return $output;
		}		
	function FindKey($arr, $key)
    	{
//		print_r($arr);
		$ret = 0;
		for($i=0; $i<count($arr); $i++)
			{
			if($arr[$i]['name'] == $key)
				{
				$ret = $arr[$i]['value'];
				}
			}
		return $ret;
        }		
	function TPL_CreateFormElement($content, $array_index) /*создать объект form с аттрибутами*/
		{
		$pfx = ($array_index<0)?'':'['.$array_index.']';
		for ($k=0; $k<count($content); $k++)
			{
			if ($content[$k]['name'] == 'listLabel')
				{
				$elList['caption'][] = $content[$k]['value'];
				}
			if ($content[$k]['name'] == 'listValue')
				{
				$elList['value'][] = $content[$k]['value'];
				}
			if ($content[$k]['name'] == 'name')
				{
				$elName = $content[$k]['value'].$pfx;
				}
			if ($content[$k]['name'] == 'type')
				{
				$elType = $content[$k]['value'];
				}
			if ($content[$k]['name'] == 'onChange')
				{
				$elOnChange = $content[$k]['value'];
				}
			}
		if(!$elId)
			$elId = $elName;
		if(!$elOnChange)
			$elOnChange = '';

		$el = array('name' => $elName, 
				'type' => $elType, 
				'style' => 'WIDTH: 200px', 
				'id' => $elId, 
				'class' => 'input2', 
				//'emptyValue' => 0,
				//'onChange' => 'OneParamCheck(this.form, this),this.form.submit()'*/ 
				);
		$el['onChange'] = $elOnChange;
		if ($elType == 'select')
			{
			$el['value'] = $elList['value'];
			$el['caption'] = $elList['caption'];
			}
		$full_ret = $this->Create_HTML_Element($el);			
		return $full_ret;		
		}
	function TPL_CreateTable($content) /*создать объект Table с аттрибутами*/
		{
		$full_ret['table_colStyle'] = $content['colStyle'];
		$full_ret['table_colHeader'] = $content['colHeader'];
		$full_ret['table_colAlias'] = $content['colAlias'];		
		$full_ret['table_headerAlign'] = $content['headerAlign'];		
		$full_ret['table_label'] = $content['label'];
		$full_ret['body_id'] = $content['bodyId'];
		$full_ret['style'] = $content['style'];
		return $full_ret;		
		}
	function TPL_CreateForm($content) /*создать объект form с аттрибутами*/
		{
		$full_ret['form_action'] = $content['action'];
		$full_ret['form_isHidden'] = $content['isHidden'];
		$full_ret['form_name'] = $content['name'];
		$full_ret['form_method'] = $content['method'];
		$full_ret['form_onSubmit'] = $content['onSubmit'];
		$full_ret['form_emptyCheck'] = $content['emptyCheck'];
		$full_ret['form_elementCaption'] = $content['elementCaption'];
		return $full_ret;		
		}
	function TPL_GetMenu($content)
		{
		global $NAV, $CONST, $ROUT;
		$curPos = $NAV->GetPos();
		$curMenuId = 0;
		$retName = array( 0 => 'MenuItems', 1 => 'SubMenuItems', 2 => 'links', 3 => 'lvl_4', 4 => 'lvl_5');
//		for($k=0; $k<count($curPos); $k++)
		$stop = 0;
		$k = 0;
		$curParId = 0;
		$activeLink = '';
		$curHandler = $content[$curPos[0]]['catalog']['sc_handler'];
		$curNodeName = $content[$curPos[0]]['catalog']['sc_name'];
		$label['caption'] = array();
		$label['link'] = array();		
//		print_r($content);
		if($CONST['showAllSubmenuItems'])
			{
			for($i=0; $i<count($content); $i++)
				{
/*				if(($content[$i]['catalog']['sc_parId'] == $curParId))//||($CONST['showAllSubmenuItems'])
					{*/
					$item['label'] = $content[$i]['body']['caption'];
					$item['link'] = $content[$i]['body']['link'];					
					$item['name'] = $content[$i]['catalog']['sc_name'];
					$item['id'] = $content[$i]['catalog']['sc_id'];
					$item['parent'] = $content[$i]['catalog']['sc_parId'];
					$item['selected']=($i==$curPos[$k])?1:0;					
					
					//echo '<br>'.$content[$i]['catalog']['sc_name'].': '.$content[$i+1]['catalog']['sc_parId'].' == '.$content[$i]['catalog']['sc_id'].'<br>';
					if($i==$curPos[$k])
						{
						$item['handler'] = ($content[$i]['catalog']['sc_handler'])?
							$content[$i]['catalog']['sc_handler']:$curHandler;						
						if($content[$i]['catalog']['sc_handler'])							
							$activeLink = $content[$i]['body']['link'];
//						echo '<br>'.$content[$i]['catalog']['sc_name'].': '.$content[$i+1]['catalog']['sc_parId'].' == '.$content[$i]['catalog']['sc_id'].' && k == '.$k;
						}
					else
						{
						unset($item['handler']);
						}
					$full_ret[$retName[$content[$i]['level']]][] = $item;					
					$k = $content[$i+1]['level'];

				}
			$stop = 0;
			$k = 0;
			$curParId = 0;
			$curHandler = $content[$curPos[0]]['catalog']['sc_handler'];
			$curNodeName = $content[$curPos[0]]['catalog']['sc_name'];
			do
				{
//					echo 
				if($k<count($curPos))
					{
					$label['caption'][] = ($content[$curPos[$k]]['body']['title'])?$content[$curPos[$k]]['body']['title']:$content[$curPos[$k]]['body']['caption'];
					$label['link'][] = $content[$curPos[$k]]['body']['link'];
					$lastLink = $content[$curPos[$k]]['body']['link'];
					$title[] = ($content[$curPos[$k]]['body']['title'])?$content[$curPos[$k]]['body']['title']:$content[$curPos[$k]]['body']['caption'];//$content[$curPos[$k]]['body']['caption'];
					$lastTitle = ($content[$curPos[$k]]['body']['title'])?$content[$curPos[$k]]['body']['title']:$content[$curPos[$k]]['body']['caption'];//$content[$curPos[$k]]['body']['caption'];
					$curParId = $content[$curPos[$k]]['catalog']['sc_id'];
					$curNodeName = $content[$curPos[$k]]['catalog']['sc_name'];
					$curNodeId = $content[$curPos[$k]]['catalog']['sc_id'];
					$curThreadId = $content[$curPos[$k]]['catalog']['sc_thread'];
					$curLevel = $retName[$k];
					$k++;

					$curHandler = ($content[$curPos[$k]]['catalog']['sc_handler'])?$content[$curPos[$k]]['catalog']['sc_handler']:$curHandler;			
					}
				else
					{
					$stop++;
					}
				}
			while(!$stop);
			}
		else
			{
			do
				{
				for($i=0; $i<count($content); $i++)
					{
					if(($content[$i]['catalog']['sc_parId'] == $curParId))//||($CONST['showAllSubmenuItems'])
						{
						$item['label'] = $content[$i]['body']['caption'];
						$item['link'] = $content[$i]['body']['link'];					
						$item['name'] = $content[$i]['catalog']['sc_name'];
						$item['id'] = $content[$i]['catalog']['sc_id'];
						$item['parent'] = $content[$i]['catalog']['sc_parId'];
						$item['selected']=($i==$curPos[$k])?1:0;
						if($i==$curPos[$k])
							{
							$item['handler'] = ($content[$i]['catalog']['sc_handler'])?
								$content[$i]['catalog']['sc_handler']:$curHandler;
							if($content[$i]['catalog']['sc_handler'])							
								$activeLink = $content[$i]['body']['link'];
							
//							$item['activeLink'] = $content[$i]['body']['link'];
//							echo '<hr> k ='.$k.'; '.$content[$i]['body']['caption'].'<hr>';							
							}
						else
							{
							unset($item['handler']);
							}
						$full_ret[$retName[$k]][] = $item;
						}
					}
				if($k<count($curPos))
					{
					$label['caption'][] = ($content[$curPos[$k]]['body']['title'])?$content[$curPos[$k]]['body']['title']:$content[$curPos[$k]]['body']['caption'];
					$label['link'][] = $content[$curPos[$k]]['body']['link'];
					$title[] = $content[$curPos[$k]]['body']['caption'];
					$lastTitle = $content[$curPos[$k]]['body']['caption'];
					$lastLink = $content[$curPos[$k]]['body']['link'];
					$curParId = $content[$curPos[$k]]['catalog']['sc_id'];
					$curNodeName = $content[$curPos[$k]]['catalog']['sc_name'];
					$curNodeId = $content[$curPos[$k]]['catalog']['sc_id'];
					$curThreadId = $content[$curPos[$k]]['catalog']['sc_thread'];
					$curLevel = $retName[$k];
					$k++;
					$curHandler = ($content[$curPos[$k]]['catalog']['sc_handler'])?$content[$curPos[$k]]['catalog']['sc_handler']:$curHandler;
					}
				else
					{
					$stop++;
					}
				}
			while(!$stop);
			}
//		$full_ret['handler'] = $handlerTmp;//)?$handlerTmp:$curHandler;//$handlerTmp
/*		$full_ret['handler'] = ($handlerTmp)?$handlerTmp:$curHandler;//$handlerTmp*/
//		$full_ret['Chandler'] = 1;
		$full_ret['curNodeName'] = $curNodeName;
		$full_ret['curNodeId'] = $curNodeId;
		$full_ret['curLabel'] = (count($label))?$label:'';
		$full_ret['curTitle'] = $title;
		$full_ret['lastLink'] = $lastLink;		
		$full_ret['lastTitle'] = $lastTitle;	
//		$uriArr = 
		$full_ret['queryString'] = 	'http://'.$_SERVER['SERVER_NAME'].$ROUT->getStrPrt($_SERVER['QUERY_STRING'], '=', 1);	
		$full_ret['showmenu'] = 1;
		$full_ret['showsubmenu'] = 1;
		$full_ret['curLevel'] = $curLevel;
		$full_ret['curThreadId'] = $curThreadId;
		$full_ret['activeLink'] = $activeLink;//'ddd';
		$full_ret['handler'] = $curHandler;//'ddd';
		$transRetName = array_flip($retName);
		$full_ret['curLevelNumber'] = $transRetName[$curLevel];
//		echo '<br>'.$transRetName[$curLevel];
/*		print_r($full_ret);
		echo '<hr>';*/
		return $full_ret;
		}

	function TPL_GetHeader($content)	
		{
		$full_ret = $content;
		return $full_ret;
		}
	function Create_HTML_Element($param)		
		{		
		$ret_el['template'] = $param['type'].'.tpl';
		$ret_el = array_merge ($ret_el, $param);
/*		$ret_el['type'] = $param['type'];
		$ret_el['name'] = $param['name'];
		if($param['id'])
			$ret_el['id'] = $param['id'];
		if($param['class'])
			$ret_el['class'] = $param['class'];
		if($param['size'])
			$ret_el['size'] = $param['size'];
		if($param['multiple'])
			$ret_el['multiple'] = $param['multiple'];			
		if($param['skipTemplate'])
			$ret_el['skipTemplate'] = $param['skipTemplate'];			
			
		if($param['src'])
			$ret_el['src'] = $param['src'];			
		if($param['rows'])
			$ret_el['rows'] = $param['rows'];			
		if($param['cols'])
			$ret_el['cols'] = $param['cols'];			
		if($param['wrap'])
			$ret_el['wrap'] = $param['wrap'];			
			
		if($param['onClick'])
			$ret_el['onClick'] = $param['onClick'];
		if($param['onChange'])
			$ret_el['onChange'] = $param['onChange'];
		if($param['onkeyup'])
			$ret_el['onkeyup'] = $param['onkeyup'];
		if($param['onFocus'])
			$ret_el['onFocus'] = $param['onFocus'];
		if($param['onBlur'])
			$ret_el['onBlur'] = $param['onBlur'];
		if($param['CheckIt'])
			$ret_el['CheckIt'] = $param['CheckIt'];
		if($param['onMouseOver'])
			$ret_el['onMouseOver'] = $param['onMouseOver'];
		if($param['emptyValue'])
			$ret_el['emptyValue'] = true;
		if($param['style'])
			$ret_el['style'] = $param['style'];
		if($param['default'])
			{
			$ret_el['default'] = $param['default'];
			}
			
		if($param['necessary'])
			$ret_el['necessary'] = $param['necessary'];
		if($param['fake_necessary'])
			$ret_el['fake_necessary'] = $param['fake_necessary'];
		if($param['disabled'])
			$ret_el['disabled'] = $param['disabled'];
		if($param['value'])
			$ret_el['value'] = $param['value'];
		if($param['caption'])
			$ret_el['caption'] = $param['caption'];
			*/
		if($param['type'] =='FCKEDITOR')
			{
			$ret_el['value'] = $this->Create_Editor_Element($param['width'], $param['height'], $param['name'], $param['default'], $param['toolbar']);
			}
/*		else
			$ret_el['caption'] = false;*/
		return $ret_el;
		}		
	/****************OLD************************/
	function GetParents($id, $tree, $end)
		{
		//$par = array();
		$cnt = 0;
		for($i=0; $i<$end; $i++)
			{
			if($tree[$i]['catalog']['sc_id'] == $id)
				{
				$cnt++;
				$this->parents[$tree[$i]['level']] = $tree[$i]['catalog']['sc_id'];
				$this->GetParents($tree[$i]['catalog']['sc_parId'], $tree, $i);
				}			
			}
//		echo 'id = '.$tree[$i]['catalog']['sc_id'].', name = '.$tree[$i]['catalog']['sc_name'].'<br>';
		return $cnt;
		$par;
		}
	}
?>