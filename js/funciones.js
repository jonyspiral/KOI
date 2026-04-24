//Mis funciones para jQuery:
$.fn.disable = function() { 
	return this.attr('disabled', 'true');
};
$.fn.enable = function() { 
	return this.removeAttr('disabled');
};
$.fn.invisible = function() { 
	return this.css('visibility', 'hidden');
};
$.fn.visible = function() { 
	return this.css('visibility', 'visible');
};
$.fn.isVisible = function() { 
	return (this.css('display') != 'none') && (this.css('visibility') != 'hidden');
};
$.fn.uncheck = function() {
	return this.attr('checked', false);
};
$.fn.check = function() {
	return this.attr('checked', 'checked');
};
$.fn.isChecked = function() {
	return this.attr('checked') == 'checked';
};
$.fn.isDisabled = function() {
	return this.attr('disabled');
};
$.fn.enableRadio = function() {
	return this.button('option', 'disabled', false);
};
$.fn.disableRadio = function() {
	return this.button('option', 'disabled', true);
};
$.fn.enableRadioGroup = function() {
	return this.find('input[type="radio"]').button('option', 'disabled', false);
};
$.fn.disableRadioGroup = function() {
	return this.find('input[type="radio"]').button('option', 'disabled', true);
};
//$.fn.enableAcordeon (en acordeon.js)
//$.fn.disableAcordeon (en acordeon.js)
$.fn.radioClick = function() {
	var disabled = (this.button('option', 'disabled'));
	if (disabled)
		this.enableRadio();
	$(this).click();
	if (disabled)
		this.disableRadio();
};
$.fn.radioDefault = function() {
	if (!$(this).attr('default')) {
		$(this).find('input[type="radio"]').uncheck();
		$(this).find('label').removeClass('ui-state-active').attr('aria-pressed', 'false');
	} else
		$('#' + $(this).attr('default')).radioClick();
};
$.fn.radioVal = function() {
	var val = '';
	var obj = this.find('input[type="radio"]');
	obj.each(function(){
		if (this.checked)
			val = this.value;
	});
	return val;
};
$.fn.selectedText = function() {
	return this.find('option:selected').text();
};
$.fn.limpiarAutoSuggestBox = function() {
	var id = this.attr('id');
	$('#' + id + ', #' + id + '_selectedValue, #' + id + '_autoSuggestBox_selectedName').val('');
};
$.fn.slideFadeToggle = function(speed, easing, callback){
  return this.animate({opacity: 'toggle', height: 'toggle'}, speed, easing, callback);
};
$.fn.shine = function(opacidadIda, opacidadVuelta, duration){
	opacidadIda = opacidadIda || '0.05';
	opacidadVuelta = opacidadVuelta || '1';
	duration = duration || 300;
	this.animate({opacity: opacidadIda}, duration, function(){$(this).animate({opacity: opacidadVuelta}, duration);});
}
//$.fn.autoComplete (en autoSuggestBox.js)
$.extend({
	postJSON: function (url, obj, callback) {
		return $.post(url, obj, callback, 'json');
	}
});


//Defino la clase "FUNCIONES"
function Funciones(){
	this.si = 'Sí';
	this.no = 'No';
	this.autoSuggestBoxDelay = 300;
	this.jsonNull = 'null';
	this.jsonEmpty = 'empty';
	this.jsonError = 'error';
	this.jsonSuccess = 'success';
	this.jsonConfirm = 'confirm';
	this.jsonObject = 'object';
	this.jsonAlert = 'alert';
	this.jsonInfo = 'info';
	this.pathBase = '/content';
}

//Métodos de "FUNCIONES"

/* Funciones de guardar, borrar y autorizar */

Funciones.prototype.buscar = function(url, cbSuccess, msgError, callback){
	var cbAlert = function(json) {
		$('#inputBuscar').limpiarAutoSuggestBox();
		$.hideLoading();
		$.alert(funciones.getJSONMsg(json), function(){
			$('#inputBuscar').focus();
		});
	};
	var cbInfo = function(json) {
		$('#inputBuscar').limpiarAutoSuggestBox();
		$.hideLoading();
		$.info(funciones.getJSONMsg(json), function(){
			$('#inputBuscar').focus();
		});
	};
	var cbError = function(json) {
		$('#inputBuscar').limpiarAutoSuggestBox();
		$.hideLoading();
		$.error(funciones.getJSONMsg(json), function(){
			$('#inputBuscar').focus();
		});
	};
	var callbackSuccess = function(json) {
		funciones.cambiarTitulo(tituloPrograma + ' - "' + $('#inputBuscar_selectedName').val() + '"');
		cbSuccess(json.data);
		cambiarModo('buscar');
		$.hideLoading();
	};

	funciones.focusDummyLink();
	if ($('#inputBuscar').val() != ''){
		funciones.get(url, {}, callbackSuccess, callback, msgError, cbError, cbAlert, cbInfo);
	}
};
Funciones.prototype.load = function(jQueryTarget, url, successCallback, errorCallback) {
	$.showLoading();
	jQueryTarget.load(url, function(result) {
		try {
			var json = $.parseJSON(result);
			jQueryTarget.html('');
			switch (funciones.getJSONType(json)) {
				case funciones.jsonNull:
					$.error('Ocurrió un error al intentar realizar la consulta');
					break;
				case funciones.jsonError:
					$.error(funciones.getJSONMsg(json));
					cambiarModo('inicio');
					break;
				case funciones.jsonInfo:
					$.info(funciones.getJSONMsg(json));
					break;
				case funciones.jsonConfirm:
					$.confirm(funciones.getJSONMsg(json), function(r){
						if (r == funciones.si)
							funciones.load(jQueryTarget, url + json.data[0], successCallback, errorCallback);
					});
					break;
			}
			errorCallback && $.proxy(errorCallback, json);
		} catch (ex) {
			cambiarModo('buscar');
			successCallback && successCallback();
		}
		$.hideLoading();
	});
};
Funciones.prototype.guardar = function(url, obj, cbSuccess, cbAlert, parametros, showMessages){
	if (typeof parametros === 'undefined')
		parametros = '';
	if (typeof showMessages === 'undefined')
		showMessages = true;
	if (!$.isFunction(cbSuccess))
		cbSuccess = funciones.reload;
	var callbackSuccess = showMessages ? function(json) {$.success(funciones.getJSONMsg(json), $.proxy(cbSuccess, json));} : function(json) {$.proxy(cbSuccess, json)();};
	funciones.focusDummyLink();
	funciones.post(url + parametros, obj, callbackSuccess, null, null, null, cbAlert, callbackSuccess /*Es la de info*/);
};
Funciones.prototype.borrar = function(msgConfirm, urlPost, objeto, cbSuccess){
	if (!$.isFunction(cbSuccess))
		cbSuccess = function() {$.success(funciones.getJSONMsg(this), funciones.reload);};
	var callbackSuccess = function(json) {$.proxy(cbSuccess, json)();};
	$.confirm(msgConfirm, function(r){
		if (r == funciones.si) {
			funciones.post(urlPost, objeto, callbackSuccess);
		}
	});
};
Funciones.prototype.sendMail = function(url, cbSuccess) {
	funciones.post(url, {}, cbSuccess);
};
Funciones.prototype.post = function(url, obj, cbSuccess, finalCallback, msgError, cbError, cbAlert, cbInfo) {
	funciones.ajax('POST', url, obj, cbSuccess, finalCallback, msgError, cbError, cbAlert, cbInfo);
};
Funciones.prototype.get = function(url, obj, cbSuccess, finalCallback, msgError, cbError, cbAlert, cbInfo) {
	funciones.ajax('GET', url, obj, cbSuccess, finalCallback, msgError, cbError, cbAlert, cbInfo);
};
Funciones.prototype.ajax = function(method, url, obj, cbSuccess, finalCallback, msgError, cbError, cbAlert, cbInfo) {
	if (typeof method === 'undefined')
		method = 'GET';
	if (!$.isFunction(cbSuccess))
		cbSuccess = function(json){$.success(funciones.getJSONMsg(json));};
	if (!$.isFunction(cbError))
		cbError = function(json){$.error(funciones.getJSONMsg(json));};
	if (!$.isFunction(cbAlert))
		cbAlert = function(json){$.alert(funciones.getJSONMsg(json));};
	if (!$.isFunction(cbInfo))
		cbInfo = function(json){$.info(funciones.getJSONMsg(json));};

	var callback = function(json) {
		$.hideLoading();
		switch (funciones.getJSONType(json)){
			case funciones.jsonNull:
			case funciones.jsonEmpty:
				$.error('Ocurrió un error');
				break;
			case funciones.jsonAlert:
				cbAlert(json);
				break;
			case funciones.jsonError:
				cbError(json);
				break;
			case funciones.jsonInfo:
				cbInfo(json);
				break;
			case funciones.jsonObject:
				cbSuccess(json);
				break;
			case funciones.jsonSuccess:
				cbSuccess(json);
				break;
			case funciones.jsonConfirm:
				$.confirm(funciones.getJSONMsg(json), function(r){
					if (r == funciones.si) {
						funciones.ajax(method, url + json.data[0], obj, cbSuccess, cbError, cbAlert, cbInfo);
					}
				});
				break;
		}
        if (funciones.scope().$apply) {
            funciones.scope().$apply();
        }
		finalCallback;
	};
	$.showLoading();
	switch (method) {
		case 'GET': $.getJSON(url, obj, callback); break;
		case 'POST': $.postJSON(url, obj, callback); break;
	}
};
Funciones.prototype.controllerUrl = function(controllerName, parameters, appPath, basePath){
	if (typeof appPath === 'undefined') {
		appPath = window.location.pathname;
	}
	if (typeof basePath === 'undefined') {
		basePath = funciones.pathBase;
	}
	if (typeof parameters === 'undefined') {
		parameters = '';
	} else if ($.isEmptyObject(parameters)){
		parameters = '';
	} else if ($.isPlainObject(parameters)){
		parameters = funciones.serialize(parameters);
	}
	return basePath + appPath + controllerName + '.php?' + parameters
};
Funciones.prototype.reload = function() {
	window.location.reload();
};
Funciones.prototype.generoDivAutorizaciones = function(json, idUsuarioLogueado, idDiv){
	if (typeof idDiv === 'undefined')
		idDiv = 'divAutorizaciones';
	var aut = json.autorizaciones;
	var hayAut = true;
	var table = '<table class="w100p black normal registrosAlternados" cellspacing="0">';
	var arrayEstados = [];
	if (json.anulado == 'N' && json.autorizado == 'S') {
		table += '<tr><td class="h30 aCenter bLightGreen" colspan="3"><label>Autorizado</label></td></tr>';
		if (aut.autorizaciones == null)
			hayAut = false;
	}
	if (hayAut) {
		for (var i = 1; i <= aut.autorizacionTipo.cantidad; i++){
			var usuarioAutorizado = false;
			var autorizado = false;
			var puedoAutorizar = false;
			if (aut.autorizaciones != null && aut.autorizaciones[i] != null) {
				var user = aut.autorizaciones[i].usuario;
				usuarioAutorizado = user.nombre + ' ' + user.apellido;
				autorizado = (aut.autorizaciones[i].autorizado == 'S');
				if (!autorizado && user.id == idUsuarioLogueado)
					puedoAutorizar = true;
			}
			if (aut.personasAutorizacionesPendientes != null && aut.personasAutorizacionesPendientes[i] != null) {
				for (key in aut.personasAutorizacionesPendientes[i]){
					var pp = aut.personasAutorizacionesPendientes[i][key];
					if (pp.idUsuario == idUsuarioLogueado){
						puedoAutorizar = true;
						break;
					}
				}
			}
			var estado = 'Pendiente';
			arrayEstados[i] = '';
			if (usuarioAutorizado) {
				arrayEstados[i] = 'N';
				if (autorizado)
					arrayEstados[i] = 'S';
				estado = usuarioAutorizado;
			} else {
				if (puedoAutorizar) {
					arrayEstados[i] = 'P';
				}
			}
			table += '<tr>';
			table += '<td class="w20 h30 aCenter"><label>' + i + '</label></td>';
			table += '<td><label id="labelEstado_' + i + '">' + estado + '</label></td>';
			table += '<td class="w120 aCenter">';
			if (arrayEstados[i] != ''){
				var yo = (arrayEstados[i] == 'P' || (arrayEstados[i] == 'N' && puedoAutorizar));
				table += '<div id="radioGroupAutorizacion_' + i + '" class="customRadio ' + (yo ? '' : 'noEditable') + '">';
				table += '<input id="rdAutorizacion_' + i + '_S" class="textbox" type="radio" name="radioGroupAutorizacion_' + i + '" value="S" />';
				table += '<label for="rdAutorizacion_' + i + '_S"' + (yo ? ' onclick="autorizar(' + i + ', true);"' : '') + '>Sí</label>';
				table += '<input id="rdAutorizacion_' + i + '_N" class="textbox" type="radio" name="radioGroupAutorizacion_' + i + '" value="N" />';
				table += '<label for="rdAutorizacion_' + i + '_N"' + (yo ? ' onclick="autorizar(' + i + ', false);"' : '') + '>No</label>';
				table += '</div>';
			}
			table += '</td>';
			table += '</tr>';
		}
	}
	table += '</table>';
	$('#' + idDiv).html(table);
	$('.customRadio').buttonset();
	for (key in arrayEstados){
		var val = arrayEstados[key];
		if (val != 'P') {
			$('#rdAutorizacion_' + key + '_' + val).click();
			$('#radioGroupAutorizacion_' + key).disableRadioGroup();
		}
	}
};
Funciones.prototype.autorizar = function(msgConfirm, urlPost, objeto, callback){
	if (!$('#rdAutorizacion_' + objeto.numeroDeAutorizacion + '_S').isDisabled()) {
		$.prompt(msgConfirm, function(motivo){
			if (motivo) {
				funciones.goAutorizar(urlPost, objeto, motivo);
				try {callback;} catch (ex) {}
			} else
				$('#radioGroupAutorizacion_' + objeto.numeroDeAutorizacion).radioDefault();
		});
	}
};
Funciones.prototype.goAutorizar = function(postUrl, objeto, motivo, parametros) {
	if (typeof parametros === 'undefined')
		parametros = '';
	$.showLoading();
	$.postJSON(postUrl + parametros, objeto, function(json){
		$.hideLoading();
		switch (funciones.getJSONType(json)){
			case funciones.jsonNull:
			case funciones.jsonEmpty:
				$.error('Ocurrió un error');
				break;
			case funciones.jsonError:
				$.error(funciones.getJSONMsg(json));
				break;
			case funciones.jsonSuccess:
				$.success(funciones.getJSONMsg(json), function(){
					//Si fue success y dentro de los parámetros está el de confirmarUltima es porque ya quedó autorizado
					if (parametros.indexOf('confirmarUltima') != -1)
						funciones.reload();
					else {
						nro = objeto.numeroDeAutorizacion;
						$('#rdAutorizacion_' + nro + '_' + objeto.autoriza).radioClick();
						$('label[for="rdAutorizacion_' + nro + '_S"], label[for="rdAutorizacion_' + nro + '_N"]').unbind('click').removeAttr('onclick');
						$('#labelEstado_' + nro).text(nombreUsuarioLogueado);
						$('#radioGroupAutorizacion_' + nro).disableRadioGroup();
					}
				});
				break;
			case funciones.jsonConfirm:
				$.confirm(funciones.getJSONMsg(json), function(r){
					if (r == funciones.si)
						funciones.goAutorizar(postUrl, objeto, motivo, parametros + json.data[0]);
				});
				break;
		}
	});
};
Funciones.prototype.focusDummyLink = function(){
	$('#dummyLink').focus();
};

/* Funciones varias */

Funciones.prototype.scope = function(){
    var ngController = $('[ng-controller="AppCtrl"]');
    return (ngController.length) ? angular.element(ngController[0]).scope() : window;
};
Funciones.prototype.buscarClick = function(){
	$('#filtro').draggableDialogShow('#btnBuscar');
	$('#filtro').find('input:visible:enabled:text:first').focus();
};
Funciones.prototype.editarClick = function(){
	if ($('#inputBuscar_selectedValue').val() != '')
		cambiarModo('editar');
	else
		$('#inputBuscar').focus();
};
Funciones.prototype.agregarClick = function(){
	cambiarModo('agregar');
};
Funciones.prototype.cancelarBuscarClick = function(){
	cambiarModo('inicio');
};
Funciones.prototype.cancelarEditarClick = function(){
	cambiarModo('buscar');
};
Funciones.prototype.guardarClick = function(){
	var error = funciones.scope().hayErrorGuardar();
	if (!error) {
		funciones.scope().guardar();
    } else {
		$.error(error);
    }
};
Funciones.prototype.borrarClick = function(){
	if ($('#inputBuscar_selectedValue').val() != '') {
        funciones.scope().borrar();
    } else {
        $('#inputBuscar').focus();
    }
};
Funciones.prototype.pdfClick = function(url){
	funciones.newWindow(url);
};
Funciones.prototype.xlsClick = function(url){
	funciones.newWindow(url);
};
Funciones.prototype.newWindow = function(url){
	if (url != '')
		window.open(url, '_blank');
};
Funciones.prototype.cambiarTitulo = function(titulo){
	if (typeof titulo === 'undefined')
		titulo = tituloPrograma;
	$('#programaTitulo').text(titulo);
};
Funciones.prototype.cambiarModo = function(modo){
    funciones.scope().modo = modo;
	switch (modo){
		case 'inicio':
			try {funciones.limpiarScreen();} catch (ex) {}
			try {funciones.cambiarTitulo();} catch (ex) {}
			try {funciones.radioDefault();} catch (ex) {}
			try {funciones.limpiarAutoSuggestBox();} catch (ex) {}
			try {$('#filtro').draggableDialogHide();} catch (ex) {}
			try {$('#btnBuscar').show();} catch (ex) {}
			try {$('#btnEditar').hide();} catch (ex) {}
			try {$('#btnAgregar').show();} catch (ex) {}
			try {$('#btnGuardar').hide();} catch (ex) {}
			try {$('#btnCancelarBuscar').hide();} catch (ex) {}
			try {$('#btnCancelarEditar').hide();} catch (ex) {}
			try {$('#btnBorrar').hide();} catch (ex) {}
			try {$('#btnPdf').hide();} catch (ex) {}
			try {$('#btnXls').hide();} catch (ex) {}
			try {$('#inputBuscar').enable();} catch (ex) {}
			try {$('.filtroBuscar').show();} catch (ex) {}
			try {$('.inputForm').disable().val('');} catch (ex) {}
			try {$('.customRadio').disableRadioGroup();} catch (ex) {}
			try {$('.pantalla').hide();} catch (ex) {}
			try {setTimeout(function(){$('#btnBuscar').focus();}, 100);} catch (ex) {}
			try {funciones.delay('funciones.buscarClick();');} catch (ex) {}
			break;
		case 'buscar':
			try {$('#filtro').draggableDialogHide();} catch (ex) {}
			try {$('#btnBuscar').hide();} catch (ex) {}
			try {$('#btnEditar').show();} catch (ex) {}
			try {$('#btnAgregar').hide();} catch (ex) {}
			try {$('#btnGuardar').hide();} catch (ex) {}
			try {$('#btnCancelarBuscar').show();} catch (ex) {}
			try {$('#btnCancelarEditar').hide();} catch (ex) {}
			try {$('#btnBorrar').show();} catch (ex) {}
			try {$('#btnPdf').show();} catch (ex) {}
			try {$('#btnXls').show();} catch (ex) {}
			try {$('#inputBuscar').enable();} catch (ex) {}
			try {$('.filtroBuscar').show();} catch (ex) {}
			try {$('.inputForm').disable();} catch (ex) {}
			try {$('.customRadio').disableRadioGroup();} catch (ex) {}
			try {$('.pantalla').show();} catch (ex) {}
			try {$('#btnEditar').focus();} catch (ex) {}
			break;
		case 'editar':
			try {$('#filtro').draggableDialogHide();} catch (ex) {}
			try {$('#btnBuscar').hide();} catch (ex) {}
			try {$('#btnEditar').hide();} catch (ex) {}
			try {$('#btnAgregar').hide();} catch (ex) {}
			try {$('#btnGuardar').show();} catch (ex) {}
			try {$('#btnCancelarBuscar').hide();} catch (ex) {}
			try {$('#btnCancelarEditar').show();} catch (ex) {}
			try {$('#btnBorrar').hide();} catch (ex) {}
			try {$('#btnPdf').hide();} catch (ex) {}
			try {$('#btnXls').hide();} catch (ex) {}
			try {$('#inputBuscar').disable();} catch (ex) {}
			try {$('.filtroBuscar').hide();} catch (ex) {}
			try {$('.inputForm').not('.noEditable').enable();} catch (ex) {}
			try {$('.customRadio').not('.noEditable').enableRadioGroup();} catch (ex) {}
			try {$('.pantalla').show();} catch (ex) {}
			break;
		case 'agregar':
			try {funciones.limpiarScreen();} catch (ex) {}
			try {funciones.cambiarTitulo();} catch (ex) {}
			try {funciones.radioDefault();} catch (ex) {}
			try {funciones.limpiarAutoSuggestBox();} catch (ex) {}
			try {$('#filtro').draggableDialogHide();} catch (ex) {}
			try {$('#btnBuscar').hide();} catch (ex) {}
			try {$('#btnEditar').hide();} catch (ex) {}
			try {$('#btnAgregar').hide();} catch (ex) {}
			try {$('#btnGuardar').show();} catch (ex) {}
			try {$('#btnCancelarBuscar').show();} catch (ex) {}
			try {$('#btnCancelarEditar').hide();} catch (ex) {}
			try {$('#btnBorrar').hide();} catch (ex) {}
			try {$('#btnPdf').hide();} catch (ex) {}
			try {$('#btnXls').hide();} catch (ex) {}
			try {$('#inputBuscar').disable();} catch (ex) {}
			try {$('.filtroBuscar').hide();} catch (ex) {}
			try {$('.inputForm').not('noEnable').enable().val('');} catch (ex) {}
			try {$('.customRadio').not('noEnable').enableRadioGroup();} catch (ex) {}
			try {$('.pantalla').show();} catch (ex) {}
			break;
	}
};
Funciones.prototype.limpiarScreen = function(){
	try {limpiarScreen();} catch (ex) {}
};
Funciones.prototype.padLeft = function(obj, cantidad, charRelleno) {
	if (typeof charRelleno === 'undefined')
		charRelleno = '0';
	while (obj.length < cantidad)
		obj = charRelleno + obj;
	return obj;
};
Funciones.prototype.padRight = function(obj, cantidad, charRelleno) {
	if (typeof charRelleno === 'undefined')
		charRelleno = '0';
	while (obj.length < cantidad)
		obj = obj + charRelleno;
	return obj;
};
Funciones.prototype.serialize = function(obj, prefix){
    var str = [];
    for(var p in obj) {
        var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
        str.push(typeof v == "object" ? 
            funciones.serialize(v, k) :
            encodeURIComponent(k) + "=" + encodeURIComponent(v));
    }
    return str.join("&");
};
Funciones.prototype.escape = function(obj){
	if (typeof obj !== 'undefined')
		return encodeURIComponent(obj);
	return '';
};
Funciones.prototype.escapeSelector = function(obj) {
	return obj.replace(/\./g, '\\\.').replace(/@/g, '\\\@');
};
Funciones.prototype.unescape = function(obj){
	if (typeof obj !== 'undefined')
		return decodeURIComponent(obj);
	return '';
};
Funciones.prototype.roundUp = function(obj){
	if (typeof obj !== 'undefined')
		return Math.ceil(obj);
	return 0;
};
Funciones.prototype.nullOrEmpty = function(obj){
	return (typeof obj === 'undefined' || obj == '' || obj == null);
};
Funciones.prototype.objectLength = function(obj){
    var size = 0;
    for (var key in obj)
        if (obj.hasOwnProperty(key))
    		size++;
    return size;
};
Funciones.prototype.acortarString = function(obj, length, strFinal){
	if (typeof strFinal === 'undefined')
		strFinal = '';
	if (typeof obj === 'undefined' || !obj)
		return '';
	return obj.substr(0, length) + (obj.length > length ? strFinal : '');
};
Funciones.prototype.extraerNumero = function(obj){
	var NUMBERS = /[^0-9]/g;
	if (typeof obj !== 'undefined')
	    return funciones.toInt(str.replace(NUMBERS, ""));
	return 0;
};
Funciones.prototype.esFechaMenor = function(dateString1, dateString2){
	var arr1 = dateString1.split('/');
	var arr2 = dateString2.split('/');
	arr1 = arr1[2] + '-' + arr1[1] + '-' + arr1[0];
	arr2 = arr2[2] + '-' + arr2[1] + '-' + arr2[0];
	return (new Date(arr1).getTime() < new Date(arr2).getTime());
};
Funciones.prototype.esFechaMayor = function(dateString1, dateString2){
	var arr1 = dateString1.split('/');
	var arr2 = dateString2.split('/');
	arr1 = arr1[2] + '-' + arr1[1] + '-' + arr1[0];
	arr2 = arr2[2] + '-' + arr2[1] + '-' + arr2[0];
	return (new Date(arr1).getTime() > new Date(arr2).getTime());
};
Funciones.prototype.diferenciaFechas = function(dateString1, dateString2, unidad) {
	var arr1 = dateString1.split('/');
	var arr2 = dateString2.split('/');
	var d1 = new Date(arr1[2] + '-' + arr1[1] + '-' + arr1[0]);
	var d2 = new Date(arr2[2] + '-' + arr2[1] + '-' + arr2[0]);

	if (typeof unidad === 'undefined') {
		unidad = 'days';
	}

	var diff = 0;
	if (unidad == 'weeks') {
		diff = parseInt((d2.getTime() - d1.getTime()) / (24 * 3600 * 1000 * 7));
	} else if (unidad == 'months') {
		diff = (d2.getMonth() + 12 * d2.getFullYear()) - (d1.getMonth() + 12 * d1.getFullYear());
	} else if (unidad == 'years') {
		diff = d2.getFullYear() - d1.getFullYear();
	} else {
		diff = parseInt((d2.getTime() - d1.getTime()) / (24 * 3600 * 1000));
	}
	return Math.abs(diff);
};
Funciones.prototype.formatearDecimales = function(obj, decimales, separadorDecimales){
	if (typeof obj !== 'undefined')
		obj = funciones.toFloat(obj);
	if (typeof separadorDecimales === 'undefined')
		separadorDecimales = ',';
	return funciones.round(obj, decimales).toFixed(decimales).toString().replace('.', separadorDecimales);
};
Funciones.prototype.formatearMoneda = function(obj, simboloMoneda){
	if (typeof simboloMoneda === 'undefined')
		simboloMoneda = '$';
	if (typeof obj !== 'undefined' && !funciones.nullOrEmpty(obj)){
		return simboloMoneda + ' ' + funciones.formatearDecimales(funciones.toFloat(obj), 2, '.');
	}
	return simboloMoneda + ' ' + funciones.formatearDecimales('0', 2, '.');

	/*var num = obj.toString();
	if (typeof simboloMoneda === 'undefined')
		simboloMoneda = '$';
	if (typeof obj !== 'undefined' && !funciones.nullOrEmpty(obj)){
		num = simboloMoneda + ' ' + funciones.formatearDecimales(funciones.toFloat(num), 2, ',');
	} else {
		num = simboloMoneda + ' ' + funciones.formatearDecimales('0', 2, '.');
	}
	num = num.split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
	num = num.split('').reverse().join('').replace(/^[\.]/,'');

	return num;*/
};
Funciones.prototype.formatearNumeroCheque = function(numero){
	var replacedString = numero.replace('-__', '');
	return replacedString.replace('_', '');
};
Funciones.prototype.formatearPorcentaje = function(obj, decimales){
	if (typeof decimales === 'undefined') {
		decimales = 2;
	}
	if (typeof obj !== 'undefined' && !funciones.nullOrEmpty(obj)){
		return (funciones.toFloat(obj) < 10 ? '0' : '') + funciones.formatearDecimales(funciones.toFloat(obj), decimales) + ' %';
	}
	return '0' + funciones.formatearDecimales('0', decimales) + ' %';
};
Funciones.prototype.limpiarNumero = function(obj){
	if (typeof obj !== 'undefined'){
		obj = funciones.sacarMoneda(obj);
		obj = funciones.sacarPorcentaje(obj);
		return obj;
	}
	return 0;
};
Funciones.prototype.sacarMoneda = function(obj){
	if (typeof obj !== 'undefined')
		return obj.toString().replace('$', '').replace(',', '.').replace(' ', '');
	return 0;
};
Funciones.prototype.sacarPorcentaje = function(obj){
	if (typeof obj !== 'undefined')
		return obj.toString().replace('%', '').replace(',', '.').replace(' ', '');
	return 0;
};
Funciones.prototype.esNatural = function(obj){
	if (typeof obj !== 'undefined' && !funciones.nullOrEmpty(obj))
		return (funciones.toInt(obj) >= 0) && (funciones.toInt(obj).toString() === obj);
	return false;
};
Funciones.prototype.esEntero = function(obj){
	if (typeof obj !== 'undefined' && !funciones.nullOrEmpty(obj))
		return (funciones.toInt(obj).toString() === obj);
	return false;
};
Funciones.prototype.toInt = function(obj){
	if (typeof obj !== 'undefined' && !funciones.nullOrEmpty(obj) && !isNaN(obj))
		return parseInt(funciones.limpiarNumero(obj));
	return 0;
};
Funciones.prototype.toFloat = function(obj){
	if (typeof obj !== 'undefined' && !funciones.nullOrEmpty(obj))
		return parseFloat(funciones.limpiarNumero(obj));
	return 0;
};
Funciones.prototype.round = function(obj, decimales) {
	if (typeof obj !== 'undefined' && !funciones.nullOrEmpty(obj)) {
		var multiplicador = Math.pow(10, decimales);
		return funciones.toFloat(Math.round((obj * multiplicador).toFixed(decimales)) / multiplicador);
	}
	return 0;
};
Funciones.prototype.getJSONType = function(json){
	try {
		if (json == null || json == '' || typeof json === 'undefined')
			return 'null';
		switch (json.responseType){
			case -1:
				return 'null';
			case 0:
				return 'empty';
			case 1:
				return 'object';
			case 2:
				return 'error';
				break;
			case 3:
				return 'success';
				break;
			case 4:
				return 'confirm';
				break;
			case 5:
				return 'alert';
				break;
			case 6:
				return 'info';
				break;
			default:
				return 'object';
		}
	} catch (ex) {
		return 'error';
	}
};
Funciones.prototype.getJSONMsg = function(json){
	try {
		if (json == null)
			return '';
		switch (json.length){
			case -1:
			case 0:
				return '';
			default:
				return json.responseMsg;
		}
	} catch (ex) {
		return '';
	}
};
Funciones.prototype.isArray = function(obj){
	if (typeof obj !== 'undefined')
		return !(obj.constructor.toString().indexOf('Array') == -1);
	return false;
};
Funciones.prototype.inArray = function(val, arr){
	for (var i = 0; i < arr.length; i++)
		if (val == arr[i])
			return true;
	return false;
};
Funciones.prototype.sumaArray = function(array, soloPositivos){
    soloPositivos = soloPositivos || false;
	var sum = 0;
	for (i in array) {
		sum += soloPositivos ? Math.max(funciones.toInt(array[i]), 0) : funciones.toInt(array[i]);
	}
	return sum;
};
Funciones.prototype.arrayMax = function(array){
	return Math.max.apply({}, array);
};
Funciones.prototype.arrayMin = function(array){
	return Math.min.apply({}, array);
};
Funciones.prototype.limpiarAutoSuggestBox = function(){
	$('.autoSuggestBox, .autoSuggestBox_selectedValue, .autoSuggestBox_selectedName').val('');
};
Funciones.prototype.delay = function(strFunction, time){
	if (typeof time === 'undefined')
		time = funciones.autoSuggestBoxDelay;
	setTimeout(strFunction, time);
};
Funciones.prototype.inicializarJQuery = function(){
	$('.customRadio').livequery(function(){
		$(this).buttonset().each(function(){
			if (typeof $(this).attr('default') !== 'undefined'){
				$('#' + funciones.escapeSelector($(this).attr('default'))).radioClick();
			}
		});
	});
	$('#btnMiniBuscar').livequery(function() {
		$(this).click(function(){funciones.focusDummyLink(); funciones.delay('buscar();');});
	});
	$('.autoSuggestBox').livequery(function(){
		$(this).autoSuggestBox();
	});
	$('textarea').livequery(function(){
		$(this).attr('title', 'Para pasar de renglón apriete Shift+Enter');
	});
	/*
	$('input[type="checkbox"]').livequery(function(){
		$(this).koiCheckbox();
	});
	*/
	$('input[checked]').livequery(function(){
		$(this).check();
	});
	$('.textbox, :checkbox, :radio, select').livequery(function(){
		$(this).onEnterFocusNext();
	});
	$('input[validate]').livequery(function(){
		$(this).validate();
	});
	$('a.dropdown-toggle').livequery(function(){
		$(this).click(function() {
			var a = $(this);
			if (a.is('.disabled'))
				return;
			a.parent().toggleClass('open');
			a.focus();
			return false
		});
	});
	$(document).unbind('click').on('click', function() { //Esto es para la función anterior (dropdown)
		$('.btn-dropdown').removeClass('open');
	});
	$('.tabs').livequery(function(){
		$(this).tabs();
	});
	$('.draggableDialog').livequery(function(){
		$(this).draggableDialog();
	});
	$('.acordeon').livequery(function(){
		$(this).acordeon();
	});
	$('.solapas').livequery(function(){
		$(this).solapas();
	});
	//$('.datepicker').livequery(function(){
	$('[validate="Fecha"]').livequery(function(){
		$(this).removeClass('aRight').addClass('aRight');
		var callback = function(){};
		if ($(this).attr('to') || $(this).attr('from')) {
			callback = function(selectedDate){
				var option = ($(this).attr('to') ? 'minDate' : 'maxDate');
				//$('.datepicker').not(this).datepicker('option', option, selectedDate);
				$('[validate="Fecha"]').not(this).datepicker('option', option, selectedDate);
			};
		}
        if (!$(this).hasClass('noPicker')) {
            $(this).datepicker({
                showOn: 'button',
                dateFormat: 'dd/mm/yy',
                buttonImage: '/css/jquery-ui/images/calendar.gif',
                buttonImageOnly: true,
                changeMonth: true,
                numberOfMonths: 2,
                onSelect: callback
            }).keyup(function(e) {
                //Con esto se abre el popup de elegir fecha cuando aprietan F2
                if (e.keyCode == 113) {
                    $(this).next().click();
                }
            });

        }
	});
};
Funciones.prototype.radioDefault = function(){
	//Les saco a todos el select
	$('.customRadio input[type="radio"]').uncheck();
	$('.customRadio label').removeClass('ui-state-active').attr('aria-pressed', 'false');
	//Los que tienen default, se los pongo
	$('.customRadio[default]').each(function(){
		$('#' + $(this).attr('default')).radioClick();
	});
};
Funciones.prototype.random = function(maxVal) {
	return Math.floor(Math.random() * maxVal + 1);
};
Funciones.prototype.sleep = function(milisegundos) {
	var sDialogScript = 'window.setTimeout(function(){window.close();}, ' + milisegundos + ');';
	window.showModalDialog('javascript:document.writeln("<script>' + sDialogScript + '<' + '/script>")');
};
Funciones.prototype.cambiarEmpresa = function() {
	$.showLoading();
	$.postJSON('/cambiarEmpresa.php', function(json){
		$.hideLoading();
		switch (funciones.getJSONType(json)) {
			case 'success':
				funciones.reload();
				break;
			case 'error':
				$.error(funciones.getJSONMsg(json));
				break;
			default:
				$.error('No se pudo cambiar de empresa');
				break;
		}
	});
};
Funciones.prototype.executeFunctionByName = function(functionName /*, args */) {
	//Los argumentos de la función a ejecutar se mandan como parámetros opcionales a partir del segundo (splice(1))
	var context = window;
	var args = Array.prototype.slice.call(arguments).splice(1);
	var namespaces = functionName.split('.');
	var func = namespaces.pop();
	for(var i = 0; i < namespaces.length; i++) {
		context = context[namespaces[i]];
	}
	return context[func].apply(this, args);
};
Funciones.prototype.hoy = function() {
	var today = new Date(),
		dia = today.getDate(),
		mes = today.getMonth() + 1,
		anio = today.getFullYear();

	dia = (dia < 10 ? '0' + dia : dia);
	mes = (mes < 10 ? '0' + mes : mes);

	return dia + '/' + mes + '/' + anio;
};
Funciones.prototype.imgError = function(image) {
	image.onerror = '';
	image.src = 'http://i1378.photobucket.com/albums/ah113/aribachetti/nodisponible_zps0f5898e4.jpg';
	return true;
};