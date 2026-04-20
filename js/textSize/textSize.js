/*
 * Devuelve un objeto con un atributo .width y otro .height
 * Es importante pasarle un selector que tenga sólo 1 texto
 * De lo contrario devuelve el tamaño del primer texto que encuentra
 */

$.fn.textSize = function() {
	var el = this;
	var h = 0, w = 0;
	var div = document.createElement('div');
	document.body.appendChild(div);
	$(div).css({
		position: 'absolute',
		left: -1000,
		top: -1000,
		display: 'none'
	});
	$(div).html($(el).html());
	var styles = ['font-size','font-style', 'font-weight', 'font-family', 'line-height', 'text-transform', 'letter-spacing'];
	$(styles).each(function() {
		var s = this.toString();
		$(div).css(s, $(el).css(s));
	});
	h = $(div).outerHeight();
	w = $(div).outerWidth();
	$(div).remove();
	var ret = {
		height: h,
		width: w
	};
	return ret;
};
