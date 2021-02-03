	<script  type="text/javascript" >
	var titleString = '{$homeDistr.l_name}';
		$('#titleBar').text(titleString);
		var MESS = {};
		var availableNP=[];
		var homeDistr = {};
		var content = '';
		{if $npList}	
		availableNP = [
			{section name=list loop=$npList}
			{literal}{{/literal}
			label:"{$npList[list].l_name}", value:"{$npList[list].l_id}"
			{literal}}{/literal}
		{if !$smarty.section.list.last},{/if}
		{/section}
		];		
		{/if}		
		{if $homeDistr}
			homeDistr.name= "{$homeDistr.l_name}";
			homeDistr.id= "{$homeDistr.l_id}";
		{/if}
		
	</script>	
<script src="/includes/jquery/jquery.mask.min.js"></script>	
 <div id="opMenu"  >
<div id="tabs">
  <ul> 
    <li><a href="#zhournalContaner"><span id='zhournalLabel'>Журнал отчётов</span></a></li>
    <li><a href="#formContaner"><span id='formLabel'>Новый отчёт</span></a></li>
    <li><a href="#messContaner"><span id='MessLabel'>Просмотр отчёта</span></a></li>
    <li><a href="#objListContaner"><span id='MessLabel'>Список объектов</span></a></li>
  </ul>
  <div id="messContaner">
	  <div id="messBody" class="messBody">
	  </div>
  </div>
  <div id="zhournalContaner">
	  <div id="mainList">
	  
	  </div>
  </div>  
  <div id="objListContaner">
{*	  <div id="objList">*}
	  <div id="mainList">

	  
	  </div>
  </div>
  <div id="formContaner">
	  <div id="formBox">
	  <div class='formHeader'>Название отчёта</div>
		{*	<div id="tabs2lvl">
				<ul id="lvl2Contaner" class="clearFix" > 

					<li class="state-default cursorPointer" id="PMO_objList">Список объектов</li>
					<li class="state-active" id="PMO_objAdd">Новый объект</li>
				</ul>
			</div>
		<div id="tab2lvlList">
			Список объектов ->
		</div>*}
		<div id="formContent">
			<form  id="mainForm" >
				<div id="container">
				</div>
			</form> 			
			<div id="formBox_ok_btn">        
				<span id="formSubmit" class="site_enter_panel_check_btn" title="Оk"> &nbsp;Отправить&nbsp; </span>
			</div> 
		</div>
	  </div>
  </div>
</div>
 
 