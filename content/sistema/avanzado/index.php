<?php
?>

<style>
	#divAvanzado {
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
		{'nombre': 'Reiniciar Apache', 'fn': reiniciarApache}
	];

	$(document).ready(function(){
		tituloPrograma = 'Avanzado';
		cambiarModo('inicio');
		$('.box').livequery(function(){
			$(this).click(function(){
				if (this.id != '') {
					opciones[this.id].fn();
				}/* else {
					window.location = window.location + this.id + '/';
				}*/
			});
		});
		proximaOpcion(0);
	});

	function proximaOpcion(i) {
		var o = opciones[i];
		var div = $('<div id="' + i + '" class="box fLeft corner5 p10 calibri s16 bold m10 aCenter white cPointer"></div>');
		$('<span class="vaMiddle table-cell">' + o.nombre + '</span>').appendTo(div);
		div.hide().appendTo($('#divAvanzado')).show('fast', function(){
			if (i != opciones.length - 1) {
				i += 1;
				proximaOpcion(i);
			}
		});
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
	}

	function reiniciarApache() {
		$.showLoading();
		$.get('/content/sistema/avanzado/editar.php');
		checkReiniciarApache(0);
	}

	function checkReiniciarApache(elapsed) {
		$.ajax({
			url: '/content/sistema/avanzado/buscar.php',
			dataType: 'json',
			data: {},
			timeout: 8000,
			error: function() {
				if (elapsed > 60000) {
					$.error('Ocurrió un error al intentar reiniciar apache. Comuníquese con el administrador del sistema', function() {
						$.hideLoading();
					});
				} else {
					checkReiniciarApache(elapsed + 8000);
				}
			},
			success: function(){
				$.success('Apache ha sido reiniciado correctamente', function() {
					$.hideLoading();
				});
			}
		});
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divAvanzado' class='fLeft customScroll'></div>
</div>
<div id='programaPie'>
</div>