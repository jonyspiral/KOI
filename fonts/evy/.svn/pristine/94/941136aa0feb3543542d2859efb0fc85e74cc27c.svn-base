/*
 * Este plugin está retocado (http://digitalbush.com/projects/masked-input-plugin/)
 * La idea original es la siguiente
 * $(selector).mask('99/99/9999');
 * 
 * Con los cambios que hice se pueden hacer validaciones precargadas y funciones onblur
 * To_do lo que hay que hacer es al input ponerle un atributo validate="Tipo" (Ej: validate="Email", validate="Fecha")
 * y poner en el onload $('.textbox[validate]').validate();
 * 
 * En el caso de la fecha, hace el mask '39/19/9999' y además en el onblur le agrega la función $.validateFecha
 * En el caso del Email, sólo se usa una función onblur $.validateEmail
 * 
 * Para agregar una validación precargada hay que agregarla a 'preloaded' y hacerle (de ser necesario) la función onblur.
 */

(function($) {
	var pasteEventName = 'input.mask';
	var iPhone = (window.orientation != undefined);

	$.fn.validate = function() {
		return $(this).each(function(){
			$(this)./*unmask().*/mask();
		});
	};

	$.validateTelefono = function(input){
		var val = input.val();
		val = val.replace(/0/g, '');
		if (funciones.toInt(val).toString() === val)
			return true;
		input.val('');
		return false;
	};
	$.validateEmail = function(input){
		var email = input.val();
		if (email == '')
			return;
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		if (!reg.test(email)){
			input.val('');
		}
	};
	$.validateFecha = function(input){
		var fecha = input.val();
		if (fecha == '')
			return;
		var arrayDate = fecha.split('/'); 
		//create a lookup for months not equal to Feb.
		var arrayLookup = { '01' : 31, '03' : 31, 
							'04' : 30, '05' : 31,
							'06' : 30, '07' : 31,
							'08' : 31, '09' : 30,
							'10' : 31, '11' : 30,
							'12' : 31
						};
		var intDay = parseInt(arrayDate[0], 10); 
		if (arrayLookup[arrayDate[1]] != null) {
			if (intDay <= arrayLookup[arrayDate[1]] && intDay != 0)
				return true;
		}

		//Excepción para Febrero
		var intMonth = parseInt(arrayDate[1], 10);
		if (intMonth == 2) {
			var intYear = parseInt(arrayDate[2]);
			if (intDay > 0 && intDay < 29)
				return true;
			else if (intDay == 29)
				if (((intYear % 4 == 0) && (intYear % 100 != 0)) || (intYear % 400 == 0))
					return true;
		}
		input.val('');
		return false;
	};
	$.validateHora = function(input){
		var arrHora = input.val().split(':');
		if (funciones.toInt(arrHora[0]) > 23 || funciones.toInt(arrHora[1]) > 59){
			input.val('');
			return false;
		}
		return true;
	};
	$.validateRangoHora = function(input){
		var arrHoras = input.val().split(' - ');
		var arrHora1 = arrHoras[0].split(':');
		var arrHora2 = arrHoras[1].split(':');
		if (funciones.toInt(arrHora1[0]) > 23 || funciones.toInt(arrHora1[1]) > 59 || funciones.toInt(arrHora2[0]) > 23 || funciones.toInt(arrHora2[1]) > 59){
			input.val('');
			return false;
		}
		return true;
	};
	$.validateEntero = function(input){
		var val = input.val();
		if (funciones.toInt(val).toString() === val)
			return true;
		input.val('');
		return false;
	};
	$.validateEnteroPositivo = function(input){
		var val = input.val();
		if ((funciones.toInt(val) >= 0) && (funciones.toInt(val).toString() === val))
			return true;
		input.val('');
		return false;
	};
	$.validateNatural = function(input){
		var val = input.val();
		if ((funciones.toInt(val) >= 0) && (funciones.toInt(val).toString() === val))
			return true;
		input.val('');
		return false;
	};
	$.validateNumero = function(input){
		var val = input.val();
		if (funciones.toInt(val).toString() === val || funciones.toFloat(val).toString() === val)
			return true;
		input.val('');
		return false;
	};
	$.validateDecimal = function(input){
		var val = input.val();
		var decimales = val.split('.').length == 2 ? val.split('.')[1].length : 0;
		if (funciones.formatearDecimales(funciones.toFloat(val), decimales, '.').toString() === val)
			return true;
		input.val('');
		return false;
	};
	$.validateDecimalPositivo = function(input){
		var val = input.val();
		var decimales = val.split('.').length == 2 ? val.split('.')[1].length : 0;
		if ((funciones.toInt(val) >= 0) && funciones.formatearDecimales(funciones.toFloat(val), decimales, '.').toString() === val)
			return true;
		input.val('');
		return false;
	};
	$.validatePorcentaje = function(input){
		var val = input.val();
		if (funciones.toFloat(val) <= 100)
			return true;
		input.val('');
		return false;
	};



	$.mask = {
		//Predefined character definitions
		definitions: {
			'1': /[0-1]/,
			'2': /[0-2]/,
			'3': /[0-3]/,
			'4': /[0-4]/,
			'5': /[0-5]/,
			'6': /[0-6]/,
			'7': /[0-7]/,
			'8': /[0-8]/,
			'9': /[0-9]/,
			'z': /[a-z]/,
			'Z': /[A-Z]/,
			'a': /[a-zA-Z]/,
			'*': /[0-9a-zA-Z]/
		},
		dataName: "rawMaskFn",
		preloadedName: "validate",
		maskName: "mask",
		preloaded: {
			'Telefono'				: {mask: ''/*, blur: $.validateTelefono*/},
			'Cuil'					: {mask: '99999999999'},
			'Cuit'					: {mask: '99999999999'},
			'Dni'					: {mask: '99999999'},
			'CertificadoRetencion'	: {mask: '9999-9999-99999999'},
			'Factura'				: {mask: '9999-99999999'},
			'Cheque'				: {mask: '99999999?-ZZ'},
			'Fecha'					: {mask: '39/19/9999', blur: $.validateFecha},
			'Email'					: {mask: '', blur: $.validateEmail},
			'Hora'					: {mask: '29:59', blur: $.validateHora},
			'RangoHora'				: {mask: '29:59 - 29:59', blur: $.validateRangoHora},
			'Entero'				: {mask: '', blur: $.validateEntero},
			'EnteroPositivo'		: {mask: '', blur: $.validateEnteroPositivo},
			'Natural'				: {mask: '', blur: $.validateNatural},
			'Numero'				: {mask: '', blur: $.validateNumero},
			'Decimal'				: {mask: '', blur: $.validateDecimal},
			'DecimalPositivo'		: {mask: '', blur: $.validateDecimalPositivo},
			'Porcentaje'			: {mask: '999,99 %', blur: $.validatePorcentaje}
		}
	};

	$.fn.extend({
		//Helper Function for Caret positioning
		caret: function(begin, end) {
			if (this.length == 0) return;
			if (typeof begin == 'number') {
				end = (typeof end == 'number') ? end : begin;
				return this.each(function() {
					if (this.setSelectionRange) {
						this.setSelectionRange(begin, end);
					} else if (this.createTextRange) {
						var range = this.createTextRange();
						range.collapse(true);
						range.moveEnd('character', end);
						range.moveStart('character', begin);
						range.select();
					}
				});
			} else {
				if (this[0].setSelectionRange) {
					begin = this[0].selectionStart;
					end = this[0].selectionEnd;
				} else if (document.selection && document.selection.createRange) {
					var range = document.selection.createRange();
					begin = 0 - range.duplicate().moveStart('character', -100000);
					end = begin + range.text.length;
				}
				return { begin: begin, end: end };
			}
		},
		unmask: function() { return this.trigger("unmask"); },
		mask: function(mask, settings) {
			if (!mask && this.length > 0) {
				//Si tiene el attr VALIDATE, es uno de los preloaded y tiene MASK
				if ($.mask.preloaded[$(this).attr($.mask.preloadedName)] && $.mask.preloaded[$(this).attr($.mask.preloadedName)]['mask'])
					mask = $.mask.preloaded[$(this).attr($.mask.preloadedName)]['mask'];
				//Si tiene el attr MASK
				else if ($(this).attr($.mask.maskName))
					mask = $(this).attr($.mask.maskName);
				//Si tiene el attr VALIDATE, es uno de los preloaded y tiene BLUR
				else if ($.mask.preloaded[$(this).attr($.mask.preloadedName)] && $.mask.preloaded[$(this).attr($.mask.preloadedName)]['blur']) {
					$(this).bind("blur.mask", function() {
						$.mask.preloaded[$(this).attr($.mask.preloadedName)]['blur']($(this));
					});
					return;
				} else
					return;
				
			}
			settings = $.extend({
				placeholder: "_",
				completed: null
			}, settings);

			var defs = $.mask.definitions;
			var tests = [];
			var partialPosition = mask.length;
			var firstNonMaskPos = null;
			var len = mask.length;

			//$.each(mask.split(""), function(i, c) {
			//El split("") es para que devuelva un array de chars
			$(mask.split("")).each(function(i, c) {
				if (c == '?') {
					len--;
					partialPosition = i;
				} else if (defs[c]) {
					tests.push(new RegExp(defs[c]));
					if(firstNonMaskPos == null)
						firstNonMaskPos =  tests.length - 1;
				} else {
					tests.push(null);
				}
			});

			return this.trigger("unmask").each(function() {
				var input = $(this);
				var buffer = $.map(mask.split(""), function(c) { if (c != '?') return defs[c] ? settings.placeholder : c;});
				var focusText = input.val();

				function seekNext(pos) {
					while (++pos <= len && !tests[pos]){}
					return pos;
				}
				function seekPrev(pos) {
					while (--pos >= 0 && !tests[pos]){}
					return pos;
				}

				function shiftL(begin,end) {
					if(begin<0)
					   return;
					for (var i = begin,j = seekNext(end); i < len; i++) {
						if (tests[i]) {
							if (j < len && tests[i].test(buffer[j])) {
								buffer[i] = buffer[j];
								buffer[j] = settings.placeholder;
							} else
								break;
							j = seekNext(j);
						}
					}
					writeBuffer();
					input.caret(Math.max(firstNonMaskPos, begin));
				}

				function shiftR(pos) {
					for (var i = pos, c = settings.placeholder; i < len; i++) {
						if (tests[i]) {
							var j = seekNext(i);
							var t = buffer[i];
							buffer[i] = c;
							if (j < len && tests[j].test(t))
								c = t;
							else
								break;
						}
					}
				}

				function keydownEvent(e) {
					var k=e.which;

					//backspace, delete, and escape get special treatment
					if(k == 8 || k == 46 || (iPhone && k == 127)){
						var pos = input.caret(),
							begin = pos.begin,
							end = pos.end;
						
						if(end-begin==0){
							begin=k!=46?seekPrev(begin):(end=seekNext(begin-1));
							end=k==46?seekNext(end):end;
						}
						clearBuffer(begin, end);
						shiftL(begin,end-1);

						return false;
					} else if (k == 27) {//escape
						input.val(focusText);
						input.caret(0, checkVal());
						return false;
					}
				}

				function keypressEvent(e) {
					var k = e.which,
						pos = input.caret();
					if (e.ctrlKey || e.altKey || e.metaKey || k<32) {//Ignore
						return true;
					} else if (k) {
						if(pos.end-pos.begin!=0){
							clearBuffer(pos.begin, pos.end);
							shiftL(pos.begin, pos.end-1);
						}

						var p = seekNext(pos.begin - 1);
						if (p < len) {
							var c = String.fromCharCode(k);
							if (tests[p].test(c)) {
								shiftR(p);
								buffer[p] = c;
								writeBuffer();
								var next = seekNext(p);
								input.caret(next);
								if (settings.completed && next >= len)
									settings.completed.call(input);
							}
						}
						return false;
					}
				}

				function clearBuffer(start, end) {
					for (var i = start; i < end && i < len; i++) {
						if (tests[i])
							buffer[i] = settings.placeholder;
					}
				}

				function writeBuffer() { return input.val(buffer.join('')).val(); }

				function checkVal(allow) {
					//try to place characters where they belong
					var test = input.val();
					var lastMatch = -1;
					for (var i = 0, pos = 0; i < len; i++) {
						if (tests[i]) {
							buffer[i] = settings.placeholder;
							while (pos++ < test.length) {
								var c = test.charAt(pos - 1);
								if (tests[i].test(c)) {
									buffer[i] = c;
									lastMatch = i;
									break;
								}
							}
							if (pos > test.length)
								break;
						} else if (buffer[i] == test.charAt(pos) && i!=partialPosition) {
							pos++;
							lastMatch = i;
						}
					}
					if (!allow && lastMatch + 1 < partialPosition) {
						input.val("");
						clearBuffer(0, len);
					} else if (allow || lastMatch + 1 >= partialPosition) {
						writeBuffer();
						if (!allow) input.val(input.val().substring(0, lastMatch + 1));
					}
					return (partialPosition ? i : firstNonMaskPos);
				}

				input.data($.mask.dataName,function(){
					return $.map(buffer, function(c, i) {
						return ((tests[i] && c != settings.placeholder) ? c : null);
					}).join('');
				});

				if (!input.attr("readonly"))
					input
					.one("unmask", function() {
						input
							.unbind(".mask")
							.removeData($.mask.dataName);
					})
					.bind("focus.mask", function() {
						focusText = input.val();
						var pos = checkVal();
						writeBuffer();
						var moveCaret=function(){
							if (pos == mask.length)
								input.caret(0, pos);
							else
								input.caret(pos);
						};
						($.browser.msie ? moveCaret: function(){setTimeout(moveCaret, 0);})();
					})
					.bind("blur.mask", function() {
						if ($.mask.preloaded[input.attr($.mask.preloadedName)]['blur'])
							$.mask.preloaded[input.attr($.mask.preloadedName)]['blur'](input);
						checkVal();
						if (input.val() != focusText)
							input.change();
					})
					.bind("keydown.mask", keydownEvent)
					.bind("keypress.mask", keypressEvent)
					.bind(pasteEventName, function() {
						setTimeout(function() { input.caret(checkVal(true)); }, 0);
					});

				checkVal(); //Perform initial check for existing values
			});
		}
	});
})(jQuery);
