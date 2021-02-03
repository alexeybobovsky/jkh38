Array.prototype.max = function() {return Math.max.apply(null, this);};
Array.prototype.min = function() {return Math.min.apply(null, this);};
function Routine()	
	{

	 this.number_format = function( number, decimals, dec_point, thousands_sep ) 
		{	// Format a number with grouped thousands
		// 
		// +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
		// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +	 bugfix by: Michael White (http://crestidg.com)

		var i, j, kw, kd, km;

		// input sanitation & defaults
		if( isNaN(decimals = Math.abs(decimals)) ){
			decimals = 2;
		}
		if( dec_point == undefined ){
			dec_point = ",";
		}
		if( thousands_sep == undefined ){
			thousands_sep = ".";
		}

		i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

		if( (j = i.length) > 3 ){
			j = j % 3;
		} else{
			j = 0;
		}

		km = (j ? i.substr(0, j) + thousands_sep : "");
		kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
		//kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
		kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");


		return km + kw + kd;
		}

	 this.getRandomInt = function(min, max)
		{
		return Math.floor(Math.random() * (max - min + 1)) + min;
		}
	  this.trim = function( str, charlist ) {	// Strip whitespace (or other characters) from the beginning and end of a string
		// 
		// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   improved by: mdsjack (http://www.mdsjack.bo.it)
		// +   improved by: Alexander Ermolaev (http://snippets.dzone.com/user/AlexanderErmolaev)
		// +	  input by: Erkekjetter
		// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		charlist = !charlist ? ' \\s\xA0' : charlist.replace('/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g', '\$1');
		var re = new RegExp('^[' + charlist + ']+|[' + charlist + ']+$', 'g');
		return str.replace(re, '');
		}

	 this.isNumeric = function(n) 
		{
		return !isNaN(parseFloat(n)) && isFinite(n);
		}
	 this.is_string = function(mixed_var) 
		{
	  //  discuss at: http://phpjs.org/functions/is_string/
	  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  return (typeof mixed_var === 'string');
		}	
	 this.str_replace  = function( search, replace, subject ) 
		{
		// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   improved by: Gabriel Paderni
		//console.log(arguments);
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
	 this.GetStrPrt = function(str, del, indx)
		{
		strArr1 = str.split(del);
		var ret = strArr1[indx];
		return ret;
		}
	 this.showProperties = function(obj, objName) 
		{
		  var result = "The properties for the " + objName + " object:" + "\n";
		  
		  for (var i in obj) {result += i + " = " + obj[i] + "\n";}
		  
		  return result;
		}

	 this.openAuthPanel = function(obj)
		{
	//	alert('Пока этот функционал недоступен');
		
		var winName = 	'authWindow';
		var url = 		'/login/' + obj.id;
		var left, top, width, height;
		width = 		500;
		height = 		500;
		left =  		UI.windowWidth/2 - width/2;
		top =  			UI.windowHeight/2 - height/2;
		var winParams = 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',resizable=yes,scrollbars=yes,status=no';
		var newWin = window.open(url, winName , winParams);
		newWin.onClose = function(){window.opener.location.reload(true)};
		newWin.focus();
		}
	 this.submitAuthForm = function()
		{
		var error = 0;
		if(document.getElementById('Username').value == '')
			error += 1;
		if(document.getElementById('Password').value == '')
			error += 2;
		if(error == 0)	
			document.getElementById('authForm').submit();

		}	
	this.getWikiContent = function(str)
		{
		$.post("/gis2/getFromFile.php/", {type:'getWiki', str:str}, function(str)
			{
			return str;
			});				
		
		}

	this.in_array = function(needle, haystack, strict) 
		{  
		var found = false, key, strict = !!strict;	 
		for (key in haystack) {
			if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
				found = true;
				break;
			}
		} 
		return found;
		}
	this.GetStrPrtLast = function(str, del, offset) 
		{
		if(offset === undefined)
			var offset = 0;
		strArr1 = str.split(del);
		var ret = strArr1[(strArr1.length-1 - offset)];
		return ret;		
		}			
	
	this.rand = function( min, max ) 
		{	
		if( max ) 
			{
			return Math.floor(Math.random() * (max - min + 1)) + min;
			} else {
			return Math.floor(Math.random() * (min + 1));
			}
		}
	
	this.getCorrectDeclensionRu = function(num, word_0, word_1, word_2)
		{
//		console.log(arguments);
		var tenSec, ret;
		if(num>10000)
			num -= 10000;
		if (num>1000)
			num -= 1000;		
		if (num>100)
			num -= 100;		
		if(num>10)
			{
			tenSec = (Math.round(num/10 + 0.5) == Math.round(num/10)) ? Math.round(num/10)-1 : Math.round(num/10);
			if(((num>=10)&&(num<20))||(((num - (tenSec*10)) >=5 )||(num - (tenSec*10) ==0 )))
				ret = word_2;
			else if((num - (tenSec*10))>1)
				ret = word_1;
			else
				ret = word_0;
			}
		else
			{
			if((num >=5 )||(num ==0 ))
				ret = word_2;
			else if(num>1)
				ret = word_1;
			else
				ret = word_0;
			}
		return ret;
		}	

	}

