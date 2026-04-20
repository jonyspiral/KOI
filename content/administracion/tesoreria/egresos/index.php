<?php
?>

<style>
	#divIngresos {
		width: 302px;
		margin: 10% 0 0 35%;
	}
	.box {
		border-width: 3px;
	}
	span {
		height: 50px;
		width: 100px;
	}
}
</style>

<script type='text/javascript'>
	var opciones = [
		{'id': 'orden_de_pago', 'nombre': 'Orden de pago'},
		{'id': 'retiro_socios', 'nombre': 'Retiro de Socio'},
		{'id': 'ajuste', 'nombre': 'Ajuste'}
	];

	$(document).ready(function(){
		tituloPrograma = 'Egresos';
		cambiarModo('inicio');
		$('.box').livequery(function(){
			$(this).hover(function(){
				$(this).removeClass('bAllOrange bOrange').addClass('bAllDarkOrange bLightOrange');
			}, function(){
				$(this).removeClass('bAllDarkOrange bLightOrange').addClass('bAllOrange bOrange');
			});
			$(this).click(function(){
				window.location = window.location + this.id + '/';
			});
		});
		proximaOpcion(0);
	});

	function proximaOpcion(i) {
		var o = opciones[i];
		var div = $('<div id="' + o.id + '" tipo="' + o.tipo + '" class="box fLeft corner5 p10 calibri s16 bold m10 bOrange bAllOrange aCenter white cPointer"></div>');
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