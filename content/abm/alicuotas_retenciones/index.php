<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Alicuotas retenciones';

		$('.tabladinamica ').tablaDinamica(
			{
				width: '100%',
				height: 'auto',
				caption: 'Detalle',
				scrollbar: false,
				addButtonInHeader: true,
				buttons: ['Q'],
				columnsConfig: [
					{
						id: 'concepto',
						name: 'Concepto',
						width: '550px',
						css: {textAlign: 'center'},
						cellType: 'I',
						template: '<input class="textbox obligatorio w540" type="text" />'
					},
					{
						id: 'montoNosujeto',
						name: 'Monto no sujeto a retención',
						width: '205px',
						css: {textAlign: 'center'},
						cellType: 'I',
						template: '<input class="textbox obligatorio w90" type="text" validate="DecimalPositivo" />'
					},
					{
						id: 'inscriptoPorc',
						name: 'Inscripto %',
						width: '112px',
						css: {textAlign: 'center'},
						cellType: 'I',
						template: '<input class="textbox obligatorio w90" type="text" validate="DecimalPositivo" />'
					},
					{
						id: 'noInscriptoPorc',
						name: 'No inscripto %',
						width: '115px',
						css: {textAlign: 'center'},
						cellType: 'I',
						template: '<input class="textbox obligatorio w90" type="text" validate="DecimalPositivo" />'
					},
					{
						id: 'minRetencion',
						name: 'No efectuar retención menor de',
						width: '225px',
						css: {textAlign: 'center'},
						cellType: 'I',
						template: '<input class="textbox obligatorio w90" type="text" validate="DecimalPositivo" />'
					}
				],
				popUp: false,
				popUpTemplate: false,
				popUpFillMapper: false,
				saveCallback: false,
				removeCallback: false,
				pluralName: 'registros',
				notEmpty: true
			}
		);
		cambiarModo('inicio');
	});

	function buscar() {
		if($('#inputBuscarMes').val() != '' && $('#inputBuscarAno').val() != ''){
			funciones.limpiarScreen();
			var url = '/content/abm/alicuotas_retenciones/buscar.php?',
				msgError = 'No existen alicuotas para el mes "' + $('#inputBuscarMes').val() + '" año "' + $('#inputBuscarAno').val() + '"';

			cbSuccess = function(json){
				$('#inputMes').val(json.mes);
				$('#inputAno').val(json.ano);
				$('.tabladinamica').tablaDinamica('load', json.detalle);
			};
			url += 'mes=' + $('#inputBuscarMes').val();
			url += '&ano=' + $('#inputBuscarAno').val();
			funciones.buscar(url, cbSuccess, msgError);
		}
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar el nombre del impuesto';

		if ($('#inputPorcentaje').val() == '')
			return 'Debe ingresar el porcentaje del impuesto';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscarMes').val() != '' && $('#inputBuscarAno').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/alicuotas_retenciones/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return{
			mes: $('#inputMes').val(),
			ano: $('#inputAno').val(),
			detalle: $('.tabladinamica').tablaDinamica('getJson')
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el impuesto "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/abm/alicuotas_retenciones/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		$('.tabladinamica').tablaDinamica('cambiarModo', modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				break;
			case 'editar':
				break;
			case 'agregar':
				$('.tabladinamica').tablaDinamica('addRow');
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divRetenciones' class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 4, 'id' => 'tablaDatos', 'cellSpacing' => 2));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Mes:</label>';
			$cells[0][0]->style->width = '50px';
			$cells[0][1]->content = '<input id="inputMes" class="textbox obligatorio inputForm noEditable w80" rel="mes" validate="EnteroPositivo" maxlength="2" />';
			$cells[0][1]->style->width = '210px';
			$cells[0][2]->content = '<label>Año:</label>';
			$cells[0][2]->style->width = '50px';
			$cells[0][3]->content = '<input id="inputAno" class="textbox obligatorio inputForm noEditable w80" rel="ano" validate="EnteroPositivo" maxlength="4" />';
			$cells[0][3]->style->width = '210px';

			$tabla->create();
		?>
		<div class='customScroll'>
			<table id='tablaDinamica' class='tabladinamica registrosAlternados'></table>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarMes' class='filtroBuscar'>Mes:</label>
			<input id='inputBuscarMes' class='textbox filtroBuscar w180' validate='EnteroPositivo' maxlength='2' />
		</div>
		<div>
			<label for='inputBuscarAno' class='filtroBuscar'>Año:</label>
			<input id='inputBuscarAno' class='textbox filtroBuscar w180' validate='EnteroPositivo' maxlength='4' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/alicuotas_retenciones/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/alicuotas_retenciones/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/alicuotas_retenciones/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
