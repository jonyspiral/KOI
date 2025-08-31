<?php
?>

<style>
	#divIngresos {
		width: 302px;
		margin: 10% 0 0 35%;
	}
	span {
		height: 50px;
		width: 100px;
	}
}
</style>

<script type='text/javascript'>
	var opciones = [
		{'id': 'recibos', 'nombre': 'Recibos'},
		{'id': 'ajuste', 'nombre': 'Ajuste', 'tipo': 'I'},
		{'id': 'aporte_socios', 'nombre': 'Aportes de socios'},
		{'id': 'prestamo', 'nombre': 'Prestamo'}
	];

	$(document).ready(function(){
		tituloPrograma = 'Ingresos';
		cambiarModo('inicio');
		$('.box').livequery(function(){
			$(this).click(function(){
				window.location = window.location + this.id + '/';
			});
		});
		proximaOpcion(0);
	});

	function proximaOpcion(i) {
		var o = opciones[i];
		var div = $('<div id="' + o.id + '" tipo="' + o.tipo + '" class="box fLeft corner5 p10 calibri s16 bold m10 aCenter white cPointer"></div>');
		$('<span class="vaMiddle table-cell">' + o.nombre + '</span>').appendTo(div);
		div.hide().appendTo($('#divIngresos')).show('fast', function(){
			if (i != opciones.length - 1) {
				i += 1;
				proximaOpcion(i);
			}
		});
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divIngresos' class='fLeft customScroll'></div>
</div>
<div id='programaPie'>
</div>