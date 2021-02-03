jQuery.filterSimple = function(input, options) {
/*		console.log('filterSimple');
		console.log(input);
		console.log(options);*/
		
var timeout = null;
	
var $input = $(input).attr("filterSimple", "off");
	$(options.closeBtn).hide();
	$(options.closeBtn).on('click', function(event){clear(); $input.focus();});
	$(options.outerControl).on('click', function(event){
		if(options.state != 0){
			clear(); 
			$input.blur();
		}});
	$input.val(options.startMessage);	

	$input
	.keydown(function(e) {
		switch(e.keyCode) {
			case 38: // up
				break;
			case 40: // down
				break;
			case 9:  // tab
			case 13: // return
				break;
			default:
				if (timeout) clearTimeout(timeout);
				timeout = setTimeout(function(){
						onChange();
					}, options.delay);
				break;
		}
	})
	.focus(function(e) {
		if(options.state == 0){
			$input.val('');
		}
		else{
		}
			
	})
	.blur(function(e) {
		if(options.state == 0){
			$input.val(options.startMessage);
		}
		else{
		}
			
	})
	;
	function onChange() {
		var strReg = $input.val().replace(/\s/g, '').toLowerCase(); 
		if(strReg.length == 0){
			clear();
		}else{
			options.state = 1;
			$(options.closeBtn).show();
			$(options.contaner + ' li').each(function(indx, element){			
				if(element != undefined)
					{
					var elStr = $(options.contaner + ' #' +element.id + ' .contObjList span').text().replace(/[^\wа-яё]/gi, '').toLowerCase(); 
					if(elStr.indexOf(strReg) >= 0){	
						$(options.contaner + ' #' +element.id).show();
					}
					else{
						$(options.contaner + ' #' +element.id).hide();
					}				
				}
			});
		}
	}
	function clear(){		
		options.state = 0;
		$(options.closeBtn).hide();		
		$(options.contaner + ' li').show();
		$input.val('');
	}
}
jQuery.fn.filterSimple = function(options) {
	// Make sure options exists
	options = options || {};
	options.outerControl = options.outerControl || '#searchBtn2Pad';
	options.closeBtn = options.closeBtn || '#closeBtnFilter';
	options.contaner = options.contaner || '#ulO_0';
	options.delay = options.delay || 200;
	options.state = options.state || 0; //если нет запроса
	options.startMessage = options.startMessage || 'Введите поисковый запрос...'; //если нет запроса
	
	this.each(function() {
		var input = this;
		new jQuery.filterSimple(input, options);
	});


	// Don't break the chain
	return this;
}

