/*
 * Este plugin muestra una imagen de 'loading' y un fondo oscuro.
 * Por el CSS se puede configurar el background y el popup
 * Para llamarlo dentro de un div se hace $('selector').showLoading() y para ocultarlo $('selector').hideLoading()
 * Para llamarlo a pantalla completa $.showLoading(), y para ocultar TO_DOS los loading al mismo tiempo $.hideLoading();
 * 
 */

$.fn.hideLoading = function() {
	$.hideLoading($(this));
};

$.fn.showLoading = function(options) {
	if (typeof options === 'undefined')
		options = {};
	$.showLoading(options, $(this));
};

$.extend({
	//Opciones por defecto
	defaults: {
		loadingImage:	'/img/varias/loading.gif',
		popUpExtraClass:	'',		//Además de la que le puse yo
		popUpBgExtraClass:	''		//Además de la que le puse yo
	},
	options: {},
	popUp: {},
	popUpBg: {},
	target: {},
	hideLoading: function (obj) {
		var tar = $('body').find('.loadingPopUp, .loadingPopUpBg');
		if (typeof obj !== 'undefined')
			tar = $(obj).children('.loadingPopUp, .loadingPopUpBg'); 
		tar.fadeOut('fast', function(){$(this).remove();});
	},
	showLoading: function(options, obj) {
		//Reemplazo las opciones default por las ingresadas por el usuario
		var options = $.extend({}, this.defaults, this.options);
		this.target = obj;
		if (typeof obj === 'undefined')
			this.target = $('body');

		if (this.target.children('.loadingPopUp').length == 0){
			var divs = '<div class="loadingPopUp ' + options.popUpExtraClass + '"><img src="' + options.loadingImage + '"></div>';
			divs += '<div class="loadingPopUpBg ' + options.popUpBgExtraClass + '"></div>';
			this.target.append(divs);
		}

		this.popUp = this.target.children('.loadingPopUp');
		this.popUpBg = this.target.children('.loadingPopUpBg');

		this.addEvents();
		this.resizeBg();
		this.movePopUp(false);
		this.popUpBg.fadeIn('fast');
		this.popUp.fadeIn('fast');
	},
	resizeBg: function () {
		var objWidth = this.target.width(),
			objHeight = this.target.height();
		if (this.target.selector == 'body') {
			objWidth = $(window).width();
			objHeight = $(window).height();
		}
		this.popUpBg.css({
			'width': objWidth,
			'height': objHeight,
			'left': this.target.offset().left + $(window).scrollLeft(),
			'top':  this.target.offset().top + $(window).scrollTop()
		});
		return this;
	},
	movePopUp: function (visible) {
		var popupHeight = this.extraerNumero(this.popUp.css('height')),
			popupWidth = this.extraerNumero(this.popUp.css('width'));
		var newLeft = ((this.target.width() - popupWidth) / 2) + this.target.offset().left + $(window).scrollLeft();
		var newTop = ((this.target.height() - popupHeight) / 2) + this.target.offset().top + $(window).scrollTop();
		if (visible) {
			this.popUp.animate({
				'left': newLeft,
				'top': newTop
			}, {
				duration: 100,
				queue: false,
				easing: 'easeOutBack'
			});
		} else {
			this.popUp.css({
				'left': newLeft,
				'top': newTop
			});
		}
	},
	addEvents: function (){
		$(window).bind('resize', $.proxy(function () {
			this.resizeBg();
			this.movePopUp();
		}, this));
	},
	extraerNumero: function(obj){
		var NUMBERS = /[^0-9]/g;
		if (typeof obj !== 'undefined')
		    return obj.replace(NUMBERS, "");
		return 0;
	}
});
