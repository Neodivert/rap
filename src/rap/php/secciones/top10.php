<?php
	//include_once 'php/funciones/perlas.php';
	echo '<h1>TOP 10 DE PERLAS</h1>';

	$perlas = $rap->ObtenerTop10Perlas();

	foreach( $perlas as $perla ){
		$modificable = false;
		require DIR_PLANTILLAS . 'perla.php';
	}
?>
