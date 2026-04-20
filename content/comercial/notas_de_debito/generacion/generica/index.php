<?php
$tipoDocumento2 = Funciones::get('tipoDocumento2');
?>

<script type='text/javascript'>
	var pIva = '0';

	$(document).ready(function(){
		tituloPrograma = 'Generación notas de débito';
		$('#inputCliente').blur(function(){funciones.delay('ponerDescuento();');});
		$('#inputImporteNoGravado').blur(function(){funciones.delay('calcularImportes();');});
		$('#inputImporteGravado').blur(function(){funciones.delay('calcularImportes();');});
		$('#inputAplicarDescuentoComercial').change(function(){
			($(this).isChecked()) ? $('.trDescuentoComercial').show() : $('.trDescuentoComercial').hide();
			calcularImportes();
		});
		var tipoDocumento2 = '<?php echo ($tipoDocumento2 ? $tipoDocumento2 : ''); ?>';
		if (tipoDocumento2) {
			cambiarModo('agregar');
			$('#inputTipoDocumento2').val(tipoDocumento2);
		} else {
			cambiarModo('inicio');
		}
	});

	function limpiarScreen(){
	}

	function ponerDescuento() {
		if ($('#inputCliente_selectedValue').val() != '') {
			$.getJSON(funciones.controllerUrl('getInfoCliente', {
				idCliente: $('#inputCliente_selectedValue').val()
			}), function(json){
				pIva = json.data.ivaPorc;
				$('#labelDescuentoComercialPorcentaje').text(funciones.formatearPorcentaje(json.data.descuento));
				$('#labelIvaPorcentaje').text(funciones.formatearPorcentaje(json.data.ivaPorc));
			});
		} else {
			pIva = '0';
			$('#labelDescuentoComercialPorcentaje').text(funciones.formatearPorcentaje(0));
			$('#labelIvaPorcentaje').text(funciones.formatearPorcentaje(0));
		}
	}

	function calcularImportes() {
		var iGravado = funciones.toFloat($('#inputImporteGravado').val());
		var iNoGravado = funciones.toFloat($('#inputImporteNoGravado').val());
		var pDcto = $('#inputAplicarDescuentoComercial').isChecked() ? funciones.toFloat($('#labelDescuentoComercialPorcentaje').text()) : 0;

		var iDctoGravado = funciones.round(iGravado * (pDcto / 100), 2);
		var iDctoNoGravado = funciones.round(iNoGravado * (pDcto / 100), 2);
		var subGravado = iGravado - iDctoGravado;
		var subNoGravado = iNoGravado - iDctoNoGravado;

		var iIva = funciones.round((iGravado - iDctoGravado) * (pIva / 100), 2);

		$('#labelDescuentoComercialImporteGravado').text(funciones.formatearMoneda(iDctoGravado));
		$('#labelDescuentoComercialImporteNoGravado').text(funciones.formatearMoneda(iDctoNoGravado));
		$('#labelSubtotalGravado').text(funciones.formatearMoneda(subGravado));
		$('#labelSubtotalNoGravado').text(funciones.formatearMoneda(subNoGravado));
		$('#labelIvaImporte').text(funciones.formatearMoneda(iIva));
		$('#labelImporteTotal').text(funciones.formatearMoneda(iGravado + iNoGravado - iDctoGravado - iDctoNoGravado + iIva));
	}

	function hayErrorGuardar(){
		if ($('#inputCliente').val() == '')
			return 'Debe elegir un cliente';
		if ($('#inputTipoDocumento2').val() == '')
			return 'Debe elegir un tipo de documento';
		if ($('#inputImporteGravado').val() > 0 && $('#inputDetalleItem').val() == '')
			return 'Debe ingresar el detalle gravado de la NDB';
		if ($('#inputImporteNoGravado').val() > 0 && $('#inputDetalleItemNoGravado').val() == '')
			return 'Debe ingresar el detalle no gravado de la NDB';
	}

	function guardar(){
		var url = '/content/comercial/notas_de_debito/generacion/generica/agregar.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idCliente: $('#inputCliente_selectedValue').val(),
			tipoDocumento2: $('#inputTipoDocumento2').val(),
			detalleItem: $('#inputDetalleItem').val(),
			detalleItemNoGravado: $('#inputDetalleItemNoGravado').val(),
			observaciones: $('#inputObservaciones').val(),
			importeGravado: $('#inputImporteGravado').val(),
			importeNoGravado: $('#inputImporteNoGravado').val(),
			aplicarDescuento: $('#inputAplicarDescuentoComercial').isChecked() ? 'S' : 'N'
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'agregar':
				$('#inputCliente').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divNdbGenericaDatos' class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 5, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Cliente:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputCliente" class="textbox autoSuggestBox obligatorio inputForm w230" name="Cliente" rel="nombre" />';
			$cells[0][1]->style->width = '250px';
			$cells[1][0]->content = '<label>Tipo Documento:</label>';
			$cells[1][1]->content = '<select id="inputTipoDocumento2" class="textbox obligatorio inputForm w245" size="3" >';
			$cells[1][1]->content .= '<option value="' . TiposDocumento2::ndbAjuste . '">Nota de Débito Ajuste</option>';
			$cells[1][1]->content .= '<option value="' . TiposDocumento2::ndbComercial . '">Nota de Débito Comercial</option>';
			$cells[1][1]->content .= '<option value="' . TiposDocumento2::ndbFinanciera . '">Nota de Débito Financiera</option>';
			$cells[1][1]->content .= '<option value="' . TiposDocumento2::ndbChequeRechazado . '">Nota de Débito Cheque Rechazado</option>';
			$cells[1][1]->content .= '</select>';
			$cells[2][0]->content = '<label>Detalle gravado:</label>';
			$cells[2][1]->content = '<textarea id="inputDetalleItem" class="textbox inputForm inputForm w230" /></textarea>';
			$cells[3][0]->content = '<label>Detalle no gravado:</label>';
			$cells[3][1]->content = '<textarea id="inputDetalleItemNoGravado" class="textbox inputForm inputForm w230" /></textarea>';
			$cells[4][0]->content = '<label>Observaciones: </label>';
			$cells[4][1]->content = '<textarea id="inputObservaciones" class="textbox inputForm inputForm w230" /></textarea>';

			$tabla->create();
		?>
	</div>
	<div id='divNdbGenericaValores' class='fRight pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 9, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Importe gravado:</label>';
			$cells[0][0]->style->width = '200px';
			$cells[0][1]->content = '<input id="inputImporteGravado" class="textbox inputForm w90 aRight" validate="Decimal" />';
			$cells[0][1]->style->width = '120px';
			$cells[0][1]->class = 'aRight pRight150';
			$cells[1][0]->content = '<label>Importe no gravado:</label>';
			$cells[1][1]->content = '<input id="inputImporteNoGravado" class="textbox inputForm w90 aRight" validate="Decimal" />';
			$cells[1][1]->class = 'aRight pRight150';
			$cells[2][0]->content = '<label>Aplicar dcto. comercial:</label>';
			$cells[2][1]->content = '<input id="inputAplicarDescuentoComercial" type="checkbox" class="textbox inputForm" />';
			$cells[2][1]->class = 'aRight pRight150';
			$cells[3][0]->content = '<label>Dcto comer. porcentaje:</label>';
			$cells[3][1]->content = '<label id="labelDescuentoComercialPorcentaje"></label>';
			$rows[3]->class .= ' trDescuentoComercial';
			$rows[3]->style->display = 'none';
			$cells[3][1]->class = 'aRight pRight150';
			$cells[4][0]->content = '<label>Dcto comer. importe:</label>';
			$cells[4][1]->content = '<label id="labelDescuentoComercialImporteGravado"></label> | <label id="labelDescuentoComercialImporteNoGravado"></label>';
			$rows[4]->class .= ' trDescuentoComercial';
			$rows[4]->style->display = 'none';
			$cells[4][1]->class = 'aRight pRight150';
			$cells[5][0]->content = '<label>Subtotal:</label>';
			$cells[5][1]->content = '<label id="labelSubtotalGravado"></label> | <label id="labelSubtotalNoGravado"></label>';
			$cells[5][1]->class = 'aRight pRight150';
			$cells[6][0]->content = '<label>IVA porcentaje:</label>';
			$cells[6][1]->content = '<label id="labelIvaPorcentaje"></label>';
			$cells[6][1]->class = 'aRight pRight150';
			$cells[7][0]->content = '<label>IVA importe:</label>';
			$cells[7][1]->content = '<label id="labelIvaImporte"></label>';
			$cells[7][1]->class = 'aRight pRight150';
			$cells[8][0]->content = '<label class="bold">Importe total:</label>';
			$cells[8][1]->content = '<label id="labelImporteTotal" class="bold"></label>';
			$cells[8][1]->class = 'aRight pRight150';

			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'comercial/notas_de_debito/generacion/generica/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>