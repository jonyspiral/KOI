/*
 * El plugin se encarga de crear los divs adicionales.
 * Cada div debe tener su width correspondiente por CSS
 * Se debe crear una estructura como la siguiente:
 * <div class='solapas'>
 * 		<ul>
 * 			<li>Tab 1</li>
 * 			<li>Tab 2</li>
 * 		</ul>
 * 		<div>
 * 			<div id='1' style='width: 100px;'>Contenido de la tab 1</div>
 * 			<div id='2' style='width: 100px;'>Contenido de la tab 2</div>
 * 		</div>
 * </div>
 */
$.fn.restart = function(){
	//Llamarla $('.solapas').restart();
	if ($('.tituloSolapa.selected').length != 0){
		$('.contenidos').hide();
		$('.tituloSolapa.selected, .contenidoSolapa.selected').removeClass('selected');
	}
};

$.fn.solapas = function(options) {
	//Opciones por defecto
	var defaults = {
		precall:		null,	//Es una función que se ejecuta antes de cambiar de solapa (sirve para llenar el contenido)
		selectedItem:	-1,		//Es el item que está abierto por defecto. -1 es ninguno
		heightSolapas:	-1,		//Es la altura de las solapas. Si está en -1, intenta poner la altura solito (muchos problemas)
		fixedHeight:	-1		//Es la altura del contenedor. Si es -1 entonces no tiene altura fija ni customScroll 
	};
	//Reemplazo las opciones default por las ingresadas por el usuario
	var options = $.extend({}, defaults, options);


	return this.each(function (){
		$(this).children('ul:first').addClass('titulos').children('li').addClass('tituloSolapa');
		var firstSolapa = $('.tituloSolapa:first');
		var height = options.heightSolapas;
		if (height == -1) {
			height = firstSolapa.height();
			height += funciones.toInt(firstSolapa.css('paddingBottom')) + funciones.toInt(firstSolapa.css('paddingTop'));
			height += funciones.toInt(firstSolapa.css('borderBottom')) + funciones.toInt(firstSolapa.css('borderTop'));
		}
		$(this).children('ul:first').height(height);
		$(this).children('div:first').addClass('contenidos bAllOrange cornerB5').children('div').addClass('contenidoSolapa');
		if (options.fixedHeight != -1)
			$(this).children('div:first').addClass('customScroll').css('height', options.fixedHeight);
		$(this).find('ul li').unbind('click').click(function() {
			var obj = $(this);
			var divsContenido = $(this).parents('div:first').find('.contenidos').show().children('div'); //El show es importante
			var nroItem = $(this).prevAll().size();
			try {options.precall(obj);} catch (ex) {}
			obj.parent().find('.selected').removeClass('selected');
			obj.addClass('selected');
			divsContenido.hide();
			divsContenido.each(function(i){
				if (i == nroItem) {
					$(this).show().addClass('selected');
				} else {
					$(this).removeClass('selected');
				}
			});
		});
		if (options.selectedItem >= 0) {
			var selectedItem = $(this).find('ul li:eq(' + options.selectedItem + ')');
			selectedItem.click();
		} else
			$(this).children('div:first').hide();
	});
};