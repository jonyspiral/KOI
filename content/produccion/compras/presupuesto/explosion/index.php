<?php
?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Pedido de cotización por explosión';
		$('.tabladinamica').tablaDinamica(
			{
				width: '100%',
				height: 'auto',
				//caption: 'Detalle',
				scrollbar: false,
				addButtonInHeader: true,
				columnsConfig: [
					{
						id: 'proveedor',
						name: 'Provee',
						width: 'px',
						css: {textAlign: 'left'},
						cellType: 'L'
					},
					{
						id: 'material',
						name: 'Material',
						width: 'px',
						css: {textAlign: 'left'},
						cellType: 'L'
					},
					{
						id: 'color',
						name: 'Color',
						width: 'px',
						css: {textAlign: 'left'},
						cellType: 'L'
					},
					{
						id: 'precioUnitario',
						name: 'PU',
						title: 'Precio Unitario',
						width: '50px',
						css: {textAlign: 'left'},
						cellType: 'I',
						template: '<input class="textbox obligatorio w40" type="text" validate="DecimalPositivo" />'
					},
					{
						id: 'factorConversion',
						name: 'FC',
						title: 'Factor de Conversión',
						width: '50px',
						css: {textAlign: 'left'},
						cellType: 'L'
					},
					{
						id: 'consumo',
						name: 'Consumo',
						width: 'px',
						css: {textAlign: 'center'},
						cellType: 'L'
					},
					{
						id: 'importe',
						name: 'Importe',
						width: 'px',
						css: {textAlign: 'center'},
						cellType: 'L'
					},
					{
						id: 'stockSpiral',
						name: 'S. Spiral',
						title: 'Stock Spiral',
						width: 'px',
						css: {textAlign: 'center'},
						cellType: 'I',
						template: '<input class="textbox obligatorio w40" type="text" validate="DecimalPositivo" />'
					},
					{
						id: 'ums',
						name: 'UMS',
						title: 'Unidad Medida Spiral',
						width: 'px',
						css: {textAlign: 'left'},
						cellType: 'L'
					},
					{
						id: 'necesidad',
						name: 'Nec.',
						title: 'Necesidad',
						width: 'px',
						css: {textAlign: 'center'},
						cellType: 'I',
						template: '<input class="textbox obligatorio w40 disabled" type="text" validate="DecimalPositivo" />'
					},
					{
						id: 'cantidadComprar',
						name: 'Comprar',
						width: 'px',
						css: {textAlign: 'center'},
						cellType: 'I',
						template: '<input class="textbox obligatorio w40 disabled" type="text" validate="DecimalPositivo" />'
					},
					{
						id: 'cantidadComprar',
						name: 'Comprar',
						width: 'px',
						css: {textAlign: 'center'},
						cellType: 'G'
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

	function limpiarScreen() {
	}

	function buscar() {
		funciones.buscar(funciones.controllerUrl('buscar'), function(json){
			$.each(json.detalle, function(key, value) {
				if(json.usaRango == 'S'){
					that.data('usaRango', json.usaRango);

					var rango = json.data.rango,
						table = $('<table>').addClass('w100p'),
						thead = $('<thead>'),
						tbody = $('<tbody>'),
						trh = $('<tr>').addClass('bDarkGray aCenter bold bRightWhite white'),
						trb1 = $('<tr>');

					for(var i = 1; rango[i] != null && rango[i] != ''; i++){
						trh.append($('<th>').text(rango[i]));
						trb1.append($('<td>').append('<input class="textbox w25" type="text" validate="EnteroPositivo" />'));
					}
					table.append(thead.append(trh), tbody.append(trb1));
					rowCantidades.append($('<div>').append(table));
				}else {
					rowCantidades.text('-');
				}
			});
		});
	}

	function hayErrorGuardar(){
		detalle = [];

		$('#tablaPresupuestos > tbody').find('[type="checkbox"]').each(function(i, item) {
			if ($(item).isChecked()) {
				detalle.push($(item).data('data'));
			}
		});

		if(detalle.length == 0){
			return 'Debe seleccionar al menos un detalle de presupuesto para generar la órden de compra';
		}

		return false;
	}

	function guardar(){
		funciones.guardar(funciones.controllerUrl('agregar'),{
				detalle: detalle,
				idProveedor: idProveedor,
				observaciones: $('#inputObservaciones').val()
		});
	}

	function guardarClick(){
		var div = '<div class="h100 vaMiddle table-cell aLeft p10">' +
				  '<table><tbody>' +
				  '<tr><td><label for="inputObservaciones" class="filtroBuscar">Observaciones:</label></td><td><textarea id="inputObservaciones" class="textbox w190" /></td></tr>' +
				  '</tbody></table>' +
				  '</div>';
		var botones = [{value: 'Guardar', action: function() {funciones.guardarClick();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
	}

	function cancelarBuscarClick(){
		$('#divGeneracionOrdenesDeCompra').html('');
		funciones.cancelarBuscarClick()
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		$('#radioGroupAlmacen').enableRadioGroup();
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				break;
			case 'editar':
				break;
			case 'agregar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divGeneracionOrdenesDeCompra' class='w100p customScroll acordeon h480'>
		<table id='tablaDinamica' class='tabladinamica registrosAlternados'></table>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarProveedor' class='filtroBuscar'>Proveedor:</label>
			<input id='inputBuscarProveedor' class='textbox obligatorio autoSuggestBox filtroBuscar w220' name='Proveedor' />
		</div>
		<div>
			<label for='inputFechaDesde' class='filtroBuscar'>Rango fecha:</label>
			<input id='inputFechaDesde' class='textbox filtroBuscar w80' to='inputFechaHasta' validate='Fecha' />
			<input id='inputFechaHasta' class='textbox filtroBuscar w80' from='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarTipo' class='filtroBuscar'>Tipo:</label>
			<select id='inputBuscarTipo' class='textbox obligatorio filtroBuscar w220'>
				<option value='S'>Productiva</option>
				<option value='N'>No productiva</option>
			</select>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();', 'permiso' => 'produccion/compras/ordenes_compra/generacion/buscar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'guardarClick();', 'permiso' => 'produccion/compras/ordenes_compra/generacion/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
