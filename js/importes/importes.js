/**
 * Este plugin usa jPopUp y validate. Además uso cosas como la clase .textbox que me sirve para estilo y comportamiento (al apretar enter)
 *
 * $('.pluginImportes')
 */

(function($) {
	var defaults = {
		speed: 300,
		easing: '',
		changeText: true,
		showText: 'Importes',
		hideText: 'Ocultar',
		height: 'auto',
		minimoUnImporte: true,
		entradaSalida: 'E',
		chequePropio: false,
		botones: ['E', 'C', 'T', 'S'],
		divContainer: false,
		divTitle: false,
		aBtn: false,
		divToggle: false,
		divPopups: false,
		tableBodyImportes: false,
		idInputCaja: false,
		cuitLibrador: '',
		nombreLibrador: '',
		popUps: {E: false, C: false, T: false, R: false},
		saveCallback: function(){},
		removeCallback: function(){},
		importes: {}
	};
	var settings;
	var methods = {
		init: function(options) {
			$(document).keydown(function(e) {
				var tag = e.target.tagName.toLowerCase();
				if (tag != 'input' && tag != 'textarea') {
					switch (e.which) {
						case 69:
							$('.pluginImportes').importes('add', 'E');
							break;
						case 67:
							$('.pluginImportes').importes('add', 'C');
							break;
						case 84:
							$('.pluginImportes').importes('add', 'T');
							break;
						case 82:
							$('.pluginImportes').importes('add', 'S');
							break;
					}
				}
			});
			settings = $.extend(defaults, options);
			return this.each(function() {
				//var id = $(this).attr('id');
				settings.divContainer = $(this).addClass('importes-container pantalla');
				settings.divTitle = $('<div class="importes-title"><div><span>5</span><span>' + settings.showText + '</span></div></div>').appendTo(settings.divContainer);
				settings.divBtns = $('<div class="btn-dropdown pull-right"></div>');

				var auxBotones = {'E': 'Efectivo', 'C': 'Cheque', 'T': 'Transferencia', 'S': 'Retención'};
				for (i in settings.botones) {
					var b = settings.botones[i];
					if (!auxBotones[b])
						continue;
					settings.divBtns.append($('<a class="btn disabled" name="' + b + '" href="#"><span class="btn-icon">+</span><span class="btn-text">' + auxBotones[b] + '</span></a>').click(function(){
						settings.divContainer.importes('add', $(this).attr('name'));
					}));
				}

				settings.table = $('<table class="importes-striped"><thead><tr><th>Tipo</th><th>Importe</th><th>Resumen</th><th>Editar</th><th>Quitar</th></tr></thead><tbody></tbody></table>');
				settings.tableBodyImportes = settings.table.find('tbody');
				settings.divToggle = $('<div class="importes-toggle"></div>')
					.append(settings.divBtns)
					.append(settings.table)
					.css('height', settings.height)
					.appendTo(settings.divContainer)
					.hide();
				settings.divPopups = $('<div class="importes-popups"></div>').appendTo(settings.divContainer);
				settings.divTitle.children('div').click(function() {
					return settings.divToggle.is(':visible') ? methods.hide() : methods.show();
				});

				createPopUps();
			});
		},
		config: function(options) {
			if (typeof settings === 'undefined') {
				throw 'Deberá llamarse primero al método "init" y luego al config';
			}
			settings = $.extend(settings, options);
		},
		show: function() {
			return settings.divToggle.slideDown(settings.speed, settings.easing, function() {
				//noinspection JSCheckFunctionSignatures
				return settings.changeText ? settings.divTitle.children('div').children('span:nth-child(1)').text('6').next().text(settings.hideText) : false;
			});
		},
		hide: function() {
			return settings.divToggle.slideUp(settings.speed, settings.easing, function() {
				//noinspection JSCheckFunctionSignatures
				return settings.changeText ? settings.divTitle.children('div').children('span:nth-child(1)').text('5').next().text(settings.showText) : false;
			});
		},
		cambiarModo: function(modo) {
			switch (modo) {
				case 'inicio':
					this.importes('clean');
					this.importes('hide');
					this.importes('visible', false);
					this.importes('disabled', true);
					break;
				case 'buscar':
					this.importes('hide');
					this.importes('visible', true);
					this.importes('disabled', true);
					break;
				case 'agregar':
					this.importes('clean');
					this.importes('hide');
					this.importes('visible', true);
					this.importes('disabled', false);
					break;
				case 'editar':
					this.importes('visible', true);
					this.importes('disabled', false);
					break;
			}
		},
		visible: function(bool) {
			bool ? settings.divTitle.show() : settings.divTitle.hide();
		},
		disabled: function(bool) {
			var tds = settings.table.find('td:nth-child(4), td:nth-child(5), th:nth-child(4), th:nth-child(5)');
			if (bool) {
				settings.divBtns.find('a').addClass('disabled');
				tds.hide();
			} else {
				settings.divBtns.find('a').removeClass('disabled');
				tds.show();
			}
		},
		clean: function() {
			for (i in settings.importes) {
				for (j in settings.importes[i]) {
					if (settings.importes[i][j]) {
						settings.importes[i][j].remove();
					}
				}
			}
		},
		load: function(importes) {
			var i;
			for (i in importes) {
				var importe = importes[i];
				var obj = bigSwitch(importe.tipoImporte);
				if (obj) {
					if (!settings.importes[importe.tipoImporte]) {
						settings.importes[importe.tipoImporte] = [];
					}
					obj.nro = settings.importes[importe.tipoImporte].length;
					obj.fillObjectFromDB(importe.importe);
					settings.importes[importe.tipoImporte][obj.nro] = obj;
					genericSave(settings.importes[importe.tipoImporte][obj.nro]);
				}
			}
			settings.saveCallback();
		},
		add: function(tipoImporte) {
			var valid = false;
			for (i in settings.botones) {
				var b = settings.botones[i];
				if (b == tipoImporte) {
					valid = true;
					break;
				}
			}
			if (valid) {
				var obj = bigSwitch(tipoImporte);
				if (!settings.importes[tipoImporte]) {
					settings.importes[tipoImporte] = [];
				}
				obj.nro = settings.importes[tipoImporte].length;
				settings.importes[tipoImporte][obj.nro] = obj;
				obj.openPopUp();
			}
		},
		edit: function(tipoImporte, nro) {
			var obj = settings.importes[tipoImporte][nro];
			obj.openPopUp();
		},
		remove: function(tipoImporte, nro) {
			var obj = settings.importes[tipoImporte][nro];
			obj.remove();
		},
		save: function(tipoImporte, nro) {
			//noinspection JSCheckFunctionSignatures
			return settings.importes[tipoImporte][nro].save();
		},
		getJson: function() {
			var cant = 0;
			var json = {};
			for(tipoImporte in settings.importes) {
				json[tipoImporte] = [];
				for(nro in settings.importes[tipoImporte]) {
					if (settings.importes[tipoImporte][nro]) {
						cant += 1;
						json[tipoImporte].push(settings.importes[tipoImporte][nro].getJson());
					}
				}
			}
			if (!cant) {
				throw 'No puede realizar una operación sin importes';
			} else {
				return json;
			}
		},
		getImporte: function(tipoImporte){
			tipoImporte = tipoImporte || 'TOTAL';
			var sum = 0;
			for(tipo in settings.importes) {
				if (tipoImporte == 'TOTAL' || tipo == tipoImporte) {
					for(nro in settings.importes[tipo]) {
						if (settings.importes[tipo][nro]) {
							sum += funciones.toFloat(settings.importes[tipo][nro].props.importe);
						}
					}
				}
			}
			return sum;
		}
	};

	var refreshCheques = function() {
		if (!settings.idInputCaja) {
			throw 'Hay que decirle al plugin el ID del input de la caja (idInputCaja)';
		}
		var caja = $('#' + settings.idInputCaja + '_selectedValue').val();
		if (caja == '') {
			$.error('Debe seleccionar la caja del cheque');
		} else {
			var checked = {};
			$('#importes-popup-cheque-body tr input:checked').each(function() {
				checked[$(this).data('obj').id] = 1;
			});
			var obj = {
				idCaja: caja,
				fechaDesde: $('#importes-popup-content-filtros-fechadesde').val(),
				fechaHasta: $('#importes-popup-content-filtros-fechahasta').val(),
				diasDesde: $('#importes-popup-content-filtros-diasdesde').val(),
				diasHasta: $('#importes-popup-content-filtros-diashasta').val(),
				importeDesde: $('#importes-popup-content-filtros-importedesde').val(),
				importeHasta: $('#importes-popup-content-filtros-importehasta').val(),
				order: $('#importes-popup-cheque-orden').val()
			};
			$.showLoading();
			$.getJSON('/content' + window.location.pathname + 'getCheques.php?filtros=' + encodeURIComponent(JSON.stringify(obj)), function(json) {
				var cheque;
				$('#importes-popup-cheque-body').html('');
				for (var i in json.data) {
					cheque = json.data[i];
					var td = $('<input type="checkbox" id="chk_' + cheque.id + '">').data('id', cheque.id).data('obj', cheque).click(sumChecks);
					if (checked[cheque.id]) {
						td.check();
					}
					$('#importes-popup-cheque-body').append(
						$('<tr>')
							.append($('<td>').text(cheque.fechaVencimiento ? cheque.fechaVencimiento : ' '))
							.append($('<td>').text(cheque.numero ? cheque.numero : ' ').addClass('aRight'))
							.append($('<td>').text(cheque.libradorNombre ? cheque.libradorNombre : ' '))
							.append($('<td>').text(cheque.banco.nombre ? cheque.banco.nombre : ' '))
							.append($('<td>').text(cheque.diasVencimiento ? cheque.diasVencimiento : ' ').addClass('aCenter'))
							.append($('<td>').text(funciones.formatearMoneda(cheque.importe ? cheque.importe : ' ')).addClass('aRight'))
							.append($('<td>').append(td).addClass('aCenter'))
					);
				}
				sumChecks();
				$.hideLoading();
			});
		}
	};
	var sumChecks = function() {
		var t = 0;
		$('#importes-popup-cheque-body tr input:checked').each(function() {
			t += funciones.toFloat($(this).data('obj').importe);
		});
		$('#importes-popup-cheque-sumaimporte').text(funciones.formatearMoneda(t));
	};

	function createPopUps() {
		settings.popUps.E = function() {
			return $('<div id="popup-efectivo">' +
					 '<div class="importes-popup-title">Agregar efectivo</div>' +
					 '<div class="importes-popup-content">' +
					 '<table><tbody>' +
					 '<tr><td><label>Importe: </label></td><td><input type="text" class="textbox importes-input-numeric obligatorio" id="importes-efectivo-importe" validate="Decimal" /></td></tr>' +
					 '</tbody></table>' +
					 '</div>' +
					 '</div>');
		};
		settings.popUps.C = function() {
			if (settings.entradaSalida == 'E') {
				if (settings.chequePropio) {
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
							 '<tr><td><label>Cuit librador: </label></td><td><input type="text" class="textbox w200 obligatorio" id="importes-cheque-cuitlibrador" validate="Cuit" value="' + settings.cuitLibrador + '" /></td></tr>' +
							 '<tr><td><label>Nombre librador: </label></td><td><input type="text" class="textbox w200 obligatorio" id="importes-cheque-nombrelibrador" maxlength="20" value="' + settings.nombreLibrador + '" /></td></tr>' +
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
		settings.popUps.T = function() {
			return	$('<div id="popup-transferencia">' +
						'<div class="importes-popup-title">Agregar transferencia</div>' +
						'<div class="importes-popup-content">' +
						'<table><tbody>' +
						'<tr><td><label>Cuenta bancaria ' + (settings.entradaSalida == 'E' ? 'receptor' : 'emisor') + ': </label></td><td><input type="text" class="textbox w200 autoSuggestBox obligatorio" id="importes-transferencia-cuentabancaria" name="CuentaBancaria" /></td></tr>' +
						'<tr><td><label>Numero transf.: </label></td><td><input type="text" class="textbox w200 aRight" id="importes-transferencia-numerotransferencia" validate="EnteroPositivo" /></td></tr>' +
						'<tr><td><label>Importe: </label></td><td><input type="text" class="textbox w200 importes-input-numeric obligatorio" id="importes-transferencia-importe" validate="DecimalPositivo" /></td></tr>' +
						'<tr><td><label>Fecha: </label></td><td><input type="text" class="textbox w180 obligatorio" id="importes-transferencia-fechatransferencia" validate="Fecha" value="' + funciones.hoy() + '" /></td></tr>' +
						'</tbody></table>' +
						'</div>' +
						'</div>');
		};
		settings.popUps.S = function() {
			return	$('<div id="popup-retencion">' +
						'<div class="importes-popup-title">Agregar retención</div>' +
						'<div class="importes-popup-content">' +
						'<table><tbody>' +
						'<tr><td><label>Tipo: </label></td><td><input type="text" class="textbox w200 autoSuggestBox obligatorio" id="importes-retencion-tiporetencion" name="TipoRetencion" /></td></tr>' +
						'<tr><td><label>Importe: </label></td><td><input type="text" class="textbox w200 importes-input-numeric obligatorio" id="importes-retencion-importe" validate="DecimalPositivo" /></td></tr>' +
						'<tr><td><label>Fecha: </label></td><td><input type="text" class="textbox w180 obligatorio" id="importes-retencion-fecha" validate="Fecha" /></td></tr>' +
						'<tr><td><label>Nro Certificado: </label></td><td><input type="text" class="textbox w200 obligatorio aRight" id="importes-retencion-numerocertificado" validate="CertificadoRetencion" /></td></tr>' +
						'<tr><td><label>Cuit: </label></td><td><input type="text" class="textbox w200" id="importes-retencion-cuit" validate="Cuit" value="' + settings.cuitLibrador + '" /></td></tr>' +
						'<tr><td><label>Nombre: </label></td><td><input type="text" class="textbox w200" id="importes-retencion-nombre" maxlength="100" value="' + settings.nombreLibrador + '" /></td></tr>' +
						'</tbody></table>' +
						'</div>' +
						'</div>');
		};
	}
	function bigSwitch(tipoImporte) {
		switch (tipoImporte) {
			case 'E':
				return new PopUpEfectivo();
				break;
			case 'C':
				return new PopUpCheque();
				break;
			case 'T':
				return new PopUpTransferencia();
				break;
			case 'S':
				return new PopUpRetencion();
				break;
			default:
				return false;
				break;
		}
	}
	function genericOpenPopUp(obj) {
		var popup = settings.popUps[obj.tipoImporte]();
		$.jPopUp.show(popup, [
			{
				value: 'Guardar',
				action: function(){
					var error = settings.divContainer.importes('save', obj.tipoImporte, obj.nro);
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
						settings.divContainer.importes('remove', obj.tipoImporte, obj.nro);
					}
					$.jPopUp.close();
				}
			}
		], false, function(){obj.fillPopUp(popup);});
	}
	function genericSave(o) {
		if (!o.tableRow) {
			o.tableRow = {};
			o.tableRow.tdTipo = $('<td></td>');
			o.tableRow.tdImporte = $('<td class="aRight"></td>');
			o.tableRow.tdResumen = $('<td></td>');
			o.tableRow.tdEditar = $('<td class="aCenter" tipo-importe="' + o.tipoImporte + '" nro="' + o.nro + '"><label class="cPointer">Editar</label></td>').click(function(){
				settings.divContainer.importes('edit', $(this).attr('tipo-importe'), $(this).attr('nro'));
			});
			o.tableRow.tdQuitar = $('<td class="aCenter" tipo-importe="' + o.tipoImporte + '" nro="' + o.nro + '"><label class="cPointer">Quitar</label></td>').click(function(){
				settings.divContainer.importes('remove', $(this).attr('tipo-importe'), $(this).attr('nro'));
			});
			o.tableRow.row = $('<tr></tr>')
				.append(o.tableRow.tdTipo)
				.append(o.tableRow.tdImporte)
				.append(o.tableRow.tdResumen)
				.append(o.tableRow.tdEditar)
				.append(o.tableRow.tdQuitar);
			settings.tableBodyImportes.append(o.tableRow.row);
		}
		o.tableRow.tdTipo.text(o.getTextTipo());
		o.tableRow.tdImporte.text(o.getTextImporte());
		o.tableRow.tdResumen.text(o.getTextResumen());
	}
	function genericRemove(o) {
		if (o.tableRow) {
			o.tableRow.row.remove();
		}
		settings.importes[o.tipoImporte][o.nro] = false;
		settings.removeCallback();
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

	function PopUpEfectivo() {
		this.o = function() {return $('#popup-efectivo');};
		this.tipoImporte = 'E';
		this.nro = false;
		this.tableRow = false;
		this.props = {};
		this.openPopUp = function() {
			genericOpenPopUp(this);
		};
		this.save = function () {
			var error = this.validar();
			if (!error) {
				this.fillObjectFromPopUp();
				genericSave(this);
				settings.saveCallback();
			}
			return error;
		};
		this.validar = function() {
			if (!(getOrSet(this.o(), 'importes-efectivo-importe') > 0)) {
				return 'Debe ingresar un importe';
			}
			return false; //Si devuelvo false es porque está bien
		};
		this.remove = function() {
			genericRemove(this);
		};
		this.getTextImporte = function() {
			return funciones.formatearMoneda(this.props.importe);
		};
		this.getTextTipo = function() {
			return 'Efectivo';
		};
		this.getTextResumen = function() {
			return 'Importe en efectivo';
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
		this.getJson = function() {
			return this.props;
		};
	}
	function PopUpCheque() {
		this.o = function() {return $('#popup-cheque');};
		this.tipoImporte = 'C';
		this.nro = false;
		this.tableRow = false;
		this.props = {};
		this.openPopUp = function() {
			if (settings.entradaSalida == 'E' || $.isEmptyObject(this.props)) {
				genericOpenPopUp(this);
			}
		};
		this.save = function () {
			var error = this.validar();
			if (!error) {
				this.fillObjectFromPopUp();
				(settings.entradaSalida == 'E') ? genericSave(this) : this.specialSave();
				settings.saveCallback();
			}
			return error;
		};
		this.specialSave = function() {
			var i;
			var tipoImporte = 'C';
			for (i = 0; i < this.checked.length; i++) {
				var obj = bigSwitch(tipoImporte);
				obj.nro = settings.importes[tipoImporte].length;
				obj.fofpu(this.checked[i]);
				//obj.saveOne();
				genericSave(obj);
				settings.importes[tipoImporte][obj.nro] = obj;
			}
			this.remove();
		};
		this.validar = function() {
			if (settings.entradaSalida == 'E') {
				if (settings.chequePropio) {
					if (!(getOrSet(this.o(), 'importes-cheque-cuentabancaria_selectedValue') > 0)) {
						return 'Debe seleccionar la cuenta bancaria';
					}
					if (!(getOrSet(this.o(), 'importes-cheque-numero_selectedValue') > 0)) {
						return 'Debe elegir el número de cheque';
					}
					//Comprobar si ya metió el cheque
					var i = 0;
					for(tipoImporte in settings.importes) {
						for(nro in settings.importes[tipoImporte]) {
							if (this.props.numero) {
								i += this.props.numero == getOrSet(this.o(), 'importes-cheque-numero_selectedValue') ? 1 : 0;
								if (i > 1) {
									return 'No puede seleccionar dos veces un mismo cheque';
								}
							} else if (settings.importes[tipoImporte][nro] && settings.importes[tipoImporte][nro].props.numero && settings.importes[tipoImporte][nro].props.numero == getOrSet(this.o(), 'importes-cheque-numero_selectedValue')) {
								return 'No puede seleccionar dos veces un mismo cheque';
							}
						}
					}
				} else {
					if (!(getOrSet(this.o(), 'importes-cheque-banco_selectedValue') > 0)) {
						return 'Debe seleccionar el banco';
					}
					if (!(getOrSet(this.o(), 'importes-cheque-numero') != '')) {
						return 'Debe ingresar el número';
					}
					if (!(getOrSet(this.o(), 'importes-cheque-cuitlibrador') > 0)) {
						return 'Debe ingresar el cuit del librador';
					}
					if (!(getOrSet(this.o(), 'importes-cheque-nombrelibrador') != '')) {
						return 'Debe ingresar el nombre del librador';
					}
				}
				if (!(getOrSet(this.o(), 'importes-cheque-importe') > 0)) {
					return 'Debe ingresar el importe';
				}
				if (!(getOrSet(this.o(), 'importes-cheque-fechaemision') != '')) {
					return 'Debe ingresar la fecha de emisión';
				}
				if (!(getOrSet(this.o(), 'importes-cheque-fechavencimiento') != '')) {
					return 'Debe ingresar la fecha de vencimiento';
				}
				if ((funciones.esFechaMenor(getOrSet(this.o(), 'importes-cheque-fechavencimiento'), getOrSet(this.o(), 'importes-cheque-fechaemision')))) {
					return 'La fecha de vencimiento no puede ser posterior a la de emisión';
				}
				if ((funciones.diferenciaFechas(getOrSet(this.o(), 'importes-cheque-fechavencimiento'), getOrSet(this.o(), 'importes-cheque-fechaemision'), 'days') > 365)) {
					return 'La diferencia entre la fecha de emisión y la de vencimiento no puede superar los 365 días';
				}
			} else {
				if (!(this.o().find('#importes-popup-cheque-body tr').length > 0)) {
					return 'Debe seleccionar un cheque';
				}
				//Comprobar si ya metió el cheque
				var error = false;
				for(tipoImporte in settings.importes) {
					for(nro in settings.importes[tipoImporte]) {
						this.o().find('#importes-popup-cheque-body tr input:checked').each(function() {
							var cheque = $(this).data('obj');
							if (!error && settings.importes[tipoImporte][nro] && settings.importes[tipoImporte][nro].props.id && settings.importes[tipoImporte][nro].props.id == cheque.id) {
								error = 'No puede seleccionar dos veces un mismo cheque';
							}
						});
					}
				}
				return error;
			}
			return false; //Si devuelvo false es porque está bien
		};
		this.remove = function() {
			genericRemove(this);
		};
		this.getTextImporte = function() {
			return funciones.formatearMoneda(this.props.importe);
		};
		this.getTextTipo = function() {
			return 'Cheque';
		};
		this.getTextResumen = function() {
			return 'Cheque número ' + (this.props.numeroCheque ? this.props.numeroCheque : this.props.numero) + '. Vencimiento: ' + this.props.fechaVencimiento;
		};
		this.fillPopUp = function(popup) {
			if (!$.isEmptyObject(this.props)) {
				if (settings.entradaSalida == 'E') {
					if (settings.chequePropio) {
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
			if (settings.entradaSalida == 'E') {
				if (settings.chequePropio) {
					this.props.cuentaBancaria = {};
					this.props.cuentaBancaria.id = getOrSet(this.o(), 'importes-cheque-cuentabancaria_selectedValue');
					this.props.cuentaBancaria.nombreCuenta = getOrSet(this.o(), 'importes-cheque-cuentabancaria_selectedName');
					this.props.numero = getOrSet(this.o(), 'importes-cheque-numero_selectedValue');
					this.props.numeroCheque = getOrSet(this.o(), 'importes-cheque-numero_selectedName').split('[')[1].split(']')[0];
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
			} else {
				this.checked = [];
				this.o().find('#importes-popup-cheque-body tr input:checked').each($.proxy(function(key, val) {
					this.checked.push($(val).data('obj'));
				}, this));
			}
			this.props.chequePropio = settings.chequePropio ? 1 : 0;
		};
		this.fofpu = function(obj) {
			this.props.id = obj.id;
			this.props.importe = obj.importe;
			this.props.fechaVencimiento = obj.fechaVencimiento;
			this.props.numero = obj.numero;
		};
		this.fillObjectFromDB = function(importe) {
			this.props = importe;
		};
		this.getJson = function() {
			return this.props;
		};
	}
	function PopUpTransferencia() {
		this.o = function() {return $('#popup-transferencia');};
		this.tipoImporte = 'T';
		this.nro = false;
		this.tableRow = false;
		this.props = {};
		this.openPopUp = function() {
			genericOpenPopUp(this);
		};
		this.save = function () {
			var error = this.validar();
			if (!error) {
				this.fillObjectFromPopUp();
				genericSave(this);
				settings.saveCallback();
			}
			return error;
		};
		this.validar = function() {
			if (!(getOrSet(this.o(), 'importes-transferencia-cuentabancaria_selectedValue') > 0)) {
				return 'Debe seleccionar la cuenta bancaria';
			}
			if (!(getOrSet(this.o(), 'importes-transferencia-importe') > 0)) {
				return 'Debe ingresar el importe';
			}
			if (!(getOrSet(this.o(), 'importes-transferencia-fechatransferencia') != '')) {
				return 'Debe ingresar la fecha';
			}
			return false; //Si devuelvo false es porque está bien
		};
		this.remove = function() {
			genericRemove(this);
		};
		this.getTextImporte = function() {
			return funciones.formatearMoneda(this.props.importe);
		};
		this.getTextTipo = function() {
			return 'Transferencia';
		};
		this.getTextResumen = function() {
			return 'Transferencia ' + (settings.entradaSalida == 'E' ? 'a' : 'desde') + ' la cuenta "' + this.props.cuentaBancaria.nombreCuenta + '"';
		};
		this.fillPopUp = function(popup) {
			if (!$.isEmptyObject(this.props)) {
				getOrSet(popup, 'importes-transferencia-importe', this.props.importe);
				getOrSet(popup, 'importes-transferencia-fechatransferencia', this.props.transferenciaBancariaOperacion.fechaTransferencia);
				getOrSet(popup, 'importes-transferencia-numerotransferencia', this.props.transferenciaBancariaOperacion.numeroTransferencia);
				getOrSet(popup, 'importes-transferencia-cuentabancaria', this.props.cuentaBancaria.id + ' - ' + this.props.cuentaBancaria.nombreCuenta);
				getOrSet(popup, 'importes-transferencia-cuentabancaria_selectedValue', this.props.cuentaBancaria.id);
				getOrSet(popup, 'importes-transferencia-cuentabancaria_selectedName', this.props.cuentaBancaria.nombreCuenta);
			}
		};
		this.fillObjectFromPopUp = function() {
			this.props.importe = getOrSet(this.o(), 'importes-transferencia-importe');
			this.props.transferenciaBancariaOperacion = {};
			this.props.transferenciaBancariaOperacion.fechaTransferencia = getOrSet(this.o(), 'importes-transferencia-fechatransferencia');
			this.props.transferenciaBancariaOperacion.numeroTransferencia = getOrSet(this.o(), 'importes-transferencia-numerotransferencia');
			this.props.cuentaBancaria = {};
			this.props.cuentaBancaria.id = getOrSet(this.o(), 'importes-transferencia-cuentabancaria_selectedValue');
			this.props.cuentaBancaria.nombreCuenta = getOrSet(this.o(), 'importes-transferencia-cuentabancaria_selectedName');
		};
		this.fillObjectFromDB = function(importe) {
			this.props = importe;
		};
		this.getJson = function() {
			return this.props;
		};
	}
	function PopUpRetencion() {
		this.o = function() {return $('#popup-retencion');};
		this.tipoImporte = 'S';
		this.nro = false;
		this.tableRow = false;
		this.props = {};
		this.openPopUp = function() {
			genericOpenPopUp(this);
		};
		this.save = function () {
			var error = this.validar();
			if (!error) {
				this.fillObjectFromPopUp();
				genericSave(this);
				settings.saveCallback();
			}
			return error;
		};
		this.validar = function() {
			if (settings.entradaSalida == 'E') {
				if (!(getOrSet(this.o(), 'importes-retencion-tiporetencion_selectedValue') > 0)) {
					return 'Debe seleccionar el tipo de retención';
				}
				if (!(getOrSet(this.o(), 'importes-retencion-numerocertificado') != '')) {
					return 'Debe ingresar el número de certificado';
				}
				if (!(getOrSet(this.o(), 'importes-retencion-importe') > 0)) {
					return 'Debe ingresar el importe';
				}
				if (!(getOrSet(this.o(), 'importes-retencion-fecha') != '')) {
					return 'Debe ingresar la fecha de la retención';
				}
			}
			return false; //Si devuelvo false es porque está bien
		};
		this.remove = function() {
			genericRemove(this);
		};
		this.getTextImporte = function() {
			return funciones.formatearMoneda(this.props.importe);
		};
		this.getTextTipo = function() {
			return 'Retención';
		};
		this.getTextResumen = function() {
			return 'Retención';
		};
		this.fillPopUp = function(popup) {
			if (!$.isEmptyObject(this.props)) {
				if (settings.entradaSalida == 'E') {
					getOrSet(popup, 'importes-retencion-tiporetencion', this.props.tipoRetencion.id + ' - ' + this.props.tipoRetencion.nombre);
					getOrSet(popup, 'importes-retencion-tiporetencion_selectedValue', this.props.tipoRetencion.id);
					getOrSet(popup, 'importes-retencion-tiporetencion_selectedName', this.props.tipoRetencion.nombre);
					getOrSet(popup, 'importes-retencion-nombre', this.props.nombre);
					getOrSet(popup, 'importes-retencion-numerocertificado', this.props.numeroCertificado);
					getOrSet(popup, 'importes-retencion-cuit', this.props.cuit);
					getOrSet(popup, 'importes-retencion-importe', this.props.importe);
					getOrSet(popup, 'importes-retencion-fecha', this.props.fecha);
				}
			}
		};
		this.fillObjectFromPopUp = function() {
			if (settings.entradaSalida == 'E') {
				this.props.tipoRetencion = {};
				this.props.tipoRetencion.id = getOrSet(this.o(), 'importes-retencion-tiporetencion_selectedValue');
				this.props.tipoRetencion.nombre = getOrSet(this.o(), 'importes-retencion-tiporetencion_selectedName');
				this.props.nombre = getOrSet(this.o(), 'importes-retencion-nombre');
				this.props.numeroCertificado = getOrSet(this.o(), 'importes-retencion-numerocertificado');
				this.props.cuit = getOrSet(this.o(), 'importes-retencion-cuit');
				this.props.importe = getOrSet(this.o(), 'importes-retencion-importe');
				this.props.fecha = getOrSet(this.o(), 'importes-retencion-fecha');
			}
		};
		this.fillObjectFromDB = function(importe) {
			this.props = importe;
		};
		this.getJson = function() {
			return this.props;
		};
	}

	$.fn.importes = function(method) {
		if (methods[method]) {
			//Si existe el método que me piden, lo llamo, y le mando por parametro to_dos los que me mandaron excepto el primero que es el método
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			//Si no llamo a un método, o si mando un objeto como primer parámetro (config), llamo al INIT y le mando todos los parámetros
			return methods.init.apply(this, arguments);
		} else {
			$.error('No existe el método ' +  method + ' en el plugin jQuery.importes');
		}
	};
})(jQuery);