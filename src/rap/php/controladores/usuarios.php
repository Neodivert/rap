<?php
	session_start();

	require_once '../config/rutas.php';
	//require_once DIR_LIB . 'perlas.php';
	require_once DIR_LIB . 'usuarios.php';

	if( isset( $_POST['accion'] ) ){
		switch( $_POST['accion'] ){
			case 'Establecer email':
				EstablecerEmail( $_SESSION['id'], $_POST['email'] );
				header( 'Location: ../../general.php?seccion=aviso&aviso=AV_EMAIL_ESTABLECIDO' );
				exit();
			break;
			case 'Validar email':
				if( ValidarEmail( $_SESSION['id'], $_POST['cod_validacion_email'] ) ){
					header( 'Location: ../../general.php?seccion=aviso&aviso=AV_EMAIL_VALIDADO' );
				}else{
					header( 'Location: ../../general.php?seccion=aviso&error=ERROR_VALIDANDO_EMAIL' );
				}
				exit();
			break;
			case 'Establecer notificaciones':
				EstablecerNotificacionesEmail( $_SESSION['id'], $_POST );
				header( 'Location: ../../general.php?seccion=aviso&aviso=AV_NOTIFICACIONES_ESTABLECIDAS' );
				exit();
			break;
			default:
				die( 'Accion desconocida' );
			break;
		}
	}
?>
