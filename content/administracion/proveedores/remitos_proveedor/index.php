<?php
?>

<style>
</style>

<script type='text/javascript'>
	var detalle = {};
	$(document).ready(function(){
		tituloPrograma = 'Remitos proveedor';
		$('#inputBuscarRemito').attr('alt', '&esHexagono=N');
		$('#btnMiniBuscarProveedor').click(
			function() {
				if (!$(this).attr('disabled')) {
					if($(this).next().val() != '') {
						if (buscarOrdenesCompra(true)) {
							$('.tabladinamica').tablaDinamica('cambiarModo', 'agregar');
							$(this).disable();
						}
					}
				}
			});
		$('#btnMiniCancelarProveedor').click(function(){
			$('#tablaOrdenesCompra > tbody').html('');
			cambiarModo('agregar');
			$('.tabladinamica').tablaDinamica('cambiarModo', 'buscar');
			$('#inputProveedor').enable();
			$('#btnMiniBuscarProveedor').enable();
		});
		$('.tabladinamica ').tablaDinamica(
			{
				width: '100%',
				height: 'auto',
				caption: 'Detalle (Ingresar las unidades de medida del proveedor)',
				scrollbar: false,
				addButtonInHeader: true,
				buttons: ['Q'],
				addCallback: function(){
					attrAutoSuggestProveedor();
				},
				columnsConfig: [
					{
						id: 'idOrdenDeCompra',
						name: 'Cod OC',
						width: '128px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox noEditable autoSuggestOrdenDeCompra w120" name="OrdenDeCompra" />'
					},
					{
						id: 'idMaterial',
						name: 'Material',
						width: '279px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox noEditable autoSuggestMaterial w270" name="Material" />'
					},
					{
						id: 'idColor',
						name: 'Color',
						width: '110px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox noEditable w100" name="ColorMateriaPrima" />',
						focus: function(){
							var rowId = this.parent().find('[data-colid="idMaterial"]').data('rowid'),
								idMaterial = $(this.parent().find('[data-colid="idMaterial"]').children()[1]).val();

							$('#' + rowId + '_' + 'idColor').val('');
							$('#tablaDinamica-' + rowId + '_' + 'idColor').attr('alt', 'idMaterial=' + idMaterial);
						}
					},
					{
						id: 'cantidad',
						name: 'Cant.',
						width: '68px',
						css: {textAlign: 'center'},
						cellType: 'I',
						template: '<input class="textbox obligatorio w40" type="text" validate="DecimalPositivo" />',
						focus: function(){
							var rowCantidades = this.tablaDinamica('getSibling', 'cantidades').tableCell,
								idMaterial = this.tablaDinamica('getSibling', 'idMaterial').getValue(),
								idColor = this.tablaDinamica('getSibling', 'idColor').getValue();

							if(rowCantidades.data('idMaterial') != idMaterial){
								rowCantidades.html('');

								if(idMaterial != '' && idColor != ''){
									$.postJSON('/content/administracion/proveedores/remitos_proveedor/getTallesMaterial.php?idMaterial=' + idMaterial, function(json){
										if(json.data.usaRango == 'S'){
											rowCantidades.data('usaRango', json.data.usaRango);

											var rango = json.data.rango,
												table = $('<table>').addClass('w100p'),
												thead = $('<thead>'),
												tbody = $('<tbody>'),
												trh = $('<tr>').addClass('bDarkGray aCenter bold bRightWhite white'),
												trb = $('<tr>');

											for(var i = 1; rango[i] != null && rango[i] != ''; i++){
												trh.append($('<th>').text(rango[i]));
												trb.append($('<td>').append('<input class="textbox w25" type="text" validate="EnteroPositivo" />'));
											}
											table.append(thead.append(trh), tbody.append(trb));
											rowCantidades.append($('<div>').append(table));
										}else {
											rowCantidades.text('-');
										}
									});
									rowCantidades.data('idMaterial', idMaterial);
									this.tablaDinamica('getSibling', 'idOrdenDeCompra').valueElement.disable();
									this.tablaDinamica('getSibling', 'idMaterial').valueElement.disable();
									this.tablaDinamica('getSibling', 'idColor').valueElement.disable();
								}
							}
						},
						blur: function() {

						}
					},
					{
						id: 'cantidades',
						name: 'Cantidades',
						width: '314px',
						css: {textAlign: 'center'},
						cellType: 'G',
						getJson: function(o){
							var i = 1,
								cantidades = {},
								obj = $('.tabladinamica').find('[data-rowid="' + o.rowid + '"] [data-colid="' + o.colid + '"]').find('td');

							if(obj.length == 0){
								cantidades[1] = $('#' + o.rowid + '_' + 'cantidad').val();
							}else{
								obj.each(
									function(k, v){
										cantidades[i++] = $(v).find('input').val();
									});
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

	function attrAutoSuggestProveedor(){
		$('.autoSuggestMaterial').attr('alt','&idProveedor=' + $('#inputProveedor_selectedValue').val());
		$('.autoSuggestOrdenDeCompra').attr('alt','&idProveedor=' + $('#inputProveedor_selectedValue').val());
	}

	function limpiarScreen(){
		$('.tablaDinamica').tablaDinamica('clean');
		$('#tablaOrdenesCompra > tbody').html('');
	}

	function trTablaOrdenCompra(obj) {
		var tr = $('<tr>').addClass('s12'),
			cantidad,
			btn;
		if(obj.talleUnico == 'S'){
			cantidad = obj.cantidad;
		}else {
			var tablaTalles = $('<table>').addClass('w100p'),
				tablaTallesBody = $('<tbody>'),
				trTalles = $('<tr>').addClass('bDarkGray'),
				trCantidades = $('<tr>');
			for(var i = 1; i < 11; i++){
				var tdCantidad = $('<td>').addClass('aCenter').text(obj.cantidades[i].cantidad),
					tdTalles = $('<td>').addClass('aCenter bold bRightWhite white w10p').text(obj.cantidades[i].talle);
				if(i != 10){
					tdCantidad.addClass('bRightDarkGray');
				}
				trTalles.append(tdTalles);
				trCantidades.append(tdCantidad);
			}
			tablaTallesBody.append(trTalles, trCantidades);
			tablaTalles.append(tablaTallesBody);
			cantidad = tablaTalles;
		}

		btn = $('<a>').addClass('boton ocultarAlEditar').attr('href', '#').attr('title', 'Agregar al detalle')
			.click($.proxy(function() {agregarFilaEnRemito(this);}, {ordenDeCompra: obj.objOrdenDeCompra, material: obj.objMaterial, color: obj.objColor, tr: tr}))
			.append($('<img>').attr('src', '/img/botones/25/agregar.gif'));

		return tr.append(
					$('<td>').addClass('aCenter').text(obj.numero),
					$('<td>').addClass('aCenter').text((obj.fechaEntrega == null ? '-' : obj.fechaEntrega)),
					$('<td>').addClass('aLeft').text(obj.material),
					$('<td>').addClass('aCenter').text(obj.color),
					$('<td>').addClass('aCenter').append(cantidad),
					$('<td>').addClass('aCenter').append(btn)
		);
	}

	function agregarFilaEnRemito(obj) {
		var i = $('.tabladinamica').tablaDinamica('addRow');

		//Esto se hizo así porque el addRow de tablaDinamica no hace el fill correctamente cuando se trata de fields con ASB
		setTimeout(function(){
			$('#tablaDinamica-' + i + '_idOrdenDeCompra').val(obj.ordenDeCompra.id).autoComplete();
			$('#tablaDinamica-' + i + '_idMaterial, #tablaDinamica-' + i + '_idMaterial_selectedValue').val(obj.material.id).autoComplete();
            $('#tablaDinamica-' + i + '_idColor')
                .attr('linkedTo', 'tablaDinamica-' + i + '_idMaterial,Material')
                .addClass('autoSuggestBox_forceInit')
                .autoSuggestBox()
                .removeClass('autoSuggestBox_forceInit')
                .val(obj.color.id)
                .autoComplete();
			$('#tablaDinamica-' + i + '_cantidad').focus();
		}, 250);
		$('.indicador-gris').removeClass('indicador-gris');
		obj.tr.addClass('indicador-gris');
	}

    function buscar() {
		if($('#inputBuscarRemito_selectedValue').val() != ''){
			funciones.limpiarScreen();
			var url = funciones.controllerUrl('buscar', {idRemito: $('#inputBuscarRemito_selectedValue').val()}),
				msgError = 'El remito "' + $('#inputBuscarPresupuesto_selectedName').val() + '" no existe.',
				cbSuccess = function(json){
					buscarOrdenesCompra(false);
					$('#inputProveedor').val(json.idProveedor).autoComplete();
					$('#inputPuntoVenta').val(json.sucursal);
					$('#inputNumero').val(json.numero);

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

							for(var i = 1; cantidades[i].talle != null && cantidades[i].talle != ''; i++){
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
							idOrdenDeCompra: value.idOrdenDeCompra,
							idMaterial: value.idMaterial,
							idColor: value.idColor,
							cantidad: value.cantidad,
							cantidades: rowCantidades.html()
						};
					});

					$('.tabladinamica').tablaDinamica('load', jsonTablaDinamica);
					j = 0;
					$.each(jsonTablaDinamica, function(key, value) {
						$('.tabladinamica').tablaDinamica('getObj', j, 'cantidades').tableCell.data('idMaterial', value.idMaterial);
						j++;
					});
				};
			funciones.buscar(url, cbSuccess, msgError);
		}
	}

	function buscarOrdenesCompra(esAgregar) {
		if(esAgregar && $('#inputProveedor_selectedValue').val() == ''){
			$.error('Debe seleccionar un proveedor');
			return false;
		}else{
			$.showLoading();
			$.postJSON('/content/administracion/proveedores/remitos_proveedor/getOrdenesCompra.php?idProveedor=' + (esAgregar ? $('#inputProveedor_selectedValue').val() : $('#inputBuscarProveedor_selectedValue').val()), function(json){
				$.hideLoading();
				switch (funciones.getJSONType(json)) {
					case funciones.jsonInfo:
						if(esAgregar){
							$.info(funciones.getJSONMsg(json));
						}
						break;
					case funciones.jsonError:
						$.error(funciones.getJSONMsg(json));
						break;
					case funciones.jsonObject:
						$.each(json.data, function(key, value) {
							$('#tablaOrdenesCompra > tbody').addClass('aCenter').append(trTablaOrdenCompra(value));
						});
						$('#inputProveedor').disable();
						//esAgregar && $('.tabladinamica').tablaDinamica('addRow');
						break;
					default:
						$('#inputBuscar').limpiarAutoSuggestBox();
						$.error('Ocurrió un error al intentar buscar las ordenes de compra pendientes');
						break;
				}
			});
			return true;
		}
	}

	function guardar(){
		var aux = ($('#inputBuscarRemito_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/administracion/proveedores/remitos_proveedor/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar() {
		return {
			idRemito: $('#inputBuscarRemito_selectedValue').val(),
			idProveedor: $('#inputProveedor_selectedValue').val(),
			fecha: $('#inputFecha').val(),
			puntoVenta: $('#inputPuntoVenta').val(),
			numero: $('#inputNumero').val(),
			detalle: $('.tabladinamica').tablaDinamica('getJson')
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el remito"' + $('#inputBuscarRemito_selectedName').val() + '"?',
			url = '/content/administracion/proveedores/remitos_proveedor/borrar.php';
		funciones.borrar(msg, url, {idRemito: $('#inputBuscarRemito_selectedValue').val()});
	}

	function hayErrorGuardar() {
		if($('#inputProveedor_selectedValue').val() == '')
			return 'Debe seleccionar un proveedor';

		if($('#inputNumero').val() == '' || $('#inputPuntoVenta').val() == '')
			return 'Debe ingresar el numero de remito';

		var detalles = $('.tabladinamica').tablaDinamica('getJson');

		if(detalles.length == 0)
			return 'El remito debe tener al menos un detalle';

		for(var i = 0; i < detalles.length; i++){
			if(detalles[i].idMaterial == '' || detalles[i].idColor == '' || detalles[i].cantidad == '' || detalles[i].idOrdenDeCompra == ''){
				return 'Debe completar todos los campos obligatorios de los detalles';
			}
		}

		return false;
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
				$('#btnMiniCancelarProveedor').hide();
				funciones.cambiarTitulo(tituloPrograma + ' - ' + $('#inputBuscarProveedor_selectedName').val());
				$('.ocultarAlEditar').hide();
				break;
			case 'editar':
				$('.ocultarAlEditar').show();
				break;
			case 'agregar':
				$('#btnMiniCancelarProveedor').show();
				$('#inputProveedor').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divDatos'>
		<div id='divDatos' class='fLeft pantalla'>
			<?php
			$tabla = new HtmlTable(array('cantRows' => 2, 'cantCols' => 4, 'id' => 'tablaDatos', 'cellSpacing' => 4));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Proveedor:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputProveedor" class="textbox obligatorio autoSuggestBox inputForm noEditable w260" name="Proveedor" rel="proveedor" />';
			$cells[0][1]->style->width = '210px';
			$cells[0][2]->content = '<a id="btnMiniBuscarProveedor" class="boton ocultarAlEditar" href="#" title="Buscar"><img src="/img/botones/25/buscar.gif" /></a>';
			$cells[0][3]->content = '<a id="btnMiniCancelarProveedor" class="boton ocultarAlEditar" href="#" title="Cancelar"><img src="/img/botones/25/cancelar.gif" /></a>';

			$cells[1][0]->content = '<label>Número:</label>';
			$cells[1][1]->content = '<input id="inputPuntoVenta" class="textbox obligatorio inputForm noEditable aRight w35" rel="sucursal" validate="Entero" maxlength="4" />  -
									 <input id="inputNumero" class="textbox obligatorio inputForm noEditable aRight w196" rel="numero" validate="Entero" maxlength="8" />';

			$tabla->create();
			?>
		</div>

		<div id='divPrograma' class='fRight pantalla w100p'>
			<div id='divDetalles' class='well h245'>
				<div class='customScroll'>
					<table id='tablaDinamica' class='tabladinamica registrosAlternados'></table>
				</div>
			</div>

			<div id='divTablaOrdenesCompra' class='well mTop5'>
				<div class='h140 customScroll mTop10'>
					<table id="tablaOrdenesCompra" class="registrosAlternados w100p">
						<caption><span class='bold aLeft'><label>Ordenes de compra pendientes</label></span></caption>
						<thead class="tableHeader">
							<tr class="tableRow">
								<th class="w5p cornerL5">Nro.</th>
								<th class="w10p bLeftWhite">F. entr.</th>
								<th class="w20p bLeftWhite">Material</th>
								<th class="w5p bLeftWhite">Color</th>
								<th class="w50p bLeftWhite aCenter">Cantidad</th>
								<th class="w10p bLeftWhite aCenter">Agregar</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscarProveedor' class='filtroBuscar'>Proveedor:</label>
			<input id='inputBuscarProveedor' class='textbox obligatorio autoSuggestBox filtroBuscar w250' name='Proveedor' />
		</div>
		<div>
			<label for='inputBuscarRemito' class='filtroBuscar'>Remito:</label>
			<input id='inputBuscarRemito' class='textbox obligatorio autoSuggestBox filtroBuscar w250' name='RemitoProveedor' linkedTo='inputBuscarProveedor,Proveedor' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'administracion/proveedores/remitos_proveedor/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'administracion/proveedores/remitos_proveedor/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'administracion/proveedores/remitos_proveedor/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>