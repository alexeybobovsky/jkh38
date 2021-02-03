var indexLocal = function(id, label){
	this.label = label.replace(/\&quot\;/g, '"' );
	this.id = id;
}
var emptySearchStr = 'Введите поисковый запрос...';

var exluded = [' ', 'nbsp', '!','@','#','$','%','^','&','*','(',')','-','_','=','`','~',':',';','\'','"','\\','/','|','?','.',',','<','>', '№','%']; 

function initSearchObj(data) 
	{
	var $elem = $( "#searchObj" ).autocomplete({
			
		  source:  function(request, response) {
/*			console.log('response');
			console.log(showProperties(response));*/
			var results = $.ui.autocomplete.filter(data, request.term);
			response(results.slice(0, 30));
		  }
		}		
		);
	elemAutocomplete = $elem.data("ui-autocomplete") || $elem.data("autocomplete");	
	$elem.on( "autocompleteselect", function( event, ui ) {
/*		console.log(event);
		console.log(showProperties(event));*/
		GIS.showObjUL(ui.item.id);
		UI.toggleSearchBar('searchObjContaner');
	} );
	if (elemAutocomplete) {
		elemAutocomplete._renderItem = function (ul, item) {
			var newText = String(item.value).replace(
                new RegExp(this.term, "gi"),
                "<span class='ui-state-highlight'>$&</span>");
			var adr = UObjects[item.id].info.match( /<span id="adr">(.+)<\/span>/i );
			if(adr != null)
				var adrDet = adr[1].match( /,\s(.+)\s/i );
			else{
				var adrDet = [];
				adrDet[1] = '';
			}
//			console.log(adr);
			return $("<li></li>")
            .data("item.autocomplete", item)
//            .append("<div>" + newText + ' (id=' + adr + ')' + "</div>")
            .append("<div>" + newText + " <span class='searchResultAdres'>" + adrDet[1] + "</span>" + "</div>")
            .appendTo(ul);
		};		
	}
	}

	
	