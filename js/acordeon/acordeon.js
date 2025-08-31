/*
 * El plugin se encarga de crear los divs adicionales.
 * Cada div debe tener su width correspondiente por CSS
 * Se debe crear una estructura como la siguiente:
 * <div class='tabs'>
 * 		<ul>
 * 			<li>Tab 1</li>
 * 			<li>Tab 2</li>
 * 		</ul>
 * 		<div id='1' style='width: 100px;'>Contenido de la tab 1</div>
 * 		<div id='2' style='width: 100px;'>Contenido de la tab 2</div>
 * </div>
 */

$.fn.enableAcordeon = function() { 
	//Llamarla $('.acordeon').enableAcordeon();
	this.find('.tituloSeccion').removeClass('disabled');
	return this.acordeon();
};
$.fn.disableAcordeon = function() { 
	//Llamarla $('.acordeon').disableAcordeon();
	$(this).compressAcordeon();
	return this.find('.tituloSeccion').addClass('disabled').unbind('click');
};
$.fn.compressAcordeon = function(){
	//Llamarla $('.acordeon').compressAcordeon();
	if ($('.tituloSeccion.selected').length != 0){
		$('.contenidoSeccion.selected').slideToggle(function(){
			$('.tituloSeccion.selected, .contenidoSeccion.selected').removeClass('selected');
		});
	}
};
$.fn.expandAcordeon = function(){
	//Llamarla $('.acordeon').expandAcordeon();
	if ($('.tituloSeccion.selected').length != $('.tituloSeccion').length){
		$('.tituloSeccion').not('.selected').each(function(){
			$.goSlideDown($(this));
			$('.tituloSeccion, .contenidoSeccion').not('.selected').addClass('selected');
		});
	}
};

$.fn.acordeon = function(options) {
	//Opciones por defecto
	var defaults = {
		multiExpand:	false,
		precall:		null,
		fixedHeight:	true,
		op3:	'#'
	};
	//Reemplazo las opciones default por las ingresadas por el usuario
	var options = $.extend({}, defaults, options);

	if ($.goSlideDown === undefined) {
		$.extend({
			goSlideDown: function (obj) {
				obj.parent().find('.contenidoSeccion, .tituloSeccion').addClass('selected');
				//var tempHeight = obj.parent().find('.contenidoSeccion.selected').height();
				var tempHeight = obj.parent().find('.contenidoSeccion.selected').children(':eq(0)').height();
				$('.contenidoSeccion.selected').slideDown(function(){
					if (options.fixedHeight) {
						//Esta comparación entre el tempHeight y el height de los hijos la hago porque cuando los hijos del div
						//son floats entonces el padre no tiene height.
						//Como ejemplo se puede ver el ABM de Clientes (solapa Sucursales, hijos flotantes) y la Nota de pedido (solapa Articulos)
						tempHeight = Math.max(tempHeight, obj.parent().find('.contenidoSeccion.selected').children().height());
						obj.parent().find('.contenidoSeccion.selected').height(tempHeight);
					}
				});
			}
		});
	}

	return this.each(function (){
		$(this).children('div').each(function(){
			$(this).removeClass('divAcordeon').addClass('divAcordeon');
			$(this).children(':eq(0)').removeClass('tituloSeccion').addClass('tituloSeccion');
			$(this).children(':eq(1)').removeClass('contenidoSeccion').addClass('contenidoSeccion');
			$(this).find('.tituloSeccion').unbind('click').click(function(){
				var obj = $(this);
				if (!$(event.target).is('input[type="checkbox"], .koiCheckbox>label')) { //Esto es para que no se abra cuando hago click en un checkbox
					var minimize = ((obj.hasClass('selected')) ? true : false);
					if ((obj.parent().parent().find('.tituloSeccion.selected').length != 0) && (!options.multiExpand)){
						obj.parent().parent().find('.contenidoSeccion.selected').slideToggle(function(){
							obj.parent().parent().find('.tituloSeccion.selected, .contenidoSeccion.selected').removeClass('selected');
							try {options.precall(obj);} catch (ex) {}
							if (!minimize)
								$.goSlideDown(obj);
						});
					} else {
						if (minimize) {
							obj.parent().find('.contenidoSeccion.selected').slideToggle(function(){
								obj.parent().find('.tituloSeccion.selected, .contenidoSeccion.selected').removeClass('selected');
							});
						} else {
							try {options.precall(obj);} catch (ex) {}
							$.goSlideDown(obj);
						}
					}					
				}
			});
		});
	});
};