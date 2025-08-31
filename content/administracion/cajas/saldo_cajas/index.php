<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Saldos de caja';
		cambiarModo('inicio');
		buscar();
	});

	function limpiarScreen(){
		$('#divSaldoCajas').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content' + window.location.pathname + 'buscar.php';
		funciones.load($('#divSaldoCajas'), url, function() {
			$('#divSaldoCajas').fixedHeader({target: 'table'});
		});
	}

	function xlsClick(){
		funciones.xlsClick(urlToExport('xls'));
	}

	function pdfClick(){
		funciones.xlsClick(urlToExport('pdf'));
	}

	function urlToExport(tipo){
		return '/content' + window.location.pathname + 'get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php';
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divSaldoCajasWrapper' class="customScroll" style="width: 100%;">
		<div id='divSaldoCajas' class='w100p'>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
	</div>
</div>
