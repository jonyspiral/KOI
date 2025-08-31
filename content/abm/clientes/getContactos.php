<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/clientes/buscar/')) { ?>
<?php
$idCliente = Funciones::get('idCliente');
try {
	if (isset($idCliente)){
		$cliente = Factory::getInstance()->getClienteTodos($idCliente);
		foreach ($cliente->contactos as $contacto) {
			$tabla1 = new HtmlTable(array('cantRows' => 5, 'cantCols' => 2, 'id' => 'tablaDatos51', 'cellSpacing' => 10));
			$tabla1->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Nombre:</label>';
			$cells[0][0]->style->width = '135px';
			$cells[0][1]->content = '<label>' . $contacto->nombre . '</label>';
			$cells[0][1]->style->width = '260px';
			$cells[1][0]->content = '<label>Apellido:</label>';
			$cells[1][1]->content = '<label>' . $contacto->apellido . '</label>';
			$cells[2][0]->content = '<label>Área de la empresa:</label>';
			$cells[2][1]->content = '<label>' . $contacto->areaEmpresa->nombre . '</label>';
			$cells[3][0]->content = '<label>Referencia:</label>';
			$cells[3][1]->content = '<label>' . $contacto->referencia . '</label>';
			$cells[4][0]->content = '<label>Observaciones:</label>';
			$cells[4][1]->content = '<label>' . $contacto->observaciones . '</label>';

			
			$tabla2 = new HtmlTable(array('cantRows' => 4, 'cantCols' => 2, 'id' => 'tablaDatos52', 'cellSpacing' => 10));
			$tabla2->getRowCellArray($rows, $cells);

			$cantUsu = count($contacto->usuarios);
			$cells[0][0]->content = '<label>Teléfono 1:</label>';
			$cells[0][0]->style->width = '135px';
			$cells[0][1]->content = '<label>' . $contacto->telefono1 . '</label>';
			$cells[0][1]->style->width = '260px';
			$cells[1][0]->content = '<label>Celular:</label>';
			$cells[1][1]->content = '<label>' . $contacto->celular . '</label>';
			$cells[2][0]->content = '<label>Email 1:</label>';
			$cells[2][1]->content = '<label>' . $contacto->email1 . '</label>';
			$cells[3][0]->content = '<label class="blue cPointer underline"><a href="' . $contacto->getLink() . '" target="_blank">Más datos</label>';
			if ($cantUsu > 0)
				$cells[4][0]->content = '<label>Usuarios</label>';
			$j = 4;
			foreach($contacto->usuarios as $usuario) {
				$cells[$j][1]->content = '<label>' . $usuario->id . '</label>';
				$j++;
			}

			
			echo '<div>';
			echo '	<div><img src="/img/varias/perfil.gif" width="20" height="17" /> ' . $contacto->nombreApellido . ' (' . $contacto->areaEmpresa->nombre . ' ' . $contacto->telefono1 . ' ' . $contacto->celular . ') </div>';
			echo '	<div>';
			echo '		<div class="fLeft w50p">';
			$tabla1->create();
			echo '		</div>';
			echo '		<div class="fRight w50p">';
			$tabla2->create();
			echo '		</div>';
			echo '	</div>';
			echo '</div>';
		}
	} else {
		Html::jsonNull();
	}
} catch (Exception $ex) {
	Html::jsonNull();
}
?>
<?php } ?>