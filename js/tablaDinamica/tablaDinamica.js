/**
 * Este plugin usa fixedHeader, jPopUp y validate. Además uso cosas como la clase .textbox que me sirve para estilo y comportamiento (al apretar enter)
 *
 * $('.pluginTablaDinamica')
 *
 * EJEMPLO:
 $('.tabladinamica ').tablaDinamica(
 {
	width: '80%',
	height: '300px',
	caption: 'Caption, título, whatever',
	addButtonInHeader: false,
	buttons: ['E', 'Q'],
	columnsConfig: [
		{
			id: 'id',
			name: 'ID',
			title: false,
			width: '100px',
			css: {color: 'green'},
			cellType: 'L',
			template: 'Esto no debería servir',
			valueByFunction: function(o) {return 'Fila ID: ' + o.rowid + ' - Columna ID: ' + o.colid;},
			notEmpty: false,
			click: function() {alert('asd' + this.data('colid'));}
		},
		{
			id: 'nombre',
			name: 'Nom',
			title: 'Nombre',
			width: 'auto',
			css: {color: 'red', textAlign: 'center'},
			cellType: 'A',
			template: '<input class="textbox obligatorio autoSuggestBox w150" name="Cliente" />',
			valueByFunction: function() {return 6;},
			click: false
		},
		{
			id: 'cantidad',
			name: 'Cantidad',
			width: 'auto',
			cellType: 'I',
			template: '<input class="textbox obligatorio w100" type="text" />',
			valueByFunction: function(o) {return 'Hola ' + (o.rowid % 2 ? 'impar' : 'par');}
		},
		{
			id: 'edad',
			name: 'Edad',
			width: 'auto',
			css: {color: 'blue', textAlign: 'center'},
			cellType: 'S',
			template:	'<select class="textbox obligatorio w200">' +
						'<option value="0">Menor</option>' +
						'<option value="1">Mayor</option>' +
						'</select>',
			valueByFunction: function() {return 1;},
			click: false
		},
		{
			id: 'vivo',
			name: '¿Vivo?',
			width: 'auto',
			css: {color: 'yellow', textAlign: 'center'},
			cellType: 'C',
			template: '<input type="checkbox" />',
			valueByFunction: function() {return true;},
			click: false
		}
	],
	defaultRows: 2,
	validate: false,
	popUp: false,
	popUpTemplate: false,
	popUpFillMapper: false,
	saveCallback: false,
	removeCallback: false,
	pluralName: 'registros',
	minRows: 2
}
 );
 *
 */

(function($) {
	var defaults = {
		width: '100%',
		height: 'auto',
		caption: '',					//Título de la tabla
		addButtonInHeader: false,		//Poner el botón de agregar en el TH de la columna de acciones ("header" y "actions" deberán ser true)
		header: true,					//Mostrar/ocultar el header de la tabla
		actions: true,					//Mostrar/ocultar la columna de acciones
		buttons: ['Q'],					//Configuración de los botones a mostrar [Editar, Quitar]
		columnsConfig: [],				//Configuración inicial de las columnas
		columnsMapping: {},
		components: {
			container: null,
			table: null,
			caption: null,
			addButton: null,
			header: null,
			body: null
		},
		defaultRows: 0,
		scrollbar: true,
		validate: false,
		popUp: false,					//Usar o no agregar/editar con PopUp
		popUpTemplate: false,			//Si hay que agregar/editar con PopUp, acá va el template
		popUpFillMapper: false,			//Relaciones entre el nombre de la celda y un selector para indicar de donde sacar la info (es obligatoria si se usa PopUp para llenar la nueva row)
		addCallback: false,				//Callback al agregar una fila
		saveCallback: false,			//Callback al guardar con PopUp
		removeCallback: false,			//Callback al quitar
		pluralName: 'registros',		//Para lanzar una excepción en caso de intentar guardar sin registros
		minRows: true,					//Al menos una fila
		rows: [],
		rowIndex: 0
	};
	var baseColumnsConfig = {
		id: '',
		name: '',
		title: false,
		width: 'auto',
		css: {},
		cellType: 'G',
		template: false,
		valueByFunction: false,
		notEmpty: false,
		click: false,
		blur: false,
		focus: false,
		getJson: false
	};
	var methods = {
		init: function(optionsOriginal) {
			var options = angular.copy(optionsOriginal);
			this.data('tabladinamica-settings', $.extend(angular.copy(defaults), options));
			$.proxy(initialCheck, this)();
			return this.each($.proxy(function() {
				this.data('tabladinamica-settings').components.container = $('<div class="tabladinamica-container ' + (this.data('tabladinamica-settings').scrollbar ? 'scrollbar' : '') + '"></div>').css({
					width: this.data('tabladinamica-settings').width,
					height: this.data('tabladinamica-settings').height
				});
				this.data('tabladinamica-settings').components.table = $(this).addClass('tabladinamica-tabla').append($('<caption></caption>'));
				this.data('tabladinamica-settings').components.table.before(this.data('tabladinamica-settings').components.container);
				this.data('tabladinamica-settings').components.table.appendTo(this.data('tabladinamica-settings').components.container);
				//this.data('tabladinamica-settings').components.container.fixedHeader({target: '.tabladinamica-tabla'});
				this.data('tabladinamica-settings').components.caption = $('<span class="tabladinamica-caption"></span>').appendTo(this.data('tabladinamica-settings').components.table.children('caption')).text(this.data('tabladinamica-settings').caption);
				if (!this.data('tabladinamica-settings').addButtonInHeader) {
					this.data('tabladinamica-settings').components.addButton = $('<span class="tabladinamica-addbutton"><a class="boton actionAgregar" href="#"><img src="/img/botones/25/agregar.gif"></a></span>')
						.insertAfter(this.data('tabladinamica-settings').components.caption)
						.find('.actionAgregar');
				}
				if (this.data('tabladinamica-settings').header) {
					this.data('tabladinamica-settings').components.header = $('<thead class="tabladinamica-header"><tr></tr></thead>').appendTo(this.data('tabladinamica-settings').components.table).find('tr').eq(0);
				}
				this.data('tabladinamica-settings').components.body = $('<tbody class="tabladinamica-body"></tbody>').appendTo(this.data('tabladinamica-settings').components.table);

				if (this.data('tabladinamica-settings').actions) {
					var tieneEditar = (this.data('tabladinamica-settings').buttons.indexOf('E') >= 0);
					var tieneQuitar = (this.data('tabladinamica-settings').buttons.indexOf('Q') >= 0);
					this.data('tabladinamica-settings').columnsConfig.push({
						id: 'actions',
						name: (this.data('tabladinamica-settings').addButtonInHeader ? '' : (tieneEditar ? 'E' : '') + (tieneEditar && tieneQuitar ? ' / ' : '') + (tieneQuitar ? 'Q' : '')),
						title: (this.data('tabladinamica-settings').addButtonInHeader ? '' : (tieneEditar ? 'Editar' : '') + (tieneEditar && tieneQuitar ? ' / ' : '') + (tieneQuitar ? 'Quitar' : '')),
						width: (this.data('tabladinamica-settings').buttons.length * 30) + 'px',
						css: {textAlign: 'center', paddingTop: '4px'},
						template: (tieneEditar ? '<a class="boton vaMiddle actionEditar" href="#"><img src="/img/botones/25/editar.gif"></a>' : '') +
								  (tieneQuitar ? '<a class="boton vaMiddle actionQuitar" href="#"><img src="/img/botones/25/menos.gif"></a>' : '')
					});
				}

				for (i in this.data('tabladinamica-settings').columnsConfig) {
					var o = $.extend(baseColumnsConfig, this.data('tabladinamica-settings').columnsConfig[i]);
					var th = $('<th data-id="' + o.id + '"></th>')
						.data('config', o)
						.css({width: (o.width ? o.width : 'auto')})
						.html(o.name);
					if (o.title) {
						th.attr('title', o.title);
					}
					th.appendTo(this.data('tabladinamica-settings').components.header);
					this.data('tabladinamica-settings').columnsMapping[o.id] = i;
				}

				if (this.data('tabladinamica-settings').addButtonInHeader) {
					this.data('tabladinamica-settings').components.addButton = $('<span><a class="boton actionAgregar" href="#"><img src="/img/botones/25/agregar.gif"></a></span>')
						.appendTo(this.data('tabladinamica-settings').components.header.children('th[data-id=actions]'))
						.find('.actionAgregar');
				}

				this.data('tabladinamica-settings').components.addButton.click($.proxy(function() {this.tablaDinamica('add');}, this));

				for (var i = 0; i < this.data('tabladinamica-settings').defaultRows; i++) {
					this.tablaDinamica('addRow');
				}
			}, this));
		},
		config: function(options) {
			if (typeof this.data('tabladinamica-settings') === 'undefined') {
				throw 'Deberá llamarse primero al método "init" y luego al config';
			}
			this.data('tabladinamica-settings', $.extend(this.data('tabladinamica-settings'), options));
		},
		cambiarModo: function(modo) {
			switch (modo) {
				case 'inicio':
					this.tablaDinamica('clean');
					this.tablaDinamica('disabled', true);
					break;
				case 'buscar':
					this.tablaDinamica('disabled', true);
					break;
				case 'agregar':
					this.tablaDinamica('clean');
					this.tablaDinamica('disabled', false);
					break;
				case 'editar':
					this.tablaDinamica('disabled', false);
					break;
			}
		},
		disabled: function(bool) {
			var botones = this.data('tabladinamica-settings').components.table.find('.boton');
			bool ? botones.hide() : botones.show();
		},
		clean: function() {
			var i;
			for (i = this.data('tabladinamica-settings').rows.length - 1; i >= 0; i--) {
				this.tablaDinamica('removeRow', i);
			}
			this.data('tabladinamica-settings').rowIndex = 0;
		},
		add: function() {
			this.tablaDinamica('addRow');
		},
		load: function(rows) {
			var i;
			this.tablaDinamica('clean');
			for (i in rows) {
				var row = rows[i];
				this.tablaDinamica('addRow', row);
			}
		},
		getSibling: function(colid) {
			return this.parents('tr[data-rowid]:first').find('[data-colid="' + colid + '"]').data('obj');
		},
		getMe: function() {
			return this.get(0).tagName == 'TD' ? this.data('obj') : this.parents('td[data-colid]:first').data('colid');
		},
		getRow: function(rowid) {
			var tr = this.find('tr[data-rowid="' + rowid + '"]:first');
			var retObj = {};
			for (i in this.data('tabladinamica-settings').columnsConfig) {
				retObj[this.data('tabladinamica-settings').columnsConfig[i].id] = tr.find('[data-colid="' + this.data('tabladinamica-settings').columnsConfig[i].id + '"]').data('obj');
			}
			return retObj;
		},
		getObj: function(rowid, colid) {
			return this.find('tr[data-rowid="' + rowid + '"]:first [data-colid="' + colid + '"]').data('obj');
		},
		/*
		addColumn: function() {
		},*/
		addRow: function(fillObject) {
			var rowId = this.data('tabladinamica-settings').rowIndex;
			var tr = $('<tr data-rowid="' + rowId + '"></tr>');
			for (i in this.data('tabladinamica-settings').columnsConfig) {
				var config = this.data('tabladinamica-settings').columnsConfig[i];
				var td = $('<td data-rowid="' + rowId + '" data-colid="' + config.id + '"></td>');

				var obj = bigSwitch(config);
				obj.createCell(td, this);
				if (fillObject) {
					//No va a funcionar con los ASB pq tienen un delay (Y)
					obj.fillRow(fillObject);
				}
				td.data('obj', obj);
				tr.append(td);
			}

			//Bindeo eventos en el TR
			tr.find('.actionEditar').click($.proxy(function(){this.tablaDinamica('editRow', rowId);}, this));
			tr.find('.actionQuitar').click($.proxy(function(){this.tablaDinamica('removeRow', this.tablaDinamica('getRowIndex', rowId));}, this));

			//Agrego el TR al body de la tabla
			this.data('tabladinamica-settings').components.body.append(tr);

			//Agrego el TR a la lista de rows
			this.data('tabladinamica-settings').rows.push(tr);
			this.data('tabladinamica-settings').rowIndex++;

			if (this.data('tabladinamica-settings').addCallback) {
				this.data('tabladinamica-settings').addCallback(this.data('tabladinamica-settings').rowIndex - 1);
			}

			return this.data('tabladinamica-settings').rowIndex - 1;
		},
		edit: function(tipoImporte, nro) {
			var obj = this.data('tabladinamica-settings').importes[tipoImporte][nro];
			obj.openPopUp();
		},/*
		removeColumn: function(colId) {
			var colNumber = this.data('tabladinamica-settings').columnsMapping[colId];
			//Elimino la config de la columna número colNumber
			//Elimino todos los TD y TH de la columna colNumber
		},*/
		removeRow: function(rowNumber) {
			this.data('tabladinamica-settings').rows[rowNumber].remove();
			this.data('tabladinamica-settings').rows.splice(rowNumber, 1);

			if (this.data('tabladinamica-settings').removeCallback) {
				this.data('tabladinamica-settings').removeCallback(this.data('tabladinamica-settings').rowIndex - 1);
			}
		},
		getRowIndex: function(rowId) {
			//Devuelve el número de fila de la tabla que corresponde con este registro, pero empezando de 0
			var rows = $(this.data('tabladinamica-settings').components.body).find('tr[data-rowid="' + rowId + '"]').eq(0);
			return (rows.length == 1) ? (rows[0].rowIndex - 1) : false;
		},
		save: function(tipoImporte, nro) {
			//TODO
			//noinspection JSCheckFunctionSignatures
			return this.data('tabladinamica-settings').importes[tipoImporte][nro].save();
		},
		getJson: function() {
			var json = [];
			for (i in this.data('tabladinamica-settings').rows) {
				var row = this.data('tabladinamica-settings').rows[i];
				var rowObj = {};
				$(row).children('td').each(function(i, td) {
					var o = $(td).data('obj');
					if (o.colid != 'actions') {
						var value = o.getValue();
						if (o.config.notEmpty && (value == '' || value == null)) {
							throw ('El campo "' + o.config.name + '" no puede estar en blanco (fila ' + o.rowid + ')');
						}
						rowObj[o.colid] = value;
					}
				});
				json.push(rowObj);
			}
			var error = false;
			if ($.isFunction(this.data('tabladinamica-settings').validate)) {
				error = this.data('tabladinamica-settings').validate(json);
				if (error !== true) {
					throw error;
				}
			}
			return json;
		}
	};

	function initialCheck() {
		var base = '[Tabla dinámica] Configuración incorrecta: ';
		if (this.data('tabladinamica-settings').addButtonInHeader && !this.data('tabladinamica-settings').header) {
			throw base + 'no puede incluirse el botón de agregar en el "header" porque éste está oculto';
		}
		if (this.data('tabladinamica-settings').addButtonInHeader && !this.data('tabladinamica-settings').actions) {
			throw base + 'no puede incluirse el botón de agregar en el "header" porque la columna "actions" está oculta';
		}
		if (this.data('tabladinamica-settings').actions && !this.data('tabladinamica-settings').buttons.length) {
			throw base + 'no puede mostrar la columna "actions" sin botones';
		}
		if (this.data('tabladinamica-settings').popUp && !this.data('tabladinamica-settings').popUpTemplate) {
			throw base + 'si se quiere utilizar PopUp deberá indicarse el "popUpTemplate"';
		}
		if (this.data('tabladinamica-settings').popUp && !this.data('tabladinamica-settings').popUpFillMapper) {
			throw base + 'si se quiere utilizar PopUp deberá proveer una función "saveCallback" para llenar (fill) la nueva fila';
		}
	}

	/*function createPopUps() {
		this.data('tabladinamica-settings').popUps.E = function() {
			return $('<div id="popup-efectivo">' +
					 '<div class="importes-popup-title">Agregar efectivo</div>' +
					 '<div class="importes-popup-content">' +
					 '<table><tbody>' +
					 '<tr><td><label>Importe: </label></td><td><input type="text" class="textbox importes-input-numeric obligatorio" id="importes-efectivo-importe" validate="Decimal" /></td></tr>' +
					 '</tbody></table>' +
					 '</div>' +
					 '</div>');
		};
		this.data('tabladinamica-settings').popUps.C = function() {
			if (this.data('tabladinamica-settings').entradaSalida == 'E') {
				if (this.data('tabladinamica-settings').chequePropio) {
					return $('<div id="popup-cheque">' +
							 '<div class="importes-popup-title">Agregar cheque</div>' +
							 '<div class="importes-popup-content">' +
							 '<table><tbody>' +
							 '<tr><td style="width: 150px;"><label>Cuenta bancaria: </label></td><td><input type="text" class="textbox w200 autoSuggestBox obligatorio" id="importes-cheque-cuentabancaria" name="CuentaBancaria" /></td></tr>' +
							 '<tr><td><label>Numero: </label></td><td><input type="text" class="textbox w200 autoSuggestBox obligatorio" id="importes-cheque-numero" name="ChequeraItem" linkedTo="importes-cheque-cuentabancaria,CuentaBancaria" /></td></tr>' +
							 '<tr><td><label>Importe: </label></td><td><input type="text" class="textbox w200 importes-input-numeric obligatorio" id="importes-cheque-importe" validate="DecimalPositivo" /></td></tr>' +
							 '<tr><td><label>Fecha de emisión: </label></td><td><input type="text" class="textbox w180 obligatorio" id="importes-cheque-fechaemision" validate="Fecha" value="' + funciones.hoy() + '" /></td></tr>' +
							 '<tr><td><label>Fecha de vencimiento: </label></td><td><input type="text" class="textbox w180 obligatorio" id="importes-cheque-fechavencimiento" validate="Fecha" /></td></tr>' +
							 '<tr><td><label>No a la orden: </label></td><td><input type="checkbox" class="textbox koiCheckbox" id="importes-cheque-noalaorden" /></td></tr>' +
							 '<tr><td><label>Cruzado: </label></td><td><input type="checkbox" class="textbox koiCheckbox" id="importes-cheque-cruzado" checked /></td></tr>' +
							 '</tbody></table>' +
							 '</div>' +
							 '</div>');
				} else {
					return $('<div id="popup-cheque">' +
							 '<div class="importes-popup-title">Agregar cheque</div>' +
							 '<div class="importes-popup-content">' +
							 '<table><tbody>' +
							 '<tr><td style="width: 150px;"><label>Banco: </label></td><td><input type="text" class="textbox w200 autoSuggestBox obligatorio" id="importes-cheque-banco" name="Banco" /></td></tr>' +
							 '<tr><td><label>Numero: </label></td><td><input type="text" class="textbox w200 obligatorio aRight" id="importes-cheque-numero" validate="Cheque" /></td></tr>' +
							 '<tr><td><label>Importe: </label></td><td><input type="text" class="textbox w200 importes-input-numeric obligatorio" id="importes-cheque-importe" validate="DecimalPositivo" /></td></tr>' +
							 '<tr><td><label>Fecha de emisión: </label></td><td><input type="text" class="textbox w180 obligatorio" id="importes-cheque-fechaemision" validate="Fecha" /></td></tr>' +
							 '<tr><td><label>Fecha de vencimiento: </label></td><td><input type="text" class="textbox w180 obligatorio" id="importes-cheque-fechavencimiento" validate="Fecha" /></td></tr>' +
							 '<tr><td><label>No a la orden: </label></td><td><input type="checkbox" class="textbox koiCheckbox" id="importes-cheque-noalaorden" /></td></tr>' +
							 '<tr><td><label>Cruzado: </label></td><td><input type="checkbox" class="textbox koiCheckbox" id="importes-cheque-cruzado" checked /></td></tr>' +
							 '<tr><td><label>Cuit librador: </label></td><td><input type="text" class="textbox w200 obligatorio" id="importes-cheque-cuitlibrador" validate="Cuit" value="' + this.data('tabladinamica-settings').cuitLibrador + '" /></td></tr>' +
							 '<tr><td><label>Nombre librador: </label></td><td><input type="text" class="textbox w200 obligatorio" id="importes-cheque-nombrelibrador" maxlength="20" value="' + this.data('tabladinamica-settings').nombreLibrador + '" /></td></tr>' +
							 '</tbody></table>' +
							 '</div>' +
							 '</div>');
				}
			} else {
				var div = $('<div id="popup-cheque">' +
							'<div class="importes-popup-title">Elegir cheque</div>' +
							'<div class="importes-popup-content">' +
							'<div class="importes-popup-content-filtros">' +
							'<table><tbody>' +
							'<tr><td><label>Rango días vto: </label></td><td><input type="text" class="textbox w40p importes-input-numeric" id="importes-popup-content-filtros-diasdesde" placeholder="Desde..." validate="Entero"> - <input type="text" class="textbox w40p importes-input-numeric" id="importes-popup-content-filtros-diashasta" placeholder="Hasta..." validate="Entero"></td></tr>' +
							'<tr><td><label>Rango importe: </label></td><td><input type="text" class="textbox w40p importes-input-numeric" id="importes-popup-content-filtros-importedesde" placeholder="Desde..." validate="Decimal"> - <input type="text" class="textbox w40p importes-input-numeric" id="importes-popup-content-filtros-importehasta" placeholder="Hasta..." validate="Decimal"></td></tr>' +
							'<tr><td><label>Rango fecha vto: </label></td><td><input type="text" class="textbox w36p importes-input-numeric" id="importes-popup-content-filtros-fechadesde" to="importes-popup-content-filtros-fechahasta" placeholder="Desde..." validate="Fecha"> - <input type="text" class="textbox w36p importes-input-numeric" id="importes-popup-content-filtros-fechahasta" from="importes-popup-content-filtros-fechadesde" placeholder="Hasta..." validate="Fecha"></td></tr>' +
							'<tr><td><label>Orden:</label></td><td><select id="importes-popup-cheque-orden" class="textbox filtroBuscar w90p">' +
							'<option value="1">Fecha vencimiento ascendente</option>' +
							'<option value="2">Fecha vencimiento descentente</option>' +
							'<option value="3">Días al vencimiento ascendente</option>' +
							'<option value="4">Días al vencimiento descendente</option>' +
							'<option value="5">Importe ascendente</option>' +
							'<option value="6">Importe descendente</option>' +
							'</select></td></tr>' +
							'<tr><td id="btnGoesHere" class="aRight" colspan="2"></td></tr>' +
							'</tbody></table>' +
							'</div><div class="importes-popup-lista bAllOrange corner5">' +
							'<table class="importes-striped"><thead class="tableHeader"><tr>' +
							'<th class="w80">F. venc.</th>' +
							'<th>Nro cheque</th>' +
							'<th>Librador</th>' +
							'<th>Banco</th>' +
							'<th>Días vto.</th>' +
							'<th>Importe</th>' +
							'<th></th>' +
							'</tr></thead>' +
							'<tbody id="importes-popup-cheque-body">' +
							'</tbody></table>' +
							'</div><div class="importes-popup-lista-totales">Suma importe: <span id="importes-popup-cheque-sumaimporte">$ 0,00</span></div></div>' +
							'</div>');
				div.find('#btnGoesHere').append($('<a class="boton" href="#" title="Actualizar" ><img src="/img/botones/25/actualizar.gif"></a>').click(refreshCheques));
				div.fixedHeader({target: '.importes-popup-lista'});
				return div;
			}
		};
		this.data('tabladinamica-settings').popUps.T = function() {
			return	$('<div id="popup-transferencia">' +
						'<div class="importes-popup-title">Agregar transferencia</div>' +
						'<div class="importes-popup-content">' +
						'<table><tbody>' +
						'<tr><td><label>Cuenta bancaria ' + (this.data('tabladinamica-settings').entradaSalida == 'E' ? 'receptor' : 'emisor') + ': </label></td><td><input type="text" class="textbox w200 autoSuggestBox obligatorio" id="importes-transferencia-cuentabancaria" name="CuentaBancaria" /></td></tr>' +
						'<tr><td><label>Importe: </label></td><td><input type="text" class="textbox w200 importes-input-numeric obligatorio" id="importes-transferencia-importe" validate="DecimalPositivo" /></td></tr>' +
						'<tr><td><label>Fecha: </label></td><td><input type="text" class="textbox w180 obligatorio" id="importes-transferencia-fechatransferencia" validate="Fecha" value="' + funciones.hoy() + '" /></td></tr>' +
						'</tbody></table>' +
						'</div>' +
						'</div>');
		};
		this.data('tabladinamica-settings').popUps.S = function() {
			return	$('<div id="popup-retencion">' +
						'<div class="importes-popup-title">Agregar retención</div>' +
						'<div class="importes-popup-content">' +
						'<table><tbody>' +
						'<tr><td><label>Tipo: </label></td><td><input type="text" class="textbox w200 autoSuggestBox obligatorio" id="importes-retencion-tiporetencion" name="TipoRetencion" /></td></tr>' +
						'<tr><td><label>Importe: </label></td><td><input type="text" class="textbox w200 importes-input-numeric obligatorio" id="importes-retencion-importe" validate="DecimalPositivo" /></td></tr>' +
						'<tr><td><label>Fecha: </label></td><td><input type="text" class="textbox w180 obligatorio" id="importes-retencion-fecha" validate="Fecha" /></td></tr>' +
						'<tr><td><label>Nro Certificado: </label></td><td><input type="text" class="textbox w200 obligatorio aRight" id="importes-retencion-numerocertificado" validate="CertificadoRetencion" /></td></tr>' +
						'<tr><td><label>Cuit: </label></td><td><input type="text" class="textbox w200" id="importes-retencion-cuit" validate="Cuit" value="' + this.data('tabladinamica-settings').cuitLibrador + '" /></td></tr>' +
						'<tr><td><label>Nombre: </label></td><td><input type="text" class="textbox w200" id="importes-retencion-nombre" maxlength="100" value="' + this.data('tabladinamica-settings').nombreLibrador + '" /></td></tr>' +
						'</tbody></table>' +
						'</div>' +
						'</div>');
		};
	}*/
	function bigSwitch(config) {
		switch (config.cellType) {
			case 'A':
				return new AutoSuggestBox(config);
				break;
			case 'C':
				return new Checkbox(config);
				break;
			case 'I':
				return new Input(config);
				break;
			case 'L':
				return new Label(config);
				break;
			case 'S':
				return new Select(config);
				break;
			case 'T':
				return new Textarea(config);
				break;
			default:
				return new Generic(config);
				break;
		}
	}
	function genericOpenPopUp(obj) {
		var popup = this.data('tabladinamica-settings').popUps[obj.tipoImporte]();
		$.jPopUp.show(popup, [
			{
				value: 'Guardar',
				action: function(){
					var error = this.data('tabladinamica-settings').divContainer.importes('save', obj.tipoImporte, obj.nro);
					if (!error) {
						$.jPopUp.close();
					} else {
						$.error(error);
					}
				}
			}, {
				value: 'Cancelar',
				action: function(){
					if (!obj.tableRow) {
						this.data('tabladinamica-settings').divContainer.importes('remove', obj.tipoImporte, obj.nro);
					}
					$.jPopUp.close();
				}
			}
		], false, function(){obj.fillPopUp(popup);});
	}
	function genericCreateTd(o) {
		(!$.isEmptyObject(o.config.css)) && o.tableCell.css(o.config.css);
		(o.config.click) && o.tableCell.click($.proxy(o.config.click, o.tableCell));
		(o.config.template) && o.tableCell.html(o.config.template);
	}
	function genericSave(o) {
		//TODO
		//Acá tengo q analizar a qué celda y a qué tipo de celda corresponde cada uno de los inputs del popup, y validar/asignar/guardar según eso

		if (!o.tableRow) {
			o.tableRow = {};
			o.tableRow.tdTipo = $('<td></td>');
			o.tableRow.tdImporte = $('<td class="aRight"></td>');
			o.tableRow.tdResumen = $('<td></td>');
			o.tableRow.tdEditar = $('<td class="aCenter" tipo-importe="' + o.tipoImporte + '" nro="' + o.nro + '"><label class="cPointer">Editar</label></td>').click(function(){
				this.data('tabladinamica-settings').divContainer.importes('edit', $(this).attr('tipo-importe'), $(this).attr('nro'));
			});
			o.tableRow.tdQuitar = $('<td class="aCenter" tipo-importe="' + o.tipoImporte + '" nro="' + o.nro + '"><label class="cPointer">Quitar</label></td>').click(function(){
				this.data('tabladinamica-settings').divContainer.importes('remove', $(this).attr('tipo-importe'), $(this).attr('nro'));
			});
			o.tableRow.row = $('<tr></tr>')
				.append(o.tableRow.tdTipo)
				.append(o.tableRow.tdImporte)
				.append(o.tableRow.tdResumen)
				.append(o.tableRow.tdEditar)
				.append(o.tableRow.tdQuitar);
			this.data('tabladinamica-settings').tableBodyImportes.append(o.tableRow.row);
		}
		o.tableRow.tdTipo.text(o.getTextTipo());
		o.tableRow.tdImporte.text(o.getTextImporte());
		o.tableRow.tdResumen.text(o.getTextResumen());
	}

	function genericGetValue(o) {
		return o.config.getJson(o);
	}
	function genericBlank(o) {
		o.tableCell.html('');
	}
	function genericReset(o) {
		o.blank();
		o.createCell(o.tableCell);
	}
	function genericDisable(o) {
		o.valueElement.disable();
	}
	function genericFill(o, fillObject) {
		if (fillObject && !$.isEmptyObject(fillObject)) {
			if (fillObject.hasOwnProperty(o.colid)) {
				o.setValue(fillObject[o.colid]);
			}
		}
	}
	function getOrSet(o, id, val) {
		if (typeof val == 'undefined') {
			switch (o.find('input[id="' + id + '"]').attr('type')) {
				case 'checkbox':
					return (o.find('input[id="' + id + '"]').isChecked() ? 'S' : 'N');
				default:
					return o.find('input[id="' + id + '"]').val();
			}
		} else {
			switch (o.find('input[id="' + id + '"]').attr('type')) {
				case 'checkbox':
					return (val == 'S' ? o.find('input[id="' + id + '"]').check() : o.find('input[id="' + id + '"]').uncheck());
				default:
					return o.find('input[id="' + id + '"]').val(val);
			}
		}
	}

	function Generic(config) {
		this.valueElement = null;
		this.tableCell = null;
		this.rowid = null;
		this.colid = null;
		this.config = config;
		this.createCell = function(td) {
			this.tableCell = td;
			this.valueElement = td;
			this.rowid = this.tableCell.data('rowid');
			this.colid = this.tableCell.data('colid');
			genericCreateTd(this);
		};
		this.blank = function() {
			genericBlank(this);
		};
		this.reset = function() {
			genericReset(this);
		};
		this.disable = function() {
			genericDisable(this);
		};
		this.getValue = function() {
			if ($.isFunction(this.config.getJson)) {
				return genericGetValue(this);
			}
			return (!this.valueElement) ? false : this.valueElement.html();
		};
		this.setValue = function(value) {
			(typeof value == 'string') ? this.tableCell.html(value) : this.tableCell.append(value);
			return this;
		};
		this.fillRow = function(obj) {
			genericFill(this, obj);
		};
	}
	function AutoSuggestBox(config) {
		this.valueElement = null;
		this.tableCell = null;
		this.rowid = null;
		this.colid = null;
		this.config = config;
		this.createCell = function(td, table) {
			this.tableCell = td;
			this.rowid = this.tableCell.data('rowid');
			this.colid = this.tableCell.data('colid');
			genericCreateTd(this);

			this.valueElement = this.tableCell.find('.autoSuggestBox:first');
			(!this.valueElement.hasClass('inputForm')) && this.valueElement.addClass('inputForm');
			this.valueElement.attr('id', table.attr('id') + '-' + this.rowid + '_' + this.colid);
			(this.config.blur) && this.valueElement.blur($.proxy(this.config.blur, this.tableCell));
			(this.config.focus) && this.valueElement.focus($.proxy(this.config.focus, this.tableCell));
			if ($.isFunction(this.config.valueByFunction)) {
				setTimeout($.proxy(function() {
					var val = this.config.valueByFunction(this);
					this.setValue(val);
				}, this), 1);
			}
		};
		this.blank = function() {
			genericBlank(this);
		};
		this.reset = function() {
			genericReset(this);
		};
		this.disable = function() {
			genericDisable(this);
		};
		this.getValue = function() {
			if ($.isFunction(this.config.getJson)) {
				return genericGetValue(this);
			}
			return (!this.valueElement) ? false : this.valueElement.next().val();
		};
		this.setValue = function(value) {
			if (this.valueElement) {
				setTimeout($.proxy(function() {
					this.next().val(value.id);
					this.next().next().val(value.nombre);
					this.val(value.id + (value.nombre ? ' - ' + value.nombre : ''));
				}, this.valueElement), 1000);
			}
			return this;
		};
		this.fillRow = function(obj) {
			genericFill(this, obj);
		};

		this.o = function() {return $('#popup-efectivo');};
		this.tipoImporte = 'E';
		this.nro = false;
		this.openPopUp = function() {
			genericOpenPopUp(this);
		};
		this.save = function () {
			var error = this.validar();
			if (!error) {
				this.fillObjectFromPopUp();
				genericSave(this);
				this.data('tabladinamica-settings').saveCallback();
			}
			return error;
		};
		this.validar = function() {
			if (!(getOrSet(this.o(), 'importes-efectivo-importe') > 0)) {
				return 'Debe ingresar un importe';
			}
			return false; //Si devuelvo false es porque está bien
		};
		this.fillPopUp = function(popup) {
			if (!$.isEmptyObject(this.props)) {
				getOrSet(popup, 'importes-efectivo-importe', this.props.importe);
			}
		};
		this.fillObjectFromPopUp = function() {
			this.props.importe = getOrSet(this.o(), 'importes-efectivo-importe');
		};
		this.fillObjectFromDB = function(importe) {
			this.props = importe;
		};
	}
	function Checkbox(config) {
		this.valueElement = null;
		this.tableCell = null;
		this.rowid = null;
		this.colid = null;
		this.config = config;
		this.createCell = function(td, table) {
			this.tableCell = td;
			this.rowid = this.tableCell.data('rowid');
			this.colid = this.tableCell.data('colid');
			genericCreateTd(this);

			this.valueElement = this.tableCell.find('input[type="checkbox"]:first');
			(!this.valueElement.hasClass('inputForm')) && this.valueElement.addClass('inputForm');
			this.valueElement.attr('id', table.attr('id') + '-' + this.rowid + '_' + this.colid);
			if ($.isFunction(this.config.valueByFunction)) {
				(this.config.valueByFunction(this)) ? this.valueElement.check() : this.valueElement.uncheck();
			}
		};
		this.blank = function() {
			genericBlank(this);
		};
		this.reset = function() {
			genericReset(this);
		};
		this.disable = function() {
			genericDisable(this);
		};
		this.getValue = function() {
			if ($.isFunction(this.config.getJson)) {
				return genericGetValue(this);
			}
			return (!this.valueElement || !this.valueElement.isChecked()) ? 'N' : 'S';
		};
		this.setValue = function(value) {
			if (this.valueElement) {
				(value == 'S' || value === true) ? this.valueElement.check() : this.valueElement.uncheck();
			}
			return this;
		};
		this.fillRow = function(obj) {
			genericFill(this, obj);
		};

		this.fillPopUp = function(popup) {
			if (!$.isEmptyObject(this.props)) {
				if (this.data('tabladinamica-settings').entradaSalida == 'E') {
					if (this.data('tabladinamica-settings').chequePropio) {
						getOrSet(popup, 'importes-cheque-cuentabancaria', this.props.cuentaBancaria.id + ' - ' + this.props.cuentaBancaria.nombreCuenta);
						getOrSet(popup, 'importes-cheque-cuentabancaria_selectedValue', this.props.cuentaBancaria.id);
						getOrSet(popup, 'importes-cheque-cuentabancaria_selectedName', this.props.cuentaBancaria.nombreCuenta);
						getOrSet(popup, 'importes-cheque-numero', this.props.numero + ' - [' + funciones.padLeft(this.props.numero, 8, '0') + ']');
						getOrSet(popup, 'importes-cheque-numero_selectedValue', this.props.numero);
						getOrSet(popup, 'importes-cheque-numero_selectedName', '[' + funciones.padLeft(this.props.numero, 8, '0') + ']');
						getOrSet(popup, 'importes-cheque-importe', this.props.importe);
						getOrSet(popup, 'importes-cheque-fechaemision', this.props.fechaEmision);
						getOrSet(popup, 'importes-cheque-fechavencimiento', this.props.fechaVencimiento);
						getOrSet(popup, 'importes-cheque-noalaorden', this.props.noALaOrden);
						getOrSet(popup, 'importes-cheque-cruzado', this.props.cruzado);
					} else {
						getOrSet(popup, 'importes-cheque-banco', this.props.banco.idBanco + ' - ' + this.props.banco.nombre);
						getOrSet(popup, 'importes-cheque-banco_selectedValue', this.props.banco.idBanco);
						getOrSet(popup, 'importes-cheque-banco_selectedName', this.props.banco.nombre);
						getOrSet(popup, 'importes-cheque-numero', this.props.numero);
						getOrSet(popup, 'importes-cheque-importe', this.props.importe);
						getOrSet(popup, 'importes-cheque-fechaemision', this.props.fechaEmision);
						getOrSet(popup, 'importes-cheque-fechavencimiento', this.props.fechaVencimiento);
						getOrSet(popup, 'importes-cheque-noalaorden', this.props.noALaOrden);
						getOrSet(popup, 'importes-cheque-cruzado', this.props.cruzado);
						getOrSet(popup, 'importes-cheque-cuitlibrador', this.props.libradorCuit);
						getOrSet(popup, 'importes-cheque-nombrelibrador', this.props.libradorNombre);
					}
				}
			}
		};
		this.fillObjectFromPopUp = function() {
			if (this.data('tabladinamica-settings').entradaSalida == 'E') {
				if (this.data('tabladinamica-settings').chequePropio) {
					this.props.cuentaBancaria = {};
					this.props.cuentaBancaria.id = getOrSet(this.o(), 'importes-cheque-cuentabancaria_selectedValue');
					this.props.cuentaBancaria.nombreCuenta = getOrSet(this.o(), 'importes-cheque-cuentabancaria_selectedName');
					this.props.numero = getOrSet(this.o(), 'importes-cheque-numero_selectedValue');
					this.props.importe = getOrSet(this.o(), 'importes-cheque-importe');
					this.props.fechaEmision = getOrSet(this.o(), 'importes-cheque-fechaemision');
					this.props.fechaVencimiento = getOrSet(this.o(), 'importes-cheque-fechavencimiento');
					this.props.noALaOrden = getOrSet(this.o(), 'importes-cheque-noalaorden');
					this.props.cruzado = getOrSet(this.o(), 'importes-cheque-cruzado');
				} else {
					this.props.banco = {};
					this.props.banco.idBanco = getOrSet(this.o(), 'importes-cheque-banco_selectedValue');
					this.props.banco.nombre = getOrSet(this.o(), 'importes-cheque-banco_selectedName');
					this.props.bancosucursal = {};
					this.props.bancosucursal.idBanco = getOrSet(this.o(), 'importes-cheque-bancosucursal_selectedValue');
					this.props.bancosucursal.nombre = getOrSet(this.o(), 'importes-cheque-bancosucursal_selectedName');
					this.props.numero = getOrSet(this.o(), 'importes-cheque-numero');
					this.props.importe = getOrSet(this.o(), 'importes-cheque-importe');
					this.props.fechaEmision = getOrSet(this.o(), 'importes-cheque-fechaemision');
					this.props.fechaVencimiento = getOrSet(this.o(), 'importes-cheque-fechavencimiento');
					this.props.noALaOrden = getOrSet(this.o(), 'importes-cheque-noalaorden');
					this.props.cruzado = getOrSet(this.o(), 'importes-cheque-cruzado');
					this.props.libradorCuit = getOrSet(this.o(), 'importes-cheque-cuitlibrador');
					this.props.libradorNombre = getOrSet(this.o(), 'importes-cheque-nombrelibrador');
				}
			} else{
				this.checked = [];

			}
			this.props.chequePropio = this.data('tabladinamica-settings').chequePropio ? 1 : 0;
		};
	}
	function Input(config) {
		this.valueElement = null;
		this.tableCell = null;
		this.rowid = null;
		this.colid = null;
		this.config = config;
		this.createCell = function(td, table) {
			this.tableCell = td;
			this.rowid = this.tableCell.data('rowid');
			this.colid = this.tableCell.data('colid');
			genericCreateTd(this);

			this.valueElement = this.tableCell.find('input[type="text"]:first').not('.autoSuggestBox');
			(!this.valueElement.hasClass('inputForm')) && this.valueElement.addClass('inputForm');
			this.valueElement.attr('id', table.attr('id') + '-' + this.rowid + '_' + this.colid);
			(this.config.blur) && this.valueElement.blur($.proxy(this.config.blur, this.tableCell));
			(this.config.focus) && this.valueElement.focus($.proxy(this.config.focus, this.tableCell));
			if ($.isFunction(this.config.valueByFunction)) {
				this.valueElement.val(this.config.valueByFunction(this));
			}
		};
		this.blank = function() {
			genericBlank(this);
		};
		this.reset = function() {
			genericReset(this);
		};
		this.disable = function() {
			genericDisable(this);
		};
		this.getValue = function() {
			if ($.isFunction(this.config.getJson)) {
				return genericGetValue(this);
			}
			return (!this.valueElement) ? false : this.valueElement.val();
		};
		this.setValue = function(value) {
			if (this.valueElement) {
				this.valueElement.val(value);
			}
			return this;
		};
		this.fillRow = function(obj) {
			genericFill(this, obj);
		};
	}
	function Label(config) {
		this.valueElement = null;
		this.tableCell = null;
		this.rowid = null;
		this.colid = null;
		this.config = config;
		this.createCell = function(td, table) {
			this.tableCell = td;
			this.rowid = this.tableCell.data('rowid');
			this.colid = this.tableCell.data('colid');
			genericCreateTd(this);
			(this.config.template) && this.tableCell.html('<label>' + this.config.template + '</label>');

			this.valueElement = this.tableCell.find('label:first');
			this.valueElement.attr('id', table.attr('id') + '-' + this.rowid + '_' + this.colid);
			if ($.isFunction(this.config.valueByFunction)) {
				this.valueElement.text(this.config.valueByFunction(this));
			}
		};
		this.blank = function() {
			genericBlank(this);
		};
		this.reset = function() {
			genericReset(this);
		};
		this.disable = function() {
			genericDisable(this);
		};
		this.getValue = function() {
			if ($.isFunction(this.config.getJson)) {
				return genericGetValue(this);
			}
			return (!this.valueElement) ? false : this.valueElement.text();
		};
		this.setValue = function(value) {
			if (this.valueElement) {
				this.valueElement.text(value);
			}
			return this;
		};
		this.fillRow = function(obj) {
			genericFill(this, obj);
		};
	}
	function Select(config) {
		this.valueElement = null;
		this.tableCell = null;
		this.rowid = null;
		this.colid = null;
		this.config = config;
		this.createCell = function(td, table) {
			this.tableCell = td;
			this.rowid = this.tableCell.data('rowid');
			this.colid = this.tableCell.data('colid');
			genericCreateTd(this);

			this.valueElement = this.tableCell.find('select:first');
			(!this.valueElement.hasClass('inputForm')) && this.valueElement.addClass('inputForm');
			this.valueElement.attr('id', table.attr('id') + '-' + this.rowid + '_' + this.colid);
			(this.config.blur) && this.valueElement.blur($.proxy(this.config.blur, this.tableCell));
			(this.config.focus) && this.valueElement.focus($.proxy(this.config.focus, this.tableCell));
			if ($.isFunction(this.config.valueByFunction)) {
				this.valueElement.val(this.config.valueByFunction(this));
			}
		};
		this.blank = function() {
			genericBlank(this);
		};
		this.reset = function() {
			genericReset(this);
		};
		this.disable = function() {
			genericDisable(this);
		};
		this.getValue = function() {
			if ($.isFunction(this.config.getJson)) {
				return genericGetValue(this);
			}
			return (!this.valueElement) ? false : this.valueElement.val();
		};
		this.setValue = function(value) {
			if (this.valueElement) {
				this.valueElement.val(value);
			}
			return this;
		};
		this.fillRow = function(obj) {
			genericFill(this, obj);
		};
	}
	function Textarea(config) {
		this.valueElement = null;
		this.tableCell = null;
		this.rowid = null;
		this.colid = null;
		this.config = config;
		this.createCell = function(td, table) {
			this.tableCell = td;
			this.rowid = this.tableCell.data('rowid');
			this.colid = this.tableCell.data('colid');
			genericCreateTd(this);

			this.valueElement = this.tableCell.find('textarea:first');
			(!this.valueElement.hasClass('inputForm')) && this.valueElement.addClass('inputForm');
			this.valueElement.attr('id', table.attr('id') + '-' + this.rowid + '_' + this.colid);
			(this.config.blur) && this.valueElement.blur($.proxy(this.config.blur, this.tableCell));
			(this.config.focus) && this.valueElement.focus($.proxy(this.config.focus, this.tableCell));
			if ($.isFunction(this.config.valueByFunction)) {
				this.valueElement.val(this.config.valueByFunction(this));
			}
		};
		this.blank = function() {
			genericBlank(this);
		};
		this.reset = function() {
			genericReset(this);
		};
		this.disable = function() {
			genericDisable(this);
		};
		this.getValue = function() {
			if ($.isFunction(this.config.getJson)) {
				return genericGetValue(this);
			}
			return (!this.valueElement) ? false : this.valueElement.val();
		};
		this.setValue = function(value) {
			if (this.valueElement) {
				this.valueElement.val(value);
			}
			return this;
		};
		this.fillRow = function(obj) {
			genericFill(this, obj);
		};
	}

	$.fn.tablaDinamica = function(method) {
		if (!this.length) {
			//$.error('Se intentó llamar al método ' +  method + ' con un selector de tabla dinámica que no existe');
			//Comento la linea porque a veces se llama al .clean() en el limpiarScreen que se ejecuta antes del ready, entonces la tabla ni existe
		} else if (methods[method]) {
			//Si existe el método que me piden, lo llamo, y le mando por parametro to_dos los que me mandaron excepto el primero que es el método
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			//Si no llamo a un método, o si mando un objeto como primer parámetro (config), llamo al INIT y le mando todos los parámetros
			return methods.init.apply(this, arguments);
		} else {
			$.error('No existe el método ' +  method + ' en el plugin jQuery.tablaDinamica');
		}
	};
})(jQuery);