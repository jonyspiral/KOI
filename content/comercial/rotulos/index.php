<?php
?>
<script type='text/javascript'>
	$(document).ready(function(){
		$('#inputProveedor').blur(function(){funciones.delay('ponerProveedor();');});
		tituloPrograma = 'Rótulos';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		var url = '/content/comercial/rotulos/getPdf.php?';
			url += 'idCliente=' + $('#inputBuscarCliente_selectedValue').val();
			url += '&idSucursal=' + $('#inputBuscarSucursal_selectedValue').val();
		var msgError = 'No hay clientes o sucursal con ese filtro',
			cbSuccess = function(json){
			
			};
			funciones.pdfClick(url);
	}
	
	function limpiarScreen(){
		funciones.cambiarTitulo();
	}
	
	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma);
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
		<div id='divRotulo' class='pantalla'>
		</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar w190' name='Cliente' alt='' />
		</div>
		<div>
			<label class='filtroBuscar'>Sucursal:</label>
			<input id='inputBuscarSucursal' class='textbox autoSuggestBox filtroBuscar w190' name='Sucursal' linkedTo="inputBuscarCliente,Cliente" alt='' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="../../../img/botones/25/buscar.gif" /></a>
		</div>
	</div>	
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
