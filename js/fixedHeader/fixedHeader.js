/**
 * $('#divCaca').fixedHeader({target: '.fixedHeader'});
 */

(function($) {
	var defaults = {
		container: document,
		target: 'table.fixedHeader',
		alreadyFixed : false,
		alreadyFixedDiv: false
	};
	var settings;

	var methods = {
		init: function(options) {
			settings = $.extend(defaults, options);
			settings.container = this;
			$(settings.container).scroll(function() {
				$(settings.container).find(settings.target).eq(0).not(settings.alreadyFixedDiv).each(function() {
					var	setted = !!settings.alreadyFixed,
						equal = settings.alreadyFixed && settings.alreadyFixed[0] === $(this)[0],
						settedBelow = settings.alreadyFixed && settings.alreadyFixed.position().top <= 0,
						settedPassed = settings.alreadyFixed && (settings.alreadyFixed.height() + settings.alreadyFixed.position().top) <= 0,
						actualBelow = $(this).position().top <= ($(this).find('thead').height() + $(this).find('caption').height()), //ANTES: actualBelow = $(this).position().top <= 0,
						actualBelowSetted = settings.alreadyFixed && $(this).position().top > settings.alreadyFixed.position().top;

					if (setted && (
							(settedPassed) ||
							(equal && !actualBelow) ||
							(actualBelow && actualBelowSetted) ||
							(actualBelow && !settedBelow && !actualBelowSetted)
						)) {
						methods.unfix();
						setted = false
					}
					if (actualBelow && (
							(!setted) ||
							(actualBelowSetted)
						)) {
						methods.fix($(this))
					}
				});
			});
		},
		fix: function(element) {
			settings.alreadyFixedDiv = element.clone();
			settings.alreadyFixedDiv
				.css({
					position: 'absolute',
					paddingBottom: 0,
					width: element.find('th').eq(0).parent().css('width'),
					backgroundColor: 'white',
					zIndex: 1
				});
			var temp = settings.alreadyFixedDiv.find('caption').eq(0).clone();
			settings.alreadyFixedDiv.find('caption').eq(0).remove();
			settings.alreadyFixedDiv.find('tbody').eq(0).remove();
			settings.alreadyFixedDiv.find('tfoot').eq(0).remove();
			element.find('caption').insertBefore(settings.alreadyFixedDiv.find('thead').eq(0));
			element.prepend(temp);
			settings.alreadyFixedDiv
				.find('caption').eq(0).css({
					marginBottom: '-2px',
					backgroundColor: 'white'
				});

			settings.container.prepend(settings.alreadyFixedDiv);
			settings.alreadyFixed = element;
		},
		unfix: function() {
			settings.alreadyFixedDiv.remove();
			settings.alreadyFixedDiv = false;
			settings.alreadyFixed = false;
		}
	};

	$.fn.fixedHeader = function(method) {
		if (methods[method]) {
			//Si existe el método que me piden, lo llamo, y le mando por parametro to_dos los que me mandaron excepto el primero que es el método
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			//Si no llamo a un método, o si mando un objeto como primer parámetro (config), llamo al INIT y le mando todos los parámetros
			return methods.init.apply(this, arguments);
		} else {
			$.error('No existe el método ' +  method + ' en el plugin jQuery.fixedHeader');
		}
	};
})(jQuery);