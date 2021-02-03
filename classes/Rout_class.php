<?php
class Routine
	{
	var $_1_2 = array();
	var $_1_19 = array();
	var $des = array();
	var $hang = array();
	var $namerub = array();
	var $nametho = array();
	var $namemil = array();
	var $namemrd = array();
	var $kopeek = array();
	
	function getStars($value, $max)
		{
		$star = array();
		$floored =  floor($value);
		$delta = $value - $floored;
		$filled =  ($delta>=0.7) ? $floored + 1 : $floored;
		$star['filled'] = $filled;
		for($i=0; $i<$max; $i++)
			{
			if(($i + 1) <= $filled)
				$star['value'][$i] = 1;
			else
				$star['value'][$i] = 0;			
			}
		$star['title']	= array ('Ужас', 'Плохо', 'Так себе', 'Хорошо', 'Супер' );
		return $star;
		}
	function getFilledStars($value)
		{
		$floored =  floor($value);
		$delta = $value - $floored;
		return ($delta>=0.7) ? $floored + 1 : $floored;
		}
	function makeCityPhone($value, $cityCode)
		{
		$div = '_';
		$tmpArr = explode($div, $value);
		$Val = $tmpArr[0];
		$add = ($tmpArr[1]) ? preg_replace('/\D*/', '', $tmpArr[1]) : '';
        if($Val)
			{
            $Phone = preg_replace('/\D*/', '', $Val); // убиваем все "не числа"
            if( strlen($Phone) == 11 && $Phone{0} = '8' ) // тут проверка соответствия на длинный номер - типа 84232443322
				{
                $strPhone = /*$Phone{0} . */'+7 (' . substr($Phone, 1, 4) . ') ' . substr($Phone, 5, 2) . '-' . substr($Phone, 7, 2) . '-' . substr($Phone, 9, 2); //получаем 8(423)2-443-322
				} 
            else if ( strlen($Phone) == 6 /* && $Phone{0}  2*/) // тут проверка соответствия на короткий номер - это было оставлено временно - типа 2443322
				{
				$strPhone = ($cityCode) ? '+7 (' . $cityCode . ') ' : ''; 
                $strPhone .= substr($Phone, 0, 2) . '-' . substr($Phone, 2, 2). '-' . substr($Phone, 4, 2); //получаем 44-33-22
                $strPhone .= ($add) ? ' ('.$add.')':''; //получаем 44-33-22				
				}
            else
				{
                $strPhone = "err_format";
				}            
            return $strPhone;
			}
        else
            return "";    
        }
	function phone_format($phone/*,  $format, $mask = '#'*/)
		{
/*		$mask = '#';
		$format = array(
				'6' => '##-##-##',
				'7' => '###-##-##',
				'10' => '+7 (###) ### ####',
				'11' => '# ### ###-##-##'
			);		
		$phone = preg_replace('/[^0-9]/', '', $phone);

		if(is_array($format))
		{
			if(array_key_exists(strlen($phone), $format))
			{
				$format = $format[strlen($phone)];
			}
			else
			{
				return false;
			}
		}

		$pattern = '/'.str_repeat('([0-9])?', substr_count($format, $mask)).'(.*)/';
		$format = preg_replace_callback(str_replace('#', $mask, '/([#])/'),
			function() use (&$counter){
				return '${'.(++$counter).'}';
			}, $format);
		return ($phone) ? trim(preg_replace($pattern, $format, $phone, 1)) : false;*/
		}
	function getUserAgentInfo() // 2012_06_07 отдает браузер и версию 
		{
		if (strpos($_SERVER['HTTP_USER_AGENT'],"Opera") !==false)
		   {
		     $ua="Opera";
		     $uaVers = substr($_SERVER['HTTP_USER_AGENT'],strpos($_SERVER['HTTP_USER_AGENT'],"Opera")+6,4);
		   }
		elseif (strpos($_SERVER['HTTP_USER_AGENT'],"Gecko") !==false)
		   {
		     $ua="Netscape";
		     $uaVers = substr($_SERVER['HTTP_USER_AGENT'],strpos($_SERVER['HTTP_USER_AGENT'],"Mozilla")+8,3);
		   }
		elseif (strpos($_SERVER['HTTP_USER_AGENT'],"Windows") !==false)
		   {
		     $ua="Explorer";
		     $uaVers = substr($_SERVER['HTTP_USER_AGENT'],strpos($_SERVER['HTTP_USER_AGENT'],"MSIE")+5,3);
		   }
		else
		   {
		     $ua=$_SERVER['HTTP_USER_AGENT'];
		     $uaVers=""; 
		   }
		
		return  array('name' => $ua, 'version' => $uaVers);		
		}
	function getFilesInDir($dir) // 2012_06_06 отдает список файлов в каталоге 
		{
		if ($handle = opendir($dir)) 
			{		
		    while (false !== ($file = readdir($handle))) 
				{ 
			    if ($file != "." && $file != "..") 
					{ 
			            $fileList[] = $file; 
			        } 
				}
		    closedir($handle); 
			return $fileList;
			}
		else
			return 0;			
		}
	function getCorrectURLArray($postArr) //отдает массив $post без бустых параметров
		{
		$correctPost = array();
		while (list ($key, $val) = each ($postArr)) 
			{
			if(trim($val)) 
				$correctPost[] = $val;
			}
		return($correctPost);				
		}
	function starFill($realRate)
		{
		if ($realRate)
			{					
			$rateStarFull = floor($realRate);
			if((($realRate-$rateStarFull)>=0.25)&&(($realRate-$rateStarFull)<=0.75))
				{
				$rateStarHalf = 1;
				}
			elseif(($realRate-$rateStarFull)>0.75)
				{
				$rateStarHalf = 0;
				$rateStarFull ++;
				}
			$rateStarEmpty = 10 - ($rateStarFull + $rateStarHalf);					
			for($k=0; $k<$rateStarFull; $k++)
				$ret[] = 1;
			for($k=0; $k<$rateStarHalf; $k++)				
				$ret[] = 2;
			for($k=0; $k<$rateStarEmpty; $k++)
				$ret[] = 3;
			}
		else
			for($k=0; $k<10; $k++)
				$ret[] = 3;
		return $ret;
		}
	function getFileName($str)
		{
		$pointPos = strrpos ($str, '.');
		$pathPos = 	strrpos ($str, '/');
		if($pathPos)
			{
			$ret['path'] = substr($str, 0, $pathPos);
			$startName = $pathPos +1; 
			$lengthName = $pointPos - $pathPos - 1;
			}
		else
			{
			$ret['path'] = '';
			$startName = 0;
			$lengthName = $pointPos;
			}
		if($pointPos)
			{
			$ret['name'] = substr($str, $startName, $lengthName);
			$ret['ext'] = substr($str, $pointPos+1);
			}
		else
			{
			$ret['name'] = $str;
			$ret['ext'] = '';
			}
		return $ret;
		}
	function getStrPrtArr($str, $divArr) // 19_02_2009 ВОЗВРАЩАЕТ обрезанное слово из словосочетания 
		{
		$ret = '';
		for($i=0; $i<count($divArr); $i++) 
			{			
			$find = 0;
			if((!$find))
				{
				$strArr = explode ($divArr[$i], $str);
				if(count($strArr)>1)
					{					
					$ret = ($strArr[0])?$strArr[0]:$strArr[1];
					$find = 1;
					}
				}
			}
		return $ret;		
		}

	function imageComplexUploadFoto($CONST, $tttt, $imgCnt, $sizeArr, $imgType, $urlPathRoot, $watermark) //2013_12_04  загрузка фото с масштабированием - для фотостроек
		{
		$sizeArrCnt = sizeOf($sizeArr);
		$i = 0;
		$fileCnt = 0;
		do
			{
/*		print_r($_SESSION['objectImg'][$imgType]);
		echo '<br>';*/
			$fileCnt ++;
			$tmpFilePath = $CONST['relPathPref'].'/'.$_SESSION['objectImg'][$imgType][$i]['src'];
			if(is_file($tmpFilePath))
				{
				$srcFileArr = $this->getFileName($_SESSION['objectImg'][$imgType][$i]['src']);
				$tmpPath = $this->getStrPrt($_SESSION['objectImg'][$imgType][$i]['src'], $srcFileArr['name'], 0);
				
				$destFileName = ($_SESSION['objectImg'][$imgType][$i]['position'] + 1).'.'.$srcFileArr['ext'];
				$prewFileName = $CONST['relPathPref'].'/'.$tmpPath.$srcFileArr['name'].'_prew.'.$srcFileArr['ext'];
				for($k=0; $k<$sizeArrCnt; $k++)
					{								
					$sizePath = $urlPathRoot.'/'.$sizeArr[$k].'/';
					if(!$k) //Первый размер - файл по умолчанию
						$fileSrc = $urlPathRoot.'/size/'.$destFileName;
					if(!is_dir($CONST['relPathPref'].'/'.$sizePath))
						mkdir($CONST['relPathPref'].'/'.$sizePath);
					if($sizeArr[$k] < 160) //prew size - делаем квадрат
						{
//										rename ($prewFileName, $CONST['relPathPref'].$sizePath.$destFileName);										
						$this->imageResizeSqare($tmpFilePath, $CONST['relPathPref'].$sizePath.$destFileName, $sizeArr[$k], 1);								
						}
					else		
						{
						$this->imageResizeMod($tmpFilePath, $CONST['relPathPref'].$sizePath.$destFileName, $sizeArr[$k], 0);
						$this->imageAddWatermark($CONST['relPathPref'].$sizePath.$destFileName, $watermark, 0);									
						}						
					}
					
/****************************************************************temporary for import************************************************************/
//				unlink($tmpFilePath);
//				unlink($prewFileName);
/****************************************************************temporary for import************************************************************/
				
				
/*				$ret[] = array(	'position' => $_SESSION['objectImg'][$imgType][$i]['position'] + 1, 
								'src' => $fileSrc);*/
				$ret['position'][] =  (intval($_SESSION['objectImg'][$imgType][$i]['position']) + 1);
				$ret['src'][] =  $fileSrc;
				$i ++;
				}
			}		
		while(($i< $imgCnt)&&($fileCnt<50));
		return $ret;
		}
	function imageComplexUpload($CONST, $tttt/*$_SESSION*/, $imgCnt, $sizeArr, $imgType, $urlPathRoot, $doSquare) //2013_11_14 универсальная загрузка картинок с масштабированием - для фотостроек
		{
		$sizeArrCnt = sizeOf($sizeArr);
		$i = 0;
		$fileCnt = 0;
/*		print_r($_SESSION['objectImg']);
		echo '<br>imgType - '.$imgType;
		echo '<br>';*/
		do
			{
/*		print_r($_SESSION['objectImg'][$imgType]);
		echo '<br>';*/
			$isLocal = (strpos($_SESSION['objectImg'][$imgType][$i], 'http://') === false) ? 1 : 0;
			$fileCnt ++;
			$tmpFilePath = ($isLocal) ? $CONST['relPathPref'].'/'.$_SESSION['objectImg'][$imgType][$i] : $_SESSION['objectImg'][$imgType][$i];
/*			echo '<br>';
			echo is_file($tmpFilePath);*/
//			echo '<br>';
			if((is_file($tmpFilePath))||(!$isLocal))
				{
				$srcFileArr = $this->getFileName($_SESSION['objectImg'][$imgType][$i]);
				$tmpPath = $this->getStrPrt($_SESSION['objectImg'][$imgType][$i], $srcFileArr['name'], 0);
				
				$destFileName = $fileCnt.'.'.$srcFileArr['ext'];
				$checkDstFile = $CONST['relPathPref'].$urlPathRoot.'/'.$sizeArr[0].'/'.$destFileName;
				if(!file_exists($checkDstFile)) //проверка на существование файла с таким именем
					{
					if($isLocal) $prewFileName = $CONST['relPathPref'].'/'.$tmpPath.$srcFileArr['name'].'_prew.'.$srcFileArr['ext'];
					for($k=0; $k<$sizeArrCnt; $k++)
						{								
						$sizePath = $urlPathRoot.'/'.$sizeArr[$k].'/';
						if(!$k) //Первый размер - файл по умолчанию
							$fileSrc = $urlPathRoot.'/size/'.$destFileName;
						if(!is_dir($CONST['relPathPref'].'/'.$sizePath))
							mkdir($CONST['relPathPref'].'/'.$sizePath);
						if(($doSquare == 1)&&($sizeArr[$k] < 100)) //prew size - делаем квадрат обрезкой
							{
	//										rename ($prewFileName, $CONST['relPathPref'].$sizePath.$destFileName);										
							$this->imageResizeSqare($tmpFilePath, $CONST['relPathPref'].$sizePath.$destFileName, $sizeArr[$k], 1);								
							}
						elseif(($doSquare == 2)&&($sizeArr[$k] < 90)) //prew size - делаем квадрат заполнением пустотой
							{
	//										rename ($prewFileName, $CONST['relPathPref'].$sizePath.$destFileName);										
							$this->imageResizeSqareIcon($CONST['relPathPref'].'/src/design/main/blank_'.$sizeArr[$k].'.png', $tmpFilePath, $CONST['relPathPref'].$sizePath.$fileCnt.'.png', $sizeArr[$k]);								
							}
						else									
							$this->imageResizeMod($tmpFilePath, $CONST['relPathPref'].$sizePath.$destFileName, $sizeArr[$k], 0);
							
						}
					
					if($isLocal) 
						{
/****************************************************************temporary for import************************************************************/
//						unlink($tmpFilePath);
//						unlink($prewFileName);
/****************************************************************temporary for import************************************************************/
						}
					unlink($tmpFilePath);
//					echo '</br> del '.$CONST['relPathPref'].'/'.$tmpPath.$srcFileArr['name'].'_prew.'.$srcFileArr['ext'];
					unlink($CONST['relPathPref'].'/'.$tmpPath.$srcFileArr['name'].'_prew.'.$srcFileArr['ext']);
					$ret[] = $fileSrc;
					$i ++;
					}
				}
			}		
		while(($i< $imgCnt)&&($fileCnt<50));
		return $ret;
		}
	function imageAddWatermarkDouble($img, $wmL, $wmR, $shouldIReturn) //2013_04_08 вставка двух водяных знаков - слева и справа
		{
			$sizeImg =	GetImageSize($img);
			$sizeWML = 	GetImageSize($wmL);
			$sizeWMR = 	GetImageSize($wmR);
			$wmLW = $sizeWML[0];
			$wmLH = $sizeWML[1];
			$wmRW = $sizeWMR[0];
			$wmRH = $sizeWMR[1];
			$cxL=0;
			$cyL=$sizeImg[1]-$wmLH;			
			$cxR=$sizeImg[0] - $wmRW;
			$cyR=$sizeImg[1]-$wmRH;			
			$image_wmL 	= imagecreatefrompng($wmL);
			$image_wmR 	= imagecreatefrompng($wmR);
//			$image_foto = @imagecreatetruecolor($new_width, $new_height);
			if ($sizeImg[2]==2)
				{
					$image_foto = imagecreatefromjpeg($img);
				}
			elseif ($sizeImg[2]==3)
				{
					$image_foto = imagecreatefrompng($img);
				}
			elseif ($sizeImg[2]==1)
				{
					$image_foto = imagecreatefromgif($img);
				}
			imagecopyresampled($image_foto, $image_wmL, $cxL, $cyL, 0, 0, $wmLW, $wmLH, $wmLW, $wmLH);
			imagecopyresampled($image_foto, $image_wmR, $cxR, $cyR, 0, 0, $wmRW, $wmRH, $wmRW, $wmRH);
		if($shouldIReturn)
			return $image_foto;
		else
			if ($sizeImg[2]==2)
			  {
			   imagejpeg($image_foto, $img, 100);
			  }
			  else if ($sizeImg[2]==1)
			  {
			   imagegif($image_foto, $img);
			  }
			  else if ($sizeImg[2]==3)
			  {
			   imagepng($image_foto, $img);
			  }					
		}	
	function imageAddWatermark($img, $wm, $shouldIReturn) //2013_12_04  добавление водяного знака - для фотостроек
		{
			$sizeImg =	GetImageSize($img);
			$sizeWM = 	GetImageSize($wm);
			$wmW = $sizeWM[0];
			$wmH = $sizeWM[1];
			$cx=$sizeImg[0] - $wmW - 30;
			$cy= 30;			
			$image_wm 	= imagecreatefrompng($wm);
//			$image_foto = @imagecreatetruecolor($new_width, $new_height);
			if ($sizeImg[2]==2)
				{
					$image_foto = imagecreatefromjpeg($img);
				}
			elseif ($sizeImg[2]==3)
				{
					$image_foto = imagecreatefrompng($img);
				}
			elseif ($sizeImg[2]==1)
				{
					$image_foto = imagecreatefromgif($img);
				}
			imagecopyresampled ($image_foto, $image_wm, $cx, $cy, 0, 0, $wmW, $wmH, $wmW, $wmH);
		if($shouldIReturn)
			return $image_foto;
		else
			if ($sizeImg[2]==2)
			  {
			   imagejpeg($image_foto, $img, 70);
			  }
			  else if ($sizeImg[2]==1)
			  {
			   imagegif($image_foto, $img);
			  }
			  else if ($sizeImg[2]==3)
			  {
			   imagepng($image_foto, $img);
			  }					
		}	
	function imageResizeStream($sourse, $original, $width, $height) //2012_02_09 Умное масштабирование картинок "в поток" JPG
		{
		if(!$original)
			{
			$size = GetImageSize($sourse);
			$height = (!$height)?$size[1]:$height;
			$width = (!$width)?$size[0]:$width;
			$kx = $width/$size[0];
			$ky = $height/$size[1];
			$k = ($kx>$ky)?$ky:$kx;		
			$k = ($k<1)?$k:1;
			$new_width =  $size[0] * $k;
			$new_height = $size[1] * $k;
			$image_p = @imagecreatetruecolor($new_width, $new_height);
			if ($size[2]==2)
				{
					$image_cr = imagecreatefromjpeg($sourse);
				}
			elseif ($size[2]==3)
				{
					$image_cr = imagecreatefrompng($sourse);
				}
			elseif ($size[2]==1)
				{
					$image_cr = imagecreatefromgif($sourse);
				}
			imagecopyresampled($image_p, $image_cr, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);
//			imagecopyresized($image_p, $image_cr, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);
			$ret = $image_p;
			}
		else
			{
			$ret = imagecreatefromjpeg($sourse);			
			}
		return $ret;
		}
	function imageResizeSqareIcon($blank, $src, $img, $newSize) //2013_05_13 Масштабирование картинок для иконок - получаются квадратные без обрезки на прозрачном фоне PNG
		{
		$oldSize = GetImageSize($src);
		$imgW = $oldSize[0];
		$imgH = $oldSize[1];
		$sSize = ($imgW >= $imgH) ? $imgW : $imgH;
		$k = $newSize/$sSize;
		$iWidth =  $imgW * $k;
		$iHeight = $imgH * $k;
		$srcX = $newSize/2 - $iWidth/2;		
		$srcY = $newSize/2 - $iHeight/2;				
		$imageDestS1 = @imagecreatetruecolor($iWidth, $iHeight); 
		$imageDest = imagecreatefrompng($blank);
		imagesavealpha ( $imageDest, true );
		imagealphablending( $imageDest, false );
		if ($oldSize[2]==2)
			{
				$imageSrc = imagecreatefromjpeg($src);
			}
		elseif ($oldSize[2]==3)
			{
				$imageSrc = imagecreatefrompng($src);
			}
		elseif ($oldSize[2]==1)
			{
				$imageSrc = imagecreatefromgif($src);
			}
		imagecopyresampled($imageDestS1, $imageSrc, 0, 0, 0, 0, $iWidth-1, $iHeight-1, $imgW, $imgH);					/*масштабирование*/
		$redLight = imagecolorallocate($imageDestS1, 255, 29, 43);
		$redDark = imagecolorallocate($imageDestS1, 140, 29, 43);
		imageline($imageDestS1, 0, 0, $iWidth-1, 0, $redLight);
		imageline($imageDestS1, 0, 0, 0, $iHeight-1, $redLight);
		imageline($imageDestS1, $iWidth-1, $iHeight-1, $iWidth-1, 0, $redDark);
		imageline($imageDestS1, 0, $iHeight-1, $iWidth-1, $iHeight-1, $redDark);
		imagecopyresampled ($imageDest, $imageDestS1, $srcX, $srcY, 0, 0, $iWidth, $iHeight, $iWidth, $iHeight); //наложение на прозрачный фон		
		imagepng($imageDest, $img);
		}		
	function imageResizeSqare($sourse, $new_image, $sizeDest, $fromCenter) //2013_05_13 Масштабирование картинок для превьюшек - получаются квадратные с обрезкой от центра или от края
		{
		$size = GetImageSize($sourse);
		$height = 	$size[1];
		$width = 	$size[0];
		$sqareSize = ($width >= $height) ? $height : $width;
		if($fromCenter)
			if($width >= $height)
				{ 				$srcX = $width/2 - $sqareSize/2;				$srcY = 0;				}
			else
				{				$srcY = $height/2 - $sqareSize/2;				$srcX = 0;				}			
		else
			if($width >= $height)
				{ 				$srcX = $width/2 - $sqareSize/2;				$srcY = 0;				}
			else
				{				$srcY = $height/2 - $sqareSize/2;				$srcX = 0;				}						
		$new_width = $new_height = $sizeDest;

		$imageDest = @imagecreatetruecolor($new_width, $new_height);
		if ($size[2]==2)
			{
				$imageSrc = imagecreatefromjpeg($sourse);
			}
		elseif ($size[2]==3)
			{
				$imageSrc = imagecreatefrompng($sourse);
			}
		elseif ($size[2]==1)
		  {
				$imageSrc = imagecreatefromgif($sourse);
		  }
//	echo 'X1 - '.$size[0].'; Y1 - '.$size[1].'; X2 - '.$new_width.'; Y2 - '.$new_height;
		imagecopyresampled($imageDest, $imageSrc, 0, 0, $srcX, $srcY, $new_width, $new_height, $sqareSize, $sqareSize);
			if ($size[2]==2)
			  {
			   imagejpeg($imageDest, $new_image, 70);
			  }
			  else if ($size[2]==1)
			  {
			   imagegif($imageDest, $new_image);
			  }
			  else if ($size[2]==3)
			  {
			   imagepng($imageDest, $new_image);
			  }		
		}		
	function imageResizeMod($sourse, $new_image, $width, $height) //2010_07_07 Умное масштабирование картинок
		{
		$size = GetImageSize($sourse);
		$height = (!$height)?$size[1]:$height;
		$width = (!$width)?$size[0]:$width;
		$kx = $width/$size[0];
		$ky = $height/$size[1];
		$k = ($kx>$ky)?$ky:$kx;		
		$k = ($k<1)?$k:1;
		$new_width =  $size[0] * $k;
		$new_height = $size[1] * $k;
		$image_p = @imagecreatetruecolor($new_width, $new_height);
		if ($size[2]==2)
			{
				$image_cr = imagecreatefromjpeg($sourse);
			}
		elseif ($size[2]==3)
			{
				$image_cr = imagecreatefrompng($sourse);
			}
		elseif ($size[2]==1)
		  {
				$image_cr = imagecreatefromgif($sourse);
		  }
//	echo 'X1 - '.$size[0].'; Y1 - '.$size[1].'; X2 - '.$new_width.'; Y2 - '.$new_height;
		imagecopyresampled($image_p, $image_cr, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);
			if ($size[2]==2)
			  {
			   imagejpeg($image_p, $new_image, 70);
			  }
			  else if ($size[2]==1)
			  {
			   imagegif($image_p, $new_image);
			  }
			  else if ($size[2]==3)
			  {
			   imagepng($image_p, $new_image);
			  }		
		}	
	function imageResize($sourse, $new_image, $width, $height)
		{
	    $size = GetImageSize($sourse);
	    $new_height = $height;
	    $new_width = $width;
	    if ($size[0] < $size[1])
	    $new_width=($size[0]/$size[1])*$height;
	    else
	    $new_height=($size[1]/$size[0])*$width;
	    $new_width=($new_width > $width)?$width:$new_width;
	    $new_height=($new_height > $height)?$height:$new_height;
	    $image_p = @imagecreatetruecolor($new_width, $new_height);
	    if ($size[2]==2)
		    {
		        $image_cr = imagecreatefromjpeg($sourse);
		    }
	    elseif ($size[2]==3)
			{
	       $image_cr = imagecreatefrompng($sourse);
			}
	    elseif ($size[2]==1)
	      {
	       $image_cr = imagecreatefromgif($sourse);
	      }
	    imagecopyresampled($image_p, $image_cr, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);
			if ($size[2]==2)
		      {
		       imagejpeg($image_p, $new_image, 75);
		      }
		      else if ($size[2]==1)
		      {
		       imagegif($image_p, $new_image);
		      }
		      else if ($size[2]==3)
		      {
		       imagepng($image_p, $new_image);
		      }
		}	
	function getCorrectDeclensionRu($num, $words) // Возвращает правильную словоформу для заданного числа
		{
//		$words = array('секунда', 'секунды', 'секунд');
		if($num>1000)
			$num -= 1000;
		elseif ($num>100)
			$num -= 100;		
			
		if($num>10)
			{
			$tenSec = (round($num/10 + 0.5) == round($num/10)) ? round($num/10)-1 : round($num/10);
			if((($num>=10)&&($num<20))||((($num - ($tenSec*10)) >=5 )||($num - ($tenSec*10) ==0 )))
				$ret = $words[2];
			elseif(($num - ($tenSec*10))>1)
				$ret = $words[1];
			else
				$ret = $words[0];
			}
		else
			{
			if(($num >=5 )||($num ==0 ))
				$ret = $words[2];
			elseif($num>1)
				$ret = $words[1];
			else
				$ret = $words[0];
			}
		return $ret;
		}
		
/*	function getRelativeValue($maxTrue, $maxRel, $valueTrue)  склонение
		{
		for($i=0; $i<count($listValues); $i++ )
			{
			$value = ($listValues[$i]*$maxRel)/$maxTrue;
			$ret[$i] = ($value>=0.5)?round($value):1;
			}
		return $ret;
		}	*/
	function getRelativeValues($maxTrue, $maxRel, $listValues)
		{
		for($i=0; $i<count($listValues); $i++ )
			{
			$value = ($listValues[$i]*$maxRel)/$maxTrue;
			$ret[$i] = ($value>=0.5)?round($value):1;
			}
		return $ret;
		}

	function first_letter_up($string, $coding='utf-8') 
		{
		if (function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string)) 
			{
//			echo '!
//';			echo mb_strtolower($string, $coding);
//			echo ' - '.mb_strtoupper(mb_strtolower($string, $coding), $coding);
			$Upper = mb_strtoupper($string,  $coding);
			$lower =  mb_strtolower($string, $coding);
			$string = mb_substr($Upper, 0, 1, $coding).mb_substr($lower, 1, mb_strlen($lower, $coding), $coding);
//			preg_match('#(.)#us', mb_strtoupper(mb_strtolower($string, $coding), $coding), $matches);
//			$string = $matches[1] . mb_substr($string, 1, mb_strlen($string, $coding), $coding);
			
			}
		else 
			{
			$string = ucfirst($string);
			}
		return $string;
		}		
	function strtoupper_ru($text) 
		{ 
	    $alfavitlover = array('q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m', 'ё','й','ц','у','к','е','н','г', 'ш','щ','з','х','ъ','ф','ы','в', 'а','п','р','о','л','д','ж','э', 'я','ч','с','м','и','т','ь','б','ю'); 
	    $alfavitupper = array('Q','W','E','R','T','Y','U','I','O','P','A','S','D','F','G','H','J','K','L','Z','X','C','V','B','N','M', 'Ё','Й','Ц','У','К','Е','Н','Г', 'Ш','Щ','З','Х','Ъ','Ф','Ы','В', 'А','П','Р','О','Л','Д','Ж','Э', 'Я','Ч','С','М','И','Т','Ь','Б','Ю'); 
	    return str_replace($alfavitlover, $alfavitupper, strtolower($text)); 
		}	
		
	function strtolower_ru($text) 
		{ 
	    $alfavitlover = array('ё','й','ц','у','к','е','н','г', 'ш','щ','з','х','ъ','ф','ы','в', 'а','п','р','о','л','д','ж','э', 'я','ч','с','м','и','т','ь','б','ю'); 
	    $alfavitupper = array('Ё','Й','Ц','У','К','Е','Н','Г', 'Ш','Щ','З','Х','Ъ','Ф','Ы','В', 'А','П','Р','О','Л','Д','Ж','Э', 'Я','Ч','С','М','И','Т','Ь','Б','Ю'); 
	    return str_replace($alfavitupper, $alfavitlover, strtolower($text)); 
		}	
	function getCuttedStringSmart ($str, $searchStr, $strCutLength) //обрезает фразу по целым словам посреди искомого куска
		{
		$str = strip_tags($str);
		$exluded = array('nbsp', '!','@','#','$','%','^','&','*','(',')','-','_','=','`','~',':',';','\'','"','\\','/','|','?','.',',','<','>', '№','%', '«', '»'); 
		$str = str_replace($exluded, '', mb_strtolower(trim($str), 'UTF-8'));
		if(strlen($str)> $strCutLength+5)
			{
			$firstPosition = stripos($str, $searchStr);
			$start = ( ($firstPosition + strlen($searchStr)) > $strCutLength)?(($firstPosition)-$strCutLength/2):0;						
//			$start = ($firstPosition>$strCutLength)?$firstPosition-$strCutLength:0;
			$length = $strCutLength*2;
			$postfiks = ($length<strlen($str))?' ... ':''; 
			$str = substr($str, $start, $length);
			if((stripos($str, ' ')!==0)&&($start))
				$str = substr($str, stripos($str, ' '));
			if ($start) $str = '... '.$str;
			$str .=$postfiks;	
			}
		return $str;
		}
	function getSmartCutedHTML($str, $length) //обрезает форматированный текст по целым словам   2014_12_19
		{
		if(strlen(strip_tags($str)) > $length)
			{
			$startStr = substr($str, 0, $length);
			$searchedStr = substr($str, $length);
			$del1 = '
';
			$del2 = '<br />';
			$del3 = '</div>';
			$del4 = '</p>';
			if(strstr($searchedStr, $del1))
				{
				$pos = strpos ($searchedStr, $del1);
				$strHeader = substr($searchedStr, 0, $pos  );
				}
			elseif(strstr($searchedStr, $del2))
				{
				$pos = strpos ($searchedStr, $del2);
				$strHeader = '<p>'.strip_tags(substr($searchedStr, 0, $pos  )).'</p>';
//					$newsList[$i]['news_header'] = '<p class="article_text">'.strip_tags(substr($newsList[$i]['news_body'], 0, strpos ($newsList[$i]['news_body'], $del2) )).'</p>';
				}
			elseif(strstr($searchedStr, $del3))
				{
				$pos = strpos ($searchedStr, $del3);
				$strHeader = '<p>'.strip_tags(substr($searchedStr, 0, $pos  )).'</p>';
				}
			elseif(strstr($searchedStr, $del4))
				{
				$pos = strpos ($searchedStr, $del4);
				$strHeader = '<p>'.strip_tags(substr($searchedStr, 0, $pos  )).'</p>';
				}
//				echo $startStr.$obj['infoHeader'];
			$ret['prev'] = $startStr.$strHeader;
			$ret['more'] = substr($str, $pos +  $length );
			}
		else
			$ret['prev'] = '';
		return $ret;
		}	
	function getSmartCutedString($str, $length) //обрезает фразу по целым словам  (пока нет)
		{
		$str = strip_tags($str);
		$postfiks = ($length<strlen($str))?' ... ':''; 
		if(strlen($str)> $length)
			{
			$start = 0;
			$str = substr($str, $start, $length);
			$str = substr($str, $start, strrpos ( $str, ' '));
			}
		$str .=$postfiks;	
		return $str;
		}	
	function showFoundString($str, $searchStr, $style, $strCutLength) //конвертирует найденную строку для вывода - обрезает фразу по целым словам 
		{
		$str = strip_tags($str);
		if(strlen($str)> $strCutLength+5)
			{
			$firstPosition = stripos($str, strtolower($searchStr));
			$start = ($firstPosition>$strCutLength)?$firstPosition-$strCutLength:0;
			$length = $strCutLength*2;
			$postfiks = ($length<strlen($str))?'... ':''; 
			$str = substr($str, $start, $length);
			if((stripos($str, ' ')!==0)&&($start))
				$str = substr($str, stripos($str, ' '));
			if ($start) $str = '... '.$str;
			$str .=$postfiks;	
			}
		$str = str_replace(strtolower($searchStr),'<span style="'.$style.'">'.$searchStr.'</span>', $str);
		return $str;
		}
	
	function GetListDataKvartal($curDate, $start, $end, $del)//2013_11_15 возвращает поквартальный список дат на указанный период 
		{
		$kArr = 		array("I","II","III","IV");
		$kArrMonth = 	array("I","I","I","II","II","II","III","III","III","IV","IV","IV");
		$YearCur = 		date("Y", $curDate);
		$kvartCur = 	$kArrMonth[date("m", $curDate)-1];
		$YearStart = 	$YearCur - $start;
		$YearEnd = 		$YearCur + $end;
		$retKv[0] = array('label' => '--Не определено--', 'value' => '');
		for($i = $YearStart; $i <= $YearEnd; $i++ )
			{
			for($k = 0; $k < sizeof($kArr); $k++ )
				{
				$retKv[] =  array('label' => ($i).$del.$kArr[$k], 'value' => ($i).$del.$kArr[$k]); 
				}
			}
		$ret['cur'] =  $YearCur.$del.$kvartCur;
		$ret['list'] = $retKv;		
		return $ret;
		}
		
	function build_HTML_To_BBcodes()
	{
	   $ourBBCodes = array(
	 /*  		array('[b]','<b>'),
	   		array('[/b]','</b>'),
	   		array('[code]','<pre>'),
	   		array('[/code]','</pre>'),
	   		array('[quote]','<blockquote>'),
	   		array('[/quote]','</blockquote>'),
	   		array('[i]','<i>'),
	   		array('[/i]','</i>'),
	   		array('[u]','<u>'),
	   		array('[/u]','</u>'),
	   		array('[list]','<ul>'),
	   		array('[/list]','</ul>'),
	   		array('[list=]','<ol>'),
	   		array('[/list=]','</ol>'),
	                                     */
//	      array('/\[hr\]/si','<hr>'),
	      array('/\<hr\>/si','[hr]'),
	      array('/\<b\>(.*?)\<\/b\>/si','[b]\\1[/b]'),
	      array('/\<strong\>(.*?)\<\/strong\>/si','[b]\\1[/b]'),
//	      array('/\[b\](.*?)\[\/b\]/si','<b>\\1</b>'),
	      array('/\<h1\>(.*?)\<\/h1\>/si', '[h1]\\1[/h1]'),
//	      array('/\[h1\](.*?)\[\/h1\]/si', '<h1>\\1</h1>'),
	      array('/\<h2\>(.*?)\<\/h2\>/si', '[h2]\\1[/h2]'),
	      array('/\<h3\>(.*?)\<\/h3\>/si', '[h3]\\1[/h3]'),
	      array('/\<h4\>(.*?)\<\/h4\>/si', '[h4]\\1[/h4]'),
//	      array('/\[i\](.*?)\[\/i\]/si','<i>\\1</i>'),
	      array('/\<i\>(.*?)\<\/i\>/si','[i]\\1[/i]'),
			//array('/\[u\](.*?)\[\/u\]/si', '<u>\\1</u>'),
//	      array('/\[s\](.*?)\[\/s\]/si', '<strike>\\1</strike>'),
	      array('/\<strike\>(.*?)\<\/strike\>/si', '\[s\]\\1\[/s\]'),
/*	      array('/\[ot\](.*?)\[\/ot\]/si', '<font color=gray>\\1</font>'),
	      array('/\[tt\](.*?)\[\/tt\]/si', '<tt>\\1</tt>'),
	      array('/\[br\]/si', '<br>'),
	      array('/\[img\](.*?)\[\/img\]/si', '<img src="\1" border="0">'),
	      array('/\[smile\](.*?)\[\/smile\]/si', '<img src="images/smiles/\1" border="0">'),*/
	      array('/\<img(.*?)\>/si', '[upimg] \1 [/upimg]'),
//	      array('/\[upimg\](.*?)\[\/upimg\]/si', '<img \1>'),
//	      array('/\[img\s*align=["]*([a-z]+).*\](.*?)\[\/img\]/si', '<img src="\2" align="\1" border="0">'),
//	      array('#\[url\]([a-z]+?://){1}(.*?)\[/url\]#si', '<a href="\1\2" target="_blank">\1\2</a>'),
//	      array('#\[url\](.*?)\[/url\]#si', '<a href="http://\1" target="_blank">\1</a>'),
	      array('#\<a\s*href\=\"http\:\/\/(.*?)\"\s*target=\"\_blank\"\>(.*?)\<\/a\>#si', '[url = \1]\2[/url]'),
	      array('#\<a\s*href\=\"http\:\/\/(.*?)\"\s*target=\"\_blank\"\>(.*?)\<\/a\>#si', '[url]\1[/url]'),
/*	      array('#\[url=([a-z]+?://){1}(.*?)\](.*?)\[/url\]#si', '<a href="\1\2" target="_blank">\3</a>'),
	      array('#\[url=(.*?)\](.*?)\[/url\]#si', '<a href="http://\1" target="_blank">\2</a>'),*/
	      array('#\<a\s*href\=\"mailto\:(.*?)\>(.*?)\<\/a\>#si', '<a href="mailto:\1">\1</a>'),
//	      array('#\[email\](.*?)\[/email\]#si', '[email]\1[/email]'),
	      array('#\<font\s*color\=\"(.*?)\"\>(.*?)\<\/font\>#si', '[color=\1]\2[/color]'),
//	      array('#\[color=(.*?)\](.*?)\[/color\]#si', '<font color="\1">\2</font>'),
//	      array('#\[font=(.*?)\](.*?)\[/font\]#si', '<font face="\1">\2</font>'),
	      array('#\<font\s*face\=\"(.*?)\"\>(.*?)\<\/font\>#si', '[font=\1]\2[/font]'),
	      array('#\<font\s*size\=\"\+(.*?)\"\>(.*?)\<\/font\>#si', '[size=\1]\2[/size]'),
//	      array('#\[size=([0-9]*?)\](.*?)\[/size\]#si', '<font size="+\1">\2</font>'),
	      array('/\[list\](.*?)\[\/list\]/si', '<ul>\1</ul>'),
	      array('/\[list=\](.*?)\[\/list\]/si', '<ol>\1</ol>'),
	      array('/\[list=([Aa])\](.*?)\[\/list\]/si', '<ul type="\1">\2</ul>'),
	      array('/\[\*\](.*?)\[\/\*\]/si', '<li>\1</li>'),
	      array('/\[\*\]/si', '<li>'),
	      array('/\[code\](.*?)\[\/code\]/si', '<pre>\1</pre>'),
	      array('/\[quote\](.*?)\[\/quote\]/si', '<blockquote>\1</blockquote>'),
	      array('/\[quote\]/si', '<blockquote>'),
	      array('/\[\/quote\]/si', '</blockquote>'),
	      array('/\[small\](.*?)\[\/small\]/si', '<small>\1</small>'),
	      array('/\[big\](.*?)\[\/big\]/si', '<big>\1</big>'),
	//      array('/\[src\](.*?)\[\/src\]/si','\1'),
	      /* -- non standard -- */
	      array('/\[box\](.*?)\[\/box\]/si', '<table border="1" cellpadding="4" cellspacing="0"><tr><td>\1</td></tr></table>'),
	      array('/\[box\s*color=([a-z]+)\](.*?)\[\/box\]/si', '<table bgcolor="\1" border="1" cellpadding="4" cellspacing="0"><tr><td>\2</td></tr></table>')
	   );
	   return $ourBBCodes;
		}	
	function buildbbcodes()
	{
	   $ourBBCodes = array(
	 /*  		array('[b]','<b>'),
	   		array('[/b]','</b>'),
	   		array('[code]','<pre>'),
	   		array('[/code]','</pre>'),
	   		array('[quote]','<blockquote>'),
	   		array('[/quote]','</blockquote>'),
	   		array('[i]','<i>'),
	   		array('[/i]','</i>'),
	   		array('[u]','<u>'),
	   		array('[/u]','</u>'),
	   		array('[list]','<ul>'),
	   		array('[/list]','</ul>'),
	   		array('[list=]','<ol>'),
	   		array('[/list=]','</ol>'),
	                                     */
	      array('/\[hr\]/si','<hr>'),
	      array('/\[b\](.*?)\[\/b\]/si','<b>\\1</b>'),
	      array('/\[h1\](.*?)\[\/h1\]/si', '<h1>\\1</h1>'),
	      array('/\[h2\](.*?)\[\/h2\]/si', '<h2>\\1</h2>'),
	      array('/\[h3\](.*?)\[\/h3\]/si', '<h3>\\1</h3>'),
	      array('/\[h4\](.*?)\[\/h4\]/si', '<h4>\\1</h4>'),
	      array('/\[i\](.*?)\[\/i\]/si','<i>\\1</i>'),
	      array('/\[u\](.*?)\[\/u\]/si', '<u>\\1</u>'),
	      array('/\[s\](.*?)\[\/s\]/si', '<strike>\\1</strike>'),
	      array('/\[ot\](.*?)\[\/ot\]/si', '<font color=gray>\\1</font>'),
	      array('/\[tt\](.*?)\[\/tt\]/si', '<tt>\\1</tt>'),
	      array('/\[br\]/si', '<br>'),
	      array('/\[img\](.*?)\[\/img\]/si', '<img src="\1" border="0">'),
	      array('/\[smile\](.*?)\[\/smile\]/si', '<img src="images/smiles/\1" border="0">'),
	      array('/\[upimg\](.*?)\[\/upimg\]/si', '<img \1>'),
	      array('/\[img\s*align=["]*([a-z]+).*\](.*?)\[\/img\]/si', '<img src="\2" align="\1" border="0">'),
	      array('#\[url\]([a-z]+?://){1}(.*?)\[/url\]#si', '<a href="\1\2" target="_blank">\1\2</a>'),
	      array('#\[url\](.*?)\[/url\]#si', '<a href="http://\1" target="_blank">\1</a>'),
	      array('#\[url=([a-z]+?://){1}(.*?)\](.*?)\[/url\]#si', '<a href="\1\2" target="_blank">\3</a>'),
	      array('#\[url=(.*?)\](.*?)\[/url\]#si', '<a href="http://\1" target="_blank">\2</a>'),
	      array('#\[email\](.*?)\[/email\]#si', '<a href="mailto:\1">\1</a>'),
	      array('#\[color=(.*?)\](.*?)\[/color\]#si', '<font color="\1">\2</font>'),
	      array('#\[font=(.*?)\](.*?)\[/font\]#si', '<font face="\1">\2</font>'),
	      array('#\[size=([0-9]*?)\](.*?)\[/size\]#si', '<font size="+\1">\2</font>'),
	      array('/\[list\](.*?)\[\/list\]/si', '<ul>\1</ul>'),
	      array('/\[list=\](.*?)\[\/list\]/si', '<ol>\1</ol>'),
	      array('/\[list=([Aa])\](.*?)\[\/list\]/si', '<ul type="\1">\2</ul>'),
	      array('/\[\*\](.*?)\[\/\*\]/si', '<li>\1</li>'),
	      array('/\[\*\]/si', '<li>'),
	      array('/\[code\](.*?)\[\/code\]/si', '<pre>\1</pre>'),
	      array('/\[quote\](.*?)\[\/quote\]/si', '<blockquote>\1</blockquote>'),
	      array('/\[quote\]/si', '<blockquote>'),
	      array('/\[\/quote\]/si', '</blockquote>'),
	      array('/\[small\](.*?)\[\/small\]/si', '<small>\1</small>'),
	      array('/\[big\](.*?)\[\/big\]/si', '<big>\1</big>'),
	//      array('/\[src\](.*?)\[\/src\]/si','\1'),
	      /* -- non standard -- */
	      array('/\[box\](.*?)\[\/box\]/si', '<table border="1" cellpadding="4" cellspacing="0"><tr><td>\1</td></tr></table>'),
	      array('/\[box\s*color=([a-z]+)\](.*?)\[\/box\]/si', '<table bgcolor="\1" border="1" cellpadding="4" cellspacing="0"><tr><td>\2</td></tr></table>')
	   );
	   return $ourBBCodes;
		}	
	function convertArrayIntoSelectForFilterNew($arr, $value, $caption, $defEl)//то же что и convertArrayIntoSelectForFilter только значение по умолчанию присваивается иначе (10_09_2008) 
		{
		if($arr)
			{		
			$retArr = array();
			$cnt = 0;
			$retArr['value'][$cnt] = 0;
			$retArr['text'][$cnt] = ' ';
			for($i=0; $i<count($arr); $i++)
				{				
				$retArr['value'][$i+1] = $arr[$i][$value];
				$retArr['text'][$i+1] = $arr[$i][$caption];
				$cnt = $i;
				}
			if($defEl>0)
				$retArr['default'] = $defEl;
			elseif(!$defEl)
				$retArr['default'] = 0;			
			elseif($defEl<0)
				$retArr['default'] = $arr[$cnt][$value];			
			return $retArr;
			}
		else
			{
			return $arr;
			}
		}	
	function convertArrayIntoSelectForFilter($arr, $value, $caption, $defEl)//конвертирует "полный" массив в  специальный массив для select (13_05_2008) - для фильтра с добавочным пустым элементом (22_05_2008)	
		{
		if($arr)
			{		
			$retArr = array();
			$cnt = 0;
			$retArr['value'][$cnt] = 0;
			$retArr['text'][$cnt] = ' ';
			for($i=0; $i<count($arr); $i++)
				{				
				$retArr['value'][$i+1] = $arr[$i][$value];
				$retArr['text'][$i+1] = $arr[$i][$caption];
				$cnt = $i;
				}
			if($defEl>0)
				$retArr['default'] = $arr[$defEl][$value];
			elseif(!$defEl)
				$retArr['default'] = 0;			
			elseif($defEl<0)
				$retArr['default'] = $arr[$cnt][$value];			
			return $retArr;
			}
		else
			{
			return $arr;
			}
		}
	function convertArrayIntoSelect($arr, $value, $caption, $defEl, $newElement)//конвертирует "полный" массив в  специальный массив для select (13_05_2008)	
		{
		if($arr)
			{
//			echo $value;
			$retArr = array();
			$cnt = 0;
			for($i=0; $i<count($arr); $i++)
				{				
				$retArr['value'][$i] = $arr[$i][$value];
				$retArr['text'][$i] = $arr[$i][$caption];
				if(is_array($defEl))
					{
					$tmpDef = 0;
					for($k=0; $k<count($defEl); $k++)
						{
						if($defEl[$k] == $arr[$i][$value])
							$tmpDef ++; 
						}
					$retArr['default'][$i] = ($tmpDef)?1:0;
					}				
				$cnt = $i;
				}
			if($newElement)	
				{
				$retArr['value'][] = 'new';
				$retArr['text'][] = '----Создать новую запись----';				
				}			
			if(!is_array($defEl))
				{
				if($defEl>0)
					$retArr['default'] = $defEl; //$arr[$defEl][$value];
				elseif($defEl==0)
					$retArr['default'] = 0; //$arr[$defEl][$value];
				elseif($defEl<0)
					$retArr['default'] = $arr[$cnt][$value];			
				}
			}
		elseif($newElement)	
			{
			$retArr = array();
			$retArr['value'][] = '';
			$retArr['text'][] = '';				
			$retArr['value'][] = 'new';
			$retArr['text'][] = 'Создать новую запись';				
			}
		else
			{
			$retArr = 0;
			}
		return $retArr;
		}
	function is_html($str)//проверяет - является ли текст html - тупо ищет тэги (26_10_2007)	
		{
		$tags = array(0 => "<div>", 1 => "<table>", 2 => "<p>", 3 => "<strong>");
		$find = 0;
		for($i=0; $i<count($tags); $i++)
			{
			$res = stristr($str, $tags[$i]);
			if($res)
				$find++;
			}
		return $find;
		}
	function GetRusMonth($num)//возвращает месяц (11_01_2009)	
		{
		$months = 	array("январь","февраль","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь");
		
		return $months[$num];
		}
	function GetRusDataStr($date, $showYear = false, $convertFromStr = true, $returnTime = false)//возвращает дату в строке (24_05_2017)	
		{
		$modDateArr =  $this->GetRusData(($convertFromStr)?strtotime($date):$date);
		$modDate = $modDateArr['date'].' '.$modDateArr['month'];
		if(!$showYear)
			$modDate .= (date("Y",time()) != $modDateArr['year'])?' '.$modDateArr['year'].'':'';
		else 
			$modDate .= ' '.$modDateArr['year'];
		if($returnTime)
			$modDate .= ', '.$modDateArr['hours'].':'.$modDateArr['minutes'];
		
//		return print_r($modDateArr, true);
		return $modDate;
		}
	function GetRusData($curDate)//возвращает дату (17_10_2007)	
		{
		
		$months = 	array("января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");
		$weekdays = array("воскресенье","понедельник","вторник","среда","четверг","пятница","суббота");
		$kvartalRome = 	array(1 => 'I', 2 => 'I', 3 => 'I', 4 => 'II',  5 => 'II',  6 => 'II', 7 => 'III',  8 => 'III',  9 => 'III', 10 => 'IV', 11 => 'IV', 12 => 'IV');
		$kvartalGrece = array(1 => '1', 2 => '1', 3 => '1', 4 => '2',  5 => '2',  6 => '2', 7 => '3',  8 => '3',  9 => '3', 10 => '4', 11 => '4', 12 => '4');
		$monthNum = intval(date("m", $curDate));
		$ret['hours'] = date("H", $curDate);
		$ret['minutes'] = date("i", $curDate);
		$ret['date'] = date("j", $curDate);
//		$ret['monthNum'] = $monthNum;
		$ret['month'] = $months[$monthNum-1];
		$ret['kvartalRome'] = $kvartalRome[$monthNum];
		$ret['kvartalGrece'] = $kvartalGrece[$monthNum];
		$ret['weekday'] = $weekdays[date("w", $curDate)];
		$ret['year'] = date("Y", $curDate);
		return $ret;
		}
    function GetStartedUrlLvl($urlArray, $activeLnk, $uri)//12_08_2007 - Поиск первого активного урла 
    	{		
//		echo $activeLnk;
//		print_r($urlArray);
		$maxItem = 0;
		for($i=0; $i<count($urlArray); $i++)
			{			
			$weight[] = $this->UriFirstCompare ($urlArray[$i], $uri);
			}
		for($i=0; $i<count($weight); $i++)
			if ($weight[$i] > $weight[$maxItem])
				$maxItem = $i;
		$actLinkCnt = $this->GetTxtElNumber($activeLnk, '/');
		$nearLinkCnt = $this->GetTxtElNumber($urlArray[$maxItem], '/');
		$ret['lvl'] = ($actLinkCnt <= $nearLinkCnt)?$actLinkCnt+1:$nearLinkCnt+1;
		$ret['url'] = ($actLinkCnt <= $nearLinkCnt)?$activeLnk:$urlArray[$maxItem];
		return $ret; //$urlArray[$maxItem];
        }	
    function GetTxtElNumber($str, $separator) //12_08_2007 Получение уровня текущего URL
    	{
		$inp = trim($str);
		$tmpArr = explode($separator, $inp);
		$count = 0;
		for($k=0; $k<count($tmpArr); $k++)
			{
			if(trim($tmpArr[$k]))
				$count++;
			}
//		echo '<br>str = '.$str.'; count = '.$count;
		return $count;
        }	 
	function GetStartedUrl($urlArray, $uri)//12_08_2007 - Поиск первого активного урла 
    	{
		$maxItem = 0;
		for($i=0; $i<count($urlArray); $i++)
			{
			$weight[] = $this->UriFirstCompare ($urlArray[$i], $uri);
			}
		for($i=0; $i<count($weight); $i++)
			if ($weight[$i] > $weight[$maxItem])
				$maxItem = $i;		
		return $urlArray[$maxItem];
        }	
	
    function GetCurrentLevelOfURL($urlInp) //12_08_2007 Получение уровня текущего URL
    	{
		$separator = '/';
		$inp = trim($urlInp);
		$tmpArr = explode($separator, $inp);
		$count = 0;
		for($k=0; $k<count($tmpArr); $k++)
			{
			if(trim($tmpArr[$k]))
				$count++;
			}
		return $count;
        }		
    function GetRelativePathFromURL($urlInp) //09_08_2007 Получение относительного пути из абсолютного URL
    	{
		$separator = '/';
		$inp = trim($urlInp);
		$tmpArr = explode($separator, $inp);
		$start = 0;
		$relPath = '';
		for($k=0; $k<count($tmpArr); $k++)
			{
			if(($start)&&(trim($tmpArr[$k])))
				$relPath .= '/'.trim($tmpArr[$k]);
			if(trim($tmpArr[$k]) == $_SERVER['SERVER_NAME'])
				$start++;
			}
		return $relPath;
        }		
	function nullFillArr($arr) /* 20_05_2007 заполняет нулями перед до порядка*/
		{
		global $CONST;
		$n = $CONST['orderNumberLehgth'];
		return str_pad($cnt, $n, "0", STR_PAD_LEFT);
		}
	function nullFill($cnt) /* 20_05_2007 заполняет нулями перед до порядка*/
		{
		global $CONST;
		$n = $CONST['orderNumberLehgth'];
		return str_pad($cnt, $n, "0", STR_PAD_LEFT);
		}
	function semanticInitialize()
		{	
		$this->_1_2[1]="одна "; 
		$this->_1_2[2]="две "; 

		$this->_1_19[1]="один "; 
		$this->_1_19[2]="два "; 
		$this->_1_19[3]="три "; 
		$this->_1_19[4]="четыре "; 
		$this->_1_19[5]="пять "; 
		$this->_1_19[6]="шесть "; 
		$this->_1_19[7]="семь "; 
		$this->_1_19[8]="восемь "; 
		$this->_1_19[9]="девять "; 
		$this->_1_19[10]="десять "; 

		$this->_1_19[11]="одиннацать "; 
		$this->_1_19[12]="двенадцать "; 
		$this->_1_19[13]="тринадцать "; 
		$this->_1_19[14]="четырнадцать "; 
		$this->_1_19[15]="пятнадцать "; 
		$this->_1_19[16]="шестнадцать "; 
		$this->_1_19[17]="семнадцать "; 
		$this->_1_19[18]="восемнадцать "; 
		$this->_1_19[19]="девятнадцать "; 

		$this->des[2]="двадцать "; 
		$this->des[3]="тридцать "; 
		$this->des[4]="сорок "; 
		$this->des[5]="пятьдесят "; 
		$this->des[6]="шестьдесят "; 
		$this->des[7]="семьдесят "; 
		$this->des[8]="восемдесят "; 
		$this->des[9]="девяносто "; 

		$this->hang[1]="сто "; 
		$this->hang[2]="двести "; 
		$this->hang[3]="триста "; 
		$this->hang[4]="четыреста "; 
		$this->hang[5]="пятьсот "; 
		$this->hang[6]="шестьсот "; 
		$this->hang[7]="семьсот "; 
		$this->hang[8]="восемьсот "; 
		$this->hang[9]="девятьсот "; 

		$this->namerub[1]="рубль "; 
		$this->namerub[2]="рубля "; 
		$this->namerub[3]="рублей "; 

		$this->nametho[1]="тысяча "; 
		$this->nametho[2]="тысячи "; 
		$this->nametho[3]="тысяч "; 

		$this->namemil[1]="миллион "; 
		$this->namemil[2]="миллиона "; 
		$this->namemil[3]="миллионов "; 

		$this->namemrd[1]="миллиард "; 
		$this->namemrd[2]="миллиарда "; 
		$this->namemrd[3]="миллиардов "; 

		$this->kopeek[1]="копейка "; 
		$this->kopeek[2]="копейки "; 
		$this->kopeek[3]="копеек "; 
		}
	function semantic($i,&$words,&$fem,$f)
		{
	//	global $_1_2, $_1_19, $des, $hang, $namerub, $nametho, $namemil, $namemrd; 
		$_1_2 = $this->_1_2; 
		$_1_19 = $this->_1_19; 
		$des = $this->des; 
		$hang = $this->hang; 
		$namerub = $this->namerub;  
		$nametho = $this->nametho; 
		$namemil = $this->namemil;  
		$namemrd = $this->namemrd; 
		$kopeek = $this->kopeek;  
		$words=""; 
		$fl=0; 
		if($i >= 100)
			{ 
			$jkl = intval($i / 100); 
			$words.=$hang[$jkl]; 
			$i%=100; 
			} 
		if($i >= 20)
			{ 
			$jkl = intval($i / 10); 
			$words.=$des[$jkl]; 
			$i%=10; 
			$fl=1; 
			} 
		switch($i)
			{ 
			case 1: $fem=1; break; 
			case 2: 
			case 3: 
			case 4: $fem=2; break; 
			default: $fem=3; break; 
			} 
		if( $i )
			{ 
			if( $i < 3 && $f > 0 )
				{ 
				if ( $f >= 2 ) 
					{ 
					$words.=$_1_19[$i]; 
					} 
				else 
					{ 
					$words.=$_1_2[$i]; 
					} 
				} 
			else 
				{ 
				$words.=$_1_19[$i]; 
				} 
			} 
		} 
	function num2str($L)
		{ 
		$this->semanticInitialize();
		$_1_2 = $this->_1_2; 
		$_1_19 = $this->_1_19; 
		$des = $this->des; 
		$hang = $this->hang; 
		$namerub = $this->namerub;  
		$nametho = $this->nametho; 
		$namemil = $this->namemil;  
		$namemrd = $this->namemrd; 
		$kopeek = $this->kopeek;  

		$s=" "; 
		$s1=" "; 
		$s2=" "; 
		$kop=intval( ( $L*100 - intval( $L )*100 )); 
		$L=intval($L); 
		if($L>=1000000000)
			{ 
			$many=0; 
			$this->semantic(intval($L / 1000000000),$s1,$many,3); 
			$s.=$s1.$namemrd[$many]; 
			$L%=1000000000; 
			} 
		if($L >= 1000000)
			{ 
			$many=0; 
			$this->semantic(intval($L / 1000000),$s1,$many,2); 
			$s.=$s1.$namemil[$many]; 
			$L%=1000000; 
			if($L==0)
				{ 
				$s.="рублей "; 
				} 
			}
		if($L >= 1000)
			{ 
			$many=0; 
			$this->semantic(intval($L / 1000),$s1,$many,1); 
			$s.=$s1.$nametho[$many]; 
			$L%=1000; 
			if($L==0)
				{ 
				$s.="рублей "; 
				} 
			} 
		if($L != 0)
			{ 
			$many=0; 
			$this->semantic($L,$s1,$many,0); 
			$s.=$s1.$namerub[$many]; 
			} 
		if($kop > 0)
			{ 
			$many=0; 
			$this->semantic($kop,$s1,$many,1); 
			$s.=$s1.$kopeek[$many]; 
			} 
		else 
			{ 
			$s.=" 00 копеек"; 
			} 
		return $s; 
		} 
	function getStrPrt($str, $div, $val) // 26_04_2007 ВОЗВРАЩАЕТ НУЖНЫЙ ЭЛЕМЕНТ $val СТРОКИ $str РАЗДЕЛЕННОЙ $div 
		{
		$tmpArr = explode($div, $str);
		return $tmpArr[$val];
		
		}
	function translitForUrl($str) //переводит русские буквы и спецсимволы в латиницу для URL(29_07_08) 
		{
		$tr = array(
		   "Ґ"=>"G","Ё"=>"YO","Є"=>"E","Ї"=>"YI","І"=>"I",
		   "і"=>"i","ґ"=>"g","ё"=>"yo","№"=>"_","є"=>"e",
		   "ї"=>"yi","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
		   "Д"=>"D","Е"=>"E","Ж"=>"ZH","З"=>"Z","И"=>"I",
		   "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
		   "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
		   "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
		   "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"`","Ы"=>"YI","Ь"=>"",
		   "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
		   "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"zh",
		   "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
		   "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
		   "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
		   "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"`",
		   "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya");		   
		$tr[" "] = "_";
		$tr["+"] = "_";
		$tr["*"] = "_";
		$tr["&"] = "_";
		$tr["?"] = "_";
		$tr["$"] = "_";
		$tr["%"] = "_";
		$tr["#"] = "_";
		$tr["@"] = "_";
		$tr["!"] = "_";
		$tr["="] = "_";
		$tr[">"] = "_";
		$tr["<"] = "_";
		$tr["~"] = "_";
		$tr["/"] = "";
		$tr["\\"] = "";
		$tr["«"] = "";
		$tr["»"] = "";
		$tr["\'"] = "";
		$tr["\""] = "";
		return strtr($str,$tr);
		}
	function translitEnToRU($str, $lower) //переводит латинские буквы в  русские  (17_04_09) 
		{
		if($lower)
			$str = strtolower($str);
		$tr = array(
		   "a"=>"а","b"=>"б","v"=>"в","g"=>"г","d"=>"д",
		   "e"=>"е","p"=>"п","c"=>"ц");		   
		$ret = strtr($str,$tr);
		return $ret;
		}	
	function translitMarket($str) //переводит русские буквы в латиницу (10_12_09)  для авторынка
		{
		$tr = array(
		   "Ґ"=>"G","Ё"=>"YO","Є"=>"E","Ї"=>"YI","І"=>"I",
		   "і"=>"i","ґ"=>"g","ё"=>"yo","№"=>"#","є"=>"e",
		   "ї"=>"yi","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
		   "Д"=>"D","Е"=>"E","Ж"=>"ZH","З"=>"Z","И"=>"I",
		   "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
		   "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
		   "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"C","Ч"=>"CH",
		   "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"`","Ы"=>"YI","Ь"=>"",
		   "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
		   "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"zh",
		   "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
		   "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
		   "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
		   "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"`",
		   "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya");		   
		return strtr($str,$tr);
		}
	function translit($str, $blank) //переводит русские буквы в латиницу (29_03_07) пробел
		{
		$tr = array(
		   "Ґ"=>"G","Ё"=>"YO","Є"=>"E","Ї"=>"YI","І"=>"I",
		   "і"=>"i","ґ"=>"g","ё"=>"yo","№"=>"#","є"=>"e",
		   "ї"=>"yi","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
		   "Д"=>"D","Е"=>"E","Ж"=>"ZH","З"=>"Z","И"=>"I",
		   "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
		   "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
		   "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
		   "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"`","Ы"=>"YI","Ь"=>"",
		   "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
		   "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"zh",
		   "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
		   "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
		   "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
		   "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"`",
		   "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya");		   
		if($blank)
			$tr[" "] = "_";
		return strtr($str,$tr);
		}
    function CheckAndMoveUploadedFile($fileName, $files,  $maxSize, $src)//проверяет размер загруженого файла и перемещает  егов src (07_03_11)
		{
//		echo '<hr>'.src;
		if ((is_uploaded_file($files[$fileName]['tmp_name']))&&($files[$fileName]['size']<=$maxSize))
			$ret = move_uploaded_file ($_FILES[$fileName]['tmp_name'], $src);
		return $ret;
		}	
    function GetFileList($src, $param)//возвращает список файлов в директории  (07_03_09)
		{
		$dir = opendir($src);
		while($file = readdir($dir))
			{
			if (($file!=".")&&($file!=".."))
				{     
				$tmp = array();
				if ($param)
					{
					$tmp['filename'] = $file;
					$tmp['filesize'] = filesize($src.'/'.$file);
					$tmp['fileatime'] = fileatime($src.'/'.$file);
					$tmp['filemtime'] = filemtime($src.'/'.$file);
					clearstatcache ();
					$ret[] = $tmp;
					}
				else
					{
					$ret[] = $file;
					}
				}
			}
		return $ret;
		}	
    function GetRusLocaleWord($type, $num, $pad)//возвращает назвние месяца или дня недели в правильном падеже =)) (07_01_20)
    	{
		$month = array(1=>'Январ',  2=>'Феврал',  
						3=>'Март',  4=>'Апрел',  
						5=>'Ма',  6=>'Июн',  
						7=>'Июл',  8=>'Август',  
						9=>'Сентябр',  10=>'Октябр',  
						11=>'Ноябр',  12=>'декабр');
		$LastChar = array(1 => '');
		return $res;
        }	
    function UriFirstCompare ($uri1, $uri2)//—частичное сравнение двух урлов - поиск альтернативного обработчика
    	{
		$separator = '/';
		$tmp_1 = explode($separator, trim($uri1));
		$tmp_2 = explode($separator, trim($uri2));
/*		echo '<hr>  1: ';
		print_r($tmp_1);
		echo '<hr>  2: ';
		print_r($tmp_2);
		echo '<hr>';*/
		$cnt = (count($tmp_1)>count($tmp_2))?count($tmp_2):count($tmp_1);
		$res = 0;
		for($i=1; $i<$cnt; $i++)
			{
			if(($tmp_1[$i])&&($tmp_1[$i] == $tmp_2[$i]))
				{
	//			echo '<br>'.$tmp_1[$i].' = '.$tmp_2[$i];
				$res ++;
				}
			elseif(($tmp_1[$i])&&($tmp_1[$i] != $tmp_2[$i]))
				{
				$i = $cnt;
				}
			}		
		return $res;
        }	
    function UriCompare ($uri1, $uri2) //Џолное сравнение двух урлов ()отбасывание слэшей
    	{
		$separator = '/';
		$inp[] = trim($uri1);
		$inp[] = trim($uri2);
		$out[] = '';
		$out[] = '';		
		for($i=0; $i<2; $i++)
			{
			$tmp[$i] = explode($separator, $inp[$i]);
			for($k=0; $k<count($tmp[$i]); $k++)
				{
				if(trim($tmp[$i][$k]))
					$out[$i] .= $tmp[$i][$k];					
				}
			}
/*		echo '<hr> inp = ';
		print_r($inp);
		echo '<br>tmp = ';
		print_r($tmp);
		echo '<br>out = ';
		print_r($out);
		echo '<hr>';*/
		if ($out[0] == $out[1])
			{			
			$ret = 1;
//			echo '<br>!!!!!';			
			}
		else
			{
			$ret = 0;	
//			echo '<br>------';			
			}
//		echo '<hr>';
		return $ret;
        }	
    function FindKey ($arr, $level)
    	{
		print_r($arr);
		return $ret;
        }	
	}

?>