<?php
	session_start();

	require_once '../config/rutas.php';
	require_once DIR_CLASES . 'perla.php';
	require_once DIR_CLASES . 'comentario.php';
	require_once DIR_CLASES . 'usuario.php';
	require_once DIR_CLASES . 'rap.php';
	require_once DIR_CLASES . 'notificador.php';

	$rap = RAP::ObtenerInstancia();

	if( isset( $_POST['accion'] ) ){
		$comentario = new Comentario;

		switch( $_POST['accion'] ){
			case 'Subir comentario':
				$comentario->CargarDesdeRegistro( $_POST );
				$comentario->InsertarBD( BD::ObtenerInstancia(), $_SESSION['id'] );

				$notificador = new Notificador;
				$notificador->NotificarNuevoComentarioBD( BD::ObtenerInstancia(), $comentario->ObtenerPerla(), $_SESSION['id'] );

				header( "Location: ../../general.php?seccion=mostrar_perla&perla={$comentario->ObtenerPerla()}&notificacion=OK_COMENTARIO_SUBIDO" );
				exit();
			break;
			case 'Modificar comentario':
				$comentario->CargarDesdeRegistro( $_POST );
				$comentario->InsertarBD( BD::ObtenerInstancia(), $_SESSION['id'] );
				header( "Location: ../../general.php?seccion=mostrar_perla&perla={$_POST['perla']}&notificacion=OK_COMENTARIO_MODIFICADO" );
				exit();
			break;
			case 'Borrar comentario':
				$comentario->EstablecerId( $_POST['comentario'] );
				$comentario->BorrarBD( BD::ObtenerInstancia() );
				header( "Location: ../../general.php?seccion=mostrar_perla&perla={$_POST['perla']}&notificacion=OK_COMENTARIO_BORRADO" );
				exit();
			break;
			default:
				die( 'Accion desconocida' );
			break;
		}
	}
?>
