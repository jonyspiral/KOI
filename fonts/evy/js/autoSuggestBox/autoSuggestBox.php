<?php require_once('../../premaster.php'); ?>
<?php

$limit = 10;
$name = Funciones::get('name');
$key = Funciones::get('key');
if ($key != ''){
	$key = ($key == 'givemeall' ? '' : $key);
	try {
		if (!($result = HtmlAutoSuggestBox::getSuggestRows($name, $key, $limit))){
			throw new FactoryExceptionRegistroNoExistente();
		}
		foreach($result as $row) {
			$a = array(
				'id' => $row['id'],
				'nombre' => isset($row['nombre']) ? $row['nombre'] : ''
			);
			if (isset($row['data'])) {
				$a['data'] = $row['data'];
			}
			$array[] = $a;
		}
		Html::jsonEncode('', $array);
	} catch (Exception $ex) {
		Html::jsonEmpty();
	}
}

?>