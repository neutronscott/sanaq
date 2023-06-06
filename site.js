/* try not to make this too messy, mute.. */

/* omnious key handler */
$(document).keydown(function(event){
	var cur=-1;
	switch (event.keyCode) {
	case 37: case 38:	/* left,up: rewind in group */
		var siblings=$(':focus').closest('div').find('a');
		var max=$(siblings).length - 1;
		/* get index of :focus in group */
		$.each($(siblings),function(i,dom){if ($(this).is(":focus")) {cur=i;return false;}});
		var next = ((cur - 1) < 0) ? max : (cur - 1);
		$(siblings).filter(':eq(' + next + ')').focus();
		break;
	case 39: case 40:	/* right,down: advance in group */
		var siblings=$(':focus').closest('div').find('a');
		var max=$(siblings).length - 1;
		/* get index of :focus in group */
		$.each($(siblings),function(i,dom){if ($(this).is(":focus")) {cur=i;return false;}});
		var next = ((cur + 1) > max) ? 0 : (1 + cur);
		$(siblings).filter(':eq(' + next + ')').focus();
		break;
	case 9: /* tab: change from top/bottom group */
		if ($(':focus').filter(':visible').length == 0) {
			// we lost focus
			$('.menuoptions .default').filter(':visible').focus();
		} else if ($(':focus').closest('div').hasClass('menuoptions')) {
			var bottomSelected=$(':focus').closest('.menudialog').find('.menubottomtext .selected');
			$(bottomSelected).first().focus();
		} else {
			var topSelected=$(':focus').closest('.menudialog').find('.menuoptions .selected');
			$(topSelected).first().focus();
		}
		event.preventDefault();
		break;
	case 27: /*ESC*/
		$('.activemenu').hide();
		$('.activemenu').removeClass('activemenu');
		event.preventDefault();
		break;
	case 191: /* /? */
		if (event.shiftKey == false)
			break;
		$('#submenu-help').show()
			.addClass('activemenu');
		$('#submenu-help .default').first().focus();
		return false;
		break;
	default:
		var hotkeys=$(':focus').closest('div').find('span.hotkey');
		$.each($(hotkeys),function(i){
			if ($(this).text().toUpperCase().charCodeAt() == event.keyCode) {
				$(this).closest('a').focus();
				return false;
			}
		});
		break;
	}
});

/* since this script is in head, we needa wait til ready to do anything with elements. */
$(document).ready(function(){
	/* default state */
	$('.submenu').hide();
	$('#url').focus();

	/* select all default items. */
	$('div.menudialog .default').addClass('selected');
	
	/* Enable JavaScript features */
		/* remove 'nojs' style from links */
	$.each($('a'),function(i,dom){
		$(this).removeClass('nojs');
	});
		/* use absolute position so submenus overlay main menu */
	$('.submenu').css({position:'absolute'});
		/* Submit button from main list */
	$('#submit').closest('li').remove();

	/* events */
		/* main items; TODO: generic for entire class */
	$('#url').on('click',function(){
		$('#submenu-url').show()
			.addClass('activemenu');
		$('#submenu-url .default').first().focus();
		return false;
	});
	$('#upload').click(function(){
		$('#submenu-upload').show()
			.addClass('activemenu');
		$('#submenu-upload .default').first().focus();
		return false;
	});
	$('#paste').click(function(){
		$('#submenu-paste').show()
			.addClass('activemenu');
		$('#submenu-paste .default').first().focus();
		return false;
	});

		/* bottom buttons; TODO: validate submissions? */
//	$('.button-submit').click(function(){$('form').submit();return false});
	$('.button-exit').click(function(){
		$(this).closest('.activemenu').hide()
			.removeClass('activemenu');
		var active=$('.activemenu .default').filter(':visible');
		if ($(active).length > 0)
			$(active).first().focus();
		else	//never apply .activemenu to mainmenu for some reason... hrm
			$('#mainmenu .default').first().focus();
		return false;
	});
	$('.button-help').click(function(){
		$('#submenu-help').show()
			.addClass('activemenu');
		$('#submenu-help .default').first().focus();
		return false;
	});
	/* don't just hover, focus that mofo */
	$('a').mouseover(function(){$(this).focus();});
	
	/* leave an item highlighted in both top/bottom section. */
	$('div.menuoptions a').focus(function(){
		$(this).closest('.menuoptions').find('a.selected').removeClass('selected');
		$(this).addClass('selected');
	});
	$('div.menubottomtext a').focus(function(){
		$(this).siblings('.selected').removeClass('selected');
		$(this).addClass('selected');
	});
});
$(document).on('click', '.button-submit', function() {
   $('form#f1').submit();
});
