<?php
?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Condiciones Iva';
		insertarRadioButtonTratamiento();
		insertarRadioButtonLetra();
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/abm/condiciones_iva/buscar.php?id=' + $('#inputBuscar_selectedValue').val(),	
			msgError = 'La condición de IVA "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
				for (var i = 1 ; i<=5; i++){
					var porcentaje = funciones.formatearPorcentaje(json.porcentajes[i]);
					$('#tablaDatos #inputPorcentaje' + i).val(porcentaje);
				}
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function insertarRadioButtonLetra(){
		var htmlString = "<div>";
		htmlString += "<div id='radioGroupLetra' class='customRadio w180 inline-block'>";
		htmlString += "<input id='rdLetra_A' type='radio' name='radioGroupLetra' value='A' rel='letraFactura' /><label id='A' for='rdLetra_A'>A</label>";
		htmlString += "<input id='rdLetra_B' type='radio' name='radioGroupLetra' value='B' rel='letraFactura' /><label id='B' for='rdLetra_B'>B</label>";
		htmlString += "<input id='rdLetra_E' type='radio' name='radioGroupLetra' value='E' rel='letraFactura' /><label id='E' for='rdLetra_E'>E</label>";			
		htmlString += "</div>";
		$('#radioLetra').html(htmlString);
	}
	
	function insertarRadioButtonTratamiento(){
		var htmlString = "<div>";
		htmlString += "<div id='radioGroupTratamiento' class='customRadio w180 inline-block'>";
		htmlString += "<input id='rdTratamiento_D' type='radio' name='radioGroupTratamiento' value='D' rel='tratamiento' /><label id='D' for='rdTratamiento_D'>D</label>";
		htmlString += "<input id='rdTratamiento_I' type='radio' name='radioGroupTratamiento' value='I' rel='tratamiento' /><label id='I' for='rdTratamiento_I'>I</label>";			
		htmlString += "</div>";
		$('#radioB').html(htmlString);
	}
	
	function hayErrorGuardar(){
		if ($('#inputId').val() == '')
			return 'Debe elegir un ID para la condición de IVA';
		if ($('#inputNombre').val() == '')
			return 'Debe elegir un nombre para la condición de IVA';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/condiciones_iva/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			id: $('#inputBuscar_selectedValue').val(),
			letraFactura: $('#radioGroupLetra').radioVal(),
			nombre: $('#inputNombre').val(),
			id: $('#inputId').val(),
			tratamiento: $('#radioGroupTratamiento').radioVal(),
			porcentaje1: $('#inputPorcentaje1').val(),
			porcentaje2: $('#inputPorcentaje2').val(),
			porcentaje3: $('#inputPorcentaje3').val(),
			porcentaje4: $('#inputPorcentaje4').val(),
			porcentaje5: $('#inputPorcentaje5').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar la condición de IVA "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/abm/condiciones_iva/borrar.php';
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
				$('#inputPorcentaje1').focus();
				break;
			case 'agregar':
				$('#inputId').focus();
				break;
		}
	}
</script>
<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divCondicionIva' class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 9, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);
	
			$cells[0][0]->content = '<label>ID:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputId" class="textbox obligatorio noEditable inputForm w230"  alt="" rel="id" />';
			$cells[0][1]->style->width = '250px';
			$cells[1][0]->content = '<label>Nombre:</label>';
			$cells[1][1]->content = '<input id="inputNombre" class="textbox obligatorio noEditable inputForm w230"  alt="" rel="nombre" />';
			$cells[2][0]->content = '<label>Letra Factura:</label>';
			$cells[2][1]->content = '<label id="radioLetra"></label>';
			$cells[3][0]->content = '<label>Tratamiento:</label>';
			$cells[3][1]->content = '<label id="radioB"></label>';
			$cells[4][0]->content = '<label>Porcentaje 1:</label>';
			$cells[4][1]->content = '<input id="inputPorcentaje1" class="textbox inputForm inputForm w230" rel="1" validate="Porcentaje" />';
			$cells[5][0]->content = '<label>Porcentaje 2:</label>';
			$cells[5][1]->content = '<input id="inputPorcentaje2" class="textbox inputForm inputForm w230" rel="2" validate="Porcentaje"/>';
			$cells[6][0]->content = '<label>Porcentaje 3:</label>';
			$cells[6][1]->content = '<input id="inputPorcentaje3" class="textbox inputForm inputForm w230" rel="3" validate="Porcentaje"/>';
			$cells[7][0]->content = '<label>Porcentaje 4:</label>';
			$cells[7][1]->content = '<input id="inputPorcentaje4" class="textbox inputForm inputForm w230" rel="4" validate="Porcentaje"/>';
			$cells[8][0]->content = '<label>Porcentaje 5:</label>';
			$cells[8][1]->content = '<input id="inputPorcentaje5" class="textbox inputForm inputForm w230" rel="5" validate="Porcentaje"/>';
	
			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label class='filtroBuscar'>Condición IVA:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='CondicionIva' alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/zonas/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/zonas/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/zonas/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>