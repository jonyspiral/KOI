<?php
?>

<style>
	.divContenido {
		width: 99%;
		height: 460px;
	}
	.imageBorder {
		border: 2px solid;
	}
</style>

<style id="printStyle" type="text/css" media="print">
	@page {
		size: A4;
	}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Ficha técnica';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		funciones.cambiarTitulo();
		$('#divFichaTecnica').html('');
	}

	function getParams() {
		return '&idArticulo=' + $('#inputArticulo_selectedValue').val() + '&idColorPorArticulo=' + $('#inputColor_selectedValue').val() + '&idVersion=' + $('#inputVersion_selectedValue').val();
	}

	function buscarSiguienteAnterior(idArticulo, idColor) {
		$('#inputArticulo').val(idArticulo).autoComplete();
		$('#inputColor').val(idColor).autoComplete();
		buscar();
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = '/content/produccion/producto/ficha_tecnica/buscar.php?' + getParams();
		$.showLoading();
		$.get(url, function(result) {
			try {
				var json = $.parseJSON(result);
				switch (funciones.getJSONType(json)) {
					case funciones.jsonNull:
					case funciones.jsonError:
						$.error('Ocurrió un error al intentar realizar la consulta');
						break;
					case funciones.jsonInfo:
						$.info(funciones.getJSONMsg(json));
						break;
				}
			} catch (ex) {
				$('#divFichaTecnica').html(result);
				$('#divFichaTecnica').fixedHeader({target: '#patron'});
				$('.solapas').solapas({fixedHeight: 460, heightSolapas: 28, selectedItem: 0});
				cambiarModo('buscar');
				setTimeout(function() {
					$('#liTabFichaBase').click(function(){
						$('#printStyle').html('@page {size: A4;}');
					});

					$('#liTabFichaEspecificaciones').click(function(){
						$('#printStyle').html('@page {size: A4 landscape;}');
					});

					$('#liTabFichaBase').click();

					$('.sgteAnt').hover(function() {
						$(this).stop(true, true).css('font-weight', 'bold');
					}, function() {
						$(this).stop(true, true).css('font-weight', 'normal');
					});
				}, 100);
			}
			$.hideLoading();
		});
	}

	function ampliarFoto(obj){
		$.jPopUp.show($('<div class="w600 h300 vaBottom table-cell aCenter"><img src="' + obj.src + '" height="300" />'), [{value: 'Cerrar', action: function(){$.jPopUp.close();}}]);
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				$('#btnBuscar').show();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divFichaTecnica' class='w100p'></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputArticulo' class='filtroBuscar'>Artículo:</label>
			<input id='inputArticulo' class='textbox obligatorio autoSuggestBox filtroBuscar w230' name='Articulo' />
		</div>
		<div>
			<label for='inputColor' class='filtroBuscar'>Color:</label>
			<input id='inputColor' class='textbox obligatorio autoSuggestBox filtroBuscar w230' name='ColorPorArticulo' linkedTo="inputArticulo,Articulo" />
		</div>
		<div>
			<label for='inputVersion' class='filtroBuscar'>Versión:</label>
			<input id='inputVersion' class='textbox autoSuggestBox filtroBuscar w230' name='Patron' linkedTo="inputArticulo,Articulo;inputColor,ColorPorArticulo" />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'window.print();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>