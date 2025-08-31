<?php
?>

<!-- Incluyo las librerías de gmaps. Cuando no hay internet ROMPE TODO, ver cómo hacer para solucionar esto... -->
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script src="https://maps.googleapis.com/maps/api/js?language=es&key=AIzaSyAugNf-KpA_cPeQnQB8vNbLJ5yfcNr_zOM" type="text/javascript"></script>
<script type='text/javascript'>
	var gmaps = null;

	$(document).ready(function(){
		tituloPrograma = 'Sucursales';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscarCliente_selectedValue').val() == '')//agregado los dos campos de busqueda
			return $('#inputBuscarCliente').val('');
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = funciones.controllerUrl('buscar', {idCliente: $('#inputBuscarCliente_selectedValue').val(), idSucursal: $('#inputBuscar_selectedValue').val()}),
			msgError = 'La sucursal "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos, #tablaDatos2').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#radioGroupPuntoVenta').radioVal() == 'S' && ($('#sucursalInputLatitud').val() == '' || $('#sucursalInputLongitud').val() == '')) {
			buscarCoordenadas();
			return 'Debe ingresar correctamente la dirección de la sucursal y luego utilizar el botón de "lupa" para poder generar la latitud y longitud';
		}
		return false;
	}

	function guardar(){
		var url = funciones.controllerUrl(($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar'));
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idClienteAdd: $('#sucursalInputCliente_selectedValue').val(),
			idSucursal: $('#inputBuscar_selectedValue').val(),
			idCliente: $('#inputBuscarCliente_selectedValue').val(),
			nombre: $('#sucursalInputNombre').val(),		
			telefono1: $('#sucursalInputTelefono1').val(),
			telefono2: $('#sucursalInputTelefono2').val(),
			celular: $('#sucursalInputCelular').val(),
			email1: $('#sucursalInputEmail1').val(),
			puntoVenta : $('#radioGroupPuntoVenta').radioVal(),
			fax: $('#sucursalInputFax').val(),
			calle: $('#sucursalInputCalle').val(),
			numero: $('#sucursalInputNumero').val(),
			piso: $('#sucursalInputPiso').val(),
			dpto: $('#sucursalInputDpto').val(),
			codPostal: $('#sucursalInputCP').val(),
			idPais: $('#sucursalInputPais_selectedValue').val(),
			idProvincia: $('#sucursalInputProvincia_selectedValue').val(),
			idLocalidad: $('#sucursalInputLocalidad_selectedValue').val(),
			reparto: $('#sucursalInputReparto').val(),
			vendedor: $('#sucursalInputVendedor_selectedValue').val(),
			observaciones: $('#sucursalinputObservaciones').val(),
			idEntrega: $('#sucursalInputEntrega_selectedValue').val(),
			horarioAtencion : $('#sucursalhorarioDeAtencion').val(),
			transporte : $('#sucursalInputTransporte_selectedValue').val(),
			zonaTransporte: $('#sucursalInputZonaTransporte_selectedValue').val(),
			latitud: $('#sucursalInputLatitud').val(),
			longitud: $('#sucursalInputLongitud').val(),
			horarioEntrega1: $('#sucursalHorarioEntrega1').val(),
			horarioEntrega2: $('#sucursalHorarioEntrega2').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar la sucursal "' + $('#inputBuscar_selectedName').val() + '"?',
			url = funciones.controllerUrl('borrar');
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {
			idSucursal: $('#inputBuscar_selectedValue').val(),
			idCliente: $('#inputBuscarCliente_selectedValue').val()
		};
	}

	function armoAddress() {
		var calle = $('#sucursalInputCalle').val(),
			numero = $('#sucursalInputNumero').val(),
			localidad = $('#sucursalInputLocalidad_selectedName').val(),
			provincia = $('#sucursalInputProvincia_selectedName').val(),
			pais = $('#sucursalInputPais_selectedName').val();
		if (calle == '') {
			$.alert('Debe ingresar el nombre de la calle de la sucursal');
		} else if (numero == '') {
			$.alert('Debe ingresar el número de la calle de la sucursal');
		} else if (localidad == '') {
			$.alert('Debe ingresar la localidad de la sucursal');
		} else if (provincia == '') {
			$.alert('Debe ingresar la provincia de la sucursal');
		} else if (pais == '') {
			$.alert('Debe ingresar el país de la sucursal');
		} else {
			return calle + ' ' + numero + ', ' + localidad + ', ' + provincia + ', ' + pais
		}
		return false;
	}

	function buscarCoordenadas(callbackSuccess, callbackError) {
		if (!gmaps) {
			gmaps = new google.maps.Geocoder();
		}
		var address = armoAddress();
		if (address) {
			gmaps.geocode({address: address}, function(results, status) {
				if (results.length) {
					$('#sucursalInputLatitud').val(results[0].geometry.location.lat);
					$('#sucursalInputLongitud').val(results[0].geometry.location.lng);
					callbackSuccess && callbackSuccess();
				} else {
					$.error('La dirección ingresada no produjo ningún resultado. Por favor revise que la información sea correcta');
					callbackError && callbackError();
				}
			});
		}

		/* results
		 {
			 address_components: Array[6]
			 formatted_address: "Avenida Acoyte 568, Buenos Aires, Ciudad Autónoma de Buenos Aires, Argentina"
			 geometry: {
				 bounds: jg
				 location: {
				 	B: -58.43871819999998 // Longitud
				 	k: -34.61212680000001 // Latitud
				 }
			 	location_type: "RANGE_INTERPOLATED"
			 	viewport: jg
			 }
			 partial_match: true
			 types: Array[1]
		 }
		 */

	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				break;
			case 'editar':
				$('#sucursalInputCliente').disable();
				$('.casacentral').hide();
				$('#sucursalInputNombre').focus();
				break;
			case 'agregar':
				$('.inputForm').not('.noEditable').enable();
				$('#sucursalInputCliente').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divSucursales1' class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 12, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);
  
			//imprime el cuadro con campos
			$cells[0][0]->content = '<label>Cliente:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="sucursalInputCliente" class="textbox obligatorio autoSuggestBox inputForm w230" name="Cliente" alt="" rel="cliente" />';
			$cells[0][1]->style->width = '250px';

			$cells[1][0]->content = '<label>Nombre:</label>';
			$cells[1][1]->content = '<input id="sucursalInputNombre" class="textbox obligatorio inputForm inputForm w230" rel="nombre" />';
			$cells[2][0]->content = '<label>Calle:</label>';
			$cells[2][1]->content = '<input id="sucursalInputCalle" class="textbox inputForm inputForm w230" rel="direccionCalle" />';
			$cells[3][0]->content = '<label>Número:</label>';
			$cells[3][1]->content = '<input id="sucursalInputNumero" class="textbox inputFormSuc inputForm w65" maxlength="5" rel="direccionNumero" />
							<label>Piso:</label>
							<input id="sucursalInputPiso" class="textbox inputFormSuc inputForm w25" maxlength="3" rel="direccionPiso" />
							<label>Dpto:</label>
							<input id="sucursalInputDpto" class="textbox inputFormSuc inputForm w25" maxlength="3" rel="direccionDepartamento" />';
			$cells[4][0]->content = '<label>País:</label>';
			$cells[4][1]->content = '<input id="sucursalInputPais" class="textbox autoSuggestBox inputForm w230" name="Pais" alt="" rel="direccionPais" />';
			$cells[5][0]->content = '<label>Provincia:</label>';
			$cells[5][1]->content = '<input id="sucursalInputProvincia" class="textbox autoSuggestBox inputForm w230" name="Provincia" linkedTo="sucursalInputPais,Pais"  alt=""  rel="direccionProvincia" />';
			$cells[6][0]->content = '<label>Localidad:</label>';
			$cells[6][1]->content = '<input id="sucursalInputLocalidad" class="textbox autoSuggestBox inputForm w125" name="Localidad" linkedTo="sucursalInputPais,Pais;sucursalInputProvincia,Provincia" alt="" rel="direccionLocalidad" />
									<label>CP:</label>
									<input id="sucursalInputCP" class="textbox inputFormSuc inputForm w35" maxlength="4" rel="direccionCodigoPostal" />
									<a class="boton " href="#" onclick="buscarCoordenadas();" title="Buscar" style="vertical-align: -webkit-baseline-middle;"><img src="/img/botones/25/buscar.gif"></a>';
			$cells[7][0]->content = '<label>Zona de transporte:</label>';
			$cells[7][1]->content = '<input id="sucursalInputZonaTransporte" class="textbox autoSuggestBox inputForm w230" name="ZonaTransporte"  rel="zonaTransporte" />';
			$cells[8][0]->content = '<label>Fax:</label>';
			$cells[8][1]->content = '<input id="sucursalInputFax" class="textbox inputForm w230" rel="fax" />';
			$cells[9][0]->content = '<label>Horario de atención:</label>';
			$cells[9][1]->content = '<input id="sucursalhorarioDeAtencion" class="textbox inputForm inputForm w230" name="HorarioAtencion" alt="" rel="horarioAtencion" validate="RangoHora" />';
			$cells[10][0]->content = '<label>Horario de entrega:</label>';
			$cells[10][1]->content = '<input id="sucursalHorarioEntrega1" class="textbox inputForm w100" name="HorarioAtencion" alt="" rel="horarioEntrega1" validate="RangoHora" /> y <input id="sucursalHorarioEntrega2" class="textbox inputForm w100" name="HorarioAtencion" alt="" rel="horarioEntrega2" validate="RangoHora" />';
			$cells[11][0]->content = '<label>Punto de venta:</label>';
			$cells[11][1]->content = '<div id="radioGroupPuntoVenta" class="customRadio" default="rdPuntoVentaN">' .
					'<input id="rdPuntoVentaS" class="textbox" type="radio" name="radioGroupPuntoVenta" value="S" rel="esPuntoDeVenta" /><label for="rdPuntoVentaS">S</label>' .
					'<input id="rdPuntoVentaN" class="textbox" type="radio" name="radioGroupPuntoVenta" value="N" rel="esPuntoDeVenta" /><label for="rdPuntoVentaN">N</label></div>';
				
			$tabla->create();//impresion
		?>
	</div>
	<div id='divSucursales2' class='fRight pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 11, 'cantCols' => 2, 'id' => 'tablaDatos2', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);
			//imprime campo derecha
			$cells[0][0]->content = '<label>Teléfono 1:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="sucursalInputTelefono1" class="textbox inputForm w230" maxlength="12" rel="telefono1" />';
			$cells[0][1]->style->width = '250px';
			$cells[1][0]->content = '<label>Teléfono 2:</label>';
			$cells[1][1]->content = '<input id="sucursalInputTelefono2" class="textbox inputForm w230" maxlength="12" rel="telefono2" />';
			$cells[2][0]->content = '<label>Celular:</label>';
			$cells[2][1]->content = '<input id="sucursalInputCelular" class="textbox inputForm w230" maxlength="12" rel="celular" />';
			$cells[3][0]->content = '<label>Email:</label>';
			$cells[3][1]->content = '<input id="sucursalInputEmail1" class="textbox inputForm w230" validate="Email" rel="email" />';
			$cells[4][0]->content = '<label>Sucursal entrega:</label>';
			$cells[4][1]->content = '<input id="sucursalInputEntrega" class="textbox autoSuggestBox inputForm w230" name="Sucursal" linkedTo="inputBuscarCliente,Cliente"  alt="" rel="sucursalEntrega" />';
			$cells[5][0]->content = '<label>Reparto:</label>';
			$cells[5][1]->content = '<input id="sucursalInputReparto" class="textbox inputForm w230" rel="reparto" />';
			$cells[6][0]->content = '<label>Transporte:</label>';
			$cells[6][1]->content = '<input id="sucursalInputTransporte" class="textbox autoSuggestBox inputForm w230" name="Transporte"  alt="" rel="transporte" />';			
			$cells[7][0]->content = '<label>Vendedor:</label>';
			$cells[7][1]->content = '<input id="sucursalInputVendedor" class="textbox autoSuggestBox inputForm w230" name="Vendedor"  alt="" rel="vendedor" />';			
			$cells[8][0]->content = '<label>Observaciones:</label>';
			$cells[8][1]->content = '<textarea id="sucursalinputObservaciones" class="textbox inputForm w230" rel="observaciones"></textarea>';
			$cells[9][0]->content = '<label>Latitud:</label>';
			$cells[9][1]->content = '<input id="sucursalInputLatitud" class="textbox inputForm w230 noEditable" rel="direccionLatitud" />';
			$cells[10][0]->content = '<label>Longitud:</label>';
			$cells[10][1]->content = '<input id="sucursalInputLongitud" class="textbox inputForm w230 noEditable" rel="direccionLongitud" />';

			$tabla->create();//impresion
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div><!--campos de busqueda  -->
			<label class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar w200' name='Cliente' alt='' />
		</div>
		<div>
			<label class='filtroBuscar'>Sucursal:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Sucursal' linkedTo='inputBuscarCliente,Cliente' alt='' />
		</div>
	</div><!-- fin campos busqueda -->
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/sucursales/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/sucursales/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/sucursales/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
