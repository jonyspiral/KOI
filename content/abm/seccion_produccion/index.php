<?php

$seccion = Funciones::get('id');

?>
<style>
	#divSeccion {
		width: 50%;
	}
	#divAlmacenes {
		width: 50%;
	}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Secciones producción';
		$('#btnAgregarAlmacen').click(agregarAlmacenPopUp);
		cambiarModo('inicio');
		<?php echo ($seccion ? 'buscar("' . $seccion . '");' : ''); ?>
	});

	function limpiarScreen(){
		$('#tablaAlmacenes tbody').html('');
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
		var msgError = 'La sección "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				fillSeccion(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function fillSeccion(json) {
		$('#tablaDatos').loadJSON(json);
		var bodyAlmacenes = $('#tablaAlmacenes').find('tbody').eq(0);
		for (var i = 0; i < json.almacenes.length; i++) {
			var almacen = json.almacenes[i];
			agregarAlmacenTr(bodyAlmacenes, almacen, almacen.id == json.idAlmacenDefault);
		}
	}

	function agregarAlmacenPopUp() {
		var div = $('<div class="h100 vaMiddle table-cell aLeft p10">').append(
			$('<table>').append(
				$('<tbody>').append(
					$('<tr><td><label for="inputAlmacen">Almacén: </label></td><td><input id="inputAlmacen" type="text" class="textbox autoSuggestBox obligatorio w190" name="Almacen" /></td></tr>')
				)
			)
		);
		var botones = [{value: 'Guardar', action: function() {agregarAlmacen();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
	}

	function agregarAlmacenTr(body, almacen, isDefault) {
        if (!body.find('tr').length) {
            isDefault = true;
        }
		body.append(
			$('<tr>').addClass('tableRow trAlmacen').attr('id', 'tr_' + almacen.id).data('almacen', almacen).append(
				$('<td>').addClass('aCenter').text(almacen.id),
				$('<td>').text(almacen.nombre),
				$('<td>').addClass('aCenter').append(
					$('<input>').attr('type', 'radio').attr('name', 'default').attr('title', 'Establecer por defecto').attr('value', almacen.id).attr(isDefault ? 'checked' : 'dummy', 'checked')
				),
				$('<td>').addClass('aCenter').append(
					$('<a>').addClass('boton').attr('href', '#').attr('title', 'Quitar almacen')
						.click($.proxy(function() {quitarAlmacen(this);}, almacen))
						.append($('<img>').attr('src', '/img/botones/25/cancelar.gif'))
				)
			)
		);
	}

	function quitarAlmacen(almacen) {
		$('#tr_' + almacen.id).remove()
	}

	function agregarAlmacen() {
		var almacen = {
			id: $('#inputAlmacen_selectedValue').val(),
			nombre: $('#inputAlmacen_selectedName').val()
		};
		agregarAlmacenTr($('#tablaAlmacenes').find('tbody').eq(0), almacen, false);
		$.jPopUp.close(function(){
			$('#tr_' + almacen.id).shine();
		});
	}

	function hayErrorGuardar() {
		if ($('#inputNombre').val() == '') {
			return 'Debe ingresar un nombre para la sección';
		}
		return false;
	}

	function guardar() {
		var url = funciones.controllerUrl($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar() { // TODO
		return {
			id : $('#inputCodigo').val(),
            nombre : $('#inputNombre').val(),
            nombreCorto : $('#inputNombreCorto').val(),
            imprimeStickers : $('#inputImprimeStickers').val(),
            jerarquiaSeccion : $('#inputJerarquiaSeccion').val(),
            idSeccionSuperior : $('#inputSeccionSuperior_selectedValue').val(),
            ingresaAlStock : $('#inputIngresaAlStock').val(),
            interrumpible : $('#inputInterrumpible').val(),
            idUnidadDeMedida : $('#inputUnidadDeMedida').val(),
            almacenes : armarAlmacenes(),
            idAlmacenDefault: $('input[name="default"]:checked').val()
		};
	}

	function armarAlmacenes() {
		var almacenes = [];
        $('.trAlmacen').each(function() {
            almacenes.push($(this).data('almacen').id);
        });
		return almacenes;
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar la sección "' + $('#inputBuscar_selectedName').val() + '"?',
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
				$('#inputCodigo').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
    <div id='divSeccion' class='fLeft pantalla'>
        <?php
        $tabla = new HtmlTable(array('cantRows' => 11, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
        $tabla->getRowCellArray($rows, $cells);

        $cells[0][0]->content = '<label>Código:</label>';
        $cells[0][0]->style->width = '150px';
        $cells[0][1]->content = '<input id="inputCodigo" class="textbox obligatorio inputForm w230 noEditable" rel="id" />';
        $cells[0][1]->style->width = '250px';
        $cells[1][0]->content = '<label>Nombre:</label>';
        $cells[1][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w230" rel="nombre" />';
        $cells[2][0]->content = '<label>Nombre corto:</label>';
        $cells[2][1]->content = '<input id="inputNombreCorto" class="textbox obligatorio inputForm w230" rel="nombreCorto" />';
        $cells[3][0]->content = '<label>Imprime Stickers:</label>';
        $cells[3][1]->content = '<select id="inputImprimeStickers" class="textbox obligatorio inputForm w230" rel="imprimeStickers" >
                                    <option value="N">No</option>
                                    <option value="S">Sí</option>
                                </select>';
        $cells[4][0]->content = '<label>Jerarquía Sección:</label>';
        $cells[4][1]->content = '<select id="inputJerarquiaSeccion" class="textbox obligatorio inputForm w230" rel="jerarquiaSeccion" >
                                    <option value="P">Principal</option>
                                    <option value="S">Subordinado</option>
                                </select>';
        $cells[5][0]->content = '<label>Sección superior:</label>';
        $cells[5][1]->content = '<input id="inputSeccionSuperior" class="textbox autoSuggestBox inputForm w230" name="SeccionProduccion" rel="seccionSuperior" />';
        $cells[6][0]->content = '<label>Ingresa al Stock:</label>';
        $cells[6][1]->content = '<select id="inputIngresaAlStock" class="textbox obligatorio inputForm w230" rel="ingresaAlStock" >
                                    <option value="N">No</option>
                                    <option value="S">Sí</option>
                                </select>';
        $cells[7][0]->content = '<label>Interrumpible:</label>';
        $cells[7][1]->content = '<select id="inputInterrumpible" class="textbox obligatorio inputForm w230" rel="interrumpible" >
                                    <option value="S">Sí</option>
                                    <option value="N">No</option>
                                </select>';
        $cells[8][0]->content = '<label>Unidad de medida:</label>';
        $cells[8][1]->content = '<select id="inputUnidadDeMedida" class="textbox obligatorio inputForm w230" rel="idUnidadDeMedida" >
                                    <option value="P">Par</option>
                                    <option value="M">Metro</option>
                                </select>';

        $tabla->create();
        ?>
    </div>
    <div id='divAlmacenes' class='fRight pantalla'>
        <div class="fLeft">
            <h3>Almacenes</h3>
        </div>
        <div class="fRight">
            <a href="#" id="btnAgregarAlmacen" class="boton" title="Agregar almacén"><img src="/img/botones/25/agregar.gif" /></a>
        </div>
        <table id='tablaAlmacenes' class='registrosAlternados w100p'>
            <thead class='tableHeader'>
                <tr>
                    <th class="w20p">ID</th>
                    <th class="w50p">Nombre</th>
                    <th class="w15p" title="Predefinido">Predef.</th>
                    <th class="w15p">Quitar</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label class='filtroBuscar'>Sección:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='SeccionProduccion'  alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/seccion_produccion/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/seccion_produccion/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/seccion_produccion/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
