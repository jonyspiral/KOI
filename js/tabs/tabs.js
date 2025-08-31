/*
 * El plugin se encarga de crear los divs adicionales.
 * Cada div debe tener su width correspondiente por CSS
 * Se debe crear una estructura como la siguiente:
 * <div class='tabs'>
 * 		<ul>
 * 			<li>Tab 1</li>
 * 			<li>Tab 2</li>
 * 		</ul>
 * 		<div>
 * 			<div id='tab1'>Contenido de la tab 1</div>
 * 		</div>
 * 		<div>
 * 			<div id='tab2'>Contenido de la tab 2</div>
 * 		</div>
 * </div>
 */

$.fn.tabs = function() {
	return this.each(function (){
		$(this).children('ul').append('<div class="moving_bg"></div>');
		$(this).append('<div class="tabs_content"><div class="tabs_slider"></div></div>');
		$(this).children('div:not(".tabs_content"):not(".moving_bg")').appendTo('.tabs_slider');
		var firstItem = $(this).find('ul li:first');
		firstItem.addClass('selected');
		$('.moving_bg').offset({top: firstItem.offset()['top'], left: firstItem.offset()['left']}).width(firstItem.outerWidth());
		$(this).find('.tabs_slider').children('div').invisible().hide();
		$(this).find('.tabs_slider').children('div:first').visible().show();
		$(this).find('ul li').click(function() {
			var divDad = $(this).parents('div:first');
			var divSlider = divDad.find('.tabs_slider');
			var nroItem = $(this).prevAll().size();
			$(this).parent().find('.selected').removeClass('selected');
			$(this).addClass('selected');
			/* Pongo tod_os en invisible y hide para que no ocupen espacio
			 * Luego, a los anteriores al seleccionado los pongo en invisible pero ocupando espacio
			 * Y al que se le hizo click lo pongo visible  */
			divSlider.children('div').invisible().hide();
			divSlider.children('div').each(function(i){
				if (i < nroItem)
					$(this).invisible().show();
				if (i == nroItem)
					$(this).visible().show();
			});
			var background = divDad.find('.moving_bg');
			$(background).stop().animate({
				left: $(this).position()['left'],
				top: $(this).position()['top'],
				width: $(this).outerWidth()
			}, {
				duration: 300
			});

			var margin = divDad.find('.tabs_content').width();
			margin = margin * (nroItem);
			margin = margin * -1;
			
			divSlider.stop().animate({
				marginLeft: margin + "px"
			}, {
				duration: 300
			});
		});
	});
};
