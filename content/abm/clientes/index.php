<?php

$cliente = false;
try {
	if (Funciones::get('id'))
		$cliente = Factory::getInstance()->getClienteTodos(Funciones::toInt(Funciones::get('id')));
	elseif (Funciones::get('buscarCuit')) {
		$clientes = Factory::getInstance()->getListObject('ClienteTodos', 'cuit = ' . Datos::objectToDB(Funciones::get('buscarCuit')));
		if (count($clientes) == 1)
			$cliente = $clientes[0];
	}
} catch (Exception $ex) {
	$cliente = false;
}

?>

<style>
	#divSucursales {
		height: 440px;
	}
	#divContactos {
		height: 440px;
	}
</style>

<!-- Incluyo las librerías de gmaps -->
<script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/gmaps/gmaps.js'></script>
<script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/gmaps/jsapi.js'></script>
<script type='text/javascript'>
	var haySucursalesNuevas = 0,
		esCliente = <?php echo (Usuario::logueado()->esCliente() ? 'true' : 'false'); ?>,
		esVendedor = <?php echo (Usuario::logueado()->esVendedor() ? 'true' : 'false'); ?>;

	$(document).ready(function(){
		tituloPrograma = 'Clientes';
		$('#inputCuit').blur(function(){setTimeout('validarCuit();', funciones.autoSuggestBoxDelay);});
		cambiarModo('inicio');
		<?php echo ($cliente ? 'buscar("' . $cliente->id . '");' : ''); ?>

		$('.solapas').solapas({fixedHeight: 480, heightSolapas: 28, selectedItem: 0});
		//$('.solapas').solapas({fixedHeight: 400, heightSolapas: 47, precall: loadMe}).restart();

		<?php if (Funciones::get('idCliente')) { ?>
		$('#inputBuscar, #inputBuscar_selectedValue').val(<?php echo Funciones::get('idCliente'); ?>).blur();
		buscar();
		<?php } ?>

		$('#inputEntregarSucEntrega').change(function(){
			if($(this).isChecked()) {
				$('#inputSucursalEntrega').show();
			} else {
				$('#inputSucursalEntrega').hide();
				$('#inputSucursalEntrega').val('').autoComplete();
			}
		});
		$('#inputEntregarSucEntrega').change();
	});

	function manejarClienteVendedor(modo){
		if (esCliente) {
			$('#liTabCreditos').invisible();
			$('#liTabComercial').invisible();
			$('#liTabContactos').invisible();
		} else if (esVendedor) {
			if (modo == 'agregar') {
				
				$('#liTabDatos').visible();
				$('#liTabCreditos').visible();
				$('#radioGroupCalificacion').disableRadioGroup();
				$('#inputLimiteDeCredito').disable();
				$('#inputDescuentoEspecial').disable();
				$('#liTabComercial').invisible();
				$('#liTabSucursales').invisible();
				$('#liTabContactos').invisible();
			} else if (modo == 'editar') {
				
				$('#liTabDatos').visible();
				$('#liTabCreditos').invisible();
				$('#liTabComercial').invisible();
				$('#liTabSucursales').visible();
				$('#liTabContactos').invisible();
				//Campos que no pueden editar los vendedores
				$('#inputRazonSocial').disable();
				$('#inputNombre').disable();
				$('#radioGroupCondicionIva').disableRadioGroup();
			} else {
				$('#liTabDatos').visible();
				$('#liTabCreditos').visible();
				$('#liTabComercial').visible();
				$('#liTabSucursales').visible();
				$('#liTabContactos').visible();
			}
		}
	}

	function validarCuit(){
		var cuit = $('#inputCuit').val();
		if (cuit != '') {
			$.postJSON('/content/abm/clientes/validarCuit.php?cuit=' + cuit, function(json){
				switch (funciones.getJSONType(json)){
					case funciones.jsonSuccess:
						break;
					case funciones.jsonNull:
					case funciones.jsonEmpty:
					case funciones.jsonError:
					default:
						$.error(funciones.getJSONMsg(json));
						$('#inputCuit').val('').focus();
						break;
				}
			});
		}
	}

	function getSucursales(idCliente, sucursales){
		$('#divSucursales').html('');
		var newHtml = '<div class="acordeon">',
			ii = 0,
			jj = 0;
		if (typeof sucursales === 'undefined' || sucursales.length == 0) {
			$.get(funciones.controllerUrl('getSucursal'), function(div){
				newHtml += div;
				newHtml += '</div>';
				$('#divSucursales').html(newHtml);
			});
		} else {
			$(sucursales).each(function(){
				var idSucursal = this.id;
				$.get(funciones.controllerUrl('getSucursal', {idCliente: idCliente, idSucursal: idSucursal}), function(div){
					newHtml += div;
					if (jj == (sucursales.length - 1)){
						$.get(funciones.controllerUrl('getSucursal'), function(div){
							newHtml += div;
							newHtml += '</div>';
							$('#divSucursales').html(newHtml);
							$('#divSucursales .inputFormSuc').disable();
							funciones.delay('$("#divSucursales .customRadio").disableRadioGroup();');
						});
					}
					jj++;
				});
				ii++;
			});
		}
	}

	function getContactos(idCliente){
		$('#divContactos').html('');
		$.get('/content/abm/clientes/getContactos.php?idCliente=' + idCliente, function(html){
			$('#divContactos').html('<div class="acordeon">' + html + '</div>');
		});
	}

	function limpiarScreen(){
		$('#ayuda').text('');
		$('#divAutorizaciones').html('');
		$('#labelAutorizaciones').text('');
		$('#divSucursales').html('');
		$('#divContactos').html('');
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined') {
			$('#inputBuscar').val(idBuscar).autoComplete();
		}
		var url = '/content/abm/clientes/buscar.php?idCliente=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'El cliente "' + $('#inputBuscar_selectedName').val() + '" no existe o no tiene permiso para visualizarlo.',
			cbSuccess = function(json){
				$('#inputSucursalFiscal').attr('alt', 'idCliente=' + json.cliente.id);
				$('#inputSucursalCentral').attr('alt', 'idCliente=' + json.cliente.id);
				$('#inputSucursalCobranza').attr('alt', 'idCliente=' + json.cliente.id);
				$('#divCampos').loadJSON(json);
				getSucursales(json.cliente.id, json.sucursales);
				getContactos(json.cliente.id);
				$('#labelAutorizaciones').text('Autorizaciones');
				funciones.generoDivAutorizaciones(json.cliente, '<?php echo Usuario::logueado()->id; ?>');
				cambiarModo('buscar');
				if (<?php echo ($cliente ? 'json.cliente.id == "' . $cliente->id . '"' : 'false'); ?> && <?php echo (Funciones::get('clickSucu') ? 'true' : 'false'); ?>) {
					$('#ayuda').text('Ingrese las sucursales');
					$('#liTabSucursales').click();
				}
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function miniEditarClick(idSuc){
		$('#btnEditar_25_' + idSuc).hide();
		$('#btnAceptar_25_' + idSuc).show();
		$('#btnCancelar_25_' + idSuc).show();
		$('#btnBorrar_25_' + idSuc).show();
		$('.inputForm_' + idSuc).enable();
		$('#sucursal_' + idSuc + '_inputNombre').focus();
		$('#sucursal_' + idSuc + ' .customRadio').enableRadioGroup();
	}

	function miniBorrarClick(idSuc){
		$.confirm('¿Está seguro que desea borrar la sucursal "' + $('#sucursal_' +  idSuc + '_inputNombre').val() + '"?', function(r){
			if (r == funciones.si){
				$('#sucursal_' + idSuc).attr('borrar', '1');
				$('#sucursal_' + idSuc).slideUp();
				if ($('#sucursal_' + idSuc).attr('nuevasucursal')) {
					$('#sucursal_' + idSuc).find('.inputFormSuc').val('');
					$('#sucursal_' + idSuc).find('.autoSuggestBox').limpiarAutoSuggestBox();
					$('#sucursal_n_inputNombre').val('NUEVA...');
					$('#sucursal_' + idSuc).slideDown();
				}
			}
		});
	}

	function miniAceptarClick(idSuc){
		$('#btnEditar_25_' + idSuc).show();
		$('#btnAceptar_25_' + idSuc).hide();
		$('#btnCancelar_25_' + idSuc).hide();
		$('#btnBorrar_25_' + idSuc).hide();
		$('.inputForm_' + idSuc).disable();
		$('#sucursal_' + idSuc + ' .customRadio').disableRadioGroup();
		$('#btnEditar_25_' + idSuc).focus();
		if ($('#sucursal_' + idSuc).attr('nuevasucursal')){
			$('#sucursal_' + idSuc + ' .tituloSeccion').text($('#sucursal_' + idSuc + '_inputNombre').val());
		}
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar el nombre del cliente';
		if ($('#inputRazonSocial').val() == '')
			return 'Debe ingresar la razón social del cliente';
		if ($('#inputCuit').val() == '')
			return 'Debe ingresar el cuit del cliente';
		if ($('#radioGroupCondicionIva').radioVal() == '')
			return 'Debe seleccionar una condición de IVA';
		if ($('#inputBuscar_selectedValue').val() == '') {
			if ($('#inputCalle').val() == '')
				return 'Debe ingresar la calle del cliente';
			if ($('#inputNumero').val() == '')
				return 'Debe ingresar el número de dirección del cliente';
			if ($('#inputPais_selectedValue').val() == '')
				return 'Debe ingresar el país del cliente';
			if ($('#inputProvincia_selectedValue').val() == '')
				return 'Debe ingresar la provincia del cliente';
			if ($('#inputLocalidad_selectedValue').val() == '')
				return 'Debe ingresar la localidad del cliente';
			if ($('#inputContactoNombre').val() == '')
				return 'Debe ingresar el nombre del contacto';
			if ($('#inputContactoApellido').val() == '')
				return 'Debe ingresar el apellido del contacto';
			if ($('#inputDni').val() == '')
				return 'Debe ingresar el DNI del contacto';
		}
		if ($('#inputTelefono').val() == '')
			return 'Debe ingresar el teléfono del cliente';
		if ($('#inputEmail').val() == '')
			return 'Debe ingresar el email del cliente';
		<?php if (!Usuario::logueado()->esVendedor()) { ?>
		if ($('#inputVendedor_selectedValue').val() == '') {
			$('#liTabComercial').click();
			return 'Debe elegir un vendedor';
		}
		<?php } ?>
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/clientes/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar(),
			function(){ //Callback success
				if (haySucursalesNuevas || url == 'agregar')
					window.location = './?clickSucu=1&id' + (url == 'editar' ? '=' + $('#inputBuscar_selectedValue').val() : 'Cuit=' + $('#inputCuit').val());
				else
					window.location = './';
			}, 
			function(){ //Callback alert
				window.location = './?buscarCuit=' + $('#inputCuit').val();
			}
		);
	}

	function armoObjetoGuardar(){
		var cli = {cliente: {
			idCliente: $('#inputBuscar_selectedValue').val(),
			nombre: $('#inputNombre').val(),
			razonSocial: $('#inputRazonSocial').val(),
			cuit: $('#inputCuit').val(),
			dni: $('#inputDni').val(),
			condicionIva: $('#radioGroupCondicionIva').radioVal(),
			idRubro: $('#inputRubro_selectedValue').val(),
			sucursalFiscal: $('#inputSucursalFiscal_selectedValue').val(),
			sucursalCentral: $('#inputSucursalCentral_selectedValue').val(),
			sucursalCobranza: $('#inputSucursalCobranza_selectedValue').val(),
			sucursalEntrega: $('#inputSucursalEntrega_selectedValue').val(),
			entregarSucEntrega: ($('#inputEntregarSucEntrega').isChecked() ? 'S' : 'N'),
			calle: $('#inputCalle').val(),
			numero: $('#inputNumero').val(),
			piso: $('#inputPiso').val(),
			dpto: $('#inputDpto').val(),
			codPostal: $('#inputCodPostal').val(),
			idPais: $('#inputPais_selectedValue').val(),
			idProvincia: $('#inputProvincia_selectedValue').val(),
			idLocalidad: $('#inputLocalidad_selectedValue').val(),
			telefono1: $('#inputTelefono').val(),
			interno1: $('#inputInterno').val(),
			email: $('#inputEmail').val(),
			transporte: $('#inputTransporte_selectedValue').val(),
			nombreContacto: $('#inputNombreContacto').val(),
			apellidoContacto: $('#inputApellidoContacto').val(),
			plazoMaximo: $('#inputTolerancia').val(),
			formaDePago: $('#inputFormaDePago_selectedValue').val(),
			calificacion: $('#radioGroupCalificacion').radioVal(),
			limiteDeCredito: $('#inputLimiteDeCredito').val(),
			primeraEntega: $('#inputPrimeraEntrega').val(),
			descuentoEspecial: $('#inputDescuentoEspecial').val(),
			observacionesCobranza: $('#inputObservacionesCobranza').val(),
			observaciones: $('#inputObservaciones').val(),
			marcasQueComercializa: $('#inputMarcasQueComercializa').val(),
			referenciasBancarias: $('#inputReferenciasBancarias').val(),
			referenciasComerciales: $('#inputReferenciasComerciales').val(),
			idGrupoEmpresa: $('#inputGrupoEmpresa_selectedValue').val(),
			listaAplicable: $('#radioGroupListaAplicable').radioVal(),
			idVendedor: $('#inputVendedor_selectedValue').val(),
			sucursales: armoSucursales()
		}};
		return cli;
	}

	function armoSucursales(){
		var sucs = [];
		$('#divSucursales .acordeon>div').each(function(){
			var idSuc = $(this).attr('idSucursal');
			var nueva = ($(this).attr('nuevasucursal') ? true : false);
			var borrar = ($(this).attr('borrar') ? true : false);
			var nombre = $(this).find('#sucursal_' + idSuc + '_inputNombre').val();
			if (nueva && nombre != 'NUEVA...' && !borrar)
				haySucursalesNuevas = 1;
			var suc = {
				esNueva: nueva,
				borrar: borrar,
				id: idSuc,
				nombre: nombre,
				calle: $(this).find('#sucursal_' + idSuc + '_inputCalle').val(),
				numero: $(this).find('#sucursal_' + idSuc + '_inputNumero').val(),
				piso: $(this).find('#sucursal_' + idSuc + '_inputPiso').val(),
				dpto: $(this).find('#sucursal_' + idSuc + '_inputDpto').val(),
				pais: $(this).find('#sucursal_' + idSuc + '_inputPais_selectedValue').val(),
				provincia: $(this).find('#sucursal_' + idSuc + '_inputProvincia_selectedValue').val(),
				localidad: $(this).find('#sucursal_' + idSuc + '_inputLocalidad_selectedValue').val(),
				codPostal: $(this).find('#sucursal_' + idSuc + '_inputCodPostal').val(),
				telefono1: $(this).find('#sucursal_' + idSuc + '_inputTelefono1').val(),
				telefono2: $(this).find('#sucursal_' + idSuc + '_inputTelefono2').val(),
				celular: $(this).find('#sucursal_' + idSuc + '_inputCelular').val(),
				fax: $(this).find('#sucursal_' + idSuc + '_inputFax').val(),
				email: $(this).find('#sucursal_' + idSuc + '_inputEmail').val(),
				horarioDeAtencion: $(this).find('#sucursal_' + idSuc + '_inputHorarioDeAtencion').val(),
				esPuntoDeVenta: $(this).find('#sucursal_' + idSuc + '_radioPuntoVenta').radioVal(),
				sucursalEntrega: $(this).find('#sucursal_' + idSuc + '_inputSucursalEntrega_selectedValue').val(),
				reparto: $(this).find('#sucursal_' + idSuc + '_inputReparto').val(),
				transporte: $(this).find('#sucursal_' + idSuc + '_inputTransporte_selectedValue').val(),
				vendedor: $(this).find('#sucursal_' + idSuc + '_inputVendedor_selectedValue').val(),
				observaciones: $(this).find('#sucursal_' + idSuc + '_inputObservaciones').val(),
				latitud: $(this).find('#sucursal_' + idSuc + '_inputLatitud').val(),
				longitud: $(this).find('#sucursal_' + idSuc + '_inputLongitud').val()
			};
			sucs[sucs.length] = suc;
		});
		return sucs;
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el cliente "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/abm/clientes/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {idCliente: $('#inputBuscar_selectedValue').val()};
	}

	function autorizar(nro, bool) {
		var msg = '¿Está seguro que desea ' + (bool ? 'autorizar' : 'rechazar') + ' el cliente?</br></br>Motivo:',
			url = funciones.controllerUrl('autorizar');
		funciones.autorizar(msg, url, armoObjetoAutorizar(nro, bool));
	}

	function armoObjetoAutorizar(nro, bool, motivo){
		return {
			idCliente: $('#inputBuscar_selectedValue').val(),
			numeroDeAutorizacion: nro,
			autoriza: (bool ? 'S' : 'N'),
			motivo: motivo
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('.botonera.mini').hide();
				$('.trDirecciones').show();
				$('.trFiscalCentral').hide();
				$('#liTabDatos').click();
				break;
			case 'buscar':
				$('#inputCodigoCliente').show();
				$('#lblCod').show();
				$('.botonera.mini').each(function(){
					$(this).find('[title="Aceptar"]').each(function(){
						$(this).click();
					});
					$(this).hide();
				});
				$('.trDirecciones').hide();
				$('.trFiscalCentral').show();
				$('.contactos').hide();
				$('.trTransporte').hide();
				$('#inputEntregarSucEntrega').change();
				break;
			case 'editar':
				$('#inputCodigoCliente').disable();
				<?php if (Usuario::logueado()->esVendedor() || Usuario::logueado()->esCliente()) { ?>
				if ($('#divCreditos').isVisible() || $('#divComercial').isVisible() || $('#divContactos').isVisible())
					$('#liTabDatos').click();
				<?php } ?>
				$('#inputProveedor').disable();
				$('.botonera.mini').removeClass('hidden').show();
				$('.trDirecciones').hide();
				$('.trFiscalCentral').show();
				$('inputNombreContacto').hide();
				$('.contactos').hide();
				$('#divSucursales .customRadio').disableRadioGroup();
				$('#inputRazonSocial').focus();
				break;
			case 'agregar':
				$('#inputCodigoCliente').hide();
				$('#lblCod').hide();
				$('.botonera.mini').removeClass('hidden').show();
				$('.trDirecciones').show();
				$('.trFiscalCentral').hide();
				$('#ayuda').text('* Ingrese los datos correspondientes al domicilio fiscal');
				$('.contactos').show();
				$('.noEnable').disable();
				$('#inputRazonSocial').focus();
				break;
		}
		manejarClienteVendedor(modo);
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divCampos' class='w100p pantalla solapas'>
		<ul class="titulos">
			<li id='liTabDatos'>Datos</li>
			<li id='liTabCreditos'>Créditos</li>
			<li id='liTabComercial'>Comercial</li>
			<li id='liTabSucursales'>Sucursales</li>
			<li id='liTabContactos'>Contactos y Usuarios</li>
		</ul>
		<div>
			<div>
				<div id='divDatos1' class='fLeft'>
					<?php
					$tabla = new HtmlTable(array('cantRows' => 19, 'cantCols' => 2, 'id' => 'tablaDatos11', 'class' => 'cliente', 'cellSpacing' => 5));
					$tabla->getRowCellArray($rows, $cells);
					for ($i = 0; $i < $tabla->cantRows; $i++)
						if ($i >= 7 && $i <= 10)
							$rows[$i]->class = 'trFiscalCentral';
						elseif ($i >= 11 && $i <= 15)
							$rows[$i]->class = 'trDirecciones';
						elseif ($i == 16)
							$rows[$i]->class = 'contactos';

					$cells[0][0]->content = '<label  id="lblCod">Código Cliente:</label>';
					$cells[0][0]->style->width = '135px';
					$cells[0][1]->content = '<input id="inputCodigoCliente" class="textbox obligatorio inputForm w230" rel="id" />';
					$cells[0][1]->style->width = '260px';
					$cells[1][0]->content = '<label>Razón social:</label>';
					$cells[1][1]->content = '<input id="inputRazonSocial" class="textbox obligatorio inputForm w230" rel="razonSocial" />';
					$cells[2][0]->content = '<label>Nombre fantasía:</label>';
					$cells[2][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w230" rel="nombre" />';
					$cells[3][0]->content = '<label>CUIT:</label>';
					$cells[3][1]->content = '<input id="inputCuit" class="textbox obligatorio inputForm w230 noEditable" validate="Cuit" rel="cuit" />';
					$cells[4][0]->content = '<label>DNI:</label>';
					$cells[4][1]->content = '<input id="inputDni" class="textbox obligatorio inputForm w230 " validate="Dni" rel="dni" />';
					$cells[5][0]->content = '<label>Condición IVA:</label>';
					$cells[5][1]->content = '<div id="radioGroupCondicionIva" class="customRadio" rel="condicionIva"><input id="rdCondicionIvaRI" class="textbox inputCondicionIva" type="radio" name="radioGroupCondicionIva" value="RI" rel="id" /><label for="rdCondicionIvaRI">R.I.</label>' .
											'<input id="rdCondicionIvaMO" class="textbox inputCondicionIva" type="radio" name="radioGroupCondicionIva" value="MO" rel="id" /><label for="rdCondicionIvaMO">MO.</label>' .
											'<input id="rdCondicionIvaEX" class="textbox inputCondicionIva" type="radio" name="radioGroupCondicionIva" value="EX" rel="id" /><label for="rdCondicionIvaEX">Exento/Exp</label></div>';
					$cells[6][0]->content = '<label>Rubro:</label>';
					$cells[6][1]->content = '<input id="inputRubro" class="textbox autoSuggestBox inputForm w230" name="Rubro" rel="rubro" />';
					$cells[7][0]->content = '<label>Dom. fiscal:</label>';
					$cells[7][1]->content = '<input id="inputSucursalFiscal" class="textbox autoSuggestBox inputForm w230" name="Sucursal" linkedTo="inputBuscar,Cliente" alt="" rel="sucursalFiscal" />';
					$cells[8][0]->content = '<label>Casa central:</label>';
					$cells[8][1]->content = '<input id="inputSucursalCentral" class="textbox autoSuggestBox inputForm w230" name="Sucursal" linkedTo="inputBuscar,Cliente" alt="" rel="sucursalCentral" />';
					$cells[9][0]->content = '<label>Dom. cobranza:</label>';
					$cells[9][1]->content = '<input id="inputSucursalCobranza" class="textbox autoSuggestBox inputForm w230" name="Sucursal" linkedTo="inputBuscar,Cliente" alt="" rel="sucursalCobranza" />';

					$cells[10][0]->content = '<label>Dom. entrega:</label>';
					$cells[10][1]->content = '<input type="checkbox" id="inputEntregarSucEntrega" class="textbox koiCheckbox inputForm" rel="entregarSucEntrega" >
											 <input id="inputSucursalEntrega" class="textbox autoSuggestBox inputForm w200" name="Sucursal" linkedTo="inputBuscar,Cliente" alt="" rel="sucursalEntrega" />';

					$cells[11][0]->content = '<label>*Calle:</label>';
					$cells[11][1]->content = '<input id="inputCalle" class="textbox obligatorio inputForm w230" />';
					$cells[12][0]->content = '<label>*Número:</label>';
					$cells[12][1]->content = '<input id="inputNumero" class="textbox obligatorio inputForm w65" maxlength="5" validate="EnteroPositivo" />
											<label>Piso:</label>
											<input id="inputPiso" class="textbox inputForm w25" maxlength="3" />
											<label>Dpto:</label>
											<input id="inputDpto" class="textbox inputForm w25" maxlength="3" />';
					$cells[13][0]->content = '<label>*País:</label>';
					$cells[13][1]->content = '<input id="inputPais" class="textbox obligatorio autoSuggestBox inputForm w230" name="Pais" alt="" />';
					$cells[14][0]->content = '<label>*Provincia:</label>';
					$cells[14][1]->content = '<input id="inputProvincia" class="textbox obligatorio autoSuggestBox inputForm w230" name="Provincia" linkedTo="inputPais,Pais" alt="" />';
					$cells[15][0]->content = '<label>*Localidad:</label>';
					$cells[15][1]->content = '<input id="inputLocalidad" class="textbox obligatorio autoSuggestBox inputForm w135" name="Localidad" linkedTo="inputPais,Pais;inputProvincia,Provincia" alt="" />
											<label>CP:</label>
											<input id="inputCodPostal" class="textbox inputForm w50" />';
					$cells[16][0]->content = '<label>Contacto:</label>';
					$cells[16][1]->content = '<input id="inputNombreContacto" class="textbox obligatorio inputForm w110" placeholder="Nombre" /><input id="inputApellidContacto" class="textbox obligatorio inputForm w105" placeholder="Apellido" />';
					$cells[17][0]->content = (!Usuario::logueado()->esCliente()) ? '<label id="labelAutorizaciones">Autorizaciones</label>' : '';
					$cells[17][1]->content = ' ';
					$cells[18][0]->colspan = 2;
					$cells[18][0]->content = '<div id="divAutorizaciones" class="customScroll pRight10"></div>';

					$tabla->create();
					?>
				</div>
				<div id='divDatos2' class='fRight'>
					<?php
					$tabla = new HtmlTable(array('cantRows' => 7, 'cantCols' => 2, 'id' => 'tablaDatos12', 'class' => 'cliente', 'cellSpacing' => 5));
					$tabla->getRowCellArray($rows, $cells);

					$rows[2]->class = 'trTransporte';

					$cells[0][0]->content = '<label>Teléfono:</label>';
					$cells[0][0]->style->width = '135px';
					$cells[0][1]->content = '<input id="inputTelefono" class="textbox obligatorio inputForm w135" maxlength="12" rel="telefono1" validate="Telefono" />
												<label>Int:</label>
												<input id="inputInterno" class="textbox inputForm w50" maxlength="4" rel="interno1" />';
					$cells[0][0]->style->width = '135px';
					$cells[1][0]->content = '<label>Email:</label>';
					$cells[1][1]->content = '<input id="inputEmail" class="textbox obligatorio inputForm w230" validate="Email" rel="email" />';

					$cells[2][0]->content = '<label>Transporte:</label>';
					$cells[2][1]->content = '<input id="inputTransporte" class="textbox obligatorio autoSuggestBox inputForm w230" name="Transporte" rel="transporte" />';

					$cells[3][0]->content = '<label>Observaciones:</label>';
					$cells[3][1]->content = '<textarea id="inputObservaciones" class="textbox inputForm w230" rel="observaciones" ></textarea>';
					$cells[4][0]->content = '<label>Marcas que comercializa:</label>';
					$cells[4][1]->content = '<textarea id="inputMarcasQueComercializa" class="textbox inputForm w230" rel="marcasQueComercializa" ></textarea>';
					$cells[5][0]->content = '<label>Referencias bancarias:</label>';
					$cells[5][1]->content = '<textarea id="inputReferenciasBancarias" class="textbox inputForm w230" rel="referenciasBancarias" ></textarea>';
					$cells[6][0]->content = '<label>Referencias comerciales:</label>';
					$cells[6][1]->content = '<textarea id="inputReferenciasComerciales" class="textbox inputForm w230" rel="referenciasComerciales" ></textarea>';

					$tabla->create();
					?>
				</div>
			</div>
			<div>
				<?php
				$tabla = new HtmlTable(array('id' => 'tablaDatos2', 'class' => 'cliente', 'cellSpacing' => 10));
				for ($i = 0; $i < 7; $i++) {
					$rows[$i] = new HtmlTableRow();
					$cells[$i][0] = new HtmlTableCell();
					$cells[$i][1] = new HtmlTableCell();
				}
				$cells[0][0]->content = '<label>Tolerancia:</label>';
				$cells[0][0]->style->width = '135px';
				$cells[0][1]->content = '<input id="inputTolerancia" class="textbox inputForm w230 ' . (Usuario::logueado()->tieneRol('creditos') ? '' : 'noEnable noEditable') . '" validate="Entero" rel="creditoPlazoMaximo" />';
				$cells[0][1]->style->width = '260px';
				$cells[1][0]->content = '<label>Forma de pago:</label>';
				$cells[1][1]->content = '<input id="inputFormaDePago" class="textbox autoSuggestBox inputForm w230 ' . (Usuario::logueado()->tieneRol('creditos') ? '' : 'noEnable noEditable') . '" name="FormaDePago" alt="" rel="creditoFormaDePago" />';
				$cells[2][0]->content = '<label>Calificación:</label>';
				$cells[2][1]->content = '<div id="radioGroupCalificacion" class="customRadio ' . (Usuario::logueado()->tieneRol('creditos') ? '' : 'noEnable noEditable') . '"><input id="rdCalificacion1" class="textbox inputCalificacion" type="radio" name="radioGroupCalificacion" value="1" rel="calificacion" /><label for="rdCalificacion1">1</label>' .
										'<input id="rdCalificacion2" class="textbox inputCalificacion" type="radio" name="radioGroupCalificacion" value="2" rel="calificacion" /><label for="rdCalificacion2">2</label>' .
										'<input id="rdCalificacion3" class="textbox inputCalificacion" type="radio" name="radioGroupCalificacion" value="3" rel="calificacion" /><label for="rdCalificacion3">3</label>' .
										'<input id="rdCalificacion4" class="textbox inputCalificacion" type="radio" name="radioGroupCalificacion" value="4" rel="calificacion" /><label for="rdCalificacion4">4</label>' .
										'<input id="rdCalificacion5" class="textbox inputCalificacion" type="radio" name="radioGroupCalificacion" value="5" rel="calificacion" /><label for="rdCalificacion5">5</label>' .
										'<input id="rdCalificacion6" class="textbox inputCalificacion" type="radio" name="radioGroupCalificacion" value="6" rel="calificacion" /><label for="rdCalificacion6">6</label>' .
										'</div>';
				$cells[3][0]->content = '<label>Límite de crédito:</label>';
				$cells[3][1]->content = '<input id="inputLimiteDeCredito" class="textbox inputForm w230 ' . (Usuario::logueado()->tieneRol('creditos') ? '' : 'noEnable noEditable') . '" rel="creditoLimite" />';
				$cells[4][0]->content = '<label>Descuento especial:</label>';
				$cells[4][1]->content = '<input id="inputDescuentoEspecial" class="textbox inputForm w230 ' . (Usuario::logueado()->tieneRol('creditos') ? '' : 'noEnable noEditable') . '" rel="creditoDescuentoEspecial" />';
				$cells[5][0]->content = '<label>Fecha últ. calif.:</label>';
				$cells[5][1]->content = '<label id="labelFechaUltCalif" class="w230" rel="fechaUltimaCalificacion"></label>';
				$cells[6][0]->content = '<label>Observaciones:</label>';
				$cells[6][1]->content = '<textarea id="inputObservacionesCobranza" class="textbox inputForm w230 ' . (Usuario::logueado()->tieneRol('creditos') ? '' : 'noEnable noEditable') . '" rel="observacionesCobranza"></textarea>';
				for($i = 0; $i < 7; $i++) {
					$rows[$i]->addCell($cells[$i][0]);
					$rows[$i]->addCell($cells[$i][1]);
					$tabla->addRow($rows[$i]);
				}
				$tabla->create();
				?>
			</div>
			<div>
				<?php
				$tabla = new HtmlTable(array('id' => 'tablaDatos3', 'class' => 'cliente', 'cellSpacing' => 10));
				for ($i = 0; $i < 4; $i++) {
					$rows[$i] = new HtmlTableRow();
					$cells[$i][0] = new HtmlTableCell();
					$cells[$i][1] = new HtmlTableCell();
				}
				$cells[0][0]->content = '<label>Primera entrega:</label>';
				$cells[0][0]->style->width = '135px';
				$cells[0][1]->content = '<input id="inputPrimeraEntrega" class="textbox inputForm w230" rel="creditoPrimeraEntrega" />';
				$cells[0][1]->style->width = '260px';
				$cells[1][0]->content = '<label>Grupo empresa:</label>';
				$cells[1][1]->content = '<input id="inputGrupoEmpresa" class="textbox autoSuggestBox inputForm w230" name="GrupoEmpresa" rel="grupoEmpresa" />';
				$cells[2][0]->content = '<label>Lista aplicable:</label>';
				$cells[2][1]->content = '<div id="radioGroupListaAplicable" class="customRadio" default="rdN"><input id="rdN" class="textbox inputListaAplicable" type="radio" name="radioGroupListaAplicable" value="N" rel="listaAplicable" /><label for="rdN">N</label>' .
										'<input id="rdD" class="textbox inputListaAplicable" type="radio" name="radioGroupListaAplicable" value="D" rel="listaAplicable" /><label for="rdD">D</label></div>';
				$cells[3][0]->content = '<label>Vendedor:</label>';
				$cells[3][1]->content = '<input id="inputVendedor" class="textbox autoSuggestBox inputForm w230 obligatorio" name="Vendedor" rel="vendedor" />';
				for($i = 0; $i < 4; $i++) {
					$rows[$i]->addCell($cells[$i][0]);
					$rows[$i]->addCell($cells[$i][1]);
					$tabla->addRow($rows[$i]);
				}
				$tabla->create();
				?>
			</div>
			<div>
				<div id='divSucursales' class='customScroll'>
				</div>
			</div>
			<div>
				<div id='divContactos' class='customScroll'>
				</div>
			</div>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div class='fLeft pLeft10'>
		<label id='ayuda'></label>
	</div>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='ClienteTodos' alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/clientes/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/clientes/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/clientes/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
