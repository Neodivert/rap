<?php
	session_start();

	require_once 'recursos/config.php';
	require_once DIR_LIB . 'perlas.php';

	if( isset( $_GET['accion'] ) ){
		switch( $_GET['accion'] ){
			case 'denunciar_perla':
				DenunciarPerla( $_SESSION['id'], $_GET['perla'] );
			break;
			default:
				die( 'Accion desconocida' );
			break;
		}
	}
?>
