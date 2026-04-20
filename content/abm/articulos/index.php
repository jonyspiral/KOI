<?php

$articulo = Funciones::get('id');

$producto = Usuario::logueado()->puede('abm/articulos/editar/producto/');
$comercial = Usuario::logueado()->puede('abm/articulos/editar/comercial/');

?>
<style>
	#divProducto {
		padding-top: 10px;
	}
	#divArticulo {
		width: 50%;
	}
	#divColores {
		width: 50%;
	}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Artículos';
		$('#btnAgregarColor').click(agregarColorPopUp);
		$('#tablaColoresComercial th').click(thComercialClick);
		$('.btnQuitarCurva').livequery(function() {
			$(this).click(quitarCurvaModal);
		});
		$('#btnAgregarCurva').livequery(function() {
			$(this).click(function(){
				var curva = {
					id: $('#inputAgregarCurva_selectedValue').val()
				};
				funciones.get(funciones.controllerUrl('getInfoCurva'), {idCurva: $('#inputAgregarCurva_selectedValue').val()}, function(json) {
					curva.nombre = json.data.nombre;
					curva.cantidad = json.data.cantidad;
					agregarCurvaModal($('#tablaCurvas tbody'), curva);
				});
			});
		});
		cambiarModo('inicio');
		$('.solapas').solapas({fixedHeight: 430, heightSolapas: 28, selectedItem: 0});
		<?php echo ($articulo ? 'buscar("' . $articulo . '");' : ''); ?>
	});

	function limpiarScreen(){
		$('#tablaColores tbody').html('');
		$('#tablaColoresComercial tbody').html('');
		$('#liProducto').click();
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = funciones.controllerUrl('buscar', {
			id: $('#inputBuscar_selectedValue').val()
		});
		var msgError = 'El artículo "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				<?php if ($producto) { ?>
				fillProducto(json);
				<?php } ?>
				<?php if ($comercial) { ?>
				fillComercial(json);
				<?php } ?>
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function fillProducto(json) {
		$('#tablaDatos').loadJSON(json);
		var bodyColores = $('#tablaColores').find('tbody').eq(0);
		for (var i = 0; i < json.colores.length; i++) {
			var color = json.colores[i];
			agregarColorTr(bodyColores, color);
		}
	}

	function agregarColorPopUp() {
		var div = $('<div class="h100 vaMiddle table-cell aLeft p10">').append(
			$('<table>').append(
				$('<tbody>').append(
					$('<tr><td><label for="inputColor">Color: </label></td><td><input id="inputColor" type="text" class="textbox autoSuggestBox obligatorio w190" name="Color" /></td></tr>')
				)
			)
		);
		var botones = [{value: 'Guardar', action: function() {agregarColor();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
	}

	function agregarColorTr(body, color) {
		body.append(
			$('<tr>').addClass('tableRow trColor').attr('id', 'tr_' + color.id).data('color', color).append(
				$('<td>').addClass('aCenter').text(color.id),
				$('<td>').text(color.nombre),
				$('<td>').addClass('aCenter').append(
					$('<a>').addClass('boton').attr('href', '#').attr('title', 'Quitar color')
						.click($.proxy(function() {quitarColor(this);}, color))
						.append($('<img>').attr('src', '/img/botones/25/cancelar.gif'))
				)
			)
		);
	}

	function quitarColor(color) {
		$('#tr_' + color.id).remove()
	}

	function agregarColor() {
		var color = {
			id: $('#inputColor_selectedValue').val(),
			nombre: $('#inputColor_selectedName').val()
		};
		agregarColorTr($('#tablaColores').find('tbody').eq(0), color);
		$.jPopUp.close(function(){
			$('#tr_' + color.id).shine();
		});
	}

	/* **** COMERCIAL **** */

	function fillComercial(json) {
		$('#tablaDatos2').loadJSON(json);
		var bodyColores = $('#tablaColoresComercial').find('tbody').eq(0);
		for (var i = 0; i < json.colores.length; i++) {
			var color = json.colores[i];
			color.articulo = json;
			agregarColorTrComercial(bodyColores, color);
		}
		funciones.delay($.proxy(function() {fillTablaComercial(this);}, json));
	}

	function agregarColorTrComercial(body, color) {
		body.append(
			$('<tr>').addClass('tableRow trColorComercial aCenter').attr('id', 'tr_comercial_' + color.id).data('color', color).append(
				$('<td>').addClass('aLeft').text(color.nombre),
				$('<td>').append($('<input>').addClass('w85p textbox inputForm autoSuggestBox categoriaCalzadoUsuario aLeft').attr('id', 'inputCategoriaCalzadoUsuario_' + color.id).attr('name', 'CategoriaCalzadoUsuario').disable()),
				$('<td>').append($('<input>').addClass('w85p textbox inputForm autoSuggestBox tipoProductoStock').attr('id', 'inputTipoProductoStock_' + color.id).attr('name', 'TipoProductoStock').disable()),
				$('<td>').append(
					$('<select>').addClass('w85p textbox inputForm formaDeComercializacion').attr('id', 'inputFormaDeComercializacion_' + color.id).append(
						$('<option>').val('A').text('Agotado'),
						$('<option>').val('M').text('Modular'),
						$('<option>').val('T').text('Limit. stock'),
						$('<option>').val('L').text('Libre')
					).disable()
				),
				$('<td>').append($('<input>').addClass('w80p textbox inputForm precioMayoristaDolar aRight').attr('id', 'inputPrecioMayoristaDolar_' + color.id).attr('validate', 'DecimalPositivo').disable()),
				$('<td>').append($('<input>').addClass('w80p textbox inputForm precioDistribuidor aRight').attr('id', 'inputPrecioDistribuidor_' + color.id).attr('validate', 'DecimalPositivo').disable()),
				$('<td>').append($('<input>').addClass('w80p textbox inputForm precioMinoristaDolar aRight').attr('id', 'inputPrecioMinoristaDolar_' + color.id).attr('validate', 'DecimalPositivo').disable()),
				$('<td>').append($('<input>').addClass('w80p textbox inputForm precioDistribuidorMinorista aRight').attr('id', 'inputPrecioDistribuidorMinorista_' + color.id).attr('validate', 'DecimalPositivo').disable()),
				$('<td>').addClass('aCenter').append(
					$('<a>').addClass('boton curvas').attr('href', '#').attr('title', 'Editar curvas').attr('id', 'inputCurvas_' + color.id)
						.click($.proxy(function() {editarCurvas(this);}, color))
						.append($('<img>').attr('src', '/img/botones/25/editar.gif'))
						.data('curvas', color.curvas)
				)
			)
		);
	}

	function fillTablaComercial(json) {
		for (var i = 0; i < json.colores.length; i++) {
			var color = json.colores[i];
			if (color.categoriaCalzadoUsuario.id) $('#inputCategoriaCalzadoUsuario_' + color.id).val(color.categoriaCalzadoUsuario.id + ' - ' + color.categoriaCalzadoUsuario.nombre).next().val(color.categoriaCalzadoUsuario.id);
			if (color.tipoProductoStock.id) $('#inputTipoProductoStock_' + color.id).val(color.tipoProductoStock.id + ' - ' + color.tipoProductoStock.nombre).next().val(color.tipoProductoStock.id);
			$('#inputFormaDeComercializacion_' + color.id).val(color.formaDeComercializacion);
			$('#inputPrecioMayoristaDolar_' + color.id).val(color.precioMayoristaDolar);
			$('#inputPrecioDistribuidor_' + color.id).val(color.precioDistribuidor);
			$('#inputPrecioMinoristaDolar_' + color.id).val(color.precioMinoristaDolar);
			$('#inputPrecioDistribuidorMinorista_' + color.id).val(color.precioDistribuidorMinorista);
		}
	}

	function thComercialClick(item) {
		var div = '<div id="modalComercial" class="h100 vaMiddle table-cell aLeft p10"><table><tbody>';
		var asb = false;
		switch (item.target.id) {
			case 'thCategoriaCalzadoUsuario':
				div += '<tr><td class="w200"><label for="thCategoriaCalzadoUsuarioTodos">Categoria calzado usuario:</label></td><td><input id="thCategoriaCalzadoUsuarioTodos" class="textbox obligatorio autoSuggestBox w190" name="CategoriaCalzadoUsuario" /></td></tr>';
				asb = true;
				break;
			case 'thTipoProductoStock':
				div += '<tr><td class="w200"><label for="thTipoProductoStockTodos">Tipo producto stock:</label></td><td><input id="thTipoProductoStockTodos" class="textbox obligatorio autoSuggestBox w190" name="TipoProductoStock" /></td></tr>';
				asb = true;
				break;
			case 'thFormaDeComercializacion':
				div += '<tr><td class="w200"><label for="thFormaDeComercializacionTodos">Forma de comercialización:</label></td><td>' +
					   '<select id="thFormaDeComercializacionTodos" class="textbox obligatorio w190">' +
					   '	<option value="A">Agotado</option>' +
					   '	<option value="M">Modular</option>' +
					   '	<option value="T">Limit. stock</option>' +
					   '	<option value="L">Libre</option>' +
					   '</select>' +
					   '</td></tr>';
				break;
			case 'thPrecioMayoristaDolar':
				div += '<tr><td class="w200"><label for="thPrecioMayoristaDolarTodos">Precio de lista:</label></td><td><input id="thPrecioMayoristaDolarTodos" class="textbox obligatorio w100 aRight" validate="DecimalPositivo" /></td></tr>';
				break;
			case 'thPrecioDistribuidor':
				div += '<tr><td class="w200"><label for="thPrecioDistribuidorTodos">Precio distribuidor:</label></td><td><input id="thPrecioDistribuidorTodos" class="textbox obligatorio w100 aRight" validate="DecimalPositivo" /></td></tr>';
				break;
			case 'thPrecioMinoristaDolar':
				div += '<tr><td class="w200"><label for="thPrecioMinoristaDolarTodos">Precio al público de lista:</label></td><td><input id="thPrecioMinoristaDolarTodos" class="textbox obligatorio w100 aRight" validate="DecimalPositivo" /></td></tr>';
				break;
			case 'thPrecioDistribuidorMinorista':
				div += '<tr><td class="w200"><label for="thPrecioDistribuidorMinoristaTodos">Precio al público distribuidor:</label></td><td><input id="thPrecioDistribuidorMinoristaTodos" class="textbox obligatorio w100 aRight" validate="DecimalPositivo" /></td></tr>';
				break;
			default:
				return false;
		}
		div += '</tbody></table></div>';
		var botones = [{value: 'Modificar todos', action: $.proxy(function() {
			var inputs = $('.' + $(this.target).data('modClass'));
			if (asb) {
				var id = $('#' + this.target.id + 'Todos_selectedValue').val();
				var name = $('#' + this.target.id + 'Todos_selectedName').val();
				inputs.val(id + ' - ' + name).next().val(id).next(name);
			} else {
				inputs.val($('#' + this.target.id + 'Todos').val());
			}
			$.jPopUp.close();
		}, item)}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
		$('#modalComercial input:first').focus();
	}

	function editarCurvas(color) {
		var div1 = $('<div>').append(
			$('<table id="tablaCurvas" class="registrosAlternados w100p p10">').append(
				$('<thead class="tableHeader">').append($('<tr>'))
			)
		);
		div1.find('tr').append($('<th class="w25p">').text('Curva'));
		for (var j = 1; j <= 8; j++) {
			div1.find('tr').append($('<th class="w8p">').text(color.articulo.rangoTalle.posicion[j] ? color.articulo.rangoTalle.posicion[j] : '-'))
		}
		div1.find('tr').append($('<th class="w11p">').text('Quitar'));
		div1.find('table').append($('<tbody>'));

		var div2 = $('<div>').append(
			$('<table class="p10">').append(
				$('<tbody>').append(
					$('<tr>').append(
						$('<td class="w200">').append($('<label for="inputAgregarCurva">').text('Agregar curva:')),
						$('<td class="w200">').append($('<input id="inputAgregarCurva" class="textbox obligatorio autoSuggestBox w190" name="Curva" />')),
						$('<td class="w200">').append($('<a id="btnAgregarCurva" class="boton" href="#" title="Agregar curva">').append($('<img src="/img/botones/25/agregar.gif" />')))
					)
				)
			)
		);
		var botones = [{value: 'Guardar', action: $.proxy(function() {
			var curvas = [];
			$('.trCurva').each(function() {
				curvas.push($(this).data('curva'));
			});
			$('#inputCurvas_' + this.id).data('curvas', curvas);
			$.jPopUp.close();
		}, color)}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];

		$.jPopUp.show(div1.html() + div2.html(), botones);

		var tbody = $('#tablaCurvas').find('tbody:first');
		var curvas = $('#inputCurvas_' + color.id).data('curvas');
		for (var i = 0; i < curvas.length; i++) {
			var curva = curvas[i];
			agregarCurvaModal(tbody, curva);
		}

		$('#tablaCurvas input:first').focus();
	}

	function agregarCurvaModal(tbody, curva) {
		var tr = $('<tr id="trCurva_' + curva.id + '" class="trCurva aCenter">');
		tr.append($('<td class="aLeft">').append($('<label>').text(curva.nombre)));
		for (var k = 1; k <= 8; k++) {
			tr.append($('<td>').append($('<label>').text(curva.cantidad[k] ? curva.cantidad[k] : '-')));
		}
		tr.append($('<td>').append($('<a class="boton btnQuitarCurva" href="#" title="Quitar curva">').append($('<img src="/img/botones/25/menos.gif">'))));

		tbody.append(tr);
		$('#trCurva_' + curva.id).data('curva', curva);
	}

	function quitarCurvaModal(e) {
		var curva = $(e.target).parents('tr:first').data('curva');
		$('#trCurva_' + curva.id).remove();
	}

	/* ******************* */

	function hayErrorGuardar() {
		if ($('#inputNombre').val() == '') {
			return 'Debe ingresar un nombre para el artículo';
		}
		return false;
	}

	function guardar(){
		var solapa = $('.contenidoSolapa.selected').first().data('solapa');
		var url = funciones.controllerUrl($('#inputBuscar_selectedValue').val() != '' ? solapa : 'agregar');
		funciones.guardar(url, armoObjetoGuardar(solapa));
	}

	function armoObjetoGuardar(solapa){
		var obj = {
			id : $('#inputBuscar_selectedValue').val()
		};
		switch (solapa) {
			case 'producto':
				obj = $.extend(obj, {
					nombre : $('#inputNombre').val(),
					naturaleza : $('#inputNaturaleza').val(),
					idProveedor : $('#inputProveedor_selectedValue').val(),
					idMarca : $('#inputMarca_selectedValue').val(),
					idLineaProducto : $('#inputLineaProducto_selectedValue').val(),
					idTemporada : $('#inputTemporada_selectedValue').val(),
					idCliente : $('#inputCliente_selectedValue').val(),
					idRangoTalle : $('#inputRangoTalle_selectedValue').val(),
					idRutaProduccion : $('#inputRutaProduccion_selectedValue').val(),
					origen : $('#inputOrigen').val(),
					idHorma : $('#inputHorma_selectedValue').val(),
					colores : armarColores(solapa)
				});
				break;
			case 'comercial':
				obj = $.extend(obj, {
					fechaDeLanzamiento: $('#inputFechaDeLanzamiento').val(),
					colores : armarColores(solapa)
				});
				break;
		}
		return obj;
	}

	function armarColores(solapa) {
		var colores = [];
		switch (solapa) {
			case 'producto':
				$('.trColor').each(function() {
					colores.push($(this).data('color').id);
				});
				break;
			case 'comercial':
				colores = {};
				$('.trColorComercial').each(function() {
					var idColor = $(this).data('color').id;
					colores[idColor] = {
						idCategoriaCalzadoUsuario: $('#inputCategoriaCalzadoUsuario_' + idColor + '_selectedValue').val(),
						idTipoProductoStock: $('#inputTipoProductoStock_' + idColor + '_selectedValue').val(),
						formaDeComercializacion: $('#inputFormaDeComercializacion_' + idColor).val(),
						precioMayoristaDolar: $('#inputPrecioMayoristaDolar_' + idColor).val(),
						precioDistribuidor: $('#inputPrecioDistribuidor_' + idColor).val(),
						precioMinoristaDolar: $('#inputPrecioMinoristaDolar_' + idColor).val(),
						precioDistribuidorMinorista: $('#inputPrecioDistribuidorMinorista_' + idColor).val(),
						curvas: $('#inputCurvas_' + idColor).data('curvas')
					};
				});
				break;
		}
		return colores;
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el artículo"' + $('#inputBuscar_selectedName').val() + '"?',
			url = funciones.controllerUrl('borrar');
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {
			id: $('#inputBuscar_selectedValue').val()
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				break;
			case 'editar':
				$('#inputNombre').focus();
				break;
			case 'agregar':
				$('#inputNombre').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div class="solapas pantalla">
		<ul>
			<?php if ($producto) { ?>
			<li id="liProducto">PRODUCTO</li>
			<?php } ?>
			<?php if ($comercial) { ?>
			<li>COMERCIAL</li>
			<?php } ?>
		</ul>
		<div>
			<?php if ($producto) { ?>
			<div id="divProducto" data-solapa="producto">
				<div id='divArticulo' class='fLeft'>
					<?php
					$tabla = new HtmlTable(array('cantRows' => 11, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
					$tabla->getRowCellArray($rows, $cells);

					$cells[0][0]->content = '<label>Nombre:</label>';
					$cells[0][0]->style->width = '150px';
					$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w230" rel="nombre" />';
					$cells[0][1]->style->width = '250px';
					$cells[1][0]->content = '<label>Naturaleza:</label>';
					$cells[1][1]->content = '<select id="inputNaturaleza" class="textbox obligatorio inputForm w230" rel="naturaleza" >
												<option value="PT">Producto terminado</option>
												<option value="SE">Semi-elaborado</option>
											</select>';
					$cells[2][0]->content = '<label>Proveedor:</label>';
					$cells[2][1]->content = '<input id="inputProveedor" class="textbox autoSuggestBox inputForm w230" name="Proveedor" rel="proveedor" />';
					$cells[3][0]->content = '<label>Marca:</label>';
					$cells[3][1]->content = '<input id="inputMarca" class="textbox autoSuggestBox inputForm w230" name="Marca" rel="marca" />';
					$cells[4][0]->content = '<label>Linea producto:</label>';
					$cells[4][1]->content = '<input id="inputLineaProducto" class="textbox autoSuggestBox inputForm w230" name="LineaProducto" rel="lineaProducto" />';
					$cells[5][0]->content = '<label>Temporada:</label>';
					$cells[5][1]->content = '<input id="inputTemporada" class="textbox autoSuggestBox inputForm w230" name="Temporada" rel="temporada" />';
					$cells[6][0]->content = '<label>Cliente:</label>';
					$cells[6][1]->content = '<input id="inputCliente" class="textbox autoSuggestBox inputForm w230" name="Cliente" rel="cliente" />';
					$cells[7][0]->content = '<label>Rango de talle:</label>';
					$cells[7][1]->content = '<input id="inputRangoTalle" class="textbox autoSuggestBox inputForm w230" name="RangoTalle" rel="rangoTalle" />';
					$cells[8][0]->content = '<label>Ruta de producción:</label>';
					$cells[8][1]->content = '<input id="inputRutaProduccion" class="textbox autoSuggestBox inputForm w230" name="RutaProduccion" rel="rutaProduccion" />';
					$cells[9][0]->content = '<label>Origen:</label>';
					$cells[9][1]->content = '<select id="inputOrigen" class="textbox obligatorio inputForm w230" rel="origen" >
												<option value="N">Nacional</option>
												<option value="I">Importado</option>
											</select>';
					$cells[10][0]->content = '<label>Horma:</label>';
					$cells[10][1]->content = '<input id="inputHorma" class="textbox autoSuggestBox inputForm w230" name="Horma" rel="horma" />';

					$tabla->create();
					?>
				</div>
				<div id='divColores' class='fRight'>
					<div class="fLeft">
						<h3>Colores</h3>
					</div>
					<div class="fRight">
						<a href="#" id="btnAgregarColor" class="boton" title="Agregar color"><img src="/img/botones/25/agregar.gif" /></a>
					</div>
					<table id='tablaColores' class='registrosAlternados w100p'>
						<thead class='tableHeader'>
							<tr>
								<th class="w25p">ID</th>
								<th class="w60p">Color</th>
								<th class="w15p">Quitar</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<?php } ?>
			<?php if ($comercial) { ?>
			<div id="divComercial" data-solapa="comercial">
				<div id='divArticuloComercial'>
					<?php
					$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 4, 'id' => 'tablaDatos2', 'cellSpacing' => 10));
					$tabla->getRowCellArray($rows, $cells);

					$cells[0][0]->content = '<label>Fecha de lanzamiento:</label>';
					$cells[0][0]->style->width = '150px';
					$cells[0][1]->content = '<input id="inputFechaDeLanzamiento" class="textbox inputForm w180" rel="fechaDeLanzamiento" validate="Fecha" />';
					$cells[0][1]->style->width = '250px';

					$tabla->create();
					?>
				</div>
				<div id='divColoresComercial'>
					<table id='tablaColoresComercial' class='registrosAlternados w100p'>
						<thead class='tableHeader cPointer'>
							<tr>
								<th class="w15p underlineHover">Color</th>
								<th id="thCategoriaCalzadoUsuario" data-mod-class="categoriaCalzadoUsuario" class="w15p underlineHover" title="Categoría calzado usuario">Categoría</th>
								<th id="thTipoProductoStock" data-mod-class="tipoProductoStock" class="w15p underlineHover" title="Tipo de producto en stock">Tipo prod.</th>
								<th id="thFormaDeComercializacion" data-mod-class="formaDeComercializacion" class="w15p underlineHover" title="Forma de comercialización">F. Comer.</th>
								<th id="thPrecioMayoristaDolar" data-mod-class="precioMayoristaDolar" class="w8p underlineHover" title="Precio de lista">P. Lista</th>
								<th id="thPrecioDistribuidor" data-mod-class="precioDistribuidor" class="w8p underlineHover" title="Precio distribuidor">P. Distr</th>
								<th id="thPrecioMinoristaDolar" data-mod-class="precioMinoristaDolar" class="w8p underlineHover" title="Precio al público lista">P.P.Lista</th>
								<th id="thPrecioDistribuidorMinorista" data-mod-class="precioDistribuidorMinorista" class="w8p underlineHover" title="Precio al público distribuidor">P.P.Distr</th>
								<th class="w8p underlineHover">Curvas</th>
							</tr>
						</thead>
						<tbody class="aCenter"></tbody>
					</table>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label class='filtroBuscar'>Artículo:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='ArticuloTodos'  alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/articulos/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/articulos/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/articulos/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
