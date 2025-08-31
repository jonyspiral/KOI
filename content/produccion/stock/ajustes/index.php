<?php

?>

<style>
	#divGarantias {
		height: 490px;
	}
</style>

<script type='text/javascript'>
	var objGlobal = {};

	$(document).ready(function(){
		tituloPrograma = 'Ajustes de stock PT';
		$('.tabladinamica ').tablaDinamica(
			{
				width: '100%',
				height: 'auto',
				scrollbar: false,
				addButtonInHeader: true,
				buttons: ['Q'],
				columnsConfig: [
					{
						id: 'tipo',
						name: 'Tipo',
						width: '70px',
						css: {textAlign: 'center'},
						cellType: 'S',
						template: '<select class="textbox obligatorio w70" ><option value="POS">POS</option><option value="NEG">NEG</option></select>'
					},
					{
						id: 'idAlmacen',
						name: 'Almacen',
						width: '140px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox w120" name="Almacen" />'
					},
					{
						id: 'idArticulo',
						name: 'Articulo',
						width: '200px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox w180" name="Articulo" />'
					},
					{
						id: 'idColor',
						name: 'Color',
						width: '140px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox w120" name="ColorPorArticulo" />',
						focus: function(){
							this.tablaDinamica('getMe').valueElement.val('').attr('alt', 'idArticulo=' + this.tablaDinamica('getSibling', 'idArticulo').getValue());
						}
					},
					{
						id: 'motivo',
						name: 'Motivo',
						width: '160px',
						css: {textAlign: 'center'},
						cellType: 'T',
						template: '<textarea class="textbox obligatorio noResize w140" rows="3" style="height: auto;"></textarea>',
						focus: focusMotivo
					},
					{
						id: 'cantidades',
						name: 'Cantidades',
						width: 'auto',
						css: {textAlign: 'center'},
						cellType: 'G',
						getJson: function(o) {
							var i = 1,
								cantidades = {},
								inputsCantidades = o.tableCell.tablaDinamica('getSibling', 'cantidades').tableCell.find('input');

							inputsCantidades.each(
								function(k, v){
									cantidades[i++] = $(v).val();
								}
							);

							return cantidades;
						}
					},
					{
						id: 'total',
						name: 'Total',
						width: '50px',
						css: {textAlign: 'center'},
						cellType: 'L',
						template: '0'
					}
				],
				notEmpty: true
			}
		);
		$('#divGarantias').fixedHeader({target: 'table'});
		cambiarModo('agregar');
	});

	function limpiarScreen() {
		$('.tablaDinamica').tablaDinamica('clean');
	}

	function focusMotivo() {
		var that = this,
			objCantidades = this.tablaDinamica('getSibling', 'cantidades'),
			idAlmacen = this.tablaDinamica('getSibling', 'idAlmacen').getValue(),
			idArticulo = this.tablaDinamica('getSibling', 'idArticulo').getValue(),
			idColor = this.tablaDinamica('getSibling', 'idColor').getValue();

		if (this.data('idArticulo') != idArticulo){
			if (idAlmacen && idArticulo && idColor){
				$.postJSON(funciones.controllerUrl('getInfoArticulo', {idAlmacen: idAlmacen, idArticulo: idArticulo, idColor: idColor}), function(json){
					objCantidades.setValue('');
					var rango = json.data.rangoTalle,
						stock = json.data.stock,
						table = $('<table>').addClass('w100p'),
						thead = $('<thead>'),
						tbody = $('<tbody>'),
						trh = $('<tr>').addClass('bDarkGray aCenter bold bRightWhite white'),
						trs = $('<tr>').addClass('s13'),
						tri = $('<tr>');

					for (var i = 1; i <= 8; i++) {
						var input = $('<input class="textbox aCenter w25" type="text" validate="EnteroPositivo" />');
						input.blur(function(){
							var total = 0,
								obj = that.tablaDinamica('getSibling', 'cantidades').tableCell.find('input');

							obj.each(
								function(k, v){
									if ($(v).hasClass('talleValido')) {
										if ($.validateEnteroPositivo($(v))) {
											total += funciones.toInt($(v).val());
										}
									} else {
										$(v).val('');
									}
								});

							that.tablaDinamica('getSibling', 'total').setValue(total);
						});
						(rango[i]) ? input.addClass('talleValido') : input.disable();

						trh.append($('<th>').text(rango[i] ? rango[i] : '--'));
						trs.append($('<td>').append(rango[i] ? stock[i] : '--'));
						tri.append($('<td>').append(input));
					}
					objCantidades.setValue(table.append(thead.append(trh), tbody.append(trs).append(tri)));
				});
				this.data('idArticulo', idArticulo);
				this.tablaDinamica('getSibling', 'idAlmacen').disable();
				this.tablaDinamica('getSibling', 'idArticulo').disable();
				this.tablaDinamica('getSibling', 'idColor').disable();
			}
		}
	}

	function hayErrorGuardar(){
		var ajustes = $('.tabladinamica').tablaDinamica('getJson');
		if (ajustes.length == 0)
			return 'Debe realizar al menos un ajuste';
		return false;
	}

	function guardar(){
		funciones.guardar(funciones.controllerUrl('agregar'), armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			ajustes: $('.tabladinamica').tablaDinamica('getJson')
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				cambiarModo('agregar');
				break;
			case 'buscar':
				cambiarModo('agregar');
				break;
			case 'editar':
				cambiarModo('agregar');
				break;
			case 'agregar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divGarantias' class='w100p customScroll'>
		<table id='tablaDinamica' class='tabladinamica registrosAlternados'></table>
	</div>
</div>
<div id='programaPie'>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'comercial/notas_de_credito/generacion/devolucion/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
	</div>
</div>
