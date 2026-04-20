<?php
?>
<script type='text/javascript'>
	$(document).ready(function(){
		$('#inputProveedor').blur(function(){funciones.delay('ponerProveedor();');});
		tituloPrograma = 'Guía de porte';
		cambiarModo('inicio');
	});

	function hayErrorGuardar(){
		/*
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar el nombre del contacto';
		if ($('#inputApellido').val() == '')
			return 'Debe ingresar el apellido del contacto';
		if (($('#rdCliente').isChecked()) && (($('#inputCliente_selectedValue').val() == '') || ($('#inputSucursal_selectedValue').val() == '')))
			return 'Debe elegir un cliente y sucursal para vincular el contacto';
		if (($('#rdProveedor').isChecked()) && ($('#inputProveedor_selectedValue').val() == ''))
			return 'Debe elegir un proveedor para vincular el contacto';
		*/
		return false;
	}

	function ponerProveedor() {
		$.getJSON('/content/produccion/guia_de_porte/getInfoProveedor.php?idProveedor=' + $('#inputProveedor_selectedValue').val(), function(json){
			$('#tablaDatos').loadJSON(json.data);
			//$('#inputVendedor').autoComplete(json.data.vendedor);
		});
	}

	function armoDetalle(){
		var detalle = [];
		for (var i = 1; i <= 14; i++) {
			var cant = $('.inputCant[itemNro="' + i + '"]').val();
			var deta = $('.inputDetalle[itemNro="' + i + '"]').val();
			detalle[i] = {cantidad: cant, detalle: deta};
		}
		return detalle;
	}

	function armoObjetoGuardar(){
		return {
			numeroGuia: $('#inputNumeroGuia').val(),
			fecha: $('#inputFecha').val(),
			senores: $('#inputProveedor_selectedName').val(),
			clienteNro: $('#inputClienteNro').val(),
			direccionCalle: $('#inputDireccionCalle').val(),
			direccionNumero: $('#inputDireccionNumero').val(),
			direccionPiso: $('#inputDireccionPiso').val(),
			direccionDpto: $('#inputDireccionDpto').val(),
			direccionLocalidad: $('#inputDireccionLocalidad').val(),
			direccionCP: $('#inputDireccionCP').val(),
			cuit: $('#inputCuit').val(),
			condicionIva: $('#radioGroupCondicionIva').radioVal(),
			transportistaSenor: $('#inputTransportistaSenor').val(),
			transportistaDomicilio: $('#inputTransportistaDomicilio').val(),
			transportistaCuit: $('#inputTransportistaCuit').val(),
			transportistaDni: $('#inputTransportistaDni').val(),
			detalle: armoDetalle()
		};
	}

	function pdfClick() {
		var url = '/content/produccion/guia_de_porte/getPdf.php';
			url += '?' + funciones.serialize(armoObjetoGuardar());
		if (!hayErrorGuardar())
			funciones.pdfClick(url);
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#btnPdf').hide();
				funciones.delay('$("#btnAgregar").focus()', 100);
				break;
			case 'agregar':
				$('#btnPdf').show();
				$("#inputNumeroGuia").focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divFormulario' class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 14, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Número de guía:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputNumeroGuia" class="textbox inputForm w230" validate="" mask="0001-00099999" />';
			$cells[0][1]->style->width = '250px';
			$cells[1][0]->content = '<label>Fecha:</label>';
			$cells[1][1]->content = '<input id="inputFecha" class="textbox inputForm w210" validate="Fecha" />';
			$cells[2][0]->content = '<label>Proveedor:</label>';
			$cells[2][1]->content = '<input id="inputProveedor" class="textbox inputForm w230 autoSuggestBox" name="Proveedor" />';
			$cells[3][0]->content = '<label>Cliente Nº:</label>';
			$cells[3][1]->content = '<input id="inputClienteNro" class="textbox inputForm w230" validate="Entero" rel="idProveedor" />';
			$cells[4][0]->content = '<label>Calle:</label>';
			$cells[4][1]->content = '<input id="inputDireccionCalle" class="textbox inputForm w230" rel="direccionCalle" />';
			$cells[5][0]->content = '<label>Numero:</label>';
			$cells[5][1]->content = '<input id="inputDireccionNumero" class="textbox inputForm w65" validate="Entero" rel="direccionNumero" />
									<label>Piso:</label>
									<input id="inputDireccionPiso" class="textbox inputForm w25" maxlength="3" validate="Entero" rel="direccionPiso" />
									<label>Dpto:</label>
									<input id="inputDireccionDpto" class="textbox inputForm w25" maxlength="3" validate="Entero" rel="direccionDepartamento" />';
			$cells[6][0]->content = '<label>Localidad:</label>';
			$cells[6][1]->content = '<input id="inputDireccionLocalidad" class="textbox inputForm w135" rel="direccionLocalidad" />
									<label>CP:</label>
									<input id="inputDireccionCP" class="textbox inputForm w45" maxlength="4" validate="Entero" rel="direccionCodigoPostal" />';
			$cells[7][0]->content = '<label>CUIT:</label>';
			$cells[7][1]->content = '<input id="inputCuit" class="textbox inputForm w230" validate="Cuit" rel="cuit" />';
			$cells[8][0]->content = '<label>Condición IVA:</label>';
			$cells[8][1]->content = '<div id="radioGroupCondicionIva" class="customRadio" rel="condicionIva"><input id="rdCondicionIvaRI" class="textbox inputCondicionIva" type="radio" name="radioGroupCondicionIva" value="RI" rel="id" /><label for="rdCondicionIvaRI">R.I.</label>' .
									 '<input id="rdCondicionIvaMO" class="textbox inputCondicionIva" type="radio" name="radioGroupCondicionIva" value="MO" rel="id" /><label for="rdCondicionIvaMO">MO.</label></div>';
			$cells[9][0]->content = '<label>Transportista</label>';
			$cells[10][0]->content = '<label>Señor:</label>';
			$cells[10][1]->content = '<input id="inputTransportistaSenor" class="textbox inputForm w230" />';
			$cells[11][0]->content = '<label>Domicilio:</label>';
			$cells[11][1]->content = '<input id="inputTransportistaDomicilio" class="textbox inputForm w230" />';
			$cells[12][0]->content = '<label>CUIT:</label>';
			$cells[12][1]->content = '<input id="inputTransportistaCuit" class="textbox inputForm w230" validate="Cuit" />';
			$cells[13][0]->content = '<label>DNI:</label>';
			$cells[13][1]->content = '<input id="inputTransportistaDni" class="textbox inputForm w230" validate="Dni" />';

			$tabla->create();//impresion
		?>
	</div>
	<div id='divFormulario' class='fRight pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 15, 'cantCols' => 2, 'id' => 'tablaDatos2', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Cantidad</label>';
			$cells[0][0]->style->width = '50px';
			$cells[0][0]->class = 'aCenter';
			$cells[0][1]->content = '<label>Detalle</label>';
			$cells[0][1]->style->width = '350px';
			$cells[0][1]->class = 'aCenter';
			$cells[1][0]->content = '<input itemNro="1" class="textbox inputForm w50 inputCant" validate="Numero" />';	
			$cells[1][1]->content = '<input itemNro="1" class="textbox inputForm w350 inputDetalle" maxlength="50" />';
			$cells[2][0]->content = '<input itemNro="2" class="textbox inputForm w50 inputCant" validate="Numero" />';	
			$cells[2][1]->content = '<input itemNro="2" class="textbox inputForm w350 inputDetalle" maxlength="50" />';
			$cells[3][0]->content = '<input itemNro="3" class="textbox inputForm w50 inputCant" validate="Numero" />';	
			$cells[3][1]->content = '<input itemNro="3" class="textbox inputForm w350 inputDetalle" maxlength="50" />';
			$cells[4][0]->content = '<input itemNro="4" class="textbox inputForm w50 inputCant" validate="Numero" />';	
			$cells[4][1]->content = '<input itemNro="4" class="textbox inputForm w350 inputDetalle" maxlength="50" />';
			$cells[5][0]->content = '<input itemNro="5" class="textbox inputForm w50 inputCant" validate="Numero" />';	
			$cells[5][1]->content = '<input itemNro="5" class="textbox inputForm w350 inputDetalle" maxlength="50" />';
			$cells[6][0]->content = '<input itemNro="6" class="textbox inputForm w50 inputCant" validate="Numero" />';	
			$cells[6][1]->content = '<input itemNro="6" class="textbox inputForm w350 inputDetalle" maxlength="50" />';
			$cells[7][0]->content = '<input itemNro="7" class="textbox inputForm w50 inputCant" validate="Numero" />';	
			$cells[7][1]->content = '<input itemNro="7" class="textbox inputForm w350 inputDetalle" maxlength="50" />';
			$cells[8][0]->content = '<input itemNro="8" class="textbox inputForm w50 inputCant" validate="Numero" />';	
			$cells[8][1]->content = '<input itemNro="8" class="textbox inputForm w350 inputDetalle" maxlength="50" />';
			$cells[9][0]->content = '<input itemNro="9" class="textbox inputForm w50 inputCant" validate="Numero" />';	
			$cells[9][1]->content = '<input itemNro="9" class="textbox inputForm w350 inputDetalle" maxlength="50" />';
			$cells[10][0]->content = '<input itemNro="10" class="textbox inputForm w50 inputCant" validate="Numero" />';	
			$cells[10][1]->content = '<input itemNro="10" class="textbox inputForm w350 inputDetalle" maxlength="50" />';
			$cells[11][0]->content = '<input itemNro="11" class="textbox inputForm w50 inputCant" validate="Numero" />';	
			$cells[11][1]->content = '<input itemNro="11" class="textbox inputForm w350 inputDetalle" maxlength="50" />';
			$cells[12][0]->content = '<input itemNro="12" class="textbox inputForm w50 inputCant" validate="Numero" />';	
			$cells[12][1]->content = '<input itemNro="12" class="textbox inputForm w350 inputDetalle" maxlength="50" />';
			$cells[13][0]->content = '<input itemNro="13" class="textbox inputForm w50 inputCant" validate="Numero" />';	
			$cells[13][1]->content = '<input itemNro="13" class="textbox inputForm w350 inputDetalle" maxlength="50" />';
			$cells[14][0]->content = '<input itemNro="14" class="textbox inputForm w50 inputCant" validate="Numero" />';	
			$cells[14][1]->content = '<input itemNro="14" class="textbox inputForm w350 inputDetalle" maxlength="50" />';

			$tabla->create();//impresion
		?>
	</div>
</div>
<div id='programaPie'>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
