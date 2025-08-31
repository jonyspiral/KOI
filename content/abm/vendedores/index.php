<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Vendedores';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/abm/vendedores/buscar.php?id=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'El personal "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos, #tablaDatos2').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		//MÁS COSAS OBLIGATORIAS
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar el nombre';
		if ($('#inputApellido').val() == '')
			return 'Debe ingresar el apellido';
		if ($('#inputDni').val() == '')
			return 'Debe ingresar el DNI';
		if ($('#inputLegajo').val() == '')
			return 'Debe ingresar el legajo';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/vendedores/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			id: $('#inputBuscar_selectedValue').val(),
			nombre: $('#inputNombre').val(),		
			apellido: $('#inputApellido').val(),
			dni: $('#inputDni').val(),
			direccion: $('#inputCalle').val(),
			numero: $('#inputNumero').val(),
			piso: $('#inputPiso').val(),
			dpto: $('#inputDpto').val(),
			pais: $('#inputPais_selectedValue').val(),
			provincia: $('#inputProvincia_selectedValue').val(),
			localidad: $('#inputLocalidad_selectedValue').val(),
			codPostal: $('#inputCP').val(),
			telefono: $('#inputTelefono').val(),
			email: $('#inputEmail').val(),
			antiguedad: $('#inputAntiguedad').val(),
			fajaHoraria: $('#inputFajaHoraria_selectedValue').val(),
			ingreso: $('#inputIngreso').val(),
			egreso: $('#inputEgreso').val(),
			modalidadRetribucion: $('#inputModalidadRetribucion').val(),
			comision: $('#inputComision').val(),
			celular: $('#inputCelular').val(),
			valorHora: $('#inputValorHora').val(),
			valorMes: $('#inputValorMes').val(),
			valorQuincena: $('#inputValorQuincena').val(),
			fechaNacimiento: $('#inputFechaNacimiento').val(),
			cuil: $('#inputCuil').val(),
			legajo: $('#inputLegajo').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el personal "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/abm/personal/borrar.php';
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
	<div id='divPersonal' class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 13, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);
  		
			//imprime el cuadro con campos	
		
			
			
			$cells[0][0]->content = '<label>Nombre:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm inputForm w230" rel="nombre" />';
			$cells[0][1]->style->width = '250px';
			
			$cells[1][0]->content = '<label>Apellido:</label>';
			$cells[1][1]->content = '<input id="inputApellido" class="textbox inputForm obligatorio inputForm w230"   rel="apellido" />';
				
			$cells[2][0]->content = '<label>DNI:</label>';
			$cells[2][1]->content = '<input id="inputDni" class="textbox inputForm obligatorio inputForm w230"   rel="dni" />';
			
			$cells[3][0]->content = '<label>Cuil:</label>';
			$cells[3][1]->content = '<input id="inputCuil" class="textbox inputForm inputForm w230" validate="Cuil" rel="cuil" />';
			
			$cells[4][0]->content = '<label>Fecha de nacimiento:</label>';
			$cells[4][1]->content = '<input id="inputFechaNacimiento" class="textbox inputForm inputForm w210" validate="Fecha" rel="fechaNacimiento" />';

			$cells[5][0]->content = '<label>Calle:</label>';
			$cells[5][1]->content = '<input id="inputCalle" class="textbox inputForm inputForm w230" rel="direccionCalle" />';
			
			$cells[6][0]->content = '<label>Numero:</label>';
			$cells[6][1]->content = '<input id="inputNumero" class="textbox inputForm inputForm w65" rel="direccionNumero" />
									<label>Piso:</label>
									<input id="inputPiso" class="textbox inputFormSuc inputForm w25" maxlength="3" rel="direccionPiso" />
									<label>Dpto:</label>
									<input id="inputDpto" class="textbox inputFormSuc inputForm w25" maxlength="3" rel="direccionDepartamento" />';
			
			$cells[7][0]->content = '<label>Pais:</label>';
			$cells[7][1]->content = '<input id="inputPais" class="textbox inputForm autoSuggestBox inputForm w230" name="Pais"   rel="direccionPais" />';
			
			$cells[8][0]->content = '<label>Provincia:</label>';
			$cells[8][1]->content = '<input id="inputProvincia" class="textbox inputForm autoSuggestBox inputForm w230" name="Provincia" linkedTo="inputPais,Pais" name="Provincia"   rel="direccionProvincia" />';
			
			$cells[9][0]->content = '<label>Localidad:</label>';
			$cells[9][1]->content = '<input id="inputLocalidad" class="textbox inputForm autoSuggestBox inputForm w135" name="Localidad" linkedTo="inputPais,Pais;inputProvincia,Provincia"   rel="direccionLocalidad" />
									<label>CP:</label>
									<input id="inputCP" class="textbox inputFormSuc inputForm w45" maxlength="4" rel="direccionCodigoPostal" />';
			
			$cells[10][0]->content = '<label>Teléfono:</label>';
			$cells[10][1]->content = '<input id="inputTelefono" class="textbox inputForm inputForm w230" rel="telefono" />';
			
			$cells[11][0]->content = '<label>Celular:</label>';
			$cells[11][1]->content = '<input id="inputCelular" class="textbox inputForm inputForm w230" rel="celular" />';
				
			
			$cells[12][0]->content = '<label>Email:</label>';
			$cells[12][1]->content = '<input id="inputEmail" class="textbox inputForm inputForm w230" validate="Email" rel="email" />';
			
			
			
			$tabla->create();//impresion
		?>
	</div>
	<div id='divPersonal2' class='fRight pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 10, 'cantCols' => 2, 'id' => 'tablaDatos2', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);
  
			//imprime el cuadro con campos	
			
			$cells[0][0]->content = '<label>Legajo:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputLegajo" class="textbox obligatorio inputForm inputForm w230"  rel="legajo" />';
			$cells[0][1]->style->width = '250px';

			$cells[1][0]->content = '<label>Ingreso:</label>';	
			$cells[1][1]->content = '<input id="inputIngreso" class="textbox inputForm inputForm w210" validate="Fecha" rel="fechaIngreso" />';

			$cells[2][0]->content = '<label>Egreso:</label>';
			$cells[2][1]->content = '<input id="inputEgreso" class="textbox inputForm inputForm w210" validate="Fecha" rel="fechaEgreso" />';

			$cells[3][0]->content = '<label>Antigüedad Gremio:</label>';
			$cells[3][1]->content = '<input id="inputAntiguedad" class="textbox inputForm inputForm w210" validate="Fecha" rel="fechaAntiguedadGremio" />';
			
			$cells[4][0]->content = '<label>Modalidad Retribución:</label>';
			$cells[4][1]->content = '<input id="inputModalidadRetribucion" class="textbox inputForm inputForm w230" rel="modalidadRetribucion" />';

			$cells[5][0]->content = '<label>% comisión:</label>';
			$cells[5][1]->content = '<input id="inputComision" class="textbox inputForm inputForm w230" Validate="DecimalPositivo" rel="porcComisionVtas" />';
			
			$cells[6][0]->content = '<label>Faja horaria:</label>';
			$cells[6][1]->content = '<input id="inputFajaHoraria" class="textbox inputForm autoSuggestBox  inputForm w230" name="FajaHoraria"  rel="fajaHoraria" />';

			$cells[7][0]->content = '<label>Valor hora:</label>';
			$cells[7][1]->content = '<input id="inputValorHora" class="textbox inputForm inputForm w230" rel="valorHora" />';

			$cells[8][0]->content = '<label>Valor mes:</label>';
			$cells[8][1]->content = '<input id="inputValorMes" class="textbox inputForm inputForm w230" rel="valorMes" />';
			
			$cells[9][0]->content = '<label>Valor Quincena:</label>';
			$cells[9][1]->content = '<input id="inputValorQuincena" class="textbox inputForm inputForm w230" rel="valorQuincena" />';
			
			$tabla->create();//impresion
		?>
	</div>
	</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div><!--campos de busqueda  -->
			<label class='filtroBuscar'>Vendedor:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Vendedor' />
		</div>
	</div><!-- fin campos busqueda -->
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/vendedores/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/vendedores/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/vendedores/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
