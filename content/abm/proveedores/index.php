<?php

$proveedor = false;
try {
	if (Funciones::get('id')) {
		$proveedor = Factory::getInstance()->getProveedorTodos(Funciones::toInt(Funciones::get('id')));
	}
} catch (Exception $ex) {
}

?>

<style>
	#divContactos {
		height: 440px;
	}
</style>

<script type='text/javascript'>
	$(document).ready(function () {
		tituloPrograma = 'Proveedores';
		$('#inputCuit').blur(function(){setTimeout('validarCuit();', funciones.autoSuggestBoxDelay);});
		cambiarModo('inicio');
		<?php echo ($proveedor ? 'buscar("' . $proveedor->id . '");' : ''); ?>
		$('.trConceptoImpuestoGanancias').hide();

		$('#inputRetenerImpuestoGanancias').click(function () {
			if ($('#inputRetenerImpuestoGanancias').isChecked()) {
				$('.trConceptoImpuestoGanancias').show();
			} else {
				$('.trConceptoImpuestoGanancias').hide();
			}
		});
		$('.solapas').solapas({fixedHeight: 460, heightSolapas: 28, selectedItem: 0});
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined') {
			return $('#inputBuscar').val(idBuscar).blur();
		}
		if ($('#inputBuscar_selectedValue').val() == '') {
			return $('#inputBuscar').val('');
		}
		var url = '/content/abm/proveedores/buscar.php?id=' + $('#inputBuscar_selectedValue').val(), msgError = 'El proveedor "' + $('#inputBuscar_selectedName').val() + '" no existe.', cbSuccess = function (json) {
				$('#tablaDatos, #tablaDatos2').loadJSON(json);
				if (json.retenerImpuestoGanancias == 'S') {
					$('#inputConceptoImpuestoGanancias').val(json.conceptoRetenGanancias).autoComplete();
					$('.trConceptoImpuestoGanancias').show();
				}
				getContactos(json.id);
				$('#labelAutorizaciones').text('Autorizaciones');
				funciones.generoDivAutorizaciones(json, '<?php echo Usuario::logueado()->id; ?>');
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function getContactos(idProveedor) {
		$('#divContactos').html('');
		$.get('/content/abm/proveedores/getContactos.php?idProveedor=' + idProveedor, function (html) {
			$('#divContactos').html('<div class="acordeon">' + html + '</div>');
		});
	}

	function limpiarScreen() {
		$('#divAutorizaciones').html('');
		$('#labelAutorizaciones').text('');
	}

	function hayErrorGuardar() {
		if ($('#inputNombre').val() == '') {
			return 'Debe ingresar un nombre para el proveedor';
		}
		if ($('#inputRazonSocial').val() == '') {
			return 'Debe ingresar una razón social para el proveedor';
		}
		if ($('#inputCondicionIva').val() == '') {
			return 'Debe ingresar una condición de IVA para el proveedor';
		}
		if ($('#inputImputacionGeneral').val() == '') {
			return 'Debe ingresar una imputación general para el proveedor';
		}
		if ($('#inputImputacionEspecifica').val() == '') {
			return 'Debe ingresar una imputación específica para el proveedor';
		}
		if ($('#inputImputacionHaber').val() == '') {
			return 'Debe ingresar una imputación haber para el proveedor';
		}
		if ($('#inputPlazoPago').val() == '') {
			return 'Debe ingresar un plazo de pago para el proveedor';
		}
		if ($('#inputRetenerImpuestoGanancias').isChecked()) {
			if ($('#inputConceptoImpuestoGanancias_selectedValue').val() == '') {
				return 'Debe ingresar un concepto de retencion de ganancias';
			}
		}

		return false;
	}

	function validarCuit(){
		var cuit = $('#inputCuit').val();
		if (cuit != '') {
			$.postJSON('/content/abm/proveedores/validarCuit.php?cuit=' + cuit, function(json){
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

	function guardar() {
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/abm/proveedores/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar() {
		return {
			id: $('#inputBuscar_selectedValue').val(),
			razonSocial: $('#inputRazonSocial').val(),
			rubro: $('#inputRubro').val(),
			calle: $('#inputCalle').val(),
			numero: $('#inputNumero').val(),
			piso: $('#inputPiso').val(),
			dpto: $('#inputDpto').val(),
			pais: $('#inputPais_selectedValue').val(),
			provincia: $('#inputProvincia_selectedValue').val(),
			localidad: $('#inputLocalidad_selectedValue').val(),
			codPostal: $('#inputCP').val(),
			telefono1: $('#inputTelefono1').val(),
			telefono2: $('#inputTelefono2').val(),
			email: $('#inputEmail').val(),
			fax: $('#inputFax').val(),
			horarioDeAtencion: $('#inputHorarioAtencion').val(),
			paginaWeb: $('#inputPaginaWeb').val(),
			tipoProveedor: $('#inputTipoProveedor_selectedValue').val(),
			cuit: $('#inputCuit').val(),
			transporte: $('#inputTransporte_selectedValue').val(),
			condicionIva: $('#inputCondicionIva_selectedValue').val(),
			nombre: $('#inputNombre').val(),
			retenerIva: $('#inputRetenerIva').val(),
			observaciones: $('#inputObservaciones').val(),
			imputacionGeneral: $('#inputImputacionGeneral_selectedValue').val(),
			imputacionEspecifica: $('#inputImputacionEspecifica_selectedValue').val(),
			imputacionHaber: $('#inputImputacionHaber_selectedValue').val(),
			retenerImpuestoGanancias: ($('#inputRetenerImpuestoGanancias').isChecked() ? 'S' : 'N'),
			conceptoImpuestoGanancias: $('#inputConceptoImpuestoGanancias_selectedValue').val(),
			plazoPago: $('#inputPlazoPago').val()
		};
	}

	function borrar() {
		var msg = '¿Está seguro que desea borrar el proveedor "' + $('#inputBuscar_selectedName').val() + '"?', url = '/content/abm/proveedores/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar() {
		return {
			id: $('#inputBuscar_selectedValue').val()
		};
	}

	function autorizar(nro, bool) {
		var msg = '¿Está seguro que desea ' + (bool ? 'autorizar' : 'rechazar') + ' el proveedor?</br></br>Motivo:',
			url = funciones.controllerUrl('autorizar');
		funciones.autorizar(msg, url, armoObjetoAutorizar(nro, bool));
	}

	function armoObjetoAutorizar(nro, bool, motivo){
		return {
			idProveedor: $('#inputBuscar_selectedValue').val(),
			numeroDeAutorizacion: nro,
			autoriza: (bool ? 'S' : 'N'),
			motivo: motivo
		};
	}

	function cambiarModo(modo) {
		funciones.cambiarModo(modo);
		switch (modo) {
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
	<div id='divCampos' class='w100p pantalla solapas'>
		<ul class="titulos">
			<li id='liTabDatos'>Datos</li>
			<li id='liTabContactos'>Contactos</li>
		</ul>
		<div>
			<div>
				<div id="divDatos1" class="fLeft">
					<?php
					$tabla = new HtmlTable(array('cantRows' => 19, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 3));
					$tabla->getRowCellArray($rows, $cells);

					$rows[18]->class = 'trConceptoImpuestoGanancias';

					//imprime el cuadro con campos

					$cells[0][0]->content = '<label>Nombre:</label>';
					$cells[0][0]->style->width = '150px';
					$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio  inputForm w230"   rel="nombre" />';
					$cells[0][1]->style->width = '250px';

					$cells[1][0]->content = '<label>Razón social:</label>';
					$cells[1][1]->content = '<input id="inputRazonSocial" class="textbox obligatorio  inputForm w230" rel="razonSocial" />';

					$cells[2][0]->content = '<label>CUIT:</label>';
					$cells[2][1]->content = '<input id="inputCuit" class="textbox  inputForm w230"  validate="Cuit"  rel="cuit" />';

					$cells[3][0]->content = '<label>Tipo proveedor:</label>';
					$cells[3][1]->content = '<input id="inputTipoProveedor" class="textbox autoSuggestBox inputForm w230" name="TipoProveedor"  rel="tipoProveedor" />';

					$cells[4][0]->content = '<label>Rubro:</label>';
					$cells[4][1]->content = '<input id="inputRubro" class="textbox inputForm w230" rel="rubroPalabra" />';

					$cells[5][0]->content = '<label>Condición IVA:</label>';
					$cells[5][1]->content = '<input id="inputCondicionIva" class="textbox obligatorio autoSuggestBox inputForm w230" name="CondicionIva" rel="condicionIva" />';

					$cells[6][0]->content = '<label>Imputación general:</label>';
					$cells[6][1]->content = '<input id="inputImputacionGeneral" class="textbox autoSuggestBox obligatorio inputForm w230" name="Imputacion" rel="imputacionGeneral" />';

					$cells[7][0]->content = '<label>Imputación específica:</label>';
					$cells[7][1]->content = '<input id="inputImputacionEspecifica" class="textbox autoSuggestBox obligatorio inputForm w230" name="Imputacion" rel="imputacionEspecifica" />';

					$cells[8][0]->content = '<label>Imputación haber:</label>';
					$cells[8][1]->content = '<input id="inputImputacionHaber" class="textbox autoSuggestBox obligatorio inputForm w230" name="Imputacion" rel="imputacionHaber" />';

					$cells[9][0]->content = '<label>Plazo pago (días):</label>';
					$cells[9][1]->content = '<input id="inputPlazoPago" class="textbox obligatorio inputForm w230" validate="DecimalPositivo" maxlength="3" rel="plazoPago" />';

					$cells[10][0]->content = '<label>Transporte:</label>';
					$cells[10][1]->content = '<input id="inputTransporte" class="textbox autoSuggestBox inputForm w230" name="Transporte"  rel="transporte" />';

					$cells[11][0]->content = '<label>Teléfono 1:</label>';
					$cells[11][1]->content = '<input id="inputTelefono1" class="textbox  inputForm w230"   rel="telefono1" />';

					$cells[12][0]->content = '<label>Teléfono 2:</label>';
					$cells[12][1]->content = '<input id="inputTelefono2" class="textbox  inputForm w230"   rel="telefono2" />';

					$cells[13][0]->content = '<label>Fax:</label>';
					$cells[13][1]->content = '<input id="inputFax" class="textbox  inputForm w230"   rel="fax" />';

					$cells[14][0]->content = '<label>Horario de atención:</label>';
					$cells[14][1]->content = '<input id="inputHorarioAtencion" class="textbox  inputForm w230"  validate="RangoHora" rel="horariosAtencion" />';

					$cells[15][0]->content = '<label>Email:</label>';
					$cells[15][1]->content = '<input id="inputEmail" class="textbox  inputForm w230" validate="Email"   rel="email" />';

					$cells[16][0]->content = '<label>Página web:</label>';
					$cells[16][1]->content = '<input id="inputPaginaWeb" class="textbox  inputForm w230" rel="paginaweb" />';

					$cells[17][0]->content = '<label>Retiene ganancias:</label>';
					$cells[17][1]->content = '<input type="checkbox" id="inputRetenerImpuestoGanancias" class="textbox koiCheckbox inputForm" rel="retenerImpuestoGanancias" >';

					$cells[18][0]->content = '<label>Concepto ganancias:</label>';
					$cells[18][1]->content = '<input id="inputConceptoImpuestoGanancias" class="textbox obligatorio autoSuggestBox inputForm w230" name="ConceptoRetencionGanancias"  rel="conceptoRetenGanancias" />';

					$tabla->create(); //impresion
					?>

				</div>
				<div id="divDatos2" class="fRight">

					<?php
					$tabla = new HtmlTable(array('cantRows' => 8, 'cantCols' => 2, 'id' => 'tablaDatos2', 'cellSpacing' => 3));
					$tabla->getRowCellArray($rows, $cells);

					//imprime el cuadro con campos

					$cells[0][0]->content = '<label>Calle:</label>';
					$cells[0][0]->style->width = '150px';
					$cells[0][1]->content = '<input id="inputCalle" class="textbox  inputForm w230"   rel="direccionCalle" />';
					$cells[0][1]->style->width = '250px';

					$cells[1][0]->content = '<label>Número:</label>';
					$cells[1][1]->content = '<input id="inputNumero" class="textbox  inputForm w65"   rel="direccionNumero" />
								     <label>Piso:</label>
									 <input id="inputPiso" class="textbox inputFormSuc inputForm w25" maxlength="3" rel="direccionPiso" />
			 						 <label>Dpto:</label>
									 <input id="inputDpto" class="textbox inputFormSuc inputForm w25" maxlength="3" rel="direccionDepartamento" />';

					$cells[2][0]->content = '<label>País:</label>';
					$cells[2][1]->content = '<input id="inputPais" class="textbox autoSuggestBox  inputForm w230" name="Pais"   rel="direccionPais" />';

					$cells[3][0]->content = '<label>Provincia:</label>';
					$cells[3][1]->content = '<input id="inputProvincia" class="textbox autoSuggestBox  inputForm w230" name="Provincia" linkedTo="inputPais,Pais"   rel="direccionProvincia" />';

					$cells[4][0]->content = '<label>Localidad:</label>';
					$cells[4][1]->content = '<input id="inputLocalidad" class="textbox autoSuggestBox  inputForm w135"  name="Localidad" linkedTo="inputPais,Pais;inputProvincia,Provincia"  rel="direccionLocalidad" />
									 <label>CP:</label>
									 <input id="inputCP" class="textbox inputFormSuc inputForm w45" maxlength="4" rel="direccionCodigoPostal" />';


					$cells[5][0]->content = '<label>Observaciones:</label>';
					$cells[5][1]->content = '<textarea id="inputObservaciones" class="textbox  inputForm w230"   rel="observaciones" ></textarea>';

					$cells[6][0]->content = '<label id="labelAutorizaciones">Autorizaciones</label>';
					$cells[6][1]->content = ' ';
					$cells[7][0]->colspan = 2;
					$cells[7][0]->content = '<div id="divAutorizaciones" class="customScroll pRight10"></div>';


					$tabla->create(); //impresion
					?>
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
	<div id='filtro' class='draggableDialog hidden'>
		<div><!--campos de busqueda  -->
			<label for='inputBuscar' class='filtroBuscar'>Proveedores:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='ProveedorTodos' alt=''/>
		</div>
	</div>
	<!-- fin campos busqueda -->
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/proveedores/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/proveedores/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/proveedores/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
