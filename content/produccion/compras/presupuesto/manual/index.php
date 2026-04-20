<?php
?>

<style>
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		arrayIva = {};
		tituloPrograma = 'Pedido de cotización';
		$('#inputProveedor').blur(function(){
			funciones.delay('blurProveedor();');
		});
		$('.tabladinamica').tablaDinamica(
			{
				width: '100%',
				height: 'auto',
				caption: 'Detalle',
				scrollbar: false,
				addButtonInHeader: true,
				buttons: ['Q'],
				addCallback: function(){
					blurProveedor();
					$('.tabladinamica').find('.autoSuggestBox').autoComplete();
				},
				columnsConfig: [
					{
						id: 'fechaEntrega',
						name: 'F. entrega',
						width: '108px',
						css: {textAlign: 'center'},
						cellType: 'I',
						template: '<input class="textbox obligatorio w80" type="text" validate="Fecha" />'
					},
					{
						id: 'idMaterial',
						name: 'Material',
						width: '180px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox noEditable autoSuggestMaterial w180" name="Material" />'
					},
					{
						id: 'idColor',
						name: 'Color',
						width: '120px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox noEditable w120" name="ColorMateriaPrima" />',
						focus: function(){
							var idMaterial = this.tablaDinamica('getSibling', 'idMaterial').getValue();
							this.tablaDinamica('getMe').valueElement.val('').attr('alt', 'idMaterial=' + idMaterial);
						}
					},
					{
						id: 'cantidad',
						name: 'Cant.',
						width: '50px',
						css: {textAlign: 'center'},
						cellType: 'I',
						template: '<input class="textbox obligatorio w35" type="text" validate="DecimalPositivo" />',
						focus: function(){
							var rowCantidades = this.tablaDinamica('getSibling', 'cantidades').tableCell,
								rowCantidad = this.tablaDinamica('getSibling', 'cantidad').tableCell,
								idMaterial = this.tablaDinamica('getSibling', 'idMaterial').getValue(),
								idColor = this.tablaDinamica('getSibling', 'idColor').getValue(),
								rowInfo = this.tablaDinamica('getSibling', 'info').tableCell;

							if(rowCantidad.data('idMaterial') != idMaterial){
								if(idMaterial != '' && idColor != ''){
									$.postJSON(funciones.controllerUrl('getTallesMaterial', {idMaterial: idMaterial}), function(json){
										rowInfo.html('').append(
											$('<label>').addClass('s12').text('Min: ' + json.data.cantidadMinima + ' [' + json.data.unidadMedida + ']'),
											$('<br>'),
											$('<label>').addClass('s12').text('x' + json.data.cantidadMultiplo + ' [' + json.data.unidadMedida + ']')
										);
										rowCantidades.html('');

										if(json.data.usaRango == 'S'){
											rowCantidad.data('usaRango', json.data.usaRango);

											var rango = json.data.rango,
												table = $('<table>').addClass('w100p'),
												thead = $('<thead>'),
												tbody = $('<tbody>'),
												trh = $('<tr>').addClass('bDarkGray aCenter bold bRightWhite white'),
												trb1 = $('<tr>');

											for(var i = 1; rango[i] != null && rango[i] != ''; i++){
												trh.append($('<th>').text(rango[i]));
												trb1.append($('<td>').append('<input class="textbox inputForm w25" type="text" validate="EnteroPositivo" />'));
											}
											table.append(thead.append(trh), tbody.append(trb1));
											rowCantidades.html($('<div>').append(table));
										}else {
											rowCantidades.html('-');
										}
									});
									rowCantidad.data('idMaterial', idMaterial);
									this.tablaDinamica('getSibling', 'idMaterial').valueElement.disable();
									this.tablaDinamica('getSibling', 'idColor').valueElement.disable();
								}
							}
						}
					},
					{
						id: 'info',
						name: 'Info.',
						width: '90px',
						css: {textAlign: 'left'},
						cellType: 'G'
					},
					{
						id: 'cantidades',
						name: 'Cantidades',
						width: '466px',
						css: {textAlign: 'center'},
						cellType: 'G',
						getJson: function(o){
							var i = 1,
								cantidades = {},
								obj = $('.tabladinamica').find('[data-rowid="' + o.rowid + '"] [data-colid="' + o.colid + '"]').find('tr');

							if(obj.length == 0){
								var cantidad = $('.tabladinamica').find('[data-rowid="' + o.rowid + '"] [data-colid="cantidad"]').find('input').val();
								cantidades[1] = {cantidad: cantidad};
							}else{
								var objetoCantidad = $(obj[1]).find('td');
								for(var j = 0; objetoCantidad.length > j; j++){
									cantidades[i++] = {cantidad: $(objetoCantidad[j]).find('input').val()}
								}
							}

							return cantidades;
						}
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
		$('#divDetalles').fixedHeader({target: 'table'});
		cambiarModo('inicio');
	});

	function blurProveedor(){
		$('.autoSuggestMaterial').attr('alt','&idProveedor=' + $('#inputProveedor_selectedValue').val());
	}

	function limpiarScreen(){
		//TODO ver como limpio tablas
	}

	function buscar() {
		var condicion = $('#inputBuscarProveedor_selectedValue').val() == '' || $('#inputBuscarPresupuesto_selectedValue').val() == '';

		if(!condicion){
			funciones.limpiarScreen();
			var url = funciones.controllerUrl('buscar', {id: $('#inputBuscarPresupuesto_selectedValue').val()}),
				msgError = 'El remito "' + $('#inputBuscarPresupuesto_selectedName').val() + '" no existe.',
				cbSuccess = function(json){
					$('#inputProveedor').val(json.idProveedor).autoComplete();
					$('#inputLoteDeProduccion').val(json.idLoteDeProduccion).autoComplete();
					$('#inputTipo').val(json.tipo);
					$('#inputObservaciones').val(json.observaciones);

					var jsonTablaDinamica = {},
						j = 0;
					$.each(json.detalle, function(key, value) {
						if(value.usaRango == 'S'){
							var cantidades = value.cantidades,
								table = $('<table>').addClass('w100p'),
								thead = $('<thead>'),
								tbody = $('<tbody>'),
								trh = $('<tr>').addClass('bDarkGray aCenter bold bRightWhite white'),
								trb1 = $('<tr>'),
								rowCantidades;

							for(var i = 1; cantidades[i]; i++){
								trh.append($('<th>').text(cantidades[i].talle));
								trb1.append($('<td>').append('<input class="textbox inputForm w25" type="text" validate="EnteroPositivo" value="' + cantidades[i].cantidad + '" />'));
							}
							table.append(thead.append(trh), tbody.append(trb1));
							rowCantidades = $('<div>').append(table);
						}else {
							rowCantidades = $('<div>').text('-');
						}

						jsonTablaDinamica[j++] =
							{
								fechaEntrega: value.fechaEntrega,
								idMaterial: value.idMaterial,
								idColor: value.idColor,
								cantidad: value.cantidad,
								info: 'Min: ' + value.cantidadMinima + ' [' + value.unidadMedida + ']<br>x' + value.cantidadMultiplo + ' [' + value.unidadMedida + ']',
								cantidades: rowCantidades.html()
							};
					});

					$('.tabladinamica').tablaDinamica('load', jsonTablaDinamica);
					j = 0;
					$.each(jsonTablaDinamica, function(key, value) {
						$('.tabladinamica').tablaDinamica('getObj', j, 'cantidad').tableCell.data('idMaterial', value.idMaterial);
						$('.tabladinamica').tablaDinamica('getObj', j, 'info').valueElement.addClass('s12');
						j++;
					});
				};
			funciones.buscar(url, cbSuccess, msgError);
		}
	}

	function guardar(){
		var aux = ($('#inputBuscarPresupuesto_selectedValue').val() != '' ? 'editar' : 'agregar');
		funciones.guardar(funciones.controllerUrl(aux), {
			id: $('#inputBuscarPresupuesto_selectedValue').val(),
			idProveedor: $('#inputProveedor_selectedValue').val(),
			idLoteDeProduccion: $('#inputLoteDeProduccion_selectedValue').val(),
			tipo: $('#inputTipo').val(),
			observaciones: $('#inputObservaciones').val(),
			detalle: $('.tabladinamica').tablaDinamica('getJson')
		});
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el predido de cotización Nº "' + $('#inputBuscarPresupuesto_selectedValue').val() + '"?';
		funciones.borrar(msg, funciones.controllerUrl('borrar'), {id: $('#inputBuscarPresupuesto_selectedValue').val()});
	}

	function hayErrorGuardar() {
		if($('#inputProveedor_selectedValue').val() == '')
			return 'Debe seleccionar un proveedor';

		if($('#inputTipo').val() == '')
			return 'Debe ingresar el tipo de pedido de cotización';

		var detalles = $('.tabladinamica').tablaDinamica('getJson');

		if(detalles.length == 0)
			return 'El remito debe tener al menos un detalle';

		for(var i = 0; i < detalles.length; i++){
			if(detalles[i].fechaEntrega == '' || detalles[i].idMaterial == '' || detalles[i].idColor == ''
			   || detalles[i].cantidad == '' || detalles[i].precioUnitario == ''){
				return 'Debe completar todos los campos obligatorios de los detalles';
			}
		}

		return false;
	}

	function pdfClick(){
		funciones.pdfClick(funciones.controllerUrl('getPdf', {id: $('#inputBuscarPresupuesto_selectedValue').val()}));
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		$('.tabladinamica').tablaDinamica('cambiarModo', modo);
		switch (modo){
			case 'inicio':
				$('.pantalla').hide();
				$('#inputProveedor').focus();
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma + ' - ' + $('#inputBuscarPresupuesto_selectedValue').val());
				break;
			case 'editar':
				break;
			case 'agregar':
				$('.tabladinamica').tablaDinamica('addRow');
				$('#inputProveedor').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divDatos'>
		<div id='divDatos1' class='fLeft pantalla'>
			<?php
			$tabla = new HtmlTable(array('cantRows' => 3, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 2));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Proveedor:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputProveedor" class="textbox obligatorio autoSuggestBox inputForm noEditable w200" name="Proveedor" rel="proveedor" />';
			$cells[0][1]->style->width = '210px';

			$cells[1][0]->content = '<label>Tipo:</label>';
			$cells[1][1]->content = '<select id="inputTipo" class="textbox obligatorio inputForm no-editable w200" rel="productiva" >';
			$cells[1][1]->content .= '<option value="">---</option>';
			$cells[1][1]->content .= '<option value="S">Productiva</option>';
			$cells[1][1]->content .= '<option value="N">No productiva</option>';
			$cells[1][1]->content .= '</select>';

			$cells[2][0]->content = '<label>Lote:</label>';
			$cells[2][1]->content = '<input id="inputLoteDeProduccion" class="textbox autoSuggestBox inputForm w200" name="LoteDeProduccion" rel="loteDeProduccion" />';

			$tabla->create();
			?>
		</div>

		<div id='divDatos2' class='fLeft pantalla'>
			<?php
			$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 2));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Observaciones:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<textarea id="inputObservaciones" class="textbox inputForm w200" rel="observaciones"></textarea>';
			$cells[0][1]->style->width = '210px';

			$tabla->create();
			?>
		</div>

		<div id='divPrograma' class='fRight pantalla w100p'>
			<div id='divDetalles' class='well h400'>
				<div class='customScroll'>
					<table id='tablaDinamica' class='tabladinamica registrosAlternados'></table>
				</div>
			</div>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscarProveedor' class='filtroBuscar'>Proveedor:</label>
			<input id='inputBuscarProveedor' class='textbox autoSuggestBox obligatorio filtroBuscar w200' name='Proveedor' />
		</div>
		<div>
			<label for='inputBuscarPresupuesto' class='filtroBuscar'>Pedido de cotización:</label>
			<input id='inputBuscarPresupuesto' class='textbox autoSuggestBox obligatorio filtroBuscar w200' name='Presupuesto' linkedTo='inputBuscarProveedor,Proveedor' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'produccion/compras/presupuesto/manual/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'produccion/compras/presupuesto/manual/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'produccion/compras/presupuesto/manual/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>