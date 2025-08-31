<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Tipos de notificaciones';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined')
			return $('#inputBuscar').val(idBuscar).blur();
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/sistema/notificaciones/tipos_de_notificaciones/buscar.php?id=' + $('#inputBuscar_selectedValue').val(),	
			msgError = 'El tipo de notificación "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function hayErrorGuardar(){
		if ($('#inputNombre').val() == '')
			return 'Debe elegir un nombre para el tipo de notificación';
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/sistema/notificaciones/tipos_de_notificaciones/' + aux + '.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}
	

	function armoObjetoGuardar(){
		return {
			id: $('#inputBuscar_selectedValue').val(),
			nombre: $('#inputNombre').val(),
			accionNotificacion: $('#inputAccionNotif').val(),
			accionCumplido: $('#inputAccionCumplido').val(),
			accionAnular: $('#inputAccionAnular').val(),
			anularAlCumplir: $('#inputAnularAlCumplir').isChecked() ? 'S' : 'N',
			link: $('#inputLink').val(), 
			detalle: $('#inputDetalle').val(),
			imagen: $('#inputImagen').val()
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el tipo de notificación "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/sistema/notificaciones/tipos_de_notificaciones/borrar.php';
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
	<div id='divTiposnotificaciones' class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 8, 'cantCols' => 3, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Nombre:</label>';
			$cells[0][0]->style->width = '150px';
			$cells[0][1]->content = '<input id="inputNombre" class="textbox obligatorio inputForm w350"  alt="" rel="nombre" />';
			$cells[0][1]->style->width = '250px';
			$cells[0][2]->content = '<label>Ej: Nueva nota de pedido</label>';
			$cells[0][2]->style->width = '420px';
			$cells[1][0]->content = '<label>Acción notificación:</label>';
			$cells[1][1]->content = '<input id="inputAccionNotif" class="textbox inputForm w350" rel="accionNotificacion" />';
			$cells[1][2]->content = '<label>Ej: comercial/pedidos/nota_de_pedido/agregar/ </label>';
			$cells[2][0]->content = '<label>Acción cumplido:</label>';
			$cells[2][1]->content = '<input id="inputAccionCumplido" class="textbox inputForm w350" rel="accionCumplido" />';
			$cells[2][2]->content = '<label>Ej: comercial/pedidos/nota_de_pedido/autorizar/ </label>';
			$cells[3][0]->content = '<label>Acción anular:</label>';
			$cells[3][1]->content = '<input id="inputAccionAnular" class="textbox inputForm w350" rel="accionAnular" />';
			$cells[3][2]->content = '<label>Ej: comercial/pedidos/nota_de_pedido/borrar/ </label>';
			$cells[4][0]->content = '<label>Anular al cumplir</label>';
			$cells[4][1]->content = '<input type="checkbox" id="inputAnularAlCumplir" class="textbox inputForm" rel="anularAlCumplir" />';
			$cells[4][2]->content = '<label>Cuando ocurra la "acción cumplido" se eliminará la notificación</label>';
			$cells[5][0]->content = '<label>Link "Ver más":</label>';
			$cells[5][1]->content = '<input id="inputLink" class="textbox inputForm w350" rel="link" />';
			$cells[5][2]->content = '<label>Ej: comercial/pedidos/nota_de_pedido/ </label>';
			$cells[6][0]->content = '<label>Detalle:</label>';
			$cells[6][1]->content = '<textarea id="inputDetalle" class="textbox inputForm w350" rel="detalle" /></textarea>';
			$cells[6][2]->content = '<label>Ej: Se ha creado una nueva nota de pedido número </label>';
			$cells[7][0]->content = '<label>Imagen:</label>';
			$cells[7][1]->content = '<input id="inputImagen" class="textbox inputForm w350" rel="imagen" />';
			$cells[7][2]->content = '<label>Ej: nuevoPedido.gif </label>';

			$tabla->create();
		?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div><!--campos de busqueda  -->
			<label class='filtroBuscar'>Tipo de notificación:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='TipoNotificacion' alt='' />
		</div>
		
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'sistema/notificaciones/tipos_de_notificaciones/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'sistema/notificaciones/tipos_de_notificaciones/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'sistema/notificaciones/tipos_de_notificaciones/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
