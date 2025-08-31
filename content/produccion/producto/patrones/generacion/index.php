<?php
?>

<style>
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		arrayIva = {};
		tituloPrograma = 'Generación de Patrones';

		$('.tabladinamica').tablaDinamica(
			{
				width: '100%',
				height: 'auto',
				caption: 'Detalle',
				scrollbar: false,
				addButtonInHeader: true,
				buttons: ['Q'],
				columnsConfig: [
					{
						id: 'idSeccion',
						name: 'Sección',
						width: '100px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox w110" name="SeccionProduccion" />'
					},
					{
						id: 'idConjunto',
						name: 'Conjunto',
						width: '160px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox w160" name="Conjunto" />'
					},
					{
						id: 'idMaterial',
						name: 'Material',
						width: '180px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox w540" name="Material" />'
					},
					{
						id: 'idColor',
						name: 'Color',
						width: '120px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox w200" name="ColorMateriaPrima" />',
						focus: function() {
							var idMaterial = this.tablaDinamica('getSibling', 'idMaterial').getValue(),
								that = this;
							this.tablaDinamica('getMe').valueElement.attr('alt', 'idMaterial=' + idMaterial);

							if (this.tablaDinamica('getMe').valueElement.data('idMaterial') != idMaterial) {
								if (idMaterial) {
									$.getJSON(funciones.controllerUrl('getUnidadDeMedida', {idMaterial: idMaterial}), function(json) {
										that.tablaDinamica('getSibling', 'unidadDeMedida').valueElement.html(json.data.unidadDeMedida);
										that.tablaDinamica('getMe').valueElement.data('idMaterial', idMaterial);
									});
								} else {
									that.tablaDinamica('getSibling', 'unidadDeMedida').valueElement.html('-');
								}
							}
						}
					},
					{
						id: 'consumoPar',
						name: 'Consumo par',
						width: '50px',
						css: {textAlign: 'center'},
						cellType: 'I',
						template: '<input class="textbox obligatorio w50" type="text" validate="DecimalPositivo" />'
					},
					{
						id: 'unidadDeMedida',
						name: 'U. Medida',
						width: '60px',
						css: {textAlign: 'center'},
						cellType: 'L',
						template: '-'
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

		$('#inputBuscarClonar').change(function(){
			if($('#inputBuscarClonar').val() == 'S'){
				$('.clonarArticulo').show();
			}else{
				$('.clonarArticulo').hide();
			}
		});

		cambiarModo('inicio');
	});

	function limpiarScreen() {
	}

	function buscar() {
		var condicion = $('#inputBuscarArticulo_selectedValue').val() == '' || $('#inputBuscarColor_selectedValue').val() == '' || $('#inputBuscarVersion_selectedValue').val() == '';

		if ($('#inputBuscarClonar').val() == 'S') {
			condicion |= $('#inputBuscarClonarArticulo_selectedValue').val() == '' || $('#inputBuscarClonarColor_selectedValue').val() == '';
		}

		if (!condicion) {
			funciones.limpiarScreen();
			var url = funciones.controllerUrl('buscar',
											  {
												  idArticulo: $('#inputBuscarArticulo_selectedValue').val(),
												  idColor: $('#inputBuscarColor_selectedValue').val(),
												  idVersion: $('#inputBuscarVersion_selectedValue').val(),
												  clonar: $('#inputBuscarClonar').val(),
												  idArticuloClonar: $('#inputBuscarClonarArticulo_selectedValue').val(),
												  idColorClonar: $('#inputBuscarClonarColor_selectedValue').val(),
												  copiarImagenes: $('#inputCopiarImagenes').val()
											  }), msgError = 'No existe el patrón buscado',
				cbSuccess = function(json) {
					$('#inputArticulo').val(json.idArticulo).autoComplete();
					$('#inputColor').val(json.idColor).autoComplete();
					$('#inputVersion').val(json.idVersion);
					$('.tabladinamica').tablaDinamica('load', json.detalle);
				};
			funciones.buscar(url, cbSuccess, msgError);
		}
	}

	function guardar(){
		funciones.guardar(funciones.controllerUrl(($('#inputBuscarArticulo_selectedValue').val() != '' ? 'editar' : 'agregar')), {
			idArticulo: $('#inputArticulo_selectedValue').val(),
			idColor: $('#inputColor_selectedValue').val(),
			idVersion: $('#inputVersion').val(),
			detalle: $('.tabladinamica').tablaDinamica('getJson')
		});
	}

	function hayErrorGuardar() {
		if($('#inputArticulo_selectedValue').val() == '')
			return 'Debe seleccionar un artículo';

		if($('#inputColor_selectedValue').val() == '')
			return 'Debe seleccionar un color';

		if($('#inputVersion_selectedValue').val() == '')
			return 'Debe ingresar una versión de patrón';

		var detalles = $('.tabladinamica').tablaDinamica('getJson');

		if(detalles.length == 0)
			return 'El patrón debe tener al menos un detalle';

		for(var i = 0; i < detalles.length; i++){
			if(!(detalles[i].idSeccion || detalles[i].idConjunto || detalles[i].idMaterial || detalles[i].idColor || detalles[i].consumoPar)) {
				return 'Debe completar todos los campos obligatorios del patrón';
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
				$('#inputBuscarClonar').val('N').change();
				break;
			case 'buscar':
				break;
			case 'editar':
				break;
			case 'agregar':
				$('.tabladinamica').tablaDinamica('addRow');
				$('#inputArticulo').focus();
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

			$cells[0][0]->content = '<label for="inputArticulo">Artículo:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputArticulo" class="textbox obligatorio autoSuggestBox inputForm w200" name="Articulo" />';
			$cells[0][1]->style->width = '210px';

			$cells[1][0]->content = '<label for="inputColor">Color:</label>';
			$cells[1][1]->content = '<input id="inputColor" class="textbox obligatorio autoSuggestBox inputForm w200" name="ColorPorArticulo" linkedTo="inputArticulo,Articulo" />';

			$cells[2][0]->content = '<label for="inputVersion">Versión:</label>';
			$cells[2][1]->content = '<input id="inputVersion" class="textbox obligatorio inputForm w200" validate="EnteroPositivo" />';

			$tabla->create();
			?>
		</div>

		<div id='divPrograma' class='fRight pantalla w100p'>
			<div id='divDetalles' class='well h410'>
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
			<label for='inputBuscarArticulo' class='filtroBuscar'>Artículo:</label>
			<input id='inputBuscarArticulo' class='textbox obligatorio autoSuggestBox filtroBuscar w230' name='Articulo' />
		</div>
		<div>
			<label for='inputBuscarColor' class='filtroBuscar'>Color:</label>
			<input id='inputBuscarColor' class='textbox obligatorio autoSuggestBox filtroBuscar w230' name='ColorPorArticulo' linkedTo='inputBuscarArticulo,Articulo' />
		</div>
		<div>
			<label for='inputBuscarVersion' class='filtroBuscar'>Versión:</label>
			<input id='inputBuscarVersion' class='textbox obligatorio autoSuggestBox filtroBuscar w230' name='Patron' linkedTo="inputBuscarArticulo,Articulo;inputBuscarColor,ColorPorArticulo" />
		</div>
		<div>
			<label for="inputBuscarClonar" class='filtroBuscar'>Clonar:</label>
			<select id='inputBuscarClonar' class='textbox obligatorio filtroBuscar w230'>
				<option value='S'>Si</option>
				<option value='N'>No</option>
			</select>
		</div>
		<div class="clonarArticulo">
			<label for='inputBuscarClonarArticulo' class='filtroBuscar'>Artículo:</label>
			<input id='inputBuscarClonarArticulo' class='textbox autoSuggestBox obligatorio filtroBuscar w230' name='Articulo' />
		</div>
		<div class="clonarArticulo">
			<label for='inputBuscarClonarColor' class='filtroBuscar'>Color:</label>
			<input id='inputBuscarClonarColor' class='textbox obligatorio autoSuggestBox filtroBuscar w230' name='ColorPorArticulo' linkedTo='inputBuscarClonarArticulo,Articulo' />
		</div>
		<div class="clonarArticulo">
			<label for="inputCopiarImagenes" class='filtroBuscar'>Copiar imagenes:</label>
			<select id='inputCopiarImagenes' class='textbox obligatorio filtroBuscar w230'>
				<option value='S'>Si</option>
				<option value='N'>No</option>
			</select>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'produccion/producto/patrones/generacion/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'produccion/producto/patrones/generacion/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'produccion/producto/patrones/generacion/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>