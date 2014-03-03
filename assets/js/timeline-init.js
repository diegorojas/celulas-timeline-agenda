jQuery.fn.conRollover = function(type) {
	var lstart,lend;
	var tstart,tend;

	jQuery(this).append('\n<div class="image_roll_glass"></div><div class="image_roll_zoom"></div>');

	switch (type) {
		case 'top' : lstart='0'; lend='0'; tstart='-100%'; tend='0'; break;
		case 'right' : lstart='100%'; lend='0'; tstart='0'; tend='0'; break;
		case 'bottom' : lstart='0'; lend='0'; tstart='100%'; tend='0'; break;
		case 'left' : lstart='-100%'; lend='0'; tstart='0'; tend='0'; break;
	}

	jQuery(this).find('.image_roll_zoom').css({left:lstart, top:tstart});
	jQuery(this).hover(function(){
		jQuery(this).find('.image_roll_zoom').stop(true, true).animate({left: lend, top:tend},200);
		jQuery(this).find('.image_roll_glass').stop(true, true).fadeIn(200);
	},function() {
		jQuery(this).find('.image_roll_zoom').stop(true).animate({left:lstart, top:tstart},200);
		jQuery(this).find('.image_roll_glass').stop(true, true).fadeOut(200);
	});
};

jQuery(window).load(function() {
	function connectImage() {
		jQuery('.image_rollover_top').unbind('hover').conRollover('top');
		jQuery('.image_rollover_right').unbind('hover').conRollover('right');
		jQuery('.image_rollover_bottom').unbind('hover').conRollover('bottom');
		jQuery('.image_rollover_left').unbind('hover').conRollover('left');
	}

	connectImage();
});

jQuery(window).load(function() {
	function addZero( num ) {
		return ( num >= 0 && num < 10 ) ? '0' + num : num + '';
	}

	var now = new Date(),
		strDate = addZero( now.getDate() ) + '/' + addZero( now.getMonth() + 1 ) + '/' + now.getFullYear();

	jQuery( '#timeline' ).timeline({
		startItem: strDate,
		categories: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro']
	});
});

