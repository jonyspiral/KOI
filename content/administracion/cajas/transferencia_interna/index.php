<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Transferencia Interna';
		$('.pluginImportes').importes({height: '250px', entradaSalida: 'E', idInputCaja: 'inputCajaSalida', botones: ['E'], saveCallback: refreshImportes, removeCallback: refreshImportes});
		$('#inputObservaciones').blur(function(){
			$('.pluginImportes').importes('show').find('.btn-dropdown .btn:first').focus();
		});
		$('#inputCajaSalida').blur(function(){funciones.delay('getInfoCaja();');});
		cambiarModo('agregar');
	});

	function limpiarScreen(){
		$('.pluginImportes').importes('clean');
		$('#efectivoCaja').text(funciones.formatearMoneda(0));
		$('#importeNeto').text(funciones.formatearMoneda(0));
		$('#importeTotal').text(funciones.formatearMoneda(0));
	}

	function refreshImportes() {
		var total = $('.pluginImportes').importes('getImporte');
		$('#importeTotal').text(funciones.formatearMoneda(total));
		calcularImporteCaja();
	}

	function getInfoCaja(){
		if ($('#inputCajaSalida_selectedValue').val() == ''){
			$('#efectivoCaja').data('importe', 0);
			calcularImporteCaja();
		} else {
			$.postJSON('/content/administracion/cajas/transferencia_interna/getInfoCaja.php?idCaja=' + $('#inputCajaSalida_selectedValue').val(), function(json){
				$('#efectivoCaja').data('importe', json.data && json.data['importeEfectivo'] ? json.data.importeEfectivo : 0);
				calcularImporteCaja();
			});
		}
	}

	function calcularImporteCaja(){
		$('#efectivoCaja').text(funciones.formatearMoneda($('#efectivoCaja').data('importe') - funciones.toFloat($('.pluginImportes').importes('getImporte', 'E'))));
	}

	function hayErrorGuardar(){
		if($('#inputCajaSalida').val() == '')
			return 'Debe seleccionar una caja de origen';

		if($('#inputCajaEntrada').val() == '')
			return 'Debe seleccionar una caja de destino';

		return false;
	}

	function guardar(){
		var url = '/content/administracion/cajas/transferencia_interna/agregar.php?';
		try {
			funciones.guardar(url, armoObjetoGuardar());
		} catch (ex) {
			$.error(ex);
		}
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		$('.pluginImportes').importes('cambiarModo', modo);
		switch (modo){
			case 'inicio':
				$('#btnAgregar').focus();
				break;
			case 'buscar':
				break;
			case 'editar':
				break;
			case 'agregar':
				$('#inputCajaSalida').focus();
				$('#inputFechaDocumento').val(funciones.hoy());
				break;
		}
	}

	function armoObjetoGuardar(){
		return {
			datos: {
				idCaja_E: $('#inputCajaEntrada_selectedValue').val(),
				idCaja_S: $('#inputCajaSalida_selectedValue').val(),
				fechaDocumento: $('#inputFechaDocumento').val(),
				observaciones: $('#inputObservaciones').val()
			},
			importes: $('.pluginImportes').importes('getJson')
		};
	}

</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divDatosTransfInterna' class='fLeft pantalla'>
		<?php
		$tabla = new HtmlTable(array('cantRows' => 5, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
		$tabla->getRowCellArray($rows, $cells);

		$cells[0][0]->style->width = '150px';
		$cells[0][0]->content = '<label>Caja origen:</label>';
		$cells[0][1]->style->width = '250px';
		$cells[0][1]->content = '<input id="inputCajaSalida" class="textbox obligatorio autoSuggestBox inputForm w230" name="CajaPorUsuario" />';


		$cells[1][0]->content = '<label>Caja destino:</label>';
		$cells[1][1]->content = '<input id="inputCajaEntrada" class="textbox obligatorio autoSuggestBox inputForm w230" name="CajaPosiblesTransferenciaInterna" linkedTo="inputCajaSalida,Caja"/>';

		$cells[2][0]->content = '<label>Fecha documento:</label>';
		$cells[2][1]->content = '<input id="inputFechaDocumento" class="textbox obligatorio inputForm aRight w210" validate="Fecha" rel="fecha" />';

		$cells[3][0]->content = '<label>Observaciones:</label>';
		$cells[3][1]->content = '<textarea id="inputObservaciones" class="textbox inputForm w230" rel="observaciones" ></textarea>';

		$cells[4][0]->content = '<label>Efvo restante en caja:</label>';
		$cells[4][1]->content = '<label id="efectivoCaja" class="s16">$ 0.00</label>';

		$tabla->create();//impresion
		?>
	</div>
	<div class='fRight pantalla w50p'>
		<div class='well'>
			<?php
			$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 2, 'id' => 'tablaTotales', 'class' => 'w100p', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->class = 'w50p';
			$cells[0][0]->content = '<label class="bold">Importe total:</label>';
			$cells[0][1]->class = 'w50p aRight';
			$cells[0][1]->content = '<label id="importeTotal" class="bold">$ 0.00</label>';

			$tabla->create();
			?>
		</div>
	</div>
	<div class="pluginImportes"></div>
</div>
<div id='programaPie'>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'administracion/cajas/transferencia_interna/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
