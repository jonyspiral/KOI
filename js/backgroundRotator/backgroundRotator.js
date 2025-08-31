/*
 * El plugin se encarga de rotar el background de un elemento entre diferentes imagenes
 */

$.fn.backgroundRotator = function(options) {

	//Opciones por defecto
	var defaults = {
		fadeTime: 200,
		interval: 5000,
		images: [],
		initialImage: ''
	};
	//Reemplazo las opciones default por las ingresadas por el usuario
	options = $.extend({}, defaults, options);

	return this.each(function () {
    var image = $(this);
		if (options.initialImage) {
			image.css('background-image', 'url("' + options.initialImage + '")');
		}
		if (options.images.length > 1) {
			$(function () {
				var i = options.images.length > 1 ? 1 : 0;
				setInterval(function () {
					image.fadeOut(options.fadeTime, function () {
						image.css('background-image', 'url("' + options.images[i] + '")');
						image.fadeIn(options.fadeTime);
						i = (i === (options.images.length - 1)) ? 0 : i + 1;
					});
				}, options.interval);
			})
		}
	});
};