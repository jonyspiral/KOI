<?php

?>

<style>
	#divAjustes {
		height: 490px;
	}
</style>

<script type='text/javascript'>
	var objGlobal = {};

	$(document).ready(function(){
		tituloPrograma = 'Ajustes de stock MP';
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
						width: '120px',
						css: {textAlign: 'center'},
						cellType: 'S',
						template: '<select class="textbox obligatorio w120"><option value="POS">POS</option><option value="NEG">NEG</option></select>'
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
						id: 'idMaterial',
						name: 'Material',
						width: '200px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox w180" name="Material" />'
					},
					{
						id: 'idColor',
						name: 'Color',
						width: '140px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox w120" name="ColorMateriaPrima" />',
						focus: function(){
							this.tablaDinamica('getMe').valueElement.val('').attr('alt', 'idMaterial=' + this.tablaDinamica('getSibling', 'idMaterial').getValue());
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
		$('#divAjustes').fixedHeader({target: 'table'});
		cambiarModo('agregar');
	});

	function limpiarScreen() {
		$('.tablaDinamica').tablaDinamica('clean');
	}

	function focusMotivo() {
		var that = this,
			objCantidades = this.tablaDinamica('getSibling', 'cantidades'),
			idAlmacen = this.tablaDinamica('getSibling', 'idAlmacen').getValue(),
			idMaterial = this.tablaDinamica('getSibling', 'idMaterial').getValue(),
			idColor = this.tablaDinamica('getSibling', 'idColor').getValue();

		if (this.data('idMaterial') != idMaterial) {
			if (idAlmacen && idMaterial && idColor) {
				$.postJSON(funciones.controllerUrl('getInfoMaterial', {idAlmacen: idAlmacen, idMaterial: idMaterial, idColor: idColor}), function(json){
					objCantidades.setValue('');
					var rango = json.data.rango,
						stock = json.data.stock,
						table = $('<table>').addClass('w100p'),
						thead = $('<thead>'),
						tbody = $('<tbody>'),
						trh = $('<tr>').addClass('bDarkGray aCenter bold bRightWhite white'),
						trs = $('<tr>').addClass('s13'),
						tri = $('<tr>');

					for (var i = 1; i <= 8; i++) {
						var input = $('<input class="textbox aCenter w25" type="text" validate="DecimalPositivo" />');
						input.blur(function(){
							var total = 0,
								obj = that.tablaDinamica('getSibling', 'cantidades').tableCell.find('input');

							obj.each(
								function(k, v){
									if ($(v).hasClass('talleValido')) {
										if ($.validateDecimalPositivo($(v))) {
											total += funciones.toFloat($(v).val());
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
				this.data('idMaterial', idMaterial);
				this.tablaDinamica('getSibling', 'idAlmacen').disable();
				this.tablaDinamica('getSibling', 'idMaterial').disable();
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
	<div id='divAjustes' class='w100p customScroll'>
		<table id='tablaDinamica' class='tabladinamica registrosAlternados'></table>
	</div>
</div>
<div id='programaPie'>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'produccion/stock_mp/ajustes/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
	</div>
</div>
