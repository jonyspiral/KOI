(function($) {
	$.extend({
		ListaAlternadaObject: {
			options: {
				id: 'listaAlternada',
				clase: 'registrosAlternados w100p'
			},
			table: null,
			target: $('#programaContenido'),
			create: function (target, trs, options) {
				this.target = target;
				this.options = $.extend(true, this.options, options);
			    this.table = $('<table>').attr('id', this.options.id).attr('class', this.options.clase);
				for (var i = 0; i < trs.length; i++) {
					this.table.append(this.addTr(trs[i]));
			    }
			    this.target.append(this.table);
			},
			addTr: function(trObj) {
				var options = {
					w1: 65,
					w2: 15,
					w3: 20,
					msg1: '',
					msg2: '',
					msg3: '',
					estados: [],
					botones: []
				};
				options = $.extend(true, options, trObj);
				this.table.append(
					$('<tr>').attr('id', (trObj.id ? trObj.id : 'tr_' + funciones.random(1000))).append(
						$('<td>').addClass('w' + options.w1 + 'p').append(this.addTdDatos(options.msg1, options.msg2, options.msg3)),
						$('<td>').addClass('w' + options.w2 + 'p').append(this.addTdEstado(options.estados)),
						$('<td>').addClass('w' + options.w3 + 'p').append(this.addTdBotones(options.botones))
					)
				);
			},
			addTdDatos: function(msg1, msg2, msg3) {
				var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
				table.append(
					$('<tr>').addClass('tableRow').append(
						$('<td>').addClass('bold aLeft').append(
							$('<label>').text(msg1)
						)
					),
					$('<tr>').addClass('tableRow').append(
						$('<td>').addClass('aLeft').append(
							$('<label>').text(msg2),
							$('<label>').addClass('fRight').text(msg3)
						)
					)
				);
				return table;
			},
			addTdEstado: function(estados) {
				/*
				 * 'estados' es una lista de objetos:
				 * {id: 'idImg', clase: 'claseImg', src: '/img/varias/cae_obtenido.png'}
				 */
				var defaults = {
					id: '',
					clase: 'pLeft10',
					src: ''
				};
				var div = $('<div>').addClass('aLeft');
				estados.each(function() {
					var options = $.extend(true, defaults, this);
					div.append($('<img>').addClass(options.clase).attr('id', options.id).attr('src', options.src));
				});
				return div;
			},
			addTdBotones: function(botones) {
				/*
				 * 'botones' es una lista de objetos:
				 * {id: 'idA', clase: 'claseA', title: 'tituloA', onclick: 'funcion(parametro1)',  src: '/img/varias/cae_obtenido.png'}
				 */
				var defaults = {
					id: '',
					clase: 'boton',
					title: '',
					onclick: '',
					src: ''
				};
				var div = $('<div>').addClass('botonera aCenter');
				botones.each(function() {
					var options = $.extend(true, defaults, this);
					btn1 = $('<a>').attr('href', '#')
									.attr('id', options.id)
									.addClass(options.clase)
									.attr('title', options.title)
									.attr('onclick', options.onclick)
									.append($('<img>').attr('src', options.src));
					div.append(btn1);
				});
				return div;
			}
		},
		addTr: function (trObj) {
			return $.ListaAlternadaObject.addTr(trObj);
		},
		listaAlternada: function(trs, options) {
			$.ListaAlternadaObject.create(this, trs, options);
		}
	});

})(jQuery);
