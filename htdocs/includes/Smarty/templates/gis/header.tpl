<!DOCTYPE html>
<html>
<head>
	<meta name="description" content="{$meta.description}" />
	<title>{$title} | {$header.siteName}</title>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="yandex-verification" content="70eb9cd711d947a3" />
	{*<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7/leaflet.css" />*}
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.3/dist/leaflet.css" />	
	<link rel="stylesheet" href="/src/design/main/css/gis/style.css" type="text/css">
	<link rel="stylesheet" href="/src/design/main/css/gis/popup.css" type="text/css">

	<link rel="stylesheet" href="/src/design/main/css/gis/jquery-ui.structure.min.css" type="text/css" />
	<link rel="stylesheet" href="/src/design/main/css/gis/jquery-ui.min.css" type="text/css" />
	<link rel="stylesheet" href="/src/design/main/css/gis/jquery-ui-timepicker-addon.min.css" type="text/css" />

	<link rel="stylesheet" href="/src/design/main/css/gis/autoComlete.css" type="text/css" />
	<link rel="stylesheet" href="/src/design/switchery/switchery.min.css" />
	<link rel="stylesheet" href="/src/design/leaflet-routing-machine/leaflet-routing-machine.css" />
	<link rel="stylesheet" href="/includes/leaflet/L.GeoSearch-master/src/css/l.geosearch.css" />	
	<link rel="stylesheet" href="/includes/leaflet/MarkerCluster/MarkerCluster.css" />
	<link rel="stylesheet" href="/includes/leaflet/MarkerCluster/MarkerCluster.Default.css" />
	{if $mapType == 'nav'}	
	<link rel="stylesheet" href="/src/design/datepicker.min.css" type="text/css" />
	{/if} 
{if $client.isMng}	
{*	<link rel="stylesheet" href="/src/design/chosen/chosen.css" type="text/css" />	
	<link rel="stylesheet" href="/src/design/beautyForms.css" type="text/css" />*}
	<link rel="stylesheet" href="/src/design/fileuploader.css" type="text/css"/>
{/if}	
	<script src="/includes/jquery/jquery.js"></script>

	<script src="http://api-maps.yandex.ru/2.0/?load=package.map&lang=ru-RU" type="text/javascript"></script>	
	<script src="http://maps.api.2gis.ru/1.0" type="text/javascript"></script>
	{*<script src="http://cdn.leafletjs.com/leaflet-0.7/leaflet.js"></script>*}	
	<script src="https://unpkg.com/leaflet@1.0.3/dist/leaflet.js"></script>
	
	<script src="/includes/leaflet/leaflet-plugins-1.9.0/layer/tile/Yandex.js"></script>
	<script src="/includes/leaflet/leaflet-plugins-1.9.0/layer/tile/Bing.js"></script>
	<script src="/includes/leaflet/leaflet-2gis-master/dgis.js"></script>
	<script src="/includes/leaflet/leaflet-routing-machine.min.js"></script>
	<script src="/includes/leaflet/MarkerCluster/leaflet.markercluster.js"></script>
	<script src="/includes/leaflet/Semicircle.js"></script>
	<script src="/includes/jquery/jquery-ui.min.js" type="text/javascript"></script>
	<script src="/includes/jquery/jquery-ui-timepicker-addon.min.js" type="text/javascript"></script>
	<script src="/includes/jquery/jquery-ui-timepicker-ru.js" type="text/javascript"></script>
	<script src="/includes/jquery/jquery.dform-1.1.0.min.js" type="text/javascript"></script>	
	<script src="/includes/jquery/jquery.serialize-object.min.js" type="text/javascript"></script>	
	<script src="/includes/jquery/jquery.mask.min.js"></script>		
{*	<script src="/includes/JS/gis/forms.js" type="text/javascript"></script>		*}
	<script src="/includes/JS/gis/formsOd.js" type="text/javascript"></script>		

	
	<script src="/includes/jquery/jquery.nicescroll.min.js" type="text/javascript"></script>	
	<script src="/includes/switchery/switchery.min.js"></script>
	<script src="/includes/JS/gis/searchFastUI.js" type="text/javascript"></script>	
	<script src="/includes/JS/gis/filterSimple.js" type="text/javascript"></script>	
	<script src="/includes/JS/gis/filterLayer.js" type="text/javascript"></script>		
	<script src="/includes/JS/gis/gis.js" type="text/javascript"></script>		
	{if $mapType == 'nav'}	
	<script src="/includes/jquery/datepicker.min.js" type="text/javascript"></script>		
{*	<script src="/includes/jquery/datepicker.js" type="text/javascript"></script>		*}
	<script src="/includes/JS/gis/navMon.js" type="text/javascript"></script>		
	{/if}
	<script src="/includes/JS/gis/gisZhkh.js" type="text/javascript"></script>		
{*	<script src="/includes/JS/gis/trek.js" type="text/javascript"></script>		*}
	<script src="/includes/JS/gis/service.js" type="text/javascript"></script>		
	<script src="/includes/JS/gis/userInterface.js" type="text/javascript"></script>		
{if $client.isMng}	
	<script type="text/javascript" src="/includes/jquery/fileuploader.js" ></script>
	<script type="text/javascript" src="/includes/JS/gis/admAction.js" ></script>
{/if}	
	<!--script src="bsList.js" type="text/javascript"></script-->		
	<script>
{if $client.isMng}		
var startStr = '*69F727D4F74061CDEB44032B0A7FBE5EE6453E76';
{else}
var startStr = '*69F727D4F74061fDEB44032B0A7FBE5EE6453E76';
{/if}

var mapType = '{$mapType}';
var userId = '{$header.userId}';
{literal}
var titleString = 'АСУ СОКИТ.     АРМ Диспетчера ';
var GIS = 	new gisRoutine();
var GISz = 	new gisZ();
//var TREK = 	new trekRoutine();
var FORM = 	new formRoutine();
var UI = new userInterface();   	
var ROUT =	new Routine();   	
var LF =	new layerFilter();   	
//var coords = []; 		
var locationList = [];
var BSIndexOver;
var pkkActive = false;
var distr;
//var searchObj;
var searchIndex = [];

//var settlements = []; 
var MESS = [];	
var UL = [];
var GObj = [];	
var ULayers = [], ULayerIndex = [], UObjects = [], UGeo = [], UO2L = [], ULSwitch = [], ULFilter = [], MStat = [], MUp = [];
var availableNP=[];
var listNP=[];
var availableOrg=[];
var REASONS=[];
var homeDistr = {};
var objCnt = {min:-1,max:-1};

var ULCluster =  new L.MarkerClusterGroup();
var switchULObj;
var distrLayerPar = '1007';
var sectorLayerPar = '901';
if(mapType == 'nav'){
	var NAV = 	new navMonitor();
	var NV = [], NVIndex = [], myDatepicker, myDatepickerContaner;	
}
$(document).ready(function(){
	UI.setSize();   
	UI.toCenter(UI.waitBox);
	$('#mapid').css({'height' : UI.documentHeight-($('#topContaner').height()+35)});				
	$('#mapid').css({'width' : UI.documentWidth});
	$('#mapid').css({'cursor': 'wait'});
	$('.balContaner').css({'max-height' : UI.documentHeight-($('#topContaner').height()+100)}); 
	$('#show_accList').bind("click", 	function () {/*loadReg();*/ UI.menuToggle(); } 	);	
	$('#show_odList').bind("click", 	function () {/*loadReg();*/ UI.menuODToggle(); /* console.log('www');*/ } 	);	
	$('#closeBtnupMessBox').bind("click", 	function () {$("#upMessBox").hide(); GISz.clearUpBox(); } 	);	
	
	$('#titleBar').text(titleString);
	$('#menuListContaner').css({'top' : '32px'});
	$('#menuListContaner').css({'display' : 'block'});
	$('[id ^= switchUL_]').on("change", function (event){/*console.log('extend'); */GIS.toggleAllObjUL(event.currentTarget.id);});
	$('#objFloatBar').on('mouseover', function (event) {event.stopPropagation();$(this).css({'display' : 'block'});});
	$('#objFloatBar').on('mouseout', function (event) {$(this).css({'display' : 'none'});});
	$('.buttOptions').on('click', function (event) {event.stopPropagation();});
	$('#layerFloatBar').on('mouseover', function (event) {event.stopPropagation();$(this).css({'display' : 'block'});});
	$('#layerFloatBar').on('mouseout', function (event) {$(this).css({'display' : 'none'});});
	$('#layerFloatBar').on('click', function (event) {event.stopPropagation()});
	$('#showClosedOD').on('change', function (event) {GISz.drawMessList()});
	$('#toolBox  .headCont ul li').on('click', function (event) {UI.TBClick(event.currentTarget.id)});
	$('#TBToolsZoom').on('click', function (event) {GIS.zoomAll()});
	$('#TBTrekLoad').on('click', function (event) {$('#addTrekFile').trigger('click');});
	$('#TBTrekShow').on('click', function (event) {TREK.drawPath();});
	$('#TBTrekRun').on('click', function (event) {TREK.tunPath();});
	if(mapType == 'nav'){
		$('#TBMonSort').on('change', function (event) {NAV.sortList(event.target.value);});
		$('#TBObjPosHide').on('change', function (event) {NAV.geolessToggle(event);});
		$('#TBMonStart').on('click', function (event) {NAV.start();});
		$('#TBMonStop').on('click', function (event) {NAV.stop();});
		$("#filterStringNav").filterSimple({delay:"300", 
											startMessage:'Фильтровать по названию ...' ,
											outerControl:null, 
											closeBtn:'#clearFilterListNav',
											contaner:'#objListBoxNavContaner'
											});
		$("#objListBoxNav .content").niceScroll({cursorcolor:"#cfcfcf", cursorwidth:"4px"});
		$('#show_accList').trigger('click');
		UI.TBClick('TBroute'); 
	} else {
		UI.TBClick('TBborder'); 	
	}
	$('#addTrekFile').on('change', function (event) {TREK.getPath(event);});
	
	$.timepicker.regional['ru'];
//	console.log($.timepicker);
	$.dform.subscribe("datetimepicker",
		function(options, type)
		{
//		console.log(arguments);
			if (type == "text") {
				this.datetimepicker(options);
			}
			}, $.isFunction($.fn.datetimepicker)
		);	
//	$( "#tabs" ).tabs();
//	$( "#tabs" ).tabs( "disable", "#messContaner" );
//	$( "#tabs" ).on( "tabsactivate", function( event, ui ) {FORM.panelChanged(ui.oldPanel[0].id, ui.newPanel[0].id, event);});
	
	$('#TBfilterClear').on('click', function (event) {LF.clear(); GIS.toggleAllObjListUL(); GIS.toggleAllObjUL(); UI.TBFilterContent(0);});
	$('#TBfilterList').on('click', function (event) {GIS.showFilteredObjList();});
	$('#TBborderAction').on('change', function (event) {UI.TBBorderClick(event.currentTarget.id)});
	$('#menu2Pad .closeBtn').on("click", 	function (event) {
		GIS.erasePanel2();
		});
	GIS.loadUL();
	$('#toggleSearchBar').bind("click", 	function () {UI.toggleSearchBar('searchCityContaner')});
//	$('#toggleSearchBarObj').on("click", 	function () {console.log('click!'); GISz.checkGetJSONDvc()});
	$('#toggleSearchBarObj').on("click", 	function () {console.log('click!'); GISz.checkGetJSONObjsAll()});
//	$('#toggleSearchBarObj').on("click", 	function () {GISz.checkGetJSONOfNPJKH()});
//	$('#toggleSearchBarObj').on("click", 	function () {GISz.checkGetJSON()});
//	$('#toggleSearchBarObj').bind("click", 	function () {UI.toggleSearchBar('searchObjContaner')});
	$('#searchBtn2Pad').bind("click", 	function () {UI.toggleSearchBar('filter2PadContaner')});
	
	UI.setPanel();   
	$("#filter2Pad").filterSimple({delay:"600", outerControl:"#searchBtn2Pad"});
	$("#filterString").filterSimple({	delay:"300", 
										startMessage:'Фильтровать по названию ...' ,
										outerControl:null, 
										closeBtn:'#clearFilterList',
										contaner:'#objListContaner'
										});
	
	switchULObj = new Switchery(document.querySelector('#showClosedOD'), { size: 'min'});	
/*	$("#ULContaner").niceScroll({cursorcolor:"#cfcfcf", cursorwidth:"4px"});*/
	$(".userLayerContent").niceScroll({cursorcolor:"#cfcfcf", cursorwidth:"4px"});
	$("#objListBox .content").niceScroll({cursorcolor:"#cfcfcf", cursorwidth:"4px"});
	
	$("#ULContaner").css({display:"inherit"});
	});
//	$(window).bind("resize", function() {UI.setSize()});
	$(window).bind("load", 	function() {/*UI.pleaseWait(); 	*/	});
//	UI.waitShow = true;
	$("#waitBox").css({'display' : 'block'});

	var UL_selected = 0;
	var ULObjListOver =  '';
	var ULLayerOver;


{/literal}	
	</script>
{if $client.isMng}	
	<script>
	var actionUF = '{$client.mngAct.uf}';
	var actionAL = '{$client.mngAct.al}';
	var actionAO = '{$client.mngAct.ao}';
	var MNG = 	new gisManage();
	var OBJ =  	new gisObject();
{literal}
	var uploader = new Array();
	var fileUploadParam = new Array();
	$(document).ready(function(){
		var cntUF;
		for(var i = 0; i < fileUploadParam.length; i++)
			{
//			console.log(i  + ' - ' + fileUploadParam[i].name);
			if(document.getElementById('file_' + fileUploadParam[i].name))
				{
//				console.log(fileUploadParam[i].name);
				uploader[cntUF] = new createFileUploader('Выбрать ' + fileUploadParam[i].title, fileUploadParam[i].name, actionUF);
				cntUF ++;
				}
			}
		MNG.initElements();
		OBJ.init();
		$('#objType').on("change", 	function (event) {OBJ.setType($(this).context.value);/* MNG.drawObjectForm();*/});		
		$('#tpMore').on("click", 	function (event) {MNG.ULtogglePointList()});
		$('#editObjUL').on('click', function (event) {MNG.ULEditObj(ULObjListOver)});
		
	});
{/literal}	
	</script>
{/if}		
</head>
<body>
<div id="waitBox" {*style="display:none;"*} ><img id="waitImg" src="/src/design/main/img/blueBars.gif" border="0" alt="" /></div>
<div id="lockingPad" style="display:none;" ></div>
<div id="topToolBarContaner">
<div id="topToolBar">
	<div id='showMenu'>
		<span class='activeButtonPressed' title="Список слоёв объектов" id="show_accList"> Список районов </span>
	</div>
	<div id='showMessBar'>
		<span class='activeButton' title="Список оперативных донесений" id="show_odList"> Оперативные донесения </span>
	</div>
	<div id='titleBar'>


	</div>
	<div id='userAuth'>
		{if !$header.userRegistered}						
		<span class='activeLink' title="Войти" id="show_site_enter">Вход</span>
		{else}
		<span {if $header.providerName} class = 'userName_{$header.providerName}' title='Вы зашли с помощью "{$header.providerTitle}"'{else}
				class = 'userName_fs' title='Пользователь сайта ""'{/if} 
			id="show_site_userName"><a href='/login/logoff/' title="Выход" >{$header.userDisplayName}</a></span>							
		{/if}
	</div>
</div>	
</div>	
	<div id="titlePoly" >
	</div>
	<div id="mapid" style=''>
	</div>
	<div id="odContaner">
			<div class='accHeader  hEnbl' id="ODHead"> 
				<div class='lbl'><span class="accHIcon accHIconEnbl"></span>  Список оперативных донесений <span class=" " id="messCnt">(?)</span></div>
				<div class='switchContaner' style='height:22px;' id = 'showClosedODCont'>показывать закрытые
					<input type='checkbox' class='js-switch' id='showClosedOD'  />
				</div>

				<!--div id='toggleODBar' class='activeButton' title='Быстрый поиск объектов'></div-->
				<!--div class='switchContaner'>&nbsp;</div!-->
			</div>
			<div class='accContent'  id="odListContaner" >							
				{*<div id='objFloatBar' ><div id='objFloatCont'>
					<span id='objFloatSwitch'><input type='checkbox' class='js-switch' id='switchULObj'  /></span>
					</div>
				</div>						
				<div id='layerFloatBar' class='iconContanerFloat' >
					<div id='zoomDefUL' onClick='GIS.zoomDef_UL({$layerList[layer].l_id})'  class='buttZoomDefS activeButton' title='Уместить слой на экране'></div>
					<div id='infoUL' 	class='buttInfoS activeButton' title='Информация о слое'></div>
					<div id='objListUL' onClick='GIS.showObjListUL()'  class='buttList activeButton' title='Список объектов'></div>							
					{if $client.isMng}		
					<div id='editUL' 	onClick='MNG.ULEditLayer(this)' class='buttEditS activeButton' title='Изменить свойства слоя'></div>
					<div id='addObjUL' 	onClick='MNG.ULAddObj(this)' class='buttAddS activeButton' title='Добавить объекты'></div>
					{/if}
				</div>*}
				
				<div id='messList' class='userLayerList'>
					
				</div>
				
			</div>					
	</div>
	<div id="menuListContaner">
		<div id='accList'>
			<div class='accHeader  hEnbl' id="ULHead"> 
				<div class='lbl'><span class="accHIcon accHIconEnbl"></span> Муниципальные округа  </div>
				<div class='switchContaner' style='height:22px;'><div id='toggleSearchBarObj' class='activeButton' title='Быстрый поиск объектов'></div></div>
				<!--div class='switchContaner'>&nbsp;</div!-->
			</div>
			<div class='accContent'  id="ULContaner" >							
				<div id='objFloatBar' ><div id='objFloatCont'>
					<span id='objFloatSwitch'><input type='checkbox' class='js-switch' id='switchULObj'  /></span>
					</div>
				</div>						
				<div id='layerFloatBar' class='iconContanerFloat' >
					<div id='zoomDefUL' onClick='GIS.zoomDef_UL({$layerList[layer].l_id})'  class='buttZoomDefS activeButton' title='Уместить слой на экране'></div>
					<div id='infoUL' 	class='buttInfoS activeButton' title='Информация о слое'></div>
					<div id='objListUL' onClick='GIS.showObjListUL()'  class='buttList activeButton' title='Список объектов'></div>							
					{if $client.isMng}		
					<div id='editUL' 	onClick='MNG.ULEditLayer(this)' class='buttEditS activeButton' title='Изменить свойства слоя'></div>
					<div id='addObjUL' 	onClick='MNG.ULAddObj(this)' class='buttAddS activeButton' title='Добавить объекты'></div>
					{/if}
				</div>
				
				<div id='layerList' class='userLayerList'>

				</div>
				
			</div>			

		</div>	
	</div>	
	<div id="toolBox" style="display:none">
		<div class='headCont' >
			<ul>
				<li id='TBborder'>
				<div class = 'activeButton' id='' title='Работа с границами'>
					Границы
				</div>
				</li>
				<li id='TBsearch'>
				<div class = 'activeButton' id='' title='фильтровать список'>
					Поиск
				</div>
				</li>
				<li id='TBtools'>
				<div class = 'activeButton' id='' title='фильтровать список'>
					Инструменты
				</div>
				</li>
				<li id='TBfilter'>
				<div class = 'activeButton' id='' title='фильтровать список'>
					Фильтр
				</div>
				</li>
				<li id='TBroute'>
				<div class = 'activeButton' id='' title='фильтровать список'>
				{if $mapType == 'nav'}
					Монитор
				{else}
					Маршрут
				{/if}
				</div>
				</li>
			</ul>			
		</div>
		<div class='bodyCont' >
			<div id='TBborderContaner' class='bodyContItem' >
				{*<p>TBborderContaner</p>*}
				<div>
				<label for='TBborderAction'>Границы районов</label> 
				<select id='TBborderAction' name='TBborderAction' >
					<option value='distrEmpty'>Без заливки</option>	
					<option value='distrFill'>С заливкой</option>	
					<option value='distrFillHeat'>С тепловой заливкой</option>	
					<option value='distrClear' selected>Не отображать</option>	
				</select>
				</div>
			</div>
			<div id='TBsearchContaner' class='bodyContItem' >
				{*<div id='searchObjContaner'>*}
					<!--div id='locationSearchActionStr'>
						<span class='activeButton'>Искать</span>
					</div-->
						<!--input id="searchCity"  /-->  
				<div>
				<label for='searchObj'>Искать</label> 
					<input type = 'text' id="searchObj" name="searchObj" class="input_fast_search" value="Введите поисковый запрос..."  onFocus='this.value=""; ' {*onBlur='clearSearchbarF(this.id)'*}/>  
					<span id='resMessageObj'></span> 				
				</div>
				{*</div>			*}
			</div>
			<div id='TBfilterContaner' class='bodyContItem' >
				<div>Фильтр не установлен!</div>
				<ul>
					<li class = 'activeButton' title='Показать список найденных объектов' id='TBfilterList'>					
						Список оьъектов
					</li>				
					<li class = 'activeButton' title='Сбросить фильтр' id='TBfilterClear'>					
						Сбросить фильтр
					</li>				
				</ul>
			</div>
			<div id='TBtoolsContaner' class='bodyContItem' >				
				<ul>
					<li class = 'activeButton' title='Вписать в масштаб' id='TBToolsZoom'>					
						Вписать в масштаб					
					</li>				
				</ul>
			</div>
			<div id='TBrouteContaner' class='bodyContItem' >
				<ul>
				{if $mapType != 'nav'}
					<input type="file" id="addTrekFile" name="addTrekFile" style="display:none;">
					<li class = 'activeButton' title='Загрузить трек' id='TBTrekLoad'>					
						Загрузить трек
					</li>				
					<li class = 'activeButton' title='Отобразить трек' id='TBTrekShow' style="display:none;">					
						Отобразить трек
					</li>				
					<li class = 'activeButton' title='Пустить бегуна' id='TBTrekRun' style="display:none;">					
						Пустить бегуна
					</li>
				{/if}
				{if $mapType == 'nav'}
					<li class = 'activeButton' title='Мониторинг автотранспорта' id='TBMonStart' >					
						Включить
					</li>	
					<li class = 'activeButton' title='Отключить мониторинг' id='TBMonStop' style="display:none;" '>					
						Выключить
					</li>

				{/if}
				</ul>
				{if $mapType == 'nav'}
					<div id='TBMonActionContaner' class='actionContaner' style="display:none;" >
					<div id='progressShow'></div>					
					<div id='navObjCnt'>Нет объектов</div>
					<div>
						<label for='TBObjPosHide'>Скрыть объекты без геопозиции</label> 
						<input type='checkbox' id='TBObjPosHide' name='TBObjPosHide'  >
					</div>
					<div>
						<label for='TBMonSort'>Сортировать</label> 
						<select id='TBMonSort' name='TBMonSort' >
							<option value='time'>По времени связи</option>	
							<option value='name' selected>По названию</option>	
						</select>					
					</div>
					</div>
				{/if}					
			</div>
		</div>
	</div>
	<div id="objListBox"  style="display:none;" >
	<div class="header">
		<div class='action'>
			{*<div class = 'filterBtn buttFilter activeButton' id='searchBtn2Pad' title='фильтровать список'></div>*}
			<div class = 'closeBtn buttClose activeButton'id='closeBtn_' onClick='$("#objListBox").hide();' title='Скрыть список'></div>
		</div>
		<div class='filterList'  {* style="display:none;" *} >
			<input type = 'text' id="filterString" name="filterString"  />  
			<div class = 'closeFilter cursorPointer'id='clearFilterList' title='Отмена'></div>
		</div>
	</div>
		<div class="content" id='objListContaner'>
		</div>	
	 </div>
	{if $mapType == 'nav'}	 
	<div id="objListBoxNav"  style="display:none;" >	
	<div class="header">

		<div class='filterList'   >
			<input type = 'text' id="filterStringNav" name="filterStringNav"  />  
			<div class = 'closeFilter cursorPointer'id='clearFilterListNav' title='Отмена'></div>
		</div>
	</div>
		<div class="content" id='objListBoxNavContaner'>
		
		</div>	
	 </div>
	{/if}
	<div id="menu2Pad">
	<div class = 'filterBtn buttFilter activeButton' id='searchBtn2Pad' title='фильтровать список'></div>
	<div class = 'closeBtn buttClose activeButton'id='closeBtn' title='Отмена'></div>
	<div class = 'menuPad' id='menuLayerObjList'>
	
		<h3></h3>				
		<div class='objPropertiesContaner'>
			<div  class='userLayerContent' id='ulC_0'>
				<div id="filter2PadContaner" class='filter2PadContaner' >
					<input type = 'text' id="filter2Pad" name="filter2Pad" class="input_fast_search" />  
					<div class = 'closeFilter cursorPointer'id='closeBtnFilter' title='Отмена'></div>
				</div>
				<div  class='objListUL' id='ulO_0'>
				</div>
			</div>
		
		</div>
	</div>		
{if $client.isMng}			
	{include file='gis/managePanel.tpl'}			
{/if}		
	</div>			
	
	<script>
	{literal}
	var mymap = L.map('mapid', {zoomControl : false});	
	{/literal}
	</script>
	<script src="/includes/JS/gis/baseLayers.js" type="text/javascript"></script>	
	<script>
	</script>
