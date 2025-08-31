<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Localidades';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var getPais = 'idPais=' + $('#inputBuscarPais_selectedValue').val(),
			getProvincia = '&idProvincia=' + $('#inputBuscarProvincia_selectedValue').val(),
			getLocalidad = '&idLocalidad=' + $('#inputBuscar_selectedValue').val(); 
		var url = '/content/abm/regiones/localidades/buscar.php?' + getPais + getProvincia + getLocalidad,
			msgError = 'La localidad "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputPais_selectedValue').val() == '')
			return 'Debe elegir a qué país pertenece la localidad';
		if ($('#inputProvincia_selectedValue').val() == '')
			return 'Debe elegir a qué provincia pertenece la localidad';
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar el nombre de la localidad';
		if ($('#inputZona_selectedValue').val() == '')
			return 'Debe elegir la zona de la localidad';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/regiones/localidades/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idPais: $('#inputPais_selectedValue').val(),
			idProvincia: $('#inputProvincia_selectedValue').val(),
			idLocalidad: $('#inputBuscar_selectedValue').val(),
			nombre: $('#inputNombre').val(),
			codigoPostal: $('#inputCodigoPostal').val(),
			idZona: $('#inputZona_selectedValue').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar la localidad "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/abm/regiones/localidades/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {
			idPais: $('#inputBuscarPais_selectedValue').val(),
			idProvincia: $('#inputBuscarProvincia_selectedValue').val(),
			idLocalidad: $('#inputBuscar_selectedValue').val()
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#divLocalidad').hide();
				break;
			case 'buscar':
				$('#divLocalidad').show();
				break;
			case 'editar':
				$('#inputNombre').focus();
				break;
			case 'agregar':
				$('#divLocalidad').show();
				$('#inputPais').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divLocalidad'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 5, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>País:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputPais" class="textbox obligatorio autoSuggestBox inputForm w230 noEditable" name="Pais" rel="pais" />';
			$cells[0][1]->style->width = '250px';
			$cells[1][0]->content = '<label>Provincia:</label>';
			$cells[1][1]->content = '<input id="inputProvincia" class="textbox obligatorio autoSuggestBox inputForm w230 noEditable" name="Provincia" linkedTo="inputPais,Pais" rel="provincia" />';
			$cells[2][0]->content = '<label>Nombre:</label>';
			$cells[2][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w230" rel="nombre" />';
			$cells[3][0]->content = '<label>Código postal:</label>';
			$cells[3][1]->content = '<input id="inputCodigoPostal" class="textbox inputForm w230" rel="codigoPostal" />';
			$cells[4][0]->content = '<label>Zona:</label>';
			$cells[4][1]->content = '<input id="inputZona" class="textbox obligatorio autoSuggestBox inputForm w230" name="Zona" rel="zona" />';

			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarPais' class='filtroBuscar'>País:</label>
			<input id='inputBuscarPais' class='textbox autoSuggestBox filtroBuscar w200' name='Pais' alt='' />
		</div>
		<div>
			<label for='inputBuscarProvincia' class='filtroBuscar'>Provincia:</label>
			<input id='inputBuscarProvincia' class='textbox autoSuggestBox filtroBuscar w200' name='Provincia' linkedTo="inputBuscarPais,Pais" alt='' />
		</div>
		<div>
			<label for='inputBuscar' class='filtroBuscar'>Localidad:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Localidad' linkedTo="inputBuscarPais,Pais;inputBuscarProvincia,Provincia" alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/regiones/localidades/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/regiones/localidades/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/regiones/localidades/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
