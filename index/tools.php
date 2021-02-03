<?

//echo $uri;
/*print_r($handler);
$curURLLvl = $ROUT->GetCurrentLevelOfURL($curURL)+1;*/
if($post[2])
	{
	$MTPL = new MANAGE_TEMPLATE;
	switch($post[2])
		{
		case 'upImg':
			{
			if(!count($_POST))
				{
				$templates = array();	
				$SMRT_TMP['name'] = 'options';
				$SMRT_TMP['body']['uploadDir'] = '/src/upload/usrimg/'.date("Y_m_d").'/';
				$SMRT['modules'][] = $SMRT_TMP;				
				$templates[] = 'elements/upImgConsole.tpl';	
				}
			else
				{
				if(($_FILES['imgFile']['size'])&&($_FILES['imgFile']['size']<=$_POST['MAX_FILE_SIZE']))
					{
/*					print_r($_POST);				
					print_r($_FILES);*/
					$upDir = '../htdocs'.$_POST['srcUpload'];
					$uploadfile = $upDir.basename($ROUT->translit(strtolower($_FILES['imgFile']['name']), 1));
					
					if(!is_dir($upDir))
						{
						mkdir($upDir);
						}
					$res = $ROUT->CheckAndMoveUploadedFile('imgFile', $_FILES,  $_POST['MAX_FILE_SIZE'],  $uploadfile);
					}
				$SMRT_TMP['name'] = 'options';
				$SMRT['modules'][] = $SMRT_TMP;				
				$templates = array();	
				$templates[] = 'emtySelfClose.tpl';	
				}
			}break;
		case 'calendar':
			{
			if($post[3])
				{
				$templates = array();	
				$SMRT_TMP['name'] = 'name';
				$SMRT_TMP['body'] = $post[3];
				$SMRT['modules'][] = $SMRT_TMP;				
				$templates[] = 'calendarShow.tpl';	
				}
			else
				{
				$error = 404;				
				}
			}break;
			
		case 'edit':
			{
			$curURL  = '/admin/content/structure/';//= $post[0]$ROUT->GetStartedUrl($handler['sc_name'], $uri);
			if($post[3] == 'editNodePar')
				{
				require_once('../htdocs/includes/Smarty/setup.php');
				$smarty = new Smarty_CMS;				
				if($post[4])
					$nodeId = $post[4];
				$node = $CNT->GetBlockBodyViaId($nodeId);
				$smarty->assign('uri', $uri);	
				if(((!$PAR_TYPE)&&($node['type']=='varchar'))||($PAR_TYPE=='VARCHAR')||($PAR_TYPE=='PAGEHEADER'))
					{
					if((!$PAR_TYPE)&&($node['type']=='varchar'))
						{
						$PAR_TYPE = ($node['name'] == 'title')?'PAGEHEADER':'VARCHAR';
						}
					$size['w'] = 500;
					$size['h'] = 250;
					}
				elseif(((!$PAR_TYPE)&&($node['type']=='text'))||($PAR_TYPE=='TEXT')||($PAR_TYPE=='INFOBLOCK'))
					{
					if((!$PAR_TYPE)&&($node['type']=='text'))
						{
						$PAR_TYPE = ($node['name'] == 'textblock')?'INFOBLOCK':'TEXT';
						}
					$size['w'] = 700;
					$size['h'] = 500;
					include('../htdocs/includes/FCKeditor/fckeditor.php');
					$oFCKeditor = new FCKeditor('PAR_VALUE');
					$oFCKeditor->Height = (isset($post[6]))?$post[6]-300:'400';
					$oFCKeditor->ToolbarSet = 'Custom';
					$oFCKeditor->BasePath = '/includes/FCKeditor/';
					$oFCKeditor->Config['SkinPath'] = $oFCKeditor->BasePath. 'editor/skins/office2003/' ;
					$oFCKeditor->Value = $node['value'];
					$output = $oFCKeditor->CreateHtml() ;	
					$smarty->assign('textEditor', $output);									
					}
				$smarty->assign('edit', $MTPL->TPL_EditNodePar($node, $PAR_TYPE, $curURL));				
				$smarty->assign('windowSize', $size);
				$smarty->display('editor_start.tpl');
				$smarty->display('editor_end.tpl');
				$templates = array();
				}
			elseif($post[3] == 'addNewNodePar')
				{
				require_once('../htdocs/includes/Smarty/setup.php');
				$smarty = new Smarty_CMS;				
				if($post[4])
					$nodeId = $post[4];
				$node = $CNT->GetCurNode($nodeId, 0);
				$smarty->assign('uri', $uri);		
				if(!isset($PAR_TYPE))
					$PAR_TYPE = 'INFOBLOCK';
				if(($PAR_TYPE=='VARCHAR')||($PAR_TYPE=='PAGEHEADER'))
					{
					$size['w'] = 500;
					$size['h'] = 250;
					$smarty->assign('edit', $MTPL->TPL_AddNodePar($node, $PAR_TYPE, $curURL));				
					}
				elseif((!$PAR_TYPE)||($PAR_TYPE=='INFOBLOCK')||($PAR_TYPE=='TEXT'))
					{
					$size['w'] = 700;
					$size['h'] = 500;
					$smarty->assign('edit', $MTPL->TPL_AddNodePar($node, $PAR_TYPE, $curURL));				
					include('../htdocs/includes/FCKeditor/fckeditor.php');
					$oFCKeditor = new FCKeditor('PAR_VALUE');
					$oFCKeditor->Height = (isset($post[6]))?$post[6]-300:'400';
					$oFCKeditor->ToolbarSet = 'Custom';
					$oFCKeditor->BasePath = '/includes/FCKeditor/';
					$oFCKeditor->Config['SkinPath'] = $oFCKeditor->BasePath. 'editor/skins/office2003/' ;
					$oFCKeditor->Value = '';
					$output = $oFCKeditor->CreateHtml() ;	
					$smarty->assign('textEditor', $output);									
					}				
				$smarty->assign('windowSize', $size);
				$smarty->display('editor_start.tpl');
				$smarty->display('editor_end.tpl');
				$templates = array();
				}

			} break;
		case 'test':
			{
			echo $CNT->GetCurNodePar(15, 'sc_thread');
			$templates = array();
			} break;
		default:
			{
			$error = 404;
			}
		}
	}
else
	{
	$error = 404;	
	}
$HISTORY_SKIP ++;	
if($error==404)	
	{
	$messBodyNews=($CONST['debugMode'])?'Запрошенная Вами страница не найдена (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')':'Запрошенная Вами страница не найдена';
	$MESS = new Message('Error', 'ERROR 404', $messBodyNews, $NAV->GetPrewURI());											
	}
if(isset($MESS))
	{
	$_SESSION['MESSAGE'] = $MESS;
	header('Location: /');	
	}	
?>