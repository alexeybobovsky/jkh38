	<script  type="text/javascript" >
	var titleString = '{$homeDistr.l_name}';
		$('#titleBar').text(titleString);
		var MESS;
		var availableNP=[];
		var listNP=[];
		var jkhObj=[];
		var availableOrg=[];
		var typeOf=[];
		var homeDistr = {};
		var REASONS=[];
		var content = '';
		{if $sectors}	
		typeOf = [
			{section name=list loop=$sectors}
			{literal}{{/literal}
			label:"{$sectors[list].name}", value:"{$sectors[list].id}"
			{literal}}{/literal}
		{if !$smarty.section.list.last},{/if}
		{/section}
		];		
		{/if}
		{if $npList}	
		listNP = {literal}{{/literal}
			{section name=list loop=$npList}			
			"n_{$npList[list].l_id}" : "{$npList[list].l_name}"			
			{if !$smarty.section.list.last},{/if}
			{/section}
		{literal}}{/literal};		
		{/if}			
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
		{if $orgList}	
		availableOrg = [
			{section name=list loop=$orgList}
			{literal}{{/literal}
			label:'{$orgList[list].name}', value:'{$orgList[list].id}'
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
    <li><a href="#zhournalContaner"><span id='zhournalLabel'>Журнал оперативных донесений</span></a></li>
    <li><a href="#formContaner"><span id='formLabel'>Новое оперативное донесение</span></a></li>
    <li><a href="#messContaner"><span id='MessLabel'>Просмотр донесения</span></a></li>
  </ul>
  <div id="messContaner">
	  <div id="messBody" class='messBody'>
	  </div>
  </div>
  <div id="zhournalContaner">
	  <div id="mainList">
	  
	  </div>
  </div>
  <div id="formContaner">
	  <div id="formBox">
		<form  id="mainForm" >
			<div id="container">
			</div>
		</form> 			
		<div id="formInfo"> 
			<span class='red' style='color: #F22;' title='Пояснение по обязательным параметрам'>*</span> В случае отсутствия информации для обязательного поля, допускается ставить знак <i>тире</i>: "<b>-</b>"
		</div> 
		<div id="formBox_ok_btn">        
			<span id="formSubmit" class="site_enter_panel_check_btn" title="Оk"> &nbsp;Отправить&nbsp; </span>
		</div> 
	  </div>
  </div>
</div>
 
 