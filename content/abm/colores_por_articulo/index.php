<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Colores por artículo';
		cambiarModo('inicio');
	});

	function buscar() {
		funciones.limpiarScreen();
		if ($('#inputBuscarArticulo_selectedValue').val() == '' || $('#inputBuscarColor_selectedValue').val() == '')
			return $('#inputBuscarArticulo, #inputBuscarColor').val('');
		var url = funciones.controllerUrl('buscar', {
			idArticulo: $('#inputBuscarArticulo_selectedValue').val(),
			id: $('#inputBuscarColor_selectedValue').val()
		}),
		msgError = 'El color por artículo "' + $('#inputBuscarArticulo_selectedName').val() + ' ' + $('#inputBuscarColor_selectedName').val() + '" no existe.',
		cbSuccess = function(json){
			$('#tablaDatos').loadJSON(json);
		};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscarColor_selectedValue').val() != '' ? 'editar' : 'agregar');
		funciones.guardar(funciones.controllerUrl(aux), armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idArticulo: $('#inputBuscarArticulo_selectedValue').val(),
			id: $('#inputBuscarColor_selectedValue').val(),
			ecommerceExiste: $('#inputEcommerceExiste').val(),
			ecommerceNombre: $('#inputEcommerceNombre').val(),
			ecommerceInfo: $('#inputEcommerceInfo').val(),
			ecommerceForSale: $('#inputEcommerceForSale').val(),
			ecommerceCondition: $('#inputEcommerceCondition').val(),
			ecommerceCategory: $('#inputEcommerceCategory_selectedValue').val(),
			ecommerceExclusive: $('#inputEcommerceExclusive').val(),
			ecommerceFeatured: $('#inputEcommerceFeatured').val(),
			ecommercePrice1: $('#inputEcommercePrice1').val(),
			ecommercePrice2: $('#inputEcommercePrice2').val(),
			ecommercePrice3: $('#inputEcommercePrice3').val(),
			ecommerceImage1: $('#inputEcommerceImage1').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el color por artículo "' + $('#inputBuscarArticulo_selectedName').val() + ' ' + $('#inputBuscarColor_selectedName').val() + '"?',
			url = funciones.controllerUrl('borrar');
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {
			idArticulo: $('#inputBuscarArticulo_selectedValue').val(),
			id: $('#inputBuscarColor_selectedValue').val()
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma + ' - ' + $('#inputBuscarArticulo_selectedName').val() + ' ' + $('#inputBuscarColor_selectedName').val());
				break;
			case 'editar':
				$('#inputEcommerceNombre').focus();
				break;
			case 'agregar':
				$('#inputEcommerceNombre').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divImpuestos' class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 12, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>[Ecommerce] Nombre:</label>';
			$cells[0][0]->style->width = '200px';
			$cells[0][1]->content = '<textarea id="inputEcommerceNombre" class="textbox inputForm w230" rel="ecommerceNombre"></textarea>';
			$cells[0][1]->style->width = '250px';

			$cells[1][0]->content = '<label>[Ecommerce] Sincronizar:</label>';
			$cells[1][1]->content = '<select id="inputEcommerceExiste" class="textbox inputForm w70" rel="ecommerceExiste">
										<option value="N">No</option>
										<option value="S">Sí</option>
									</select>';

			$cells[2][0]->content = '<label>[Ecommerce] A la venta:</label>';
			$cells[2][1]->content = '<select id="inputEcommerceForSale" class="textbox inputForm w70" rel="ecommerceForSale">
										<option value="N">No</option>
										<option value="S">Sí</option>
									</select>';

			$cells[3][0]->content = '<label>[Ecommerce] Exclusivo:</label>';
			$cells[3][1]->content = '<select id="inputEcommerceExclusive" class="textbox inputForm w70" rel="ecommerceExclusive">
										<option value="N">No</option>
										<option value="S">Sí</option>
									</select>';

			$cells[4][0]->content = '<label>[Ecommerce] Destacado:</label>';
			$cells[4][1]->content = '<select id="inputEcommerceFeatured" class="textbox inputForm w70" rel="ecommerceFeatured">
										<option value="N">No</option>
										<option value="S">Sí</option>
									</select>';

			$cells[5][0]->content = '<label>[Ecommerce] Categoría:</label>';
			$cells[5][1]->content = '<input id="inputEcommerceCategory" class="textbox inputForm autoSuggestBox w150" name="CategoriaCalzadoUsuario" rel="ecommerceCategory" />';

			$cells[6][0]->content = '<label>[Ecommerce] Condición:</label>';
			$cells[6][1]->content = '<select id="inputEcommerceCondition" class="textbox inputForm w150" rel="ecommerceCondition">
											<option value="traditional">Traditional</option>
											<option value="new">New</option>
											<option value="onsale">On sale</option>
										</select>';

			$cells[7][0]->content = '<label>[Ecommerce] Precio 1:</label>';
			$cells[7][1]->content = '<input id="inputEcommercePrice1" class="textbox inputForm w150" validate="DecimalPositivo" rel="ecommercePrice1" />';

			$cells[8][0]->content = '<label>[Ecommerce] Precio 2:</label>';
			$cells[8][1]->content = '<input id="inputEcommercePrice2" class="textbox inputForm w150" validate="DecimalPositivo" rel="ecommercePrice2" />';

			$cells[9][0]->content = '<label>[Ecommerce] Precio 3:</label>';
			$cells[9][1]->content = '<input id="inputEcommercePrice3" class="textbox inputForm w150" validate="DecimalPositivo" rel="ecommercePrice3" />';

			$cells[10][0]->content = '<label>[Ecommerce] Imagen 1:</label>';
			$cells[10][1]->content = '<input id="inputEcommerceImage1" class="textbox inputForm w230" rel="ecommerceImage1" />';

			$cells[11][0]->content = '<label>[Ecommerce] Información:</label>';
			$cells[11][1]->content = '<textarea id="inputEcommerceInfo" class="textbox inputForm w230" rel="ecommerceInfo"></textarea>';

			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarArticulo' class='filtroBuscar'>Artículo:</label>
			<input id='inputBuscarArticulo' class='textbox autoSuggestBox filtroBuscar w200' name='Articulo' />
		</div>
		<div>
			<label for='inputBuscarColor' class='filtroBuscar'>Color:</label>
			<input id='inputBuscarColor' class='textbox autoSuggestBox filtroBuscar w200' name='ColorPorArticulo' linkedTo="inputBuscarArticulo,Articulo" />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/colores_por_articulo/editar/')); ?>
		<?php //Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/colores_por_articulo/agregar/')); ?>
		<?php //Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/temporadas/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
