<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/clientes/buscar/')) { ?>
<?php

$idCliente = Funciones::get('idCliente');
$idSucursal = Funciones::get('idSucursal');

try {
	if (!isset($idCliente) || !isset($idSucursal)){
		$idSuc = 'n';
		$esNueva = 'nuevaSucursal="1"';
		$array['nombre'] = 'NUEVA...';
	} else {
		$sucursal = Factory::getInstance()->getSucursal($idCliente, $idSucursal);
		$idSuc = $sucursal->id;
		$esNueva = '';
		$array['nombre'] = $sucursal->nombre;
		$array['calle'] = $sucursal->direccionCalle;
		$array['numero'] = $sucursal->direccionNumero;
		$array['piso'] = $sucursal->direccionPiso;
		$array['dpto'] = $sucursal->direccionDepartamento;
		$array['pais'] = $sucursal->direccionPais;
		$array['provincia'] = $sucursal->direccionProvincia;
		$array['localidad'] = $sucursal->direccionLocalidad;
		$array['codigopostal'] = $sucursal->direccionCodigoPostal;
		$array['telefono1'] = $sucursal->telefono1;
		$array['telefono2'] = $sucursal->telefono2;
		$array['celular'] = $sucursal->celular;
		$array['email'] = $sucursal->email;
		$array['fax'] = $sucursal->fax;
		$array['horariodeatencion'] = $sucursal->horarioAtencion;
		$array['sucursalentrega'] = $sucursal->sucursalEntrega;
		$array['reparto'] = $sucursal->reparto;
		$array['transporte'] = $sucursal->transporte;
		$array['vendedor'] = $sucursal->vendedor;
		$array['observaciones'] = $sucursal->observaciones;
		$array['esPuntoDeVenta'] = $sucursal->esPuntoDeVenta;
		$array['latitud'] = $sucursal->direccionLatitud;
		$array['longitud'] = $sucursal->direccionLongitud;
	}

	$tabla1 = new HtmlTable(array('cantRows' => 13, 'cantCols' => 2, 'id' => 'tablaDatos41', 'cellSpacing' => 10));
	$tabla1->getRowCellArray($rows, $cells);

	$cells[0][0]->content = '<label>Nombre:</label>';
	$cells[0][0]->style->width = '135px';
	$cells[0][1]->content = '<input id="sucursal_' . $idSuc . '_inputNombre" class="textbox obligatorio inputFormSuc inputForm_' . $idSuc . ' w230" value="' . $array['nombre'] . '" />';
	$cells[0][1]->style->width = '260px';
	$cells[1][0]->content = '<label>Calle:</label>';
	$cells[1][1]->content = '<input id="sucursal_' . $idSuc . '_inputCalle" class="textbox inputFormSuc inputForm_' . $idSuc . ' w230" value="' . $array['calle'] . '" />';
	$cells[2][0]->content = '<label>Número:</label>';
	$cells[2][1]->content = '<input id="sucursal_' . $idSuc . '_inputNumero" class="textbox inputFormSuc inputForm_' . $idSuc . ' w65" maxlength="5" value="' . $array['numero'] . '" />
							<label>Piso:</label>
							<input id="sucursal_' . $idSuc . '_inputPiso" class="textbox inputFormSuc inputForm_' . $idSuc . ' w25" maxlength="3" value="' . $array['piso'] . '" />
							<label>Dpto:</label>
							<input id="sucursal_' . $idSuc . '_inputDpto" class="textbox inputFormSuc inputForm_' . $idSuc . ' w25" maxlength="3" value="' . $array['dpto'] . '" />';
	$cells[3][0]->content = '<label>País:</label>';
	$inputPais = new HtmlAutoSuggestBox(array(
			'id' => 'sucursal_' . $idSuc . '_inputPais',
			'class' => 'textbox autoSuggestBox inputFormSuc inputForm_' . $idSuc . ' w230',
			'name' => 'Pais',
			'defVal' => $array['pais']->id,
			'defName' => $array['pais']->nombre
	));
	$cells[3][1]->content = $inputPais->toString();
	$cells[4][0]->content = '<label>Provincia:</label>';
	$inputProv = new HtmlAutoSuggestBox(array(
			'id' => 'sucursal_' . $idSuc . '_inputProvincia',
			'class' => 'textbox autoSuggestBox inputFormSuc inputForm_' . $idSuc . ' w230',
			'name' => 'Provincia',
			'defVal' => $array['provincia']->id,
			'defName' => $array['provincia']->nombre,
			'linkedTo' => array(array('input' => 'sucursal_' . $idSuc . '_inputPais', 'name' => 'Pais'))
	));
	$cells[4][1]->content = $inputProv->toString();
	$cells[5][0]->content = '<label>Localidad:</label>';
	$inputLocal = new HtmlAutoSuggestBox(array(
			'id' => 'sucursal_' . $idSuc . '_inputLocalidad',
			'class' => 'textbox autoSuggestBox inputFormSuc inputForm_' . $idSuc . ' w135',
			'name' => 'Localidad',
			'defVal' => $array['localidad']->id,
			'defName' => $array['localidad']->nombre,
			'linkedTo' => array(array('input' => 'sucursal_' . $idSuc . '_inputPais', 'name' => 'Pais'), array('input' => 'sucursal_' . $idSuc . '_inputProvincia', 'name' => 'Provincia'))
	));
	$cells[5][1]->content = $inputLocal->toString() . '<label>CP:</label>
							<input id="sucursal_' . $idSuc . '_inputCodPostal" class="textbox inputFormSuc inputForm_' . $idSuc . ' w50" value="' . $array['codigopostal'] . '" />';
	$cells[6][0]->content = '<label>Teléfono 1:</label>';
	$cells[6][1]->content = '<input id="sucursal_' . $idSuc . '_inputTelefono1" class="textbox inputFormSuc inputForm_' . $idSuc . ' 230" value="' . $array['telefono1'] . '" maxlength="15" validate="Telefono" />';
	$cells[7][0]->content = '<label>Teléfono 2:</label>';
	$cells[7][1]->content = '<input id="sucursal_' . $idSuc . '_inputTelefono2" class="textbox inputFormSuc inputForm_' . $idSuc . ' 230" value="' . $array['telefono2'] . '" maxlength="15" validate="Telefono" />';
	$cells[8][0]->content = '<label>Celular:</label>';
	$cells[8][1]->content = '<input id="sucursal_' . $idSuc . '_inputCelular" class="textbox inputFormSuc inputForm_' . $idSuc . ' w230" value="' . $array['celular'] . '" />';
	$cells[9][0]->content = '<label>Email:</label>';
	$cells[9][1]->content = '<input id="sucursal_' . $idSuc . '_inputEmail" class="textbox inputFormSuc inputForm_' . $idSuc . ' w230" value="' . $array['email'] . '" validate="Email" />';
	$cells[10][0]->content = '<label>Fax:</label>';
	$cells[10][1]->content = '<input id="sucursal_' . $idSuc . '_inputFax" class="textbox inputFormSuc inputForm_' . $idSuc . ' w230" value="' . $array['fax'] . '" validate="Fax" maxlength="12" />';
	$cells[11][0]->content = '<label>Horario de atención:</label>';
	$cells[11][1]->content = '<input id="sucursal_' . $idSuc . '_inputHorarioDeAtencion" class="textbox inputFormSuc inputForm_' . $idSuc . ' w230" value="' . $array['horariodeatencion'] . '" validate="RangoHora" />';
	
	$cells[12][0]->content = '<label>Punto de venta:</label>';
	$cells[12][1]->content = '<div id="sucursal_' . $idSuc . '_radioPuntoVenta" class="customRadio" default="rdPuntoVenta_' . $idSuc . $array['esPuntoDeVenta'] . '">'.
								'<input id="rdPuntoVenta_' . $idSuc . 'S" class="textbox" type="radio"  name="radioPuntoVenta_' . $idSuc .'" value="S"  /><label for="rdPuntoVenta_' . $idSuc . 'S">S</label>' . 
								'<input id="rdPuntoVenta_' . $idSuc . 'N" class="textbox" type="radio" name="radioPuntoVenta_' . $idSuc .'" value="N"  /><label for="rdPuntoVenta_' . $idSuc . 'N">N</label></div>';

	$tabla2 = new HtmlTable(array('cantRows' => 8, 'cantCols' => 2, 'id' => 'tablaDatos42', 'cellSpacing' => 10));
	$tabla2->getRowCellArray($rows, $cells);

	$cells[0][0]->content = '<label>Sucursal entrega:</label>';
	$cells[0][0]->style->width = '135px';
	$inputSucursalEntrega = new HtmlAutoSuggestBox(array(
			'id' => 'sucursal_' . $idSuc . '_inputSucursalEntrega',
			'class' => 'textbox autoSuggestBox inputFormSuc inputForm_' . $idSuc . ' w230',
			'name' => 'Sucursal',
			'defVal' => $array['sucursalentrega']->id,
			'defName' => $array['sucursalentrega']->nombre,
			'linkedTo' => array(array('input' => 'inputBuscar', 'name' => 'Cliente')),
			'alts' => array('idCliente' => $idCliente)
	));
	$cells[0][1]->content = $inputSucursalEntrega->toString();
	$cells[0][1]->style->width = '260px';
	$cells[1][0]->content = '<label>Reparto:</label>';
	$cells[1][1]->content = '<input id="sucursal_' . $idSuc . '_inputReparto" class="textbox inputFormSuc inputForm_' . $idSuc . ' w230" value="' . $array['reparto'] . '" />';
	$cells[3][0]->content = '<label>Transporte:</label>';
	$inputTransporte = new HtmlAutoSuggestBox(array(
			'id' => 'sucursal_' . $idSuc . '_inputTransporte',
			'class' => 'textbox autoSuggestBox inputFormSuc inputForm_' . $idSuc . ' w230',
			'name' => 'Transporte',
			'defVal' => $array['transporte']->id,
			'defName' => $array['transporte']->nombre
	));
	$cells[3][1]->content = $inputTransporte->toString();
	$cells[4][0]->content = '<label>Vendedor:</label>';
	$inputVendedor = new HtmlAutoSuggestBox(array(
			'id' => 'sucursal_' . $idSuc . '_inputVendedor',
			'class' => 'textbox autoSuggestBox inputFormSuc inputForm_' . $idSuc . ' w230',
			'name' => 'Vendedor',
			'defVal' => $array['vendedor']->id,
			'defName' => $array['vendedor']->nombre
	));
	$cells[4][1]->content = $inputVendedor->toString();
	$cells[5][0]->content = '<label>Observaciones:</label>';
	$cells[5][1]->content = '<textarea id="sucursal_' . $idSuc . '_inputObservaciones" class="textbox inputFormSuc inputForm_' . $idSuc . ' w230">' . $array['observaciones'] . '</textarea>';

	$cells[6][0]->content = '<label>Latitud:</label>';
	$cells[6][1]->content = '<input id="sucursal_' . $idSuc . '_inputLatitud" class="textbox inputFormSuc inputForm_' . $idSuc . ' w230" value="' . $array['latitud'] . '" />';

	$cells[7][0]->content = '<label>Longitud:</label>';
	$cells[7][1]->content = '<input id="sucursal_' . $idSuc . '_inputLongitud" class="textbox inputFormSuc inputForm_' . $idSuc . ' w230" value="' . $array['longitud'] . '" />';
	
	
	echo '<div id="sucursal_' . $idSuc . '" idSucursal="' . $idSuc . '" ' . $esNueva . '>';
	echo '	<div>' . $array['nombre'] . '</div>';
	echo '	<div>';
	echo '		<div class="divSucursal1 fLeft">';
	$tabla1->create();
	echo '		</div>';
	echo '		<div class="divSucursal2 fRight">';
	$tabla2->create();
	echo '			<div class="botonera mini aRight pRight20 hidden">';
	Html::echoBotonera(array('boton' => 'editar', 'id' => 'btnEditar_25_' . $idSuc, 'tamanio' => '25', 'accion' => 'miniEditarClick(\'' . $idSuc . '\');', 'permiso' => 'abm/clientes/editar/'));
	Html::echoBotonera(array('boton' => 'aceptar', 'id' => 'btnAceptar_25_' . $idSuc, 'tamanio' => '25', 'accion' => 'miniAceptarClick(\'' . $idSuc . '\');', 'style' => 'display: none; '));
	Html::echoBotonera(array('boton' => 'borrar', 'id' => 'btnBorrar_25_' . $idSuc, 'tamanio' => '25', 'accion' => 'miniBorrarClick(\'' . $idSuc . '\');', 'style' => 'display: none; ', 'permiso' => 'abm/clientes/borrar/'));
	echo '			</div>';
	echo '		</div>';
	echo '	</div>';
	echo '</div>';

} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>