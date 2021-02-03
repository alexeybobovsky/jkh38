<?
$realPath = $ROUT->GetStartedUrlLvl($allMenu['curLabel']['link'], $allMenu['activeLink'], $uri);
$curURL = $realPath['url'];
$curURLLvl = $realPath['lvl'];
if($fuckedSymbolPosition = strpos($post[$curURLLvl+1], '?'))	//неверная обрезка строки запроса URL -  оставляет в параметрах знак "?" и все что за ним
	$post[$curURLLvl+1] = substr($post[$curURLLvl+1], 0, $fuckedSymbolPosition);		

/*print_r($realPath);

echo $curURL = $realPath['url'];
echo '<br>';
echo $curURLLvl = $realPath['lvl'];
$curURL .= (substr_count($curURL, '/') < $curURLLvl)?'/':'';
echo '<br>';
echo $curURL; */
if((!trim($post[$curURLLvl])))
	$act = $allMenu['curNodeName'];
else
	$act = trim($post[$curURLLvl]);
//echo $act;	
//echo '<br>'.$post[$curURLLvl+1] ;
/*print_r($_POST);*/
require_once("../classes/gd/news_class.php");			
$NEWS = new news;
$imagePrewSizeX = 90;
$imagePrewSizeY = 68;
/*
*/
$error = 0;
//$_SESSION['act'] = $act;
$recordType	=	($post[3] == 'news') ? 1 : 2 ;	//новости или статьи
$isMng = ($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)?true:false;

switch ($act)
	{
	case 'showSelectedItem': /*форма редактирования объекта (AJAX)*/
		{
		if(($ACL->GetClosedParentRight($allMenu['curNodeId'])>1))
			{
			if(intval($_POST['node']>0))
				{

				$obj = $NEWS->getCurNews(intval($_POST['node']));
//				print_r($obj);
				$contentArea[]['label'] = 'Изменение объекта';
				$tmpCont[] = $NEWS->ShowNewsEditBtn($_POST['node'], $curURL);
				$tmpCont[] = $NEWS->ShowNewsVisible($obj['news_public'], $curURL);
				$contentHTML[] =  $tmpCont;
/*				
				$contentArea[]['label'] = 'Изображение';
				$contentHTML[][] = $NEWS->ShowNewsImg($obj, $curURL);					
*/
				$contentArea[]['label'] = 'Удаление объекта';
				$contentHTML[][] = $NEWS->ShowNewsDelete($curURL);	
				
				$contentArea[]['label'] = 'Добавить новость';			
				$contentHTML[][] = $NEWS->ShowNewsAdd($curURL);
				}
			elseif(intval($_POST['node']==0))
				{
				$contentArea[]['label'] = 'Добавить новость';			
				$contentHTML[][] = $NEWS->ShowNewsAdd($curURL);
				$contentArea[]['label'] = 'Добавить категорию';			
				$contentHTML[][] = $NEWS->ShowCategoryAdd($curURL);
				}
			if(is_array($contentArea))
				{
				$templates = array();
				require_once('../htdocs/includes/Smarty/setup.php');
				$smarty = new Smarty_CMS;	
				$smarty->init();
				$smarty->assign('contentArea', $contentArea);
				$smarty->assign('contentHTML', $contentHTML);
//				$table = '';
				$table .= $smarty->fetch('manage/catalogOptionsNew.tpl');
//			$table .= $str;
//			print_r($firmList, $table);
				echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
				}
			}
		else
			{
			$error = 404;
			}
		$HISTORY_SKIP = 1;		
		}
	break;	
	case 'set':
		{
//		$_SESSION['tmp'] = $post;		
		$error=0;
		$HISTORY_SKIP = 1;		
		if(($post[$curURLLvl+1] == 'editNews')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($curNode = intval($_POST['curNode'])))
			{
			$param['name'] =  htmlspecialchars(trim($_POST['title']), ENT_QUOTES );  
			$param['nameTranslit'] =  (trim($_POST['url']))?strtolower($ROUT->translitForUrl(trim($_POST['url']))):substr(strtolower($ROUT->translitForUrl(trim($_POST['title']))), 0, 25);
//			$param['nameTranslit'] =  (trim($_POST['url']))?strtolower($ROUT->translitForUrl(trim($_POST['url']))):strtolower($ROUT->translitForUrl(trim($_POST['title'])));
			if($url = trim($_POST['srcURL']))
				{
				$param['src'] = '<a target="_blank" href="http://'.$url.'">';
				$param['src'] .=  ($caption = trim($_POST['srcCaption'])) ? $caption : $url;
				$param['src'] .=  '</a>';
				}
			elseif($caption = trim($_POST['srcCaption']))
				$param['src'] = '<p>'.$caption.'</p>';
			else				
				$param['src'] = '';
			$param['header'] =  (get_magic_quotes_gpc())?trim($_POST['head']):addslashes(trim($_POST['head']));  
 			$param['body'] =  (get_magic_quotes_gpc())?trim($_POST['body']):addslashes(trim($_POST['body']));  
			if($recordType == 2)			
				$param['dateUp'] = 1;
 			$param['category'] = $recordType;
			$imgUpdate = $imgDelete = $imgMaxNum = 0;
			while (list ($key, $val) = each ($_POST))
				{
				if(stristr($key,  'delImg~'))
					{
					$imgDelete ++;
					$img4del = $ROUT->getStrPrt($key, '~', 1);
					$delImg[] = $_POST['imgFullPath~'.$img4del];
					}
				elseif(stristr($key,  'CMP#'))
					{
					$curProp = $ROUT->getStrPrt($key, '#', 1);					
					$curImg = $ROUT->getStrPrt($curProp, '~', 1);
					$imgMaxNum = ($curImg > $imgMaxNum) ? $curImg : $imgMaxNum;
					if((isset($_POST[$curProp]))&&(($newVal = trim($_POST[$curProp]))!= $val)) //update
						{
						$imgUpdate ++;
						$img4up =  $curImg;
						$updt['src'] = $_POST['imgFullPath~'.$img4up];
						$updt['val'] = $newVal;
						$about[] = $updt;
						}
					}
				}
/*			print_r($_POST);
			print_r($about);
			print_r($delImg);*/
			/**/
			$res = $NEWS->updateNews($curNode, $param);
			if($res['error'])
				{
				$error ++;
				}	
			else
				{
				$NEWS->deleteNewsFromCat($curNode);
				for($i=0; $i<sizeof($_POST['category']); $i++)
					{
					$NEWS->addNews2Cat($curNode, $_POST['category'][$i]);
					}
				for($i=0; $i<$imgUpdate; $i++)
					if(!in_array($about[$i]['src'], $delImg))
						$NEWS->updateImg2News($curNode, $about[$i]['src'], $about[$i]['val']);
				for($i=0; $i<$imgDelete; $i++)
					{
					$srcFileArr = $ROUT->getFileName($delImg[$i]);
					$prewFile = $srcFileArr['path'].'/'.$srcFileArr['name'].'_prew.'.$srcFileArr['ext'];
					unlink($CONST['relPathPref'].'/'.$prewFile);						
					unlink($CONST['relPathPref'].$delImg[$i]);					
					$NEWS->deleteImg2News($curNode, $delImg[$i]);
					}
					
				if($imgCnt = sizeof($_SESSION['newsImg']['img']))					
					{
					$prewSize = 225;
					$fullSize = 1024;

					$date = date('Y_m_d', strtotime($_POST['news_date']));
					$typeFolder = ($recordType == 1)?'news':'doc';
					$urlPathDate = '/src/upload/'.$typeFolder.'/'.$date;						
					$urlPathRoot = $urlPathDate.'/'.$_POST['news_nameTranslit'].'/';						
					
					
					if(!is_dir($CONST['relPathPref'].'/'.$urlPathDate))
						mkdir($CONST['relPathPref'].'/'.$urlPathDate);
					if(!is_dir($CONST['relPathPref'].'/'.$urlPathRoot))
						mkdir($CONST['relPathPref'].'/'.$urlPathRoot);
					for($i=0; $i<$imgCnt; $i++)
						{
						$tmpFilePath = $CONST['relPathPref'].'/'.$_SESSION['newsImg']['img'][$i];
//						echo '<br>';
						if(is_file($tmpFilePath))
							{
							$srcFileArr = $ROUT->getFileName($_SESSION['newsImg']['img'][$i]);
							$tmpPath = $ROUT->getStrPrt($_SESSION['newsImg']['img'][$i], $srcFileArr['name'], 0);
							
							$prewFileName = $CONST['relPathPref'].'/'.$tmpPath.$srcFileArr['name'].'_prew.'.$srcFileArr['ext'];
							$destFileNamePrew = ($imgMaxNum + $i+1).'_prew.'.$srcFileArr['ext'];							
							$destFileName = ($imgMaxNum + $i+1).'.'.$srcFileArr['ext'];							
							rename ($prewFileName, $CONST['relPathPref'].$urlPathRoot.$destFileNamePrew);										
							$ROUT->imageResizeMod($tmpFilePath, $CONST['relPathPref'].$urlPathRoot.$destFileName, $fullSize, 0);
							if(isset($_POST['aboutImg_'.$srcFileArr['name']]))
								$imgAbout = htmlspecialchars($_POST['aboutImg_'.$srcFileArr['name']], ENT_QUOTES ); 
							else
								$imgAbout = ''; 
							$NEWS->addImg2News($curNode, $urlPathRoot.$destFileName, $imgAbout);
							unlink($tmpFilePath);							
							}
						}
					unset($_SESSION['newsImg']['img']);
					}			
				$catList = $NEWS->getCatListNoEmpty();
				for($i=0; $i<sizeof($catList); $i++)
					{
					$cnt = $NEWS->getNewsCntOfKeyword($catList[$i]['cat_id'],  $recordType);
					$NEWS->updNewsCategoryCounter($catList[$i]['cat_id'], $cnt, $recordType);
					}
				}
			if(!$error)
				{
				$messBox = new Message('Info', 'Изменение новости', 'Операция успешно выполнена. Для продолжения работы закройте этот диалог.', '');	
				}
			else
				{
				$messBox = new Message('Error', 'Изменение новости', 'Произошла ошибка - попробуйте повторить операцию. Для продолжения работы закройте этот диалог.', '');	
				}
			$templates = array();
			$SMRT_TMP = array();
			$SMRT_TMP['name'] = 'messBox';
			$SMRT_TMP['body'] = $messBox;
			$SMRT['modules'][] = $SMRT_TMP;
			$templates[] = 'MessageBox.tpl';	
			$error = 0;
//	echo '<p>Операция успешно выполнена. Для продолжения закройте этот диалог.<p>';
			}
		elseif(($post[$curURLLvl+1] == 'delete')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&(intval($_POST['node'])))
			{
			$curNode = intval($_POST['node']);
			$img4Del = $NEWS->getNewsImages($curNode);			
			for($i=0; $i<sizeof($img4Del); $i++)
				{
				$srcFileArr = $ROUT->getFileName($img4Del[$i]['img_src']);
				$prewFile = $srcFileArr['path'].'/'.$srcFileArr['name'].'_prew.'.$srcFileArr['ext'];
				unlink($CONST['relPathPref'].'/'.$prewFile);					
				unlink($CONST['relPathPref'].'/'.$img4Del[$i]['img_src']);					
				}			
			$templates = array();					
			$param = array();
			$res = array();
			$res = $NEWS->deleteNews($curNode);
			if($res['error'])
				{
				//$error ++;
				echo '0';
				}	
			else
				{
				$catList = $NEWS->getCatListNoEmpty();
				for($i=0; $i<sizeof($catList); $i++)
					{
					$cnt = $NEWS->getNewsCntOfKeyword($catList[$i]['cat_id'],  $recordType);
					$NEWS->updNewsCategoryCounter($catList[$i]['cat_id'], $cnt, $recordType);
					}
				echo '1';
				}
			}
		elseif(($post[$curURLLvl+1] == 'addNews')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1))
			{
//			print_r($_POST);
			$param['name'] =  htmlspecialchars(trim($_POST['title']), ENT_QUOTES );  
//			$param['nameTranslit'] =  (trim($_POST['url']))?strtolower($ROUT->translitForUrl(trim($_POST['url']))):strtolower($ROUT->translitForUrl(trim($_POST['title'])));
			$param['nameTranslit'] =  (trim($_POST['url']))?strtolower($ROUT->translitForUrl(trim($_POST['url']))):substr(strtolower($ROUT->translitForUrl(trim($_POST['title']))), 0, 25);
			if($url = trim($_POST['srcURL']))
				{
				$param['src'] = '<a target="_blank" href="http://'.$url.'">';
				$param['src'] .=  ($caption = trim($_POST['srcCaption'])) ? $caption : $url;
				$param['src'] .=  '</a>';
				}
			elseif($caption = trim($_POST['srcCaption']))
				$param['src'] = '<p>'.$caption.'</p>';
			else				
				$param['src'] = ''; 			
			$param['header'] =  (get_magic_quotes_gpc())?trim($_POST['head']):addslashes(trim($_POST['head']));  
 			$param['body'] =  (get_magic_quotes_gpc())?trim($_POST['body']):addslashes(trim($_POST['body']));  
 			$param['published'] = (isset($_POST['published']))?1:0;
 			$param['category'] = $recordType;
 			$param['auth'] = intval($USER->id);
//			print_r($param); 
			/**/
			$res = $NEWS->createNews($param);	
			if($res['error'])
				{
				$error ++;
				}	
			else
				{
				for($i=0; $i<sizeof($_POST['category']); $i++)
					{
					$NEWS->addNews2Cat($res['last_id'], $_POST['category'][$i]);
					$NEWS->incNewsCategoryCounter($_POST['category'][$i], $recordType);
					}
				if($imgCnt = sizeof($_SESSION['newsImg']['img']))					
					{
					$prewSize = 225;
					$fullSize = 1024;
					$date = date('Y_m_d', time());
					$typeFolder = ($recordType == 1)?'news':'doc';
					$sizeArr = array(1024, $prewSize);
					$sizeArrCnt = sizeOf($sizeArr);
					$urlPathDate = '/src/upload/'.$typeFolder.'/'.$date;						
					$urlPathRoot = $urlPathDate.'/'.$param['nameTranslit'].'/';						
					if(!is_dir($CONST['relPathPref'].'/'.$urlPathDate))
						mkdir($CONST['relPathPref'].'/'.$urlPathDate);
					if(!is_dir($CONST['relPathPref'].'/'.$urlPathRoot))
						mkdir($CONST['relPathPref'].'/'.$urlPathRoot);
					for($i=0; $i<$imgCnt; $i++)
						{
						$tmpFilePath = $CONST['relPathPref'].'/'.$_SESSION['newsImg']['img'][$i];
//						echo '<br>';
						if(is_file($tmpFilePath))
							{
							$srcFileArr = $ROUT->getFileName($_SESSION['newsImg']['img'][$i]);
							$tmpPath = $ROUT->getStrPrt($_SESSION['newsImg']['img'][$i], $srcFileArr['name'], 0);
							
							$prewFileName = $CONST['relPathPref'].'/'.$tmpPath.$srcFileArr['name'].'_prew.'.$srcFileArr['ext'];
							$destFileNamePrew = ($i+1).'_prew.'.$srcFileArr['ext'];							
							$destFileName = ($i+1).'.'.$srcFileArr['ext'];							
							rename ($prewFileName, $CONST['relPathPref'].$urlPathRoot.$destFileNamePrew);										
							$ROUT->imageResizeMod($tmpFilePath, $CONST['relPathPref'].$urlPathRoot.$destFileName, $fullSize, 0);
							if(isset($_POST['aboutImg_'.$srcFileArr['name']]))
								$imgAbout = htmlspecialchars($_POST['aboutImg_'.$srcFileArr['name']], ENT_QUOTES ); 
							else
								$imgAbout = ''; 
							$NEWS->addImg2News($res['last_id'], $urlPathRoot.$destFileName, $imgAbout);
							unlink($tmpFilePath);							
							}
						}
					unset($_SESSION['newsImg']['img']);
					}			
				}
			if(!$error)
				{
				$messBox = new Message('Info', 'Создание новости', 'Операция успешно выполнена. Для продолжения работы закройте этот диалог.', '');	
				}
			else
				{
				$messBox = new Message('Error', 'Создание новости', 'Произошла ошибка - попробуйте повторить операцию. Для продолжения работы закройте этот диалог.', '');	
				}
			$templates = array();
			$SMRT_TMP = array();
			$SMRT_TMP['name'] = 'messBox';
			$SMRT_TMP['body'] = $messBox;
			$SMRT['modules'][] = $SMRT_TMP;
			$templates[] = 'MessageBox.tpl';	
			$error = 0;
//	echo '<p>Операция успешно выполнена. Для продолжения закройте этот диалог.<p>';
			}
		elseif(($post[$curURLLvl+1] == 'updateCatCnt')&&($isMng))
			{
//			echo 'dddd';
			$catList = $NEWS->getCatListNoEmpty();
			for($i=0; $i<sizeof($catList); $i++)
				{
				$cnt = $NEWS->getNewsCntOfKeyword($catList[$i]['cat_id'],  $recordType);
				$NEWS->updNewsCategoryCounter($catList[$i]['cat_id'], $cnt, $recordType);
				}
			}
		elseif(($post[$curURLLvl+1] == 'visible')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&(intval($_POST['node'])))
			{
//			echo 'dddd';
			$curNode = intval($_POST['node']);
			$newState = (intval($_POST['status']))?0:1;
//			$type = trim($post[$curURLLvl+2]);
			$templates = array();			
			$param = array();
			$res=array();
			$res = $NEWS->updateNews($curNode, array('public' => $newState));			
			if($res['error'])
				{
				//$error ++;
				echo '0';
				}	
			else
				{
				echo '1';
				}
			}
		elseif(($post[$curURLLvl+1] == 'addCategory')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1))
			{
			$templates = array();
//			$name = iconv("UTF-8", "windows-1251", trim($_POST['name']));
			$name = trim($_POST['name']);
			$res = $NEWS->createCategory(htmlspecialchars($name, ENT_QUOTES));
			if($res['error'])
				{
				//$error ++;
				echo $res['errorMsg'];
				}	
			else
				{
				echo 0;
				}			
			}
		elseif(($post[$curURLLvl+1] == 'upload')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&(intval($post[$curURLLvl+3]))&&(trim($post[$curURLLvl+2])))
			{
			$fileElementName = 	trim($post[$curURLLvl+2]);
			$node = 			intval($post[$curURLLvl+3]);
			if(($_FILES[$fileElementName]['size']))
				{	
//				$_SESSION['tmp'] = $_FILES;
//				print_r($_FILES);
				$urlDir = trim($CONST['srcNews']);
//				$fileName = trim(basename($ROUT->translit(strtolower($_FILES[$fileElementName]['name']), 1)));
				$fileName = strtolower(trim(basename($ROUT->translit($_FILES[$fileElementName]['name'], 1))));
				$upDir = trim($CONST['relPathPref']).$urlDir;
				$uploadfile = $upDir.$fileName;					
//				$_SESSION['fileName'] = $fileName;
				if(!is_dir($upDir))
					{
					mkdir($upDir);
					}
				$ROUT->imageResize($_FILES[$fileElementName]['tmp_name'], $uploadfile, $imagePrewSizeX, $imagePrewSizeY);
/*				$resFile = $ROUT->CheckAndMoveUploadedFile($fileElementName, $_FILES,  1000000,  $uploadfile);
				if($resFile)
					{*/
//						chmod($uploadfile, 0644);
					$imgRes = $NEWS->updateNews($node, array('img' => $urlDir.$fileName));
//					}
				}
			}
		elseif(($post[$curURLLvl+1] == 'deleteImg')&&($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&(intval($_POST['node'])))
			{
			$curNode = intval($_POST['node']);
			$news = $NEWS->getCurNews(intval($curNode));
			$templates = array();			
			$res = $NEWS->deleteImg($news);
			if($res['error'])
				{
				echo '0';
				}	
			else
				{
				echo '1';
				}
			}

		elseif(($post[$curURLLvl+1] == 'fileUpload')&&($isMng))
			{
			$templates = array();			
			$typeFolder = ($recordType == 1)?'news':'doc';
			$uploadTmpPath = 'src/upload/'.$typeFolder.'/tmp/';
			require_once("../classes/fileUploader.php");			
			$allowedExtensions = array();
			// max file size in bytes
			$sizeLimit = 7 * 1024 * 1024;
			$uploader = new qqFileUploader($allowedExtensions, $sizeLimit, array(225, 0));
//			$str = print_r($uploader, true);

			$result = $uploader->handleUpload($CONST['relPathPref'].'/'.$uploadTmpPath);
//			$str = print_r($result, true);
			// to pass data through iframe you will need to encode all html tags
//			echo $uploadTmpPath;
			echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);			
			$templates[0] = '';	
			if($result['success'])
				{
//				$_SESSION['newsImg']['img'][] = $uploadTmpPath.$uploader->filenameOut['basename'];
				$_SESSION['newsImg']['img'][] = $uploadTmpPath.$result['filename'].'.'.$result['ext'];
				}
			}		
		else
			{
			$error ++;
			}
		if($error)
			{
			$errMess = 'Не удалось изменить запись! ';
			$errMess .= ($CONST['debugMode'])?': System Message - '.$res['errorMsg']:'';
			$MESS = new Message('Error', 'Изменение записи', $errMess, $NAV->GetPrewURI());														
			}
		
		}break;
	case 'list': /*управление слоями*/
		{
		if(($ACL->GetClosedParentRight($allMenu['curNodeId'])>1))
			{
			$list = $NEWS->getNewsList($recordType);

			
			$docList =  $NEWS->showNewsList($list, $curURL);
			$docList['selected'] = intval($_POST['node']);

			$templates = array();
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;	
			$smarty->init();
			$smarty->assign('orders', $docList);
//			$smarty->assign('curNode', intval($_POST['node']));
			echo $table = $smarty->fetch('manage/newsList.tpl');
//			echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
			}
		else
			{
			$error = 404;
			}
		}
	break;	
	case 'newsAdd': /*форма редактирования объекта (AJAX)*/
		{
		if(($ACL->GetClosedParentRight($allMenu['curNodeId'])>1))
			{
//			print_r($post);
//			echo $recordType;
			$_SESSION['editor']['access'] = 1;
			$_SESSION['editor']['folder'] = array('news', date('Y_m_d', time()));				
			$parId =  intval($post[$curURLLvl+1]);
			$templates = array();
			$templates[] = 'emptyHeader.tpl';
			$templates[] = 'manage/personEdit.tpl';
			$templates[] = 'emptyFooter.tpl';
			
			$categories = 	$NEWS->getNewsCategories();
			$catForSelect = $ROUT->convertArrayIntoSelect($categories, 'cat_id', 'cat_name', -1, 0);
			
			$contentArea[]['label'] = 'Параметры новости';
			
			$contentHTML[] = $NEWS->ShowNewsAddForm($catForSelect, $curURL);
			$SMRT_TMP = array();
			$SMRT_TMP['name'] = 'contentArea';
			$SMRT_TMP['body'] = $contentArea;
			$SMRT['modules'][] = $SMRT_TMP;
			$SMRT_TMP = array();
			$SMRT_TMP['name'] = 'contentHTML';
			$SMRT_TMP['body'] = $contentHTML;
			$SMRT['modules'][] = $SMRT_TMP;
			


			$HISTORY_SKIP = 1;		
//			print_r($_POST);
			}
		else
			{
			$error = 404;
			}
		}
	break;	
	case 'edit': /*форма редактирования объекта (AJAX)*/
		{
		if(($ACL->GetClosedParentRight($allMenu['curNodeId'])>1)&&($post[$curURLLvl+1]))
			{
//			print_r($post);
			$templates = array();
			$templates[] = 'emptyHeader.tpl';
			$templates[] = 'manage/personEdit.tpl';
			$templates[] = 'emptyFooter.tpl';
			$parId =  intval($post[$curURLLvl+1]);
			$obj = $NEWS->getCurNews(intval($post[$curURLLvl+1]));
//			print_r($obj);
			if($obj['news_src'])
				{
				$del1 = 'href="http://';
				$del2 = '</a>';
				$del3 = '">';
				$del4 = '<p>';
				$del5 = '</p>';				
				if (strstr($obj['news_src'], $del2))
					{					
					$srcStart = strpos ($obj['news_src'], $del1) + strlen($del1);
					$srcLen =  	strpos ($obj['news_src'], $del3) - $srcStart;					
					$capStart = strpos ($obj['news_src'], $del3) + strlen($del3);
					$capLen =  	strpos ($obj['news_src'], $del2) - $capStart;
					$obj['news_src_url']	= 		substr($obj['news_src'], $srcStart, $srcLen);					
					$obj['news_src_caption']	= 	substr($obj['news_src'], $capStart, $capLen);					
					}
				elseif (strstr($obj['news_src'], $del4))
					{					
					$capStart = strpos ($obj['news_src'], $del4) + strlen($del4);
					$capLen =  	strpos ($obj['news_src'], $del5) - $capStart;
					$obj['news_src_url']	= 		'';					
					$obj['news_src_caption']	= 	substr($obj['news_src'], $capStart, $capLen);					
					}
				else
					{
					$obj['news_src_url']	= 		$obj['news_src'];					
					$obj['news_src_caption']	= 	'';					
					}
				}
			$date = date('Y_m_d', strtotime($obj['news_date']));
			$typeFolder = ($recordType == 1)?'news':'doc';
			$urlPathDate = '/src/upload/'.$typeFolder.'/'.$date;						
			$urlPathRoot = $urlPathDate.'/'.$obj['news_nameTranslit'].'/';						
			$obj['defPath'] = $urlPathRoot;
			
			$obj['defPath'] = $urlPathRoot;
			//$srcFileArr = $ROUT->getFileName($_SESSION['newsImg']['img'][$i]);
			for($i=0; $i<sizeof($obj['img']); $i++)
				{
				$srcFileArr = $ROUT->getFileName($obj['img'][$i]['img_src']);
				$obj['img'][$i]['file'] = $srcFileArr['name'];
				$obj['img'][$i]['img_srcPrew'] = $srcFileArr['path'].'/'.$srcFileArr['name'].'_prew.'.$srcFileArr['ext'];
				}
//			print_r($obj);
			$categories = 	$NEWS->getNewsCategories();
			$catForSelect = $ROUT->convertArrayIntoSelect($categories, 'cat_id', 'cat_name', -1, 0);
			
			$contentArea[]['label'] = 'Параметры новости';
			
			$contentHTML[] = $NEWS->ShowNewsEditForm($obj, $catForSelect, $curURL);
			$SMRT_TMP = array();
			$SMRT_TMP['name'] = 'contentArea';
			$SMRT_TMP['body'] = $contentArea;
			$SMRT['modules'][] = $SMRT_TMP;
			$SMRT_TMP = array();
			$SMRT_TMP['name'] = 'contentHTML';
			$SMRT_TMP['body'] = $contentHTML;
			$SMRT['modules'][] = $SMRT_TMP;
			


			$HISTORY_SKIP = 1;		
//			print_r($_POST);
			}
		else
			{
			$error = 404;
			}
		}
	break;	

	case 'doc':
		{
		$labelSpec = array('статью', 'статей');
		}
	case 'news':
		{
		if(($ACL->GetClosedParentRight($allMenu['curNodeId'])>1))
			{
			if(!sizeof($labelSpec))
				$labelSpec = array('новость', 'новостей');
			$templates = array();
			$templates[] = 'AdminHeaderGB.tpl';
			$templates[] = 'AdminMenu.tpl';
			$templates[] = 'manage/newsMain.tpl';
			$list = 		$NEWS->getNewsList($recordType);			
			
			$contentArea[]['label'] = 'Добавить '.$labelSpec[0];			
			$contentHTML[][] = $NEWS->ShowNewsAdd($curURL);
			$contentArea[]['label'] = 'Добавить категорию';			
			$contentHTML[][] = $NEWS->ShowCategoryAdd($curURL);

			$SMRT_TMP['name'] = 'orders';
			$SMRT_TMP['body'] = $NEWS->showNewsList($list, $curURL); 
			$SMRT['modules'][] = $SMRT_TMP;	
			$SMRT_TMP = array();
			$SMRT_TMP['name'] = 'contentArea';
			$SMRT_TMP['body'] = $contentArea;
			$SMRT['modules'][] = $SMRT_TMP;
			$SMRT_TMP = array();
			$SMRT_TMP['name'] = 'contentHTML';
			$SMRT_TMP['body'] = $contentHTML;
			$SMRT['modules'][] = $SMRT_TMP;

			$SMRT_TMP = array();
			$SMRT_TMP['name'] = 'formLabel';
			$SMRT_TMP['body']['tree'] = 'Список '.$labelSpec[1];
			$SMRT_TMP['body']['options'] = 'Опции';
			$SMRT['modules'][] = $SMRT_TMP; /**/
//			print_r($post);*/
/*			

			$categories = 	$NEWS->getNewsCategories();
			$categories[] = array('ot_id'=>0, 'ot_name'=>''); 
			$SMRT_TMP['name'] = 'tree';
			$SMRT_TMP['body'] = $list; 
			$SMRT['modules'][] = $SMRT_TMP;	
			$SMRT_TMP = array();			
			$SMRT_TMP['name'] = 'orders';
			$SMRT_TMP['body'] = $NEWS->ShowNewsForm($curURL);// $userList; 
			$SMRT['modules'][] = $SMRT_TMP;	
			$SMRT_TMP = array();
			$SMRT_TMP['name'] = 'contentHTML';
			$catForSelect = 		$ROUT->convertArrayIntoSelect($categories, 'news_id', 'news_name', -1, 0);
			$SMRT_TMP['body'][] = $NEWS->ShowNewsNew($catForSelect, $curURL);
			$SMRT['modules'][] = $SMRT_TMP;

			$SMRT_TMP = array();
			$SMRT_TMP['name'] = 'formLabel';
			$SMRT_TMP['body']['tree'] = 'Новости';
			$SMRT_TMP['body']['options'] = 'Опции';
			$SMRT['modules'][] = $SMRT_TMP;
*/
			$templates[] = 'AdminFooter.tpl';
			}
		else
			{
			$error = 404;
			}
		}break;
		
	default :
		{
		$error = 404;
		}
	};
if($error==404)	
	{
	$messBodyNews=($CONST['debugMode'])?'Запрошенная Вами страница не найдена (from '.__FILE__ .', '. __FUNCTION__ .', '. __LINE__ .')':'Запрошенная Вами страница не найдена';
	$MESS = new Message('Error', 'ERROR 404', $messBodyNews, $NAV->GetPrewURI());											
	}
if(isset($MESS))
	{		
/*	echo '<hr>';
	print_r($MESS);*/

	$_SESSION['MESSAGE'] = $MESS;
	header('Location: /');	
	}
elseif($REDIRECT)
	{
	header('Location: '.$REDIRECT);	
	}
?>	