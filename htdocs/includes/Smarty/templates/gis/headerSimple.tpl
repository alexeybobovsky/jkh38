<html>
<head>
	<meta name="description" content="{$meta.description}" />
	<title>{$title} | {$header.siteName}</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="yandex-verification" content="70eb9cd711d947a3" />
	<link rel="stylesheet" href="/src/design/main/css/gis/style.css" type="text/css">
	<link rel="stylesheet" href="/src/design/main/css/gis/popup.css" type="text/css">
	<link rel="stylesheet" href="/src/design/main/css/gis/jquery-ui.structure.min.css" type="text/css" />
	<link rel="stylesheet" href="/src/design/main/css/gis/jquery-ui.min.css" type="text/css" />
	<link rel="stylesheet" href="/src/design/main/css/gis/jquery-ui-timepicker-addon.min.css" type="text/css" />
	{*<script src="/includes/jquery/jquery.js"></script>*}
	<script src="/includes/jquery/jquery-3.3.1.min.js"></script>
	<script src="/includes/JS/gis/service.js" type="text/javascript"></script>		
	<script src="/includes/JS/gis/userInterface.js" type="text/javascript"></script>	
{if $pageType == 'operator' or $pageType == 'adm'}	
	<script src="/includes/jquery/jquery-ui.min.js" type="text/javascript"></script>
	<script src="/includes/jquery/jquery-ui-timepicker-addon.min.js" type="text/javascript"></script>
	<script src="/includes/jquery/jquery-ui-timepicker-ru.js" type="text/javascript"></script>
	<script src="/includes/jquery/jquery.dform-1.1.0.min.js" type="text/javascript"></script>	
	<script src="/includes/jquery/jquery.serialize-object.min.js" type="text/javascript"></script>		
{*	<script src="/includes/JS/gis/forms.js" type="text/javascript"></script>		*}
{/if}
{if $pageType == 'operator'}
{*	<script src="/includes/JS/gis/forms.js" type="text/javascript"></script>		*}
	<script src="/includes/JS/gis/formsOd.js" type="text/javascript"></script>		
{elseif $pageType == 'adm'}
	<script src="/includes/JS/gis/formsAdm.js" type="text/javascript"></script>	

{/if}	
	<script  type="text/javascript" >
	var pageType = '{$pageType}';

var titleString = 'Иркутская область';
var UI = new userInterface();   	
var ROUT =	new Routine();   	
var userId = '{$header.userId}';
if ((pageType == 'operator')||(pageType == 'adm')) {
	var FORM = 	new formRoutine();	
}
	{literal}
$(document).ready(function(){
	console.log('ready');
	UI.setSize();   
	$("#waitBox").css({'display' : 'none'});	
	$('#mapid').css({'height' : UI.documentHeight-($('#topContaner').height()+35)});				
	$('#mapid').css({'width' : UI.documentWidth});
	$('#mapid').css({'cursor': 'wait'});
	$('#menuListContaner').css({'top' : '32px'});
	$('#menuListContaner').css({'display' : 'block'});
	
	UI.setPanel();   
	titleString = 'АСУ СОКИТ ';
	if(pageType == 'login'){
		titleString += '.    Авторизация';
		UI.togglePanel('', 'enterPanel', 1, ''); 
		console.log('login view');
	} else if (pageType == '404') {
		titleString += '.    Ошибка: указан неверный адрес!';

		UI.toCenter('contaner_404'); 
		console.log('404 view');
	} else if ((pageType == 'operator')||(pageType == 'adm')) {
		$.timepicker.regional['ru'];
		//console.log($.timepicker);
		$.dform.subscribe("datetimepicker",
			function(options, type)
			{
			//console.log(arguments);
				if (type == "text") {
					this.datetimepicker(options);
				}
				}, $.isFunction($.fn.datetimepicker)
			);	
		
		{/literal}
		var lId = '{$homeDistr.l_id}';
		var lName = '{$homeDistr.l_name}';
		{literal}			
		$( "#tabs" ).tabs();
		$( "#formBox" ).css({'height':'auto', 'z-index':'inherit', 'position':'relative'});
		$( "#formBox" ).show();
		$( "#tabs" ).tabs( "disable", "#messContaner" );
		if(pageType == 'operator'){
			titleString += '.    АРМ Оператора.    ' + lName;
			console.log('operators view');
			FORM.genMessListOperator(lId); 
//			console.log(REASON);
			$( "#tabs" ).on( "tabsactivate", function( event, ui ) {FORM.panelChanged(ui.oldPanel[0].id, ui.newPanel[0].id, event);});
		} else if(pageType == 'adm') {
			titleString += '.    АРМ Администрации -    ' + lName;
			console.log('adm view');
			FORM.genMessListAdm(lId); 
			
			
			
			$( "#tabs" ).tabs( "disable", "#formContaner" );
			$( "#tabs" ).tabs( "disable", "#objListContaner");
			$( "#tabs" ).on( "tabsactivate", function( event, ui ) {FORM.panelChangedAdm(ui.oldPanel[0].id, ui.newPanel[0].id, event);});			
		}
	}
	$('#titleBar').text(titleString);
	}	
	);	 
	</script>	
	{/literal}
</head> 
<body>
<div id="waitBox" {*style="display:none;"*} ><img id="waitImg" src="/src/design/main/img/blueBars.gif" border="0" alt="" /></div>
<div id="topToolBarContaner">
<div id="topToolBar">
	<div id='showMenu'>
		{*<span class='' title="Список слоёв объектов" id="show_accList"> Слои </span>*}
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
