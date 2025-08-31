<?php

?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Generación de facturas';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		var url = '/content/comercial/facturas/generacion/buscar.php?' +
					'idCliente=' + $('#inputBuscarCliente_selectedValue').val();
		$.showLoading();
		$.post(url, function(result) {
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
				$('#divGeneracionFacturas').html(result);
				$('.acordeon').acordeon({fixedHeight: false});
				$('.chkUno').click(chkUnoClick);
				cambiarModo('agregar');
			}
			$.hideLoading();
		});
	}

	function chkUnoClick(){
		var obj = $(this);
		var nro = obj.attr('id').split('_')[1].trim();
		if (obj.isChecked())
			$('#spanImporteRemito_' + nro).text(obj.attr('importe'));
		else
			$('#spanImporteRemito_' + nro).text('0,00');
	}

	function pdfClickRemito(empresa, nro) {
		var url = '/content/comercial/remitos/reimpresion/getPdf.php';
		url += '?empresa=' + empresa + '&numero=' + nro;
		funciones.pdfClick(url);
	}

	function hayErrorGuardar(){
		var chks = $('.chkUno:checked');
		if (chks.length == 0)
			return 'Debe seleccionar algo para facturar';
		return false;
	}

	function guardar(){
		var url = '/content/comercial/facturas/generacion/agregar.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		var rems = {};
		rems.remitos = [];
		var divRemitos = $('.divAcordeon');
		divRemitos.each(function(){
			var remito = {};
			var divRemito = $(this);
			var checks = divRemito.find('.chkUno:checked');
			if (checks.length > 0) {
				remito.numero = divRemito.attr('idRem');
				rems.remitos[rems.remitos.length] = remito;
			}
		});
		return rems;
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		$('#radioGroupAlmacen').enableRadioGroup();
		switch (modo){
			case 'inicio':
				$('#divGeneracionFacturas').html('');
				break;
			case 'buscar':
				break;
			case 'editar':
				break;
			case 'agregar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divGeneracionFacturas' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar w190' name='Cliente' alt='' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
