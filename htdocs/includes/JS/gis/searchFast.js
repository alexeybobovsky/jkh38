
var emptySearchStr = 'Введите поисковый запрос...';

var exluded = [' ', 'nbsp', '!','@','#','$','%','^','&','*','(',')','-','_','=','`','~',':',';','\'','"','\\','/','|','?','.',',','<','>', '№','%']; 
	 this.str_replace  = function( search, replace, subject ) 
		{
		// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   improved by: Gabriel Paderni

		if(!(replace instanceof Array)){
			replace=new Array(replace);
			if(search instanceof Array){//If search	is an array and replace	is a string, then this replacement string is used for every value of search
				while(search.length>replace.length){
					replace[replace.length]=replace[0];
					}
				}
			}
		if(!(search instanceof Array))search=new Array(search);
		while(search.length>replace.length){//If replace	has fewer values than search , then an empty string is used for the rest of replacement values
			replace[replace.length]='';
			}
		if(subject instanceof Array){//If subject is an array, then the search and replace is performed with every entry of subject , and the return value is an array as well.
			for(k in subject){
				subject[k]=str_replace(search,replace,subject[k]);
				}
				return subject;
			}	
		for(var k=0; k<search.length; k++){
			var i = subject.indexOf(search[k]);
			while(i>-1){
				subject = subject.replace(search[k], replace[k]);
				i = subject.indexOf(search[k],i);
				}
			}
		return subject;
		}
function searchLoadStartF()
	{
//	document.getElementById('searchIndicatorImg').src = loadImg.src;
	document.getElementById('resMessageF').innerHTML = '';	
//	alert($('#topToolBar').offset().top + document.getElementById('searchFast').offsetTop + document.getElementById('searchFast').offsetHeight);

	}
function searchLoadEndF()
	{
//	document.getElementById('searchIndicatorImg').src = emptyImg.src;	
	}
function clearSearchbarF(id)
	{
//	document.getElementById('resMessageF').innerHTML = '';	
//	document.getElementById('searchFast').value = emptySearchStr;
//	$("#" + id).innerHTML = '';
	$("#" + id).val(emptySearchStr);
	}
function minCharachters(moreChars)
	{
	$("#resMessageF").html('еще ' + moreChars + ROUT.getCorrectDeclensionRu(moreChars,  ' символ', ' символа', ' символов' ) + ' ...');
	}
function noMatchesF()
	{
	document.getElementById('resMessageF').innerHTML = 'Ничего не найдено';
	}
function formatStreetLocal(row, i, num, q) 
	{
	$("#resMessageF").innerHTML = '';
	var result = '';
//	console.log(showProperties( row));
/*	if (typeof row == "string")
		{*/
		var string = row[0].toLowerCase();
		var searched = str_replace(exluded, '', q.toLowerCase());	
	//	var searched = row[2].toLowerCase();	
		var start = string.indexOf(searched);
		var end = start + searched.length;
	//	type += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		result = '<div class= "multitypeContaner"><div class="findResult">';
		if(start >=0) 
			result += string.substring(0,   start) + '<span class="ac_searched">' +  searched + '</span>'  + string.substring(end); 
		else
			result += row[0];		
		result += '</div></div>';				
/*		}*/
	console.log(row + '; ' + i + '; ' + num + '; ' + q + '; result = ' + result);
	return result;	
		
	}
function formatStreetE(row, i, num) 
	{
		console.log(row + '; ' + i + '; ' + num);
	$("#resMessageF").innerHTML = '';
	var type = '';
	var string = row[0].toLowerCase();
	var searched = str_replace(exluded, '', row[1].toLowerCase());	
//	var searched = row[2].toLowerCase();	
	var start = string.indexOf(searched);
	var end = start + searched.length;
//	type += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	result = '<div class= "multitypeContaner"><div class="findResult">';
	if(start >=0) 
		result += string.substring(0,   start) + '<span class="ac_searched">' +  searched + '</span>'  + string.substring(end); 
	else
		result += row[1];		
	result += '</div><div  class="findType typeName_' +row[0] + '">' + type + '</div></div>';				
	return result;	
	}
function formatStreetF(row, i, num) 
	{
//	alert(showProperties(row));
//	alert(showProperties($.browser, 'browser'));

	document.getElementById('resMessageF').innerHTML = '';
	var result;
	var type = row[0];
	var string = row[3].toLowerCase();
	var searched = str_replace(exluded, '', row[2].toLowerCase());	
//	var searched = row[2].toLowerCase();	
	var start = string.indexOf(searched);
	var end = start + searched.length;
	if ( (row[0] == 'newsName') || (row[0] == 'newsBody') )
		type = 'новость';
	if ((row[0] == 'docName') || (row[0] == 'docBody'))
		type = 'статья';
	if (row[0] == 'catFirm')
		type = 'рубрика организаций';
	if (row[0] == 'catDocs')
		type = 'рубрика статей';
	if (row[0] == 'catNews')
		type = 'рубрика новостей';
		
	if (row[0] == 'firmInfo')
		type = 'описание организации';
	if (row[0] == 'firmName')
		type = 'название организации';
	if (row[0] == 'street')
		type = 'адрес организации';
	if (row[0] == 'phone')
		type = 'телефон организации';
	if (row[0] == 'firmWWW')
		type = 'сайт организации';
	if (row[0] == 'constrName')
		type = 'название стройки';
	if (row[0] == 'constrInfo')
		type = 'описание стройки';
	if (row[0] == 'constrSales')
		type = 'отдел продаж';
	if (row[0] == 'constrAdr')
		type = 'адрес стройки';
	if (row[0] == 'constrWWW')
		type = 'сайт стройки';
	type += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	result = '<div class= "multitypeContaner"><div class="findResult">';
	if(start >=0) 
		result += string.substring(0,   start) + '<span class="ac_searched">' +  searched + '</span>'  + string.substring(end); 
	else
		result += row[1];		
	result += '</div><div  class="findType typeName_' +row[0] + '">' + type + '</div></div>';				
	return result;
	}
function selectNameF(str) 
	{	
	GIS.showSingleObj(str.extra[2], str.extra[1]);
	$("#cityLabel").text(str.extra[3]);
	UI.toggleSearchBar('searchCityContaner');
	UI.readyToDetailSearch();
//	console.log(ROUT.getWikiContent(str.extra[3]));
//	alert(showProperties(str.extra));
//	window.location = str.extra[3];
/*	if(str.selectValue == 'layer')
		clickLayer(str.extra[2]);
	if(str.selectValue == 'org')
		showOrgObjects(str.extra[2]);
	if(str.selectValue == 'phone')
		showObject(str.extra[3], 1);
	if(str.selectValue == 'street')
		showObject(str.extra[3], 1);
	if(str.selectValue == 'newsName')
		showObject(str.extra[3], 1);
	if(str.selectValue == 'newsBody')
		showObject(str.extra[3], 1);
	document.getElementById('searchFast').value = emptySearchStr;
	document.getElementById('searchFast').blur();*/
	
	}
function initSearch(id) 
	{
//	console.log($(location).attr('href'));
	var action = ($(location).attr('href') == 'http://gis2.localhost/') ? '/getFromFile.php' :'/gis2/getFromFile.php';
	$("#" + id).autocomplete(action,
//	$("#searchFast").autocomplete(availableTags ,
		{
		minChars:2,
		lineSeparator:"##",
		cellSeparator:"**",
		maxItemsToShow:20,
		selectOnly:true,
		width:280,
		fixedContaner:'topToolBar',
		formatItem:formatStreetE,
		minCharachters:minCharachters,
		noMatchesFound:noMatchesF,
		loadingStart:searchLoadStartF,
		loadingEnd:searchLoadEndF,
		onItemSelect:selectNameF,
		extraParams:{type: id}
		}
		);		
	}
function initSearchLocal(id, data) 
	{
//	console.log(data);
	$("#" + id).autocomplete('',
//	$("#searchFast").autocomplete(availableTags ,
		{
		minChars:1,
		lineSeparator:"##",
		cellSeparator:"**",
		maxItemsToShow:20,
		selectOnly:true,
		width:280,
		matchContains:true,
		fixedContaner:'topToolBar',
		formatItem:formatStreetLocal,
		minCharachters:minCharachters,
		noMatchesFound:noMatchesF,
		loadingStart:searchLoadStartF,
		loadingEnd:searchLoadEndF,
		onItemSelect:selectNameF,
		extraParams:{type: id}
		},
		data
		);		
	}
	
	