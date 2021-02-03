<?php
define('SMARTY_DIR', str_replace("\\", "/", getcwd()).'/includes/Smarty/libs/');
require_once(SMARTY_DIR . 'Smarty.class.php');
//require('Smarty.class.php');
class Smarty_CMS extends Smarty {
   function init()
   {
		
		global  $CONST;
//        $this->Smarty();
//		$this->Smarty = new Smarty(); 
//		print_r($CONST);

		$this->setTemplateDir('../htdocs/includes/Smarty/templates/');
		$this->setCompileDir('../htdocs/includes/Smarty/templates_c/');
		$this->setConfigDir('../htdocs/includes/Smarty/configs/');
		$this->setCacheDir('../htdocs/includes/Smarty/cache/');       
		
/*		
		$this->template_dir = '../htdocs/includes/Smarty/templates/';
        $this->compile_dir  = '../htdocs/includes/Smarty/templates_c/';
        $this->config_dir   = '../htdocs/includes/Smarty/configs/';
        $this->cache_dir    = '../htdocs/includes/Smarty/cache/';
*/
        $this->caching = false;
		if($CONST['debugMode'])
			{
			if(($_SERVER['REMOTE_ADDR'] == '10.17.64.1155')||($_SERVER['SERVER_NAME'] == 'clocalhost'))
				$this->debugging = true;
			else
				$this->debugging = false;//
			}
		else
			{
			$this->debugging = false;//
			}
//		$this->debugging = false;//
		
//        $this->assign('app_name', 'AdressBook');
//	return $this;
   }
}
?> 