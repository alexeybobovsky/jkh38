/*
/*
	Sliding labels is open source code by Tim Wright of CSSKarma.com
	Use as you see fit, I'd like it if you kept this in the code, but 
	basically share it and don't be a jerk.
	
	Support:
	http://www.csskarma.com/blog/sliding-labels
*/

$(function(){
$('form#info .slider label').each(function(){
	var labelColor = '#999';
	var restingPosition = '5px';
	
	// style the label with JS for progressive enhancement
	$(this).css({
		'color' : labelColor,
		 	'position' : 'absolute',
	 		'top' : '6px',
			'left' : restingPosition,
			'display' : 'inline',
    		'z-index' : '99'
	});
	
	// grab the input value
	var inputval = $(this).next('input').val();
	
	// grab the label width, then add 5 pixels to it
	var labelwidth = $(this).width();
	var labelmove = labelwidth + 5;
	
	//onload, check if a field is filled out, if so, move the label out of the way
	if(inputval !== ''){
		$(this).stop().animate({ 'left':'-'+labelmove }, 1);
	}    	
	
	// if the input is empty on focus move the label to the left
	// if it's empty on blur, move it back
	$('input').focus(function(){
		var label = $(this).prev('label');
		var width = $(label).width();
		var adjust = width + 5;
		var value = $(this).val();
		
		if(value == ''){
			label.stop().animate({ 'left':'-'+adjust }, 'fast');
		} else {
			label.css({ 'left':'-'+adjust });
		}
	}).blur(function(){
		var label = $(this).prev('label');
		var value = $(this).val();
		
		if(value == ''){
			label.stop().animate({ 'left':restingPosition }, 'fast');
		}	
		
	});	    	
})
});