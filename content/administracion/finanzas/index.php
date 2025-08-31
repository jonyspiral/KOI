<?php
?>

<style>
	#container {
		width: 100%;
		height: 400px;
	}
</style>

<script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/highstock.js'></script>

<script type='text/javascript'>
	var chart; // globally available
	var cliente = null;

	$(document).ready(function(){
		tituloPrograma = 'Gráfico';
		cambiarModo('inicio');



		var seriesOptions = [],
			yAxisOptions = [],
			seriesCounter = 0,
			names = ['MSFT', 'AAPL', 'GOOG'],
			colors = Highcharts.getOptions().colors;

		$.each(names, function(i, name) {

			$.getJSON('http://www.highcharts.com/samples/data/jsonp.php?filename='+ name.toLowerCase() +'-c.json&callback=?',	function(data) {

				seriesOptions[i] = {
					name: name,
					data: data
				};

				// As we're loading the data asynchronously, we don't know what order it will arrive. So
				// we keep a counter and create the chart when all the data is loaded.
				seriesCounter++;

				if (seriesCounter == names.length) {
					createChart();
				}
			});
		});



		// create the chart when all data is loaded
		function createChart() {

			chart = new Highcharts.StockChart({
												  chart: {
													  renderTo: 'container'
												  },

												  rangeSelector: {
													  selected: 4
												  },

												  yAxis: {
													  labels: {
														  formatter: function() {
															  return (this.value > 0 ? '+' : '') + this.value + '%';
														  }
													  },
													  plotLines: [{
																	  value: 0,
																	  width: 2,
																	  color: 'silver'
																  }]
												  },

												  plotOptions: {
													  series: {
														  compare: 'percent'
													  }
												  },

												  tooltip: {
													  pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.change}%)<br/>',
													  valueDecimals: 2
												  },

												  series: seriesOptions
											  });
		}
	});

	function limpiarScreen(){
		$('#divVentas').html('');
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if ($('#inputBuscarFecha').val() == '')
			return $('#inputBuscarFecha').val('');
		var url = '/content/administracion/rrhh/fichajes/buscar.php?';
		url += 'desde=' + $('#inputBuscarDesde').val();
		url += '&hasta=' + $('#inputBuscarHasta').val();
		url += '&modo=' + $(':radio:checked').val();
		url += '&personal=' + $('#inputBuscarEmpleado_selectedValue').val();

		$.showLoading();
		$('#divVentas').load(url, function(result) {
			try {
				var json = $.parseJSON(result);
				switch (funciones.getJSONType(json)) {
					case funciones.jsonNull:
						$.error('Ocurrió un error al intentar realizar la consulta');
						break;
					case funciones.jsonError:
						$.error(funciones.getJSONMsg(json));
						$('#divVentas').html('');
						cambiarModo('inicio');
						break;
					case funciones.jsonInfo:
						$.info(funciones.getJSONMsg(json));
						$('#divVentas').html('');
						break;
				}
			} catch (ex) {
				cambiarModo('buscar');
			}
			$.hideLoading();
		});
		return false;
	}

	function pdfClick(){
		var finalUrl = urlToExport('pdf');
		if (finalUrl)
			funciones.pdfClick(finalUrl);
	}

	function xlsClick(){
		var finalUrl = urlToExport('xls');
		if (finalUrl)
			funciones.xlsClick(finalUrl);
	}

	function urlToExport(tipo){
		var url = '/content/administracion/rrhh/fichajes/get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?';
		url += 'desde=' + $('#inputBuscarDesde').val();
		url += '&hasta=' + $('#inputBuscarHasta').val();
		url += '&modo=' + $(':radio:checked').val();
		url += '&personal=' + $('#inputBuscarEmpleado_selectedValue').val();
		return url;
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
	<div id="container"></div>
	<div id='divFichajes' class='w100p customScroll'>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label class='filtroBuscar'>Personal:</label>
			<input id='inputBuscarEmpleado' class='textbox autoSuggestBox filtroBuscar w190' name='Personal' alt='' />
		</div>
		<div>
			<label class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w170' to='inputBuscarHasta' alt='' validate="Fecha" />
		</div>
		<div>
			<label class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w170' from='inputBuscarHasta' alt='' validate="Fecha"/>
		</div>
		<label class='filtroBuscar'>Modo:</label>
		<div id='radioGroupModo' class='customRadio w200 inline-block' default="rdModo_E">
			<input id='rdModo_E' type='radio' name='radioGroupModo' value='E' /><label for='rdModo_E'>Por empleado</label>
			<input id='rdModo_F' type='radio' name='radioGroupModo' value='F' /><label for='rdModo_F'>Por fecha</label>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>