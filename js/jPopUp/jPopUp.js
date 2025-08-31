(function ($) {
	$.extend({
		jPopUpObject: {
			defaults: {
				name: 'jPopUp',
				zIndex: 10000,
				overlay: {
					'background': '-webkit-radial-gradient(rgba(127, 127, 127, 0.5), rgba(127, 127, 127, 0.5) 35%, rgba(0, 0, 0, 0.7))',
					'opacity': 1
				},
				showDuration: 200,
				closeDuration: 100,
				moveDuration: 100,
				shake: {
					'distance': 10,
					'duration': 100,
					'transition': 'easeOutBack',
					'loops': 2
				}
			},
			options: {},
			esqueleto: {
				popup: [],
				wrapper: [],
				buttons: [],
				inputs: []
			},
			visible: false,
			i: 0,
			animation: false,
			config: function (a) {
				this.options = $.extend(true, this.options, a);
				this.moveBox();
			},
			overlay: {
				create: function (b) {
					this.options = b;
					this.element = $('<div id="' + new Date().getTime() + '"></div>');
					this.element.css($.extend({}, {
						'position': 'fixed',
						'top': 0,
						'left': 0,
						'opacity': 0,
						'display': 'none',
						'z-index': this.options.zIndex
					}, this.options.style));
					this.hidden = true;
					this.inject();
					return this;
				},
				inject: function () {
					this.target = $(document.body);
					this.target.append(this.element);
				},
				resize: function (x, y) {
					this.element.css({
						'height': 0,
						'width': 0
					});
					var a = {
						x: $(document).width(),
						y: $(document).height()
					};
					this.element.css({
						'width': '100%',
						'height': y ? y : a.y
					});
					return this;
				},
				show: function () {
					if (!this.hidden) return this;
					if (this.transition) this.transition.stop();
					this.target.bind('resize', $.proxy(this.resize, this));
					this.resize();
					this.hidden = false;
					this.transition = this.element.fadeIn(this.options.showDuration, $.proxy(function () {
						this.element.trigger('show');
					}, this));
					return this;
				},
				hide: function () {
					if (this.hidden) return this;
					if (this.transition) this.transition.stop();
					this.target.unbind('resize');
					this.hidden = true;
					this.transition = this.element.fadeOut(this.options.closeDuration, $.proxy(function () {
						this.element.trigger('hide');
						this.element.css({
							'height': 0,
							'width': 0
						});
					}, this));
					return this;
				}
			},
			create: function () {
				this.options = $.extend(true, this.defaults, this.options);
				this.overlay.create({
					style: this.options.overlay,
					zIndex: this.options.zIndex - 1,
					showDuration: this.options.showDuration,
					closeDuration: this.options.closeDuration
				});
				this.esqueleto.popup = $('<div class="' + this.options.name + '"></div>');
				this.esqueleto.popup.css({
					'display': 'none',
					'position': 'absolute',
					'top': 0,
					'left': 0,
					'height': this.options.height,
					'z-index': this.options.zIndex,
					'word-wrap': 'break-word',
					'-moz-box-shadow': '0 0 15px rgba(0, 0, 0, 0.5)',
					'-webkit-box-shadow': '0 0 15px rgba(0, 0, 0, 0.5)',
					'box-shadow': '0 0 15px rgba(0, 0, 0, 0.5)',
					'-moz-border-radius': '6px',
					'-webkit-border-radius': '6px',
					'border-radius': '6px',
					'background-color': this.options.background
				});
				this.esqueleto.wrapper = $('<div class="' + this.options.name + '-wrapper"></div>');
				this.esqueleto.popup.append(this.esqueleto.wrapper);
				this.esqueleto.wrapper.css({
					height: 'auto',
					'min-height': 80,
					'zoom': 1
				});
				$('body').append(this.esqueleto.popup);
				this.addevents();
				return this.esqueleto.popup;
			},
			addevents: function () {
				$(window).bind('resize', $.proxy(function () {
					if (this.visible) {
						this.overlay.resize();
						this.moveBox();
					}
				}, this));
				$(window).bind('scroll', $.proxy(function () {
					if (this.visible) {
						this.moveBox();
					}
				}, this));
				this.esqueleto.popup.bind('focusout', $.proxy(function (a) {
					//Con esta función, cuando el foco sale de alguno de los elementos del popup, vuelve al último botón (generalmente "cancelar")
					setTimeout($.proxy(function(){
						var newFocus = $(document.activeElement);
						if ($(this.esqueleto.popup).find(newFocus).length == 0){
							if ((typeof newFocus[0] != 'undefined') && (typeof $(this.esqueleto.popup).find('button:last')[0] != 'undefined') && (newFocus[0].offsetTop < $(this.esqueleto.popup).find('button:last')[0].offsetTop))
								$(this.esqueleto.popup).find('a:visible, input:visible:enabled, button:visible:enabled, select:visible:enabled').eq(0).focus();
							else
								$(this.esqueleto.popup).find('button:last').focus();
						}
					}, this), 1);
				}, this));
				this.esqueleto.popup.bind('keydown', $.proxy(function (a) {
					if (a.keyCode == 27) {
						this.close();
					}
				}, this));
				this.overlay.element.bind('show', $.proxy(function () {
					$(this).triggerHandler('show');
				}, this));
				this.overlay.element.bind('hide', $.proxy(function () {
					$(this).triggerHandler('close');
				}, this));
			},
			reset: function(){
				this.esqueleto.wrapper.children().remove();
			},
			show: function (g, h, j, k) {
				this.esqueleto.popup.queue(this.options.name, $.proxy(function (c) {
					this.reset();
					this.callback = $.isFunction(j) ? j : function (e) {};
					this.callbackOpen = $.isFunction(k) ? k : function (e) {};
					this.esqueleto.buttons = $('<div class="' + this.options.name + '-buttons"></div>');
					this.esqueleto.wrapper.append(this.esqueleto.buttons);
					$.each(h, $.proxy(function (i, a) {
						this.esqueleto.buttons.append($('<button>' + a.value + '</button>').bind('click', $.proxy(function (e) {
							a.action();
							e.preventDefault();
						}, this)));
					}, this));
					this.esqueleto.wrapper.prepend(g);
					this.moveBox();
					this.visible = true;
					this.overlay.show();
					this.esqueleto.popup.css({
						display: 'block',
						left: (($(document).width() - $(this.esqueleto.popup).width()) / 2)
					});
					this.moveBox();
					setTimeout($.proxy(function () {
						var b = $('input, button', this.esqueleto.popup);
						if (b.length) {
							b.get(0).focus();
						}
						this.callbackOpen();
					}, this), this.options.moveDuration);
				}, this));
				this.i++;
				if (this.i == 1) {
					this.esqueleto.popup.dequeue(this.options.name);
				}
			},
			moveBox: function () {
				var a = {
					x: $(window).width(),
					y: $(window).height()
				};
				var b = {
					x: $(window).scrollLeft(),
					y: $(window).scrollTop()
				};
				var c = this.esqueleto.popup.outerHeight(true);
				var y = 0;
				var x = 0;
				y = b.x + ((a.x - $(this.esqueleto.popup).width()) / 2);
				x = (b.y - c) - 80;
				if (this.visible) {
					if (this.animation) {
						this.animation.stop;
					}
					this.animation = this.esqueleto.popup.animate({
						left: y,
						top: b.y + ((a.y - c) / 2)
					}, {
						duration: this.options.moveDuration,
						queue: false,
						easing: 'easeOutBack'
					});
				} else {
					this.esqueleto.popup.css({
						top: x,
						left: y
					});
				}
			},
			close: function (callback) {
				if (this.visible) {
					this.esqueleto.popup.css({display: 'none', top: 0});
					this.visible = false;
					if ($.isFunction(this.callback)) {
						this.callback();
					}
					if ($.isFunction(callback)) {
						callback();
					}
					setTimeout($.proxy(function () {
						this.i--;
						this.esqueleto.popup.dequeue(this.options.name);
					}, this), this.options.closeDuration);
					if (this.i == 1)
						this.overlay.hide();
					this.moveBox();
				}
			},
			shake: function () {
				var x = this.options.shake.distance;
				var d = this.options.shake.duration;
				var t = this.options.shake.transition;
				var o = this.options.shake.loops;
				var l = this.esqueleto.popup.position().left;
				var e = this.esqueleto.popup;
				for (var i = 0; i < o; i++) {
					e.animate({
						left: l + x
					}, d, t);
					e.animate({
						left: l - x
					}, d, t);
				};
				e.animate({
					left: l + x
				}, d, t);
				e.animate({
					left: l
				}, d, t);
			}
		},
		jPopUp: {
			/**
			 * @param a Contenido del PopUp. Ej: $('div')
			 * @param b Array de botones. Ej:[{value: 'Guardar', action: function(){}}, otro más...]
			 * @param c Callback cuando el PopUp se cierra, sea cual sea el botón/acción ejecutada
			 * @param d Callback cuando se abre el PopUp
			 * @return {*}
			 */
			show: function (a, b, c, d) {
				return $.jPopUpObject.show(a, b, c, d);
			},
			close: function (c) {
				return $.jPopUpObject.close(c);
			},
			shake: function () {
				return $.jPopUpObject.shake();
			}
		}
	});
	$(function () {
		$.jPopUpObject.create();
	});
})(jQuery);