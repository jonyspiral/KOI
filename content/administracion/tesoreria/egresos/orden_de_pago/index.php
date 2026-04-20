<?php

?>

<script type='text/javascript'>
	var retieneGanancias = false;

	$(document).ready(function(){
		tituloPrograma = 'Orden de Pago';
		$('.pluginImportes').importes({height: '250px', entradaSalida: 'S', idInputCaja: 'inputCaja', botones: ['E', 'C', 'T', 'G'], saveCallback: refreshImportes, removeCallback: refreshImportes});
		$('#inputProveedor').blur(function(){funciones.delay('getInfoProveedor();');});
		//eventos click
		$('#rdProveedor').click(function(){
			$('.trOtro').hide();
			$('.trRetiene').show();
			$('.trProveedor').show();
			$('#inputProveedor').val('');
			$('#inputProveedor_selectedValue').val('');
		});
		$('#rdOtro').click(function(){
			$('.trOtro').show();
			$('.trRetiene').hide();
			$('.trProveedor').hide();
			$('#rdRetieneGananciasN').click();
		});
		$('#rdRetieneGananciasN').click(function() {retieneGananciasClick(false);});
		$('#rdRetieneGananciasS').click(function() {retieneGananciasClick(true);});
		$('#btnToggleAyuda').click(function(){
			$('#toggleAyuda').slideToggle();
		});
		$('#btnCalcularAyuda').click(getImporteRetencion);
		$('#inputObservaciones').blur(function(){
			$('.pluginImportes').importes('show').find('.btn-dropdown .btn:first').focus();
		});
		bindearEventos();
		cambiarModo('inicio');
	});

	function bindearEventos() {
		$('.saldo').hover(function() {
			$(this).stop(true, true).css('font-weight', 'bold');
		}, function() {
			$(this).stop(true, true).css('font-weight', 'normal');
		});
		$('.saldo').click(function() {
			var idProveedor = $('#inputProveedor_selectedValue').val();
			if(idProveedor != ''){
				funciones.newWindow('/administracion/proveedores/cuenta_corriente_proveedor/?idProveedor=' + idProveedor);
			}
		});
	}

	function retieneGananciasClick(bool) {
		if (bool) {
			retieneGanancias = true;
			$('#divAyudaRetencion').show();
			$('.trRetencion').show();
			$('#toggleAyuda').hide();
		} else {
			retieneGanancias = false;
			$('#divAyudaRetencion').hide();
			$('.trRetencion').hide();
		}
		refreshImportes();
	}

	function getImporteRetencion() {
		var params = 'ayuda=1&neto=' + $('#ayudaInputImporteTotal').val() + '&idProveedor=' + $('#inputProveedor_selectedValue').val(),
			importeTotal = ($('#ayudaInputImporteTotal').val() == '' ? 0 : $('#ayudaInputImporteTotal').val());
		$.getJSON('/content/administracion/tesoreria/egresos/orden_de_pago/getImporteRetencion.php?' + params, function(json){
			$('#ayudaImporteNeto').text(funciones.formatearMoneda(json.data.neto));
			$('#ayudaImporteRetencion').text(funciones.formatearMoneda(json.data.retencion));
			$('#ayudaImporteRetencion').data('importeRetencion', json.data.retencion);
			$('#ayudaLabelImporteRestante').text(funciones.formatearMoneda(importeTotal));
			$('#ayudaLabelImporteRestante').data('importeTotal', importeTotal);
			refreshImportes();
		});
	}

	function doGetInfoProveedor(paramConfirmar){
		if(!paramConfirmar){paramConfirmar = '';}

		$.getJSON(funciones.controllerUrl('getInfoProveedor', 'idProveedor=' + $('#inputProveedor_selectedValue').val() + paramConfirmar), function(json){
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
							doGetInfoProveedor(json.data[0]);
					});
					break;
				default:
					var importeTotal = ($('#ayudaInputImporteTotal').val() == '' ? 0 : $('#ayudaInputImporteTotal').val());
					$('#rdRetieneGanancias' + json.data.retener).radioClick();
					$('#labelImputacion').text(json.data.imputacion);
					$('#labelSaldo').text(funciones.formatearMoneda(json.data.saldo));
					$('#labelPlazoPago').text(json.data.plazoPago ? json.data.plazoPago + ' días' : '');
					$('#ayudaLabelImporteRestante').text(funciones.formatearMoneda(importeTotal));
					$('#ayudaLabelImporteRestante').data('importeTotal', importeTotal);
					break;
			}
		});
	}

	function getInfoProveedor(){
		doGetInfoProveedor();
	}

	function limpiarScreen(){
		$('.pluginImportes').importes('clean');
		$('#ayudaImporteNeto').text(funciones.formatearMoneda(0));
		$('#ayudaImporteRetencion').text(funciones.formatearMoneda(0));
		$('#importeNeto').text(funciones.formatearMoneda(0));
		$('#importeRetencion').text(funciones.formatearMoneda(0));
		$('#importeTotal').text(funciones.formatearMoneda(0));
		$('#labelImputacion').text('');
		$('#labelSaldo').text('');
		$('#labelPlazoPago').text('');
	}

	function refreshImportes() {
		var neto = $('.pluginImportes').importes('getImporte'),
			retencion = $('#ayudaImporteRetencion').data('importeRetencion');
		if (retieneGanancias) {
			var params = 'neto=' + neto + '&idProveedor=' + $('#inputProveedor_selectedValue').val();
			$.getJSON('/content/administracion/tesoreria/egresos/orden_de_pago/getImporteRetencion.php?' + params, function(json){
				$('#importeNeto').text(funciones.formatearMoneda(json.data.neto));
				$('#importeRetencion').text(funciones.formatearMoneda(json.data.retencion));
				$('#importeTotal').text(funciones.formatearMoneda(json.data.bruto));
				if($('#ayudaLabelImporteRestante').data('importeTotal') > 0){
					$('#ayudaLabelImporteRestante').text(funciones.formatearMoneda($('#ayudaLabelImporteRestante').data('importeTotal') - neto - retencion));
				}
			});
		} else {
			$('#importeNeto').text(funciones.formatearMoneda(neto));
			$('#importeRetencion').text(funciones.formatearMoneda(0));
			$('#importeTotal').text(funciones.formatearMoneda(neto));
		}
	}

	function hayErrorGuardar(){
		if ($('#inputCaja_selectedValue').val() == '' && $('#inputBuscar_selectedValue').val() == '') {
			return 'Debe seleccionar una caja para operar';
		}
		if ($('#radioTipoOrdenDePago').radioVal() == 'P') {
			if ($('#inputProveedor').val() == '')
				return 'Debe ingresar un proveedor';
		}
		if ($('#radioTipoOrdenDePago').radioVal() == 'OI') {
			if ($('#inputBeneficiario').val() == '')
				return 'Debe ingresar un beneficiario';
			if ($('#inputImputacion_selectedValue').val() == '')
				return 'Debe ingresar una imputación';
		}

		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/administracion/tesoreria/egresos/orden_de_pago/' + aux + '.php?';
		try {
			funciones.guardar(url, armoObjetoGuardar(), function() {
				funciones.limpiarScreen();
				cambiarModo('buscar');
				$('#inputBuscar, #inputBuscar_selectedValue').val(this.data.numero).blur();
				funciones.pdfClick('/administracion/proveedores/aplicacion/?idProveedor=' + this.data.proveedor.id);
			});
		} catch (ex) {
			$.error(ex);
		}
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined'){
			$('#inputBuscar').val(idBuscar).autoComplete();
			return $('#inputBuscar').val(idBuscar).blur();
		}
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/administracion/tesoreria/egresos/orden_de_pago/buscar.php?id=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'La orden de pago "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
				$('#rdRetieneGanancias' + (json.retieneGanancias == 'S' ? 'S' : 'N')).radioClick();
				$('#inputProveedor').autoComplete(json.proveedor); //Lo agrego porque cuando hace el complete del radio setea el val en ''
				$('#labelImputacion').text(json.imputacion.id + ' - ' + json.imputacion.nombre);
				$('.pluginImportes').importes('load', json.importePorOperacion.detalle);
				refreshImportes();
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		$('.pluginImportes').importes('cambiarModo', modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				$('.trCaja').hide();
				break;
			case 'editar':
				$('.trCaja').hide();
				$('#radioGroupTipoOrdenDePago').focus();
				break;
			case 'agregar':
				$('.trCaja').show();
				$('#inputFechaDocumento').val(funciones.hoy());
				$('#inputCaja').focus();
				break;
		}
	}

	function armoObjetoGuardar(){
		return {
			datos: {
				tipoOP: $('#radioTipoOrdenDePago').radioVal(),
				idProveedor: $('#inputProveedor_selectedValue').val(),
				beneficiario: $('#inputBeneficiario').val(),
				observaciones: $('#inputObservaciones').val(),
				idCaja_S: $('#inputCaja_selectedValue').val(),
				idImputacion: $('#inputImputacion_selectedValue').val(),
				retieneGanancias: $('#radioGroupRetieneGanancias').radioVal(),
				idOrdenDePago: $('#inputBuscar_selectedValue').val()
			},
			importes: $('.pluginImportes').importes('getJson')
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar la orden de pago número "' + $('#inputBuscar_selectedValue').val() + '"?',
			url = '/content/administracion/tesoreria/egresos/orden_de_pago/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {idOrdenDePago: $('#inputBuscar_selectedValue').val()};
	}

	function pdfClick(){
		var url = '/content/administracion/tesoreria/egresos/orden_de_pago/getPdf.php';
		url += '?id=' + $('#inputBuscar_selectedValue').val();
		funciones.pdfClick(url);
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 10, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$rows[1]->class .= ' trCaja';
			$rows[2]->class .= ' trProveedor';
			$rows[3]->class .= ' trProveedor';
			$rows[4]->class .= ' trOtro';
			$rows[5]->class .= ' trRetiene';
			$rows[6]->class .= ' trProveedor';
			$rows[7]->class .= ' trOtro';

			$cells[0][0]->style->width = '150px';
			$cells[0][0]->content = '<label>Tipo Orden de pago:</label>';
			$cells[0][1]->style->width = '250px';
			$cells[0][1]->content = '<div id="radioTipoOrdenDePago" class="customRadio" default="rdProveedor" rel="tipo">
										<input id="rdProveedor" class="textbox inputTipoOrdenDePago" type="radio" name="radioGroupTipoOrdenDePago" value="P" rel="tipoOperacion" /><label for="rdProveedor">Proveedor</label>' .
										'<input id="rdOtro" class="textbox inputTipoOrdenDePago" type="radio" name="radioGroupTipoOrdenDePago" value="O" rel="tipoOperacion" /><label for="rdOtro">Otro</label>';

			$cells[1][0]->content = '<label>Caja a utilizar:</label>';
			$cells[1][1]->content = '<span rel="importePorOperacion"><input id="inputCaja" class="textbox obligatorio autoSuggestBox inputForm w230" name="CajaPorUsuario" rel="caja" /></span>';

			$cells[2][0]->content = '<label>Proveedor:</label>';
			$cells[2][1]->content = '<input id="inputProveedor" class="textbox obligatorio autoSuggestBox inputForm w230" name="Proveedor" rel="proveedor" />';

			$cells[3][0]->content = '<label>Saldo:</label>';
			$cells[3][1]->content = '<label id="labelSaldo" class="saldo cPointer"></label>';

			$cells[4][0]->content = '<label>Beneficiario:</label>';
			$cells[4][1]->content = '<input id="inputBeneficiario" class="textbox obligatorio inputForm w230" rel="beneficiario" />';

			$cells[5][0]->content = '<label>Retiene ganancias:</label>';
			$cells[5][1]->content = '<div id="radioGroupRetieneGanancias" class="customRadio" default="rdRetieneGananciasN">' .
									'<input id="rdRetieneGananciasS" class="textbox" type="radio" name="radioGroupRetieneGanancias" value="S" /><label for="rdRetieneGananciasS">S</label>' .
									'<input id="rdRetieneGananciasN" class="textbox" type="radio" name="radioGroupRetieneGanancias" value="N" /><label for="rdRetieneGananciasN">N</label></div>';

			$cells[6][0]->content = '<label>Imputacion:</label>';
			$cells[6][1]->content = '<label id="labelImputacion"></label>';

			$cells[7][0]->content = '<label>Imputacion:</label>';
			$cells[7][1]->content = '<input id="inputImputacion" class="textbox autoSuggestBox obligatorio inputForm w230" name="Imputacion" rel="imputacion" />';

			$cells[8][0]->content = '<label>Plazo de pago:</label>';
			$cells[8][1]->content = '<label id="labelPlazoPago"></label>';

			$cells[9][0]->content = '<label>Observaciones:</label>';
			$cells[9][1]->content = '<textarea id="inputObservaciones" class="textbox inputForm w230" rel="observaciones"></textarea>';

			$tabla->create();//impresion
		?>
	</div>
	<div class='fRight pantalla w50p'>
		<div class='well'>
			<?php
			$tabla = new HtmlTable(array('cantRows' => 3, 'cantCols' => 2, 'id' => 'tablaTotales', 'class' => 'w100p', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$rows[0]->class = 'trNeto';
			$rows[1]->class = 'trRetencion';
			$rows[2]->class = 'trTotal';

			$cells[0][0]->class = 'w50p';
			$cells[0][0]->content = '<label>Importe neto:</label>';
			$cells[0][1]->class = 'w50p aRight';
			$cells[0][1]->content = '<label id="importeNeto">$ 0.00</label>';

			$cells[1][0]->class = 'w50p';
			$cells[1][0]->content = '<label>Importe retención:</label>';
			$cells[1][1]->class = 'w50p aRight';
			$cells[1][1]->content = '<label id="importeRetencion">$ 0.00</label>';

			$cells[2][0]->class = 'w50p';
			$cells[2][0]->content = '<label class="bold">Importe total:</label>';
			$cells[2][1]->class = 'w50p aRight';
			$cells[2][1]->content = '<label id="importeTotal" class="bold">$ 0.00</label>';

			$tabla->create();
			?>
		</div>
		<div id="divAyudaRetencion" class='well mTop5'>
			<div id="btnToggleAyuda" class='cPointer'>
				<label class='bold cPointer'>Ayuda (¡Gracias, Demian Marasso!) <span class="s12">&#x25BC;</span></label>
			</div>
			<div id="toggleAyuda">
			<?php
			$tabla = new HtmlTable(array('cantRows' => 4, 'cantCols' => 2, 'id' => 'tablaAyuda', 'class' => 'w100p', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$rows[1]->class = 'trTotal';
			$rows[2]->class = 'trNeto';
			$rows[3]->class = 'trRetencion';

			$cells[0][0]->class = 'w70p';
			$cells[0][0]->content = '<label>Importe total a pagar:</label>';
			$cells[0][1]->class = 'w30p aRight';
			$cells[0][1]->content = '<input id="ayudaInputImporteTotal" class="textbox inputForm w70 aRight vaTop" />
									<a id="btnCalcularAyuda" class="boton" href="#" title="Calcular"><img src="/img/botones/25/actualizar.gif"></a>';

			$cells[1][0]->class = 'w70p';
			$cells[1][0]->content = '<label>Restan:</label>';
			$cells[1][1]->class = 'w30p aRight';
			$cells[1][1]->content = '<label id="ayudaLabelImporteRestante" class="bold">$ 0.00</label>';

			$cells[2][0]->class = 'w70p';
			$cells[2][0]->content = '<label class="bold">La suma de importes (neto) deberá ser:</label>';
			$cells[2][1]->class = 'w30p aRight';
			$cells[2][1]->content = '<label id="ayudaImporteNeto" class="bold">$ 0.00</label>';

			$cells[3][0]->class = 'w70p';
			$cells[3][0]->content = '<label>La retención será de:</label>';
			$cells[3][1]->class = 'w30p aRight';
			$cells[3][1]->content = '<label id="ayudaImporteRetencion">$ 0.00</label>';

			$tabla->create();
			?>
			</div>
		</div>
	</div>
	<div class="pluginImportes"></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarProveedor' class='filtroBuscar'>Proveedor:</label>
			<input id='inputBuscarProveedor' class='textbox autoSuggestBox filtroBuscar w200' name='Proveedor' />
		</div>
		<div>
			<label for='inputBuscar' class='filtroBuscar'>Orden de pago:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='OrdenDePago' linkedTo='inputBuscarProveedor,Proveedor' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'administracion/tesoreria/egresos/orden_de_pago/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'administracion/tesoreria/egresos/orden_de_pago/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'administracion/tesoreria/egresos/orden_de_pago/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
