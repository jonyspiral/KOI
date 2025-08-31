<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Transportes';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/abm/transportes/buscar.php?idTransporte=' + $('#inputBuscar_selectedValue').val(), 
			msgError = 'El transporte "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar el nombre del transporte';				
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/transportes/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idTransporte: $('#inputBuscar_selectedValue').val(),
			nombre: $('#inputNombre').val(),		
			telefono: $('#inputTelefono').val(),
			email: $('#inputMail').val(),
			calle: $('#inputCalle').val(),
			numero: $('#inputNumero').val(),
			piso: $('#inputPiso').val(),
			dpto: $('#inputDpto').val(),
			codPostal: $('#inputCP').val(),
			idPais: $('#inputPais_selectedValue').val(),
			idProvincia: $('#inputProvincia_selectedValue').val(),
			idLocalidad: $('#inputLocalidad_selectedValue').val(),
			horarioAtencion : $('#horarioDeAtencion').val(),
			transporte : $('#inputTransporte_selectedValue').val(),
			cuit :$('#inputCuit').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el transporte "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/abm/transportes/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {
				idTransporte: $('#inputBuscar_selectedValue').val()
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
	<div id='divTransportes' class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 10, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			//imprime el cuadro con campos	
			$cells[0][0]->content = '<label>Nombre:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm inputForm w230" rel="nombre" />';
			$cells[0][1]->style->width = '250px';

			$cells[1][0]->content = '<label>Calle:</label>';
			$cells[1][1]->content = '<input id="inputCalle" class="textbox inputForm inputForm w230" rel="direccionCalle" />';

			$cells[2][0]->content = '<label>País:</label>';
			$cells[2][1]->content = '<input id="inputPais" class="textbox autoSuggestBox inputForm w230" name="Pais" alt="" rel="direccionPais" />';
			$cells[3][0]->content = '<label>Provincia:</label>';
			$cells[3][1]->content = '<input id="inputProvincia" class="textbox autoSuggestBox inputForm w230" name="Provincia" linkedTo="inputPais,Pais"  alt="" rel="direccionProvincia" />';
			$cells[4][0]->content = '<label>Localidad:</label>';
			$cells[4][1]->content = '<input id="inputLocalidad" class="textbox autoSuggestBox inputForm w230" name="Localidad" linkedTo="inputPais,Pais;inputProvincia,Provincia" alt="" rel="direccionLocalidad" />';
							
			$cells[5][0]->content = '<label>Teléfono:</label>';			
			$cells[5][1]->content = '<input id="inputTelefono" class="textbox inputForm w230" maxlength="12" rel="telefono" />';
			$cells[6][0]->content = '<label>Email:</label>';
			$cells[6][1]->content = '<input id="inputMail" class="textbox inputForm w230"  validate="Email" rel="email" />';
				
			$cells[7][0]->content = '<label>Horario de atención:</label>';
			$cells[7][1]->content = '<input id="horarioDeAtencion" class="textbox inputForm inputForm w230"  alt="" rel="horario" validate="RangoHora" />';
			$cells[8][0]->content = '<label>Cuit:</label>';
			$cells[8][1]->content = '<input id="inputCuit" class="textbox inputForm inputForm w230"  alt="" validate="Cuit" rel="cuit" />';

			$tabla->create();//impresion
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div><!--campos de busqueda  -->
			<label for='inputBuscar' class='filtroBuscar'>Transporte:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Transporte'  alt='' />
		</div>
	</div><!-- fin campos busqueda -->
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/transportes/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/transportes/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/transportes/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
