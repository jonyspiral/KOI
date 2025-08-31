<?php

$permissions = array(
	array('id' => PermisosUsuarioPorCaja::verCaja, 'name' => 'Listar caja', 'title' => 'Listar caja'),
	array('id' => PermisosUsuarioPorCaja::recibo, 'name' => 'Recibos', 'title' => 'Recibos'),
	array('id' => PermisosUsuarioPorCaja::ordenDePago, 'name' => 'Órden de Pago', 'title' => 'Órdenes de pago'),
	array('id' => PermisosUsuarioPorCaja::ajuste, 'name' => 'Ajuste', 'title' => 'Ajustes'),
	array('id' => PermisosUsuarioPorCaja::transferenciaInterna, 'name' => 'Transf. Interna', 'title' => 'Transferencias Internas'),
	array('id' => PermisosUsuarioPorCaja::transferenciaBancariaOperacion, 'name' => 'Transf. Bancaria', 'title' => 'Transferencias Bancarias'),
	array('id' => PermisosUsuarioPorCaja::ingresoChequePropio, 'name' => 'Cheque Propio', 'title' => 'Ingreso de Cheques Propios'),
	array('id' => PermisosUsuarioPorCaja::cobroChequesVentanilla, 'name' => 'Cheque por Vent.', 'title' => 'Cobro de Cheques por Ventanilla'),
	array('id' => PermisosUsuarioPorCaja::ventaCheques, 'name' => 'Venta Cheque', 'title' => 'Venta de Cheques'),
	array('id' => PermisosUsuarioPorCaja::depositoBancario, 'name' => 'Dep. Bancario', 'title' => 'Depósitos Bancarios'),
	array('id' => PermisosUsuarioPorCaja::rechazoCheque, 'name' => 'Rech. Cheque', 'title' => 'Rechazo de Cheques'),
	array('id' => PermisosUsuarioPorCaja::rendicionGastos, 'name' => 'Rend. Gasto', 'title' => 'Rendición de Gastos'),
	array('id' => PermisosUsuarioPorCaja::aporteSocio, 'name' => 'Aporte Socio', 'title' => 'Aportes de Socios'),
	array('id' => PermisosUsuarioPorCaja::acreditarCheque, 'name' => 'Acreditar Cheque', 'title' => 'Acreditar Cheques'),
	array('id' => PermisosUsuarioPorCaja::debitarCheque, 'name' => 'Debitar Cheque', 'title' => 'Debitar Cheques'),
	array('id' => PermisosUsuarioPorCaja::retiroSocio, 'name' => 'Retiro Socio', 'title' => 'Retiros de Socios'),
	array('id' => PermisosUsuarioPorCaja::prestamo, 'name' => 'Préstamo', 'title' => 'Préstamos'),
	array('id' => PermisosUsuarioPorCaja::reingresoChequeCartera, 'name' => 'Reing. Cheque', 'title' => 'Reingreso de Cheques en Cartera')
);

?>

<style>
	#divCaja {
		padding-top: 10px;
	}
	#divDatosBasicos {
		width: 50%;
	}
	#divTransferenciasInternas {
		width: 50%;
	}
	#tablaUsuarios {
		table-layout: fixed;
	}
	.tableHeaderCustom {
		font: 15px Calibri !important, sans-serif;
		padding: 0 !important;
		width: 4% !important;
		height: 115px !important;
	}
	.rotated {
		-webkit-transform: rotate(90deg);
		-moz-transform: rotate(90deg);
		-ms-transform: rotate(90deg);
		-o-transform: rotate(90deg);
		transform: rotate(90deg);
	}
	.inner {
		margin: 0 -100%;
		display: inline-block;
	}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Cajas';
		$('#btnAgregarCajaTransferencia').click(agregarCajaTransferenciaPopUp);
		$('#btnAgregarUsuario').click(agregarUsuarioPopUp);
		cambiarModo('inicio');
		$('.solapas').solapas({fixedHeight: 470, heightSolapas: 28, selectedItem: 0, precall: function (obj) {
			(obj.text() == 'PERMISOS') ? $('#divAgregarUsuario').show() : $('#divAgregarUsuario').hide();
		}});
		$('#divPermisos').fixedHeader({target: 'table'});
	});

	function limpiarScreen(){
		$('#tablaCajasTransferencias tbody').html('');
		$('#tablaUsuarios tbody').html('');
		$('#liCaja').click();
	}

	function buscar() {
		funciones.limpiarScreen();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = funciones.controllerUrl('buscar', {id: $('#inputBuscar_selectedValue').val()}),
			msgError = 'La caja "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				fillCaja(json);
				fillPermisos(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function fillCaja(json) {
		$('#tablaDatos').loadJSON(json);
		for (var i = 0; i < json.cajasPosiblesTransferenciaInterna.length; i++) {
			var aux = json.cajasPosiblesTransferenciaInterna[i];
			agregarCajaTransferenciaTr({id: aux.idCajaEntrada, nombre: aux.cajaEntrada.nombre});
		}
	}

	function agregarCajaTransferenciaPopUp() {
		var div = $('<div class="h100 vaMiddle table-cell aLeft p10">').append(
			$('<table>').append(
				$('<tbody>').append(
					$('<tr><td><label for="inputCajaEntrada">Caja destino: </label></td><td><input id="inputCajaEntrada" type="text" class="textbox autoSuggestBox obligatorio w190" name="Caja" /></td></tr>')
				)
			)
		);
		var botones = [{value: 'Guardar', action: function() {agregarCajaTransferencia();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
	}

	function agregarCajaTransferencia() {
		var cajaEntrada = {
			id: $('#inputCajaEntrada_selectedValue').val(),
			nombre: $('#inputCajaEntrada_selectedName').val()
		};
		agregarCajaTransferenciaTr(cajaEntrada);
		$.jPopUp.close(function(){
			$('#tr_' + cajaEntrada.id).shine();
		});
	}

	function agregarCajaTransferenciaTr(cajaEntrada) {
		if (! $('#tr_' + cajaEntrada.id).length) {
			$('#tablaCajasTransferencias tbody').append(
				$('<tr>').addClass('tableRow trCajaTransferencia').attr('id', 'tr_' + cajaEntrada.id).data('cajaEntrada', cajaEntrada).append(
					$('<td>').addClass('aCenter').text(cajaEntrada.id),
					$('<td>').text(cajaEntrada.nombre),
					$('<td>').addClass('aCenter').append(
						$('<a>').addClass('boton').attr('href', '#').attr('title', 'Quitar caja')
							.click($.proxy(function() {quitarCajaTransferencia(this);}, cajaEntrada))
							.append($('<img>').attr('src', '/img/botones/25/cancelar.gif'))
					)
				)
			);
		}
	}

	function quitarCajaTransferencia(cajaEntrada) {
		$('#tr_' + cajaEntrada.id).remove()
	}

	/* **** PERMISOS **** */

	function fillPermisos(json) {
		$('#tablaDatos2').loadJSON(json);
		for (var i = 0; i < json.permisos.length; i++) {
			var p = json.permisos[i];
			if (! $('#tr_usuario_' + p.idUsuario).length) {
				agregarUsuarioTr({id: p.idUsuario, nombre: p.idUsuario});
			}
			$('#checkbox_' + p.idUsuario + '_' + p.idPermiso).check();
		}
	}

	function agregarUsuarioPopUp() {
		var div = $('<div class="h100 vaMiddle table-cell aLeft p10">').append(
			$('<table>').append(
				$('<tbody>').append(
					$('<tr><td><label for="inputUsuario">Usuario: </label></td><td><input id="inputUsuario" type="text" class="textbox autoSuggestBox obligatorio w190" name="Usuario" /></td></tr>')
				)
			)
		);
		var botones = [{value: 'Guardar', action: function() {agregarUsuario();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
	}

	function agregarUsuario() {
		var usuario = {
			id: $('#inputUsuario_selectedValue').val(),
			nombre: $('#inputUsuario_selectedName').val()
		};
		agregarUsuarioTr(usuario);
		$.jPopUp.close(function(){
			$('#tr_usuario_' + usuario.id).shine();
		});
	}

	function agregarUsuarioTr(usuario) {
		if (! $('#tr_usuario_' + usuario.id).length) {
			$('#tablaUsuarios tbody').append(
				$('<tr>').addClass('tableRow trUsuario aCenter').attr('id', 'tr_usuario_' + usuario.id).data('usuario', usuario).append(
					$('<td>').addClass('aCenter').append(
						$('<a>').addClass('boton').attr('href', '#').attr('title', 'Quitar usuario')
							.click($.proxy(function() {quitarUsuario(this);}, usuario))
							.append($('<img>').attr('src', '/img/botones/25/cancelar.gif'))
					),
					$('<td>').text(usuario.nombre)
					<?php foreach ($permissions as $p) { ?>
					,
					$('<td>').append(
						$('<input>').attr('type', 'checkbox').attr('id', 'checkbox_' + usuario.id + '_' + <?= $p['id']; ?>).addClass('textbox koiCheckbox inputForm checkboxPermiso')
							.data('permiso', {idUsuario: usuario.id, idPermiso: <?= $p['id']; ?>})
					)
					<?php } ?>
				)
			);
		}
	}

	function quitarUsuario(usuario) {
		$('#tr_usuario_' + usuario.id).remove()
	}

	/* ******************* */

	function hayErrorGuardar(){
		/*
		if ($('#inputResponsable_selectedValue').val() == '')
			return 'Debe seleccionar el responsable de la caja';
		*/
		if ($('#inputCajaPadre_selectedValue').val() == '')
			return 'Debe seleccionar la caja padre de la nueva caja';
		if ($('#inputNombre').val() == '')
			return 'Debe ingresar el nombre de la caja';
		if ($('#inputImporteMaximo').val() == '')
			return 'Debe ingresar el importe máximo de la caja';
		if ($('#inputImputacion_selectedValue').val() == '')
			return 'Debe seleccionar la imputación de la caja';
		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		funciones.guardar(funciones.controllerUrl(aux), armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			id: $('#inputBuscar_selectedValue').val(),
			//idResponsable: $('#inputResponsable_selectedValue').val(),
			idCajaPadre: $('#inputCajaPadre_selectedValue').val(),
			nombre: $('#inputNombre').val(),
			//fechaLimite: $('#inputFechaLimite').val(),
			//diasCierre: $('#inputDiasCierre').val(),
			importeDescubierto: $('#inputImporteDescubierto').val(),
			importeMaximo: $('#inputImporteMaximo').val(),
			esCajaBanco: ($('#inputEsCajaBanco').isChecked() ? 'S' : 'N'),
			dispParaNegociar: $('#inputDispParaNegociar').val(),
			idImputacion: $('#inputImputacion_selectedValue').val(),
			cajasTransferencias: armarCajasTransferencias(),
			permisos: armarUsuarios()
		};
	}

	function armarCajasTransferencias() {
		var cajasEntrada = [];
		$('.trCajaTransferencia').each(function (key, val) {
			cajasEntrada.push($(val).data('cajaEntrada'));
		});
		return cajasEntrada;
	}

	function armarUsuarios() {
		var permisos = [];
		$('.checkboxPermiso').each(function (key, val) {
			if ($(val).isChecked()) {
				permisos.push($(val).data('permiso'));
			}
		});
		return permisos;
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar la caja "' + $('#inputBuscar_selectedName').val() + '"?';
		funciones.borrar(msg, funciones.controllerUrl('borrar'), armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {id: $('#inputBuscar_selectedValue').val()};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		$('#inputNombre').focus();
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id="divAgregarUsuario">
		<div class="fRight pLeft5">
			<a href="#" id="btnAgregarUsuario" class="boton" title="Agregar usuario"><img src="/img/botones/25/agregar.gif" /></a>
		</div>
		<div class="fRight" style="margin-top: -4px;">
			<h3>Agregar usuario</h3>
		</div>
	</div>
	<div class="solapas pantalla">
		<ul>
			<li id="liCaja">CAJA</li>
			<li>PERMISOS</li>
		</ul>
		<div>
			<div id="divCaja" data-solapa="caja">
				<div id='divDatosBasicos' class='fLeft'>
					<?php
					$tabla = new HtmlTable(array('cantRows' => 7, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
					$tabla->getRowCellArray($rows, $cells);

					$cells[0][0]->content = '<label>Nombre:</label>';
					$cells[0][0]->style->width = '150px';
					$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w230" type="text" rel="nombre" />';
					$cells[0][1]->style->width = '230px';

					//$cells[1][0]->content = '<label>Responsable:</label>';
					//$cells[1][1]->content = '<input id="inputResponsable" class="textbox obligatorio autoSuggestBox inputForm noEditable w230" name="Usuario" rel="responsable" />';

					$cells[1][0]->content = '<label>Caja padre:</label>';
					$cells[1][1]->content = '<input id="inputCajaPadre" class="textbox obligatorio autoSuggestBox inputForm noEditable w230" name="Caja" rel="cajaPadre" />';

					$cells[2][0]->content = '<label>Imputación:</label>';
					$cells[2][1]->content = '<input id="inputImputacion" class="textbox obligatorio autoSuggestBox inputForm w230" name="Imputacion" rel="imputacion" />';

					$cells[3][0]->content = '<label>Importe máximo:</label>';
					$cells[3][1]->content = '<input id="inputImporteMaximo" class="textbox obligatorio inputForm w230" type="text" validate="DecimalPositivo" rel="importeMaximo" />';

					$cells[4][0]->content = '<label>Importe descubierto:</label>';
					$cells[4][1]->content = '<input id="inputImporteDescubierto" class="textbox inputForm w230" type="text" validate="DecimalPositivo" rel="importeDescubierto" />';

					$cells[5][0]->content = '<label>Disponibilidad para negociar:</label>';
					$cells[5][1]->content = '<input id="inputDispParaNegociar" class="textbox inputForm w230" type="text" validate="DecimalPositivo" rel="dispParaNegociar" />';

					$cells[6][0]->content = '<label>Es caja banco:</label>';
					$cells[6][1]->content = '<input type="checkbox" id="inputEsCajaBanco" class="textbox koiCheckbox inputForm" rel="esCajaBanco" >';

					$tabla->create();
					?>
				</div>
				<div id='divTransferenciasInternas' class='fRight'>
					<div class="fLeft">
						<h3>Cajas hacia las que se puede transferir</h3>
					</div>
					<div class="fRight">
						<a href="#" id="btnAgregarCajaTransferencia" class="boton" title="Agregar caja destino"><img src="/img/botones/25/agregar.gif" /></a>
					</div>
					<table id='tablaCajasTransferencias' class='registrosAlternados w100p'>
						<thead class='tableHeader'>
							<tr>
								<th class="w25p">ID</th>
								<th class="w60p">Caja</th>
								<th class="w15p">Quitar</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<div id="divPermisos" data-solapa="permisos">
				<div>
					<table id='tablaUsuarios' class='registrosAlternados w100p'>
						<thead class='tableHeader'>
							<tr>
								<th class="w7p">Quitar</th>
								<th class="">Usuario</th>
								<?php foreach ($permissions as $p) { ?>
									<th id="thPermisos_<?= $p['id']; ?>" class="tableHeaderCustom" title="<?= $p['title']; ?>">
										<div class="rotated">
											<div class="inner"><?= $p['name']; ?></div>
										</div>
									</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody class="aCenter"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label for='inputBuscar' class='filtroBuscar'>Caja:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Caja' alt='' />
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'abm/cajas/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'abm/cajas/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'abm/cajas/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
