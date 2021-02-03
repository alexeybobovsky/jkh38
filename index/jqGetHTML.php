<?
//require_once("../classes/GetArticle_class.php");
require_once("../classes/TPL_admin_class.php");
require_once("../classes/TPL_manage_class.php");
$templates = array();
$CNT = new GetContent;
$MTPL = new MANAGE_TEMPLATE;
if($post[2])
	{
	switch ($post[2])
		{
		case 'optionsDM': /*23_10_2007 почтовые рассылки*/
			{	
//			$node = 10;
			require_once("../classes/TPL_DirectMail_class.php");
			require_once("../classes/GetDirectMail_class.php");
			$DM_TPL = new DirectMail_TEMPLATE;
			$DM_CNT = new GetDirectMail;
			
			$ACNT = new GetAdmin;
			
			$groups = $ACNT->GetGroups(2,1);
			$curDM = $DM_CNT->GetCurrentDM($node);
			$subscribers = $DM_CNT->GetSubscribers($node);
			$attachments = $DM_CNT->GetAttachments($node);
/*			print_r($attachments);
			$MA_TPL = new MarketAction_TEMPLATE;
			$MA_CNT = new GetMarketAction;			
			if($curAction['logo'])				
				$contentHTML[0][] = $MA_TPL->ShowImgAction($curAction, $location);						
			$contentHTML[0][] = $MA_TPL->ShowUploadImgAction($curAction, $location);			
//			$contentHTML[0][] = $MA_TPL->ShowStatusAction($curAction, $location);			
			$contentHTML[0][] = $MA_TPL->ShowDeleteAction($node, $location);			
			$contentHTML[1][] = $MA_TPL->showPeriodAction($curAction, $location);	*/		
			
			$contentHTML[0][] = $DM_TPL->ShowSubjectDM($curDM, $location);			
			$contentHTML[0][] = $DM_TPL->showDateDM($curDM, $location);			
			$contentHTML[0][] = $DM_TPL->ShowEditDM($node, $location);			
			$contentHTML[0][] = $DM_TPL->ShowStatusDM($curDM, $location);
			$contentHTML[0][] = $DM_TPL->ShowDeleteDM($node, $location);	
			
			$contentHTML[1][] = $DM_TPL->ShowUploadAttachment($node, $location);	
			if(is_array($attachments))			
				$contentHTML[1][] = $DM_TPL->ShowAttachmentsDM($curDM, $attachments, $location);			
			$contentHTML[2][] = $DM_TPL->ShowSubscribersDM($node, $groups, $subscribers, $location);			
			$contentArea[]['label'] = 'Редактирование';
			$contentArea[]['label'] = 'Вложения (файлы)';
			$contentArea[]['label'] = 'Получатели';
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
				$smarty->init();
			$smarty->assign('contentArea', $contentArea);
			$smarty->assign('contentHTML', $contentHTML);
			$table = $smarty->fetch('articleNewCat.tpl');
			echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
			}
		break;		
		case 'optionsReferences': /*06_05_2008 Справочники*/
			{	
//			print_r($_POST);
			if((trim($_POST['type']))&&(intval($_POST['node'])))
				{				
				$type = trim($_POST['type']);
				$id = intval($_POST['node']);
				require_once("../classes/ga/GetRef_class.php");			
				require_once("../classes/ga/TPL_Ref_class.php");			
				$getGARef = new GetReferences;
				$TPL_GARef = new REFERENCES_TEMPLATE;
				$contentArea[]['label'] = 'Изменение';
				$ref = $getGARef->getCurObject($type, 'id', $id);
//				print_r($ref);
				$addition = ($type == 'firms')?$ROUT->convertArrayIntoSelect($getGARef->getFullList('city', 0, 'name', 1), 
										'city_id', 'city_name', $ref['city_id'], 0):array();
				$contentHTML[0][] = $TPL_GARef->ShowReferenceEdit($type, $ref, $addition, $location);
				$contentArea[]['label'] = 'Удаление';
				$contentHTML[1][] = $TPL_GARef->ShowReferenceDelete($id, $type,  $location);
//				print_r($contentHTML);				
//				$contentArea[]['label'] = 'Добавление';
				require_once('../htdocs/includes/Smarty/setup.php');
				$smarty = new Smarty_CMS;
				$smarty->init();
				$smarty->assign('contentArea', $contentArea);
				$smarty->assign('contentHTML', $contentHTML);
				$table = $smarty->fetch('articleNewCat.tpl');
				echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
				}
			}
		break;
		case 'access': /*12_09_2007 права на разделы*/
			{	
			$showUsers = (isset($showUsers))?$showUsers:0;
			require_once("../classes/TPL_access_class.php");			
			require_once("../classes/GetAccess_class.php");
			$ACNT = new GetAdmin;
			$ATPL = new ADMIN_TEMPLATE;
			$AcCNT = new GetAccess;			
			$AcTPL = new ACCESS_TEMPLATE;			
			$right_tmp = $ACL->GetAllRight($node, 'SiteCatalog', 'sc_id');
			$right = $AcTPL->RightList($node, $right_tmp);
			$ret['list'] = $AcTPL->UsersWithoutRightsList($AcCNT->GetUserForRightManip(), $ACNT->GetAllGroups(0), $right['userList'], $showUsers);
			$ret['node'] = $node;

			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;	
			$smarty->init();
			$smarty->assign('users', $ret);
			$smarty->assign('rights', $right);
			$smarty->assign('showCheckbox', 1);
			$smarty->assign('checkboxValue', $showUsers);
//			$smarty->display('MessageBox.tpl');
			$smarty->display('accessList.tpl');
/*			$table = $smarty->fetch('accessList.tpl');
			echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	*/
			}
		break;		
		case 'optionsGroups': /*05_09_2007 опции групп*/
			{	
			$ACNT = new GetAdmin;
			$ATPL = new ADMIN_TEMPLATE;
			$usr = $ACNT->GetCurUser('id', $node);
			$users = $ACNT->GetAllUsers(0, 0);
			$usersToGroup = $ACNT->GetGroupUsers(2, $node);

			
			$contentHTML[1][0] = $ATPL->showGroupMemebers($usr, $usersToGroup, $users, $location);			
			$contentHTML[0][0] = $ATPL->showEditGroup($usr, $location);			
			$contentHTML[2][0] = $ATPL->showGroupDelete($usr, $location);			
			if(is_array($usersToGroup))
				$contentHTML[1][1] = $ATPL->showClearGroup($usr, $location);			
				
			
			$contentArea[]['label'] = 'Редактирование';
			$contentArea[]['label'] = 'Члены группы';
			$contentArea[]['label'] = 'Удаление';
			
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
				$smarty->init();
			$smarty->assign('contentArea', $contentArea);
			$smarty->assign('contentHTML', $contentHTML);
			$table = $smarty->fetch('articleNewCat.tpl');
			echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
			}
		break;
		case 'optionsUsers': /*30_08_2007 опции пользователей*/
			{	
			$ACNT = new GetAdmin;
			$ATPL = new ADMIN_TEMPLATE;
			$usr = $ACNT->GetCurUser('id', $node);
			$groups = $ACNT->GetGroups(2,0);
			$groupsToUser = $ACNT->GetUserGroups(0, $node);

			$contentHTML[2][0] = $ATPL->showUserMemebership($usr, $groupsToUser, $groups, $location);		
			$contentHTML[1][0] = $ATPL->ShowStatus($usr, $location);		
			$contentHTML[1][1] = $ATPL->showUserEdit($usr, $location);		
			$contentHTML[1][2] = $ATPL->showUserDelete($usr, $location);			
			$contentHTML[0][0] = $ATPL->showUserProperties($usr);			
			if(is_array($groupsToUser))
				$contentHTML[2][1] = $ATPL->showExcludeUser($usr, $location);			
					
			
			$contentArea[]['label'] = 'Подробно о пользователе';
			$contentArea[]['label'] = 'Редактирование';
			$contentArea[]['label'] = 'Членство в группах';
			
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
				$smarty->init();
			$smarty->assign('contentArea', $contentArea);
			$smarty->assign('contentHTML', $contentHTML);
			$table = $smarty->fetch('articleNewCat.tpl');
			echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
			}
		break;
		case 'optionsNews': /*05_06_2007 опции новостей и категорий*/
			{
			require_once("../classes/TPL_news_class.php");
			require_once("../classes/GetNews_class.php");
//			echo $location;
//			echo $node.' - '.$type;
			$NTPL = new NEWS_TEMPLATE;
			$NCNT = new GetNews;
			
			$idNeed = $CNT->GetCurNodeKeyword('newsContainer', -1);
//			$idNeed = $CNT->GetCurNodeKeyword('news', -1);
			$CNT->GetChildren($idNeed['catalog']['sc_parId'], 0, 1, 0, 0, -1, $idNeed['catalog']['sc_thread'], 0);
			$tree = $CNT->child;
			$CNT->Reset();
			$list = $NCNT->GetCurCatList($node, $tree);
			if($node!=$idNeed['catalog']['sc_id'])
				{
				if($type=='category')
					{
//					$contentHTML[0][0] = $NTPL->ShowNewCategory($node);
					$contentHTML[0][0] = $NTPL->ShowNewNews($node, $location);
					$contentHTML[1][0] = $NTPL->ShowEditCatName($node, $location);
					$contentArea[]['label'] = 'Добавить';
					$contentHTML[1][] = $NTPL->ShowDelCat($node, $type, $location);
					}
				else	
					{
					$nodeArt = $CNT->GetCurNode($node, -1);
//					$contentHTML[0][0] = $NTPL->ShowNewNews($node);	
					$contentHTML[0][0] = $NTPL->ShowEditCat($node, $list, $location);
					$contentHTML[0][1] = $NTPL->ShowStatus($node, $nodeArt['catalog']['sc_published'], $location);
					$contentHTML[0][2] = $NTPL->ShowEditNews($node, $location);
					$contentHTML[0][3] = $NTPL->ShowDelCat($node, $type, $location);
					}
				$contentArea[]['label'] = 'Редактировать';
//				$contentHTML[1][] = $ArTPL->ShowDelCat($node);
				}
			else
				{
				$contentHTML[0][0] = $NTPL->ShowNewCategory($node, $location);			
				$contentArea[0]['label'] = 'Добавить';		
				}
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
				$smarty->init();
			$smarty->assign('contentArea', $contentArea);
			$smarty->assign('contentHTML', $contentHTML);
			$table = $smarty->fetch('articleNewCat.tpl');
			echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
			
			}
		break;
		case 'optionsInfo': /*28_05_2007 опции информационных блоков*/
			{	
//			echo $location.' - '.$type.'<br>';
			$relPath =  $ROUT->GetRelativePathFromURL($location);
			require_once("../classes/TPL_info_class.php");
			require_once("../classes/GetInfo_class.php");
			$IATPL = new INFOADMIN_TEMPLATE;
			$brotherList = $CNT->GetBrothers($node);
			$ICNT = new GetInfo;
			$tree = $ICNT->GetInfoTree(0, 1);
			$list = $ICNT->GetNodeList($tree, $node);
			$brotherList = $ICNT->GetBrotherList($node);
			switch($type)
				{
				case 'emptyNode':
					{
					$contentArea[]['label'] = 'Добавить';
					$contentArea[]['label'] = 'Редактировать';
					$contentHTML[0][] = $IATPL->ShowNew($node, $relPath);
					$contentHTML[0][] = $IATPL->ShowNewInfo($node, $relPath);
					$contentHTML[1][] = $IATPL->ShowParents($node, $list, $relPath);
					$contentHTML[1][] = $IATPL->ShowBrothers($node, $brotherList, $relPath);
					$contentHTML[1][] = $IATPL->ShowStatus($node, $relPath);
					$contentHTML[1][] = $IATPL->ShowMenu($node, $relPath);
					$contentHTML[1][] = $IATPL->ShowDelInfo($node, $relPath);
					}
				break;
				case 'infoNode':	
					{
					$contentArea[]['label'] = 'Добавить';
					$contentArea[]['label'] = 'Редактировать';
					$contentHTML[1][] = $IATPL->ShowEdit($node, $relPath);
					$contentHTML[0][] = $IATPL->ShowNew($node, $relPath);
					$contentHTML[1][1] = $IATPL->ShowParents($node, $list, $relPath);
					$contentHTML[1][2] = $IATPL->ShowBrothers($node, $brotherList, $relPath);
					$contentHTML[1][3] = $IATPL->ShowStatus($node, $relPath);
					$contentHTML[1][4] = $IATPL->ShowMenu($node, $relPath);
					$contentHTML[1][5] = $IATPL->ShowDelInfoBlock($node, $relPath);
					$contentHTML[1][6] = $IATPL->ShowDelInfo($node, $relPath);
					}
				break;
				case 'infoFolder':	
					{
					$contentArea[]['label'] = 'Добавить';
					$contentArea[]['label'] = 'Редактировать';
					$contentHTML[0][0] = $IATPL->ShowNew($node, $relPath);
					$contentHTML[1][0] = $IATPL->ShowEdit($node, $relPath);
					$contentHTML[1][1] = $IATPL->ShowParents($node, $list, $relPath);
					$contentHTML[1][2] = $IATPL->ShowBrothers($node, $brotherList, $relPath);
					$contentHTML[1][3] = $IATPL->ShowStatus($node, $relPath);
					$contentHTML[1][4] = $IATPL->ShowMenu($node, $relPath);
					$contentHTML[1][5] = $IATPL->ShowDelInfoBlock($node, $relPath);
					$contentHTML[1][6] = $IATPL->ShowDelInfo($node, $relPath);
					}
				break;
				case 'emptyFolder':	
					{					
					$contentArea[]['label'] = 'Добавить';
					$contentHTML[0][] = $IATPL->ShowNew($node, $relPath);
					if($node>0)
						{
						$contentArea[]['label'] = 'Редактировать';
						$contentHTML[0][] = $IATPL->ShowNewInfo($node, $relPath);
						$contentHTML[1][] = $IATPL->ShowParents($node, $list, $relPath);
						$contentHTML[1][] = $IATPL->ShowBrothers($node, $brotherList, $relPath);
						$contentHTML[1][] = $IATPL->ShowStatus($node, $relPath);
						$contentHTML[1][] = $IATPL->ShowMenu($node, $relPath);
						$contentHTML[1][] = $IATPL->ShowDelInfo($node, $relPath);
						}
					}
				break;
				}
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
				$smarty->init();
			$smarty->assign('contentArea', $contentArea);
			$smarty->assign('contentHTML', $contentHTML);
			$table = $smarty->fetch('articleNewCat.tpl');
			echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
			}
		break;
		case 'optionsCatalog': /*26_04_2007 опции элементов каталога*/
			{				
//			echo $type.' - '.$node;
			$relPath =  $ROUT->GetRelativePathFromURL($location);
			$CatCNT = new GetCatalog;
			$CatTPL = new CATALOG_TEMPLATE;
//			$selectedNode = $CatCNT->GetCatalogNode($node);
			$selectedNode = $CatCNT->GetCatalogNode_NEW($node, 1);
//			print_r($selectedNode);			
			if($type=='item')
				{
				$contentHTML[] = $CatTPL->ShowEditInfo($selectedNode, $relPath);										
				if($selectedNode['body']['full_descr'])
					{
					$contentHTML[] = $CatTPL->ShowStatusInfo($selectedNode, $relPath);															
					$contentHTML[] = $CatTPL->ShowDelInfo($selectedNode, $relPath);															
					}				
				}
			$contentHTML[] = $CatTPL->ShowEditNodeStatus($selectedNode, $relPath);							
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
				$smarty->init();
			$smarty->assign('contentArea', $contentArea);
			$smarty->assign('contentHTML', $contentHTML);
			$table = $smarty->fetch('catalogOptions.tpl');
			echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
			}
		break;
		case 'catalogExpand': // 26_04_2007 открытие ветки каталога
			{
//			echo $admin, $catalogId  /*.' '.$lvl*/;
//			$admin = 1;

			$CatCNT = new GetCatalog;
			$CatTPL = new CATALOG_TEMPLATE;
			$tree = $CatCNT->GetChildrenFirstLvl_NEW($catalogId , $admin );
//			print_r($tree);
			if(is_array($tree))
				{
				$catalog['tree'] = $tree; //$CatTPL->getTree($tree);
				$catalog['curLvl'] = $lvl+1;
				}
			if($admin) $catalog['admin'] = 1;
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
				$smarty->init();
			$smarty->assign('Catalog', $catalog);
			$table = $smarty->fetch('catalogTree.tpl');
			echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
			}
		break;
		case 'optionsArticles': /*05_04_2007 опции статей и категорий*/
			{
			$ArTPL = new ARTICLE_TEMPLATE;
			$ArCNT = new GetArticle;
			$idNeed = $CNT->GetCurNodeKeyword('article', -1);
			if($node!=$idNeed['catalog']['sc_id'])
				{
				if($type=='category')
					{
					$idNeed = $CNT->GetCurNodeKeyword('article', -1);
					$CNT->GetChildren($idNeed['catalog']['sc_parId'], 0, 1, 0, 0, -1, $idNeed['catalog']['sc_thread'], 0);
					$tree = $CNT->child;
					$CNT->Reset();
					$list = $ArCNT->GetCategoryList($tree, $node);
					$contentHTML[0][0] = $ArTPL->ShowNewCategory($node);			
					$contentHTML[0][1] = $ArTPL->ShowNewArticle($node);			
					$contentHTML[1][0] = $ArTPL->ShowEditCat($node, $list);
					$contentHTML[1][1] = $ArTPL->ShowEditCatName($node);
					}
				else	
					{
					$nodeArt = $CNT->GetCurNode($node, -1);
					$contentHTML[0][0] = $ArTPL->ShowNewArticle($node);			
					$contentHTML[1][0] = $ArTPL->ShowEditArticleStatus($node, $nodeArt['catalog']['sc_published']);
					$contentHTML[1][1] = $ArTPL->ShowEditArticle($node);
					}
				$contentHTML[1][2] = $ArTPL->ShowDelCat($node, $type);
				$contentArea[]['label'] = 'Добавить';
				$contentArea[]['label'] = 'Редактировать';
//				$contentHTML[1][] = $ArTPL->ShowDelCat($node);
				}
			else
				{
				$contentHTML[0][0] = $ArTPL->ShowNewCategory($node);			
				$contentHTML[0][1] = $ArTPL->ShowNewArticle($node);			
				$contentArea[] = 0;				
				}
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
				$smarty->init();
			$smarty->assign('contentArea', $contentArea);
			$smarty->assign('contentHTML', $contentHTML);
			$table = $smarty->fetch('articleNewCat.tpl');
			echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
			}
		break;
		case 'orderSelect':			
			{		
//			echo 'orderSelect';
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
				$smarty->init();
			$brothers = $CNT->GetChildren1Level($node, 0, 0);
			$i = 0;
			$ret = '<select name="NODE_ORDER"   id="IDNODE_ORDER"    class="input2"   style="WIDTH: 200px"  >';
			do
				{
				$ret.= '<option value="'.$i.'" >';
				if (!$i)
					$ret.= 'первый';
				else
					$ret.= 'после '.$brothers[$i-1]['catalog']['sc_name'];
				$ret.= '</option>';					
				$i++;
				}
			while ($i<count($brothers));	
/*			$lang = $CNT->GetAllLanguages(2);*/
			$ret.= '<option value="'.$i.'" selected>';
			$ret.= 'последний';
			$ret.= '</option>';
			$ret.= '</select>';
//			echo $ret;
			echo $select = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$ret):$ret;	
//			echo $select = iconv("windows-1251","UTF-8",$ret );			
//			$node = $CNT->GetCurNode($nodeId, 1);
			}
		break;
		case 'properties':
			{
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
				$smarty->init();
			$node = $CNT->GetCurNode($node, 1);
			$content = $MTPL->TPL_GetCaptionTable($node);			
			$smarty->assign('content', $content);
			$table = $smarty->fetch('manage_prop_table.tpl');
			echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
//			echo $table = iconv("windows-1251","UTF-8",$table );			
			}
		break;
		case 'rightUserList':
			{
			$ACNT = new GetAdmin;
			$ATPL = new ADMIN_TEMPLATE;
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
				$smarty->init();
//			print_r($_POST); 
			$right_tmp = $ACL->GetAllRight($node, 'SiteCatalog', 'sc_id');
			$right = $ATPL->RightList($node, $right_tmp);
			$ret = $ATPL->UsersWithoutRightsList($ACNT->GetAllUsers(0, 0), $ACNT->GetAllGroups(0), $right['userList']);
			$smarty->assign('SMRT_cnt', $ret);
			$smarty->assign('SMRT_node', $node);
			$table = $smarty->fetch('userList_rights.tpl');
			echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
//			echo iconv("windows-1251","UTF-8",$table );			
			}
		break;
		case 'rightRightsList':
			{
			require_once('../htdocs/includes/Smarty/setup.php');
			$smarty = new Smarty_CMS;
				$smarty->init();
			$node = $CNT->GetCurNode($node, 1);
			$content = $MTPL->TPL_GetCaptionTable($node);			
			$smarty->assign('content', $content);
			$table = $smarty->fetch('manage_prop_table.tpl');
//			echo $table = iconv("windows-1251","UTF-8",$table );			
			echo $table = ($CONST['dinamicUTF8Convert'])?iconv("windows-1251","UTF-8",$table):$table;	
			}
		break;
		
		
		default:
			{
//			echo 'error!!!';
			}
		}
//	print_r($_RESULT);
	//$templates = array();	
	}
else
	{
//	echo 'error!!!';
	
	}

?>