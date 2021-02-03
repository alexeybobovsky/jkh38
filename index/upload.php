<?
if(!$post[2])
	{
	$templates = array();	
	$templates[] = 'AdminHeader.tpl';
	$templates[] = 'AdminMenu.tpl';
	$ATPL = new ADMIN_TEMPLATE;
	$SMRT_TMP['name'] = 'upload';
	$SMRT_TMP['body'] = $ATPL->UploadFile();
	$SMRT['modules'][] = $SMRT_TMP;	
	$templates[] = 'upload.tpl';	
	$templates[] = 'AdminFooter.tpl';	
	}
elseif($post[2] == 'set')
	{
	$fileCopy = 0;
	$mess = '';
	$templates = array();	
/*	print_r($_FILES);
	print_r($_POST);*/
	$src = '..';
	$src .= ($_POST['USERCATALOG'])?$_POST['USERCATALOG']:$_POST['PRECATALOGS'];
	if(is_file($src.$_FILES['FILE']['name'])&&($_POST['OVERWRITE']))
		{
		if(unlink($src.$_FILES['FILE']['name']))
			{
			$fileCopy++;
			$type = 'Warning';
			$mess .= 'Warning: old file will be replaced<br>';			
			}
		else
			{
			$type = 'Error';
			$mess .= 'Owerwrite error';
			}
		}
	elseif(is_file($src.$_FILES['FILE']['name'])&&(!$_POST['OVERWRITE']))
		{
		$type = 'Error';
		$mess .= 'Error: This file already exists!!!';		
		}
	elseif(!is_file($src.$_FILES['FILE']['name']))
		{
		$fileCopy++;
		}
	if((!is_dir($src))&&(!$_POST['CREATEDIR']))
		{
		$type = 'Error';
		$mess .= 'Error: No such dir!!!';		
		}
	elseif((!is_dir($src))&&($_POST['CREATEDIR']))
		{
		if(mkdir($src))
			{
			$fileCopy++;
			$mess .= 'Info:  dir '.$src.' created<br>';		
			}
		else
			{
			$type = 'Error';
			$mess .= 'Error: Cant Create dir!!!<br>';		
			}
		}		
	//CREATEDIR
	if((copy($_FILES['FILE']['tmp_name'], $src.$_FILES['FILE']['name']))&&($fileCopy))	
		{
		$type = 'Info';
		$mess .= 'file successfully uploaded';
		}	
	else
		{
		$type = 'Error';
		$mess .= 'Error: can\'t copy file '.$_FILES['FILE']['tmp_name'].' to '.$src.$_FILES['FILE']['name'];				
		}
	$MESS = new Message($type, 'File Uploader',  $mess, $NAV->GetPrewURI());								
	$SMRT_TMP['name'] = 'MESS';
	$SMRT_TMP['body'] = $MESS;
	$SMRT['modules'][] = $SMRT_TMP;
	$templates[] = 'AdminHeader.tpl';
	$templates[] = 'AdminMenu.tpl';
	$templates[] = 'Message.tpl';											
	$templates[] = 'AdminFooter.tpl';	
	}
elseif($post[2] == 'list')
	{
	if(isset($post[3]))
		{
		$templates = array();	
		$src = '../';
		while (list ($key, $val) = each ($post))
			{
			if(($key>2)&&($val))
				$src .= $val.'/';
			}		
		
		$content =  '<form action="/upload/" name = "fm"  method=post encType=multipart/form-data><input type="hidden" value="'.$src.'" name = "SRC"><b>File list</b>:<br>';					
		$dir = opendir($src);
		while($file = readdir($dir))
			{
			if (($file!=".")&&($file!=".."))
				{     
				$left = $right = '';
				$delete = '<font color=red><SPAN onMouseMove="this.style.cursor=\'hand\'; return false;" 
												onclick="if (confirmLink(\'Вы действительно желаете удалить объект?\')) deleteFile(\''.$file.'\'); return false;">
												удалить</SPAN></font>';
				$act = 'onMouseMove="this.style.cursor=\'hand\'; return false;" 
							onclick="viewFile(\''.$file.'\'); return false;"';
				if(is_dir($src.$file))
					{
					$left = '<b>[';
					$right = ']</b>';
					$act = '';
					$delete = '';
					}
				$content .=   '<font color=blue><SPAN '.$act.'>
					'.$left.$file.$right.'</SPAN></font> 
					'.$delete.'<br>';
				}
			}
		$content .=   '</form>';
		$SMRT_TMP['name'] = 'content';
		$SMRT_TMP['body'] = $content;
		$SMRT['modules'][] = $SMRT_TMP;
		$templates[] = 'fileManager.tpl';											
		$templates[] = 'AdminFooter.tpl';	
		}
	}
elseif($post[2] == 'view')
	{
	if(isset($post[3]))
		{
/*		print_r($post);
		print_r($_POST);*/
//		$fp = fopen ($_POST.$post[3], "r");
		$text =  '<div onMouseMove="this.style.cursor=\'hand\'; return false;" 
					onclick="window.history.go(-1);"; return false;"> back </div>';
		$f_arr = explode('.', $post[3]);	
		$f_arr[1] = strtolower($f_arr[1]);
		if(($f_arr[1]!='jpg')&&($f_arr[1]!='jpeg')&&($f_arr[1]!='gif')&&($f_arr[1]!='bmp')&&($f_arr[1]!='png'))
			{
			$text .=  '<pre>';
			$content = file($_POST['SRC'].$post[3]);
			while (list ($line_num, $line) = each ($content)) 
			{
			    $text .=  htmlspecialchars($line);
			}
			$text .=  '</pre>';					
			}
		else
			{
			$text .= '<div><img src="'.$_POST['SRC'].$post[3].'" border=0></div>';
			}
		
		$templates = array();	
		$SMRT_TMP['name'] = 'content';
		$SMRT_TMP['body'] = $text;
		$SMRT['modules'][] = $SMRT_TMP;
		$templates[] = 'fileView.tpl';											
		$templates[] = 'AdminFooter.tpl';	
		}
	}
elseif($post[2] == 'delete')
	{
	if(isset($post[3]))
		{
		if(unlink($_POST['SRC'].$post[3]))
			{			
			$text =  'file '.$post[3].' succesfuly deleted ';
			}
		else
			{
			$text =  'can\'t delete file '.$post[3].'';
			}
		$text .= '<div onMouseMove="this.style.cursor=\'hand\'; return false;" 
			onclick="window.history.go(-1);"; return false;"> back </div>';
		$templates = array();	
		echo $text;
		}
	}
	
?>