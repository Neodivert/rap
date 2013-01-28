<?php
	require_once 'recursos/config.php';

	if( isset( $_GET['aviso'] ) ){
		echo "{$avisos[$_GET['aviso']]}<br/>";
	}else if( isset( $_GET['error'] ) ){
		echo "{$errores[$_GET['error']]}<br/>";
	}
	echo '<a href="general.php?seccion=lista_perlas">Volver a la pagina principal</a>';
?>
