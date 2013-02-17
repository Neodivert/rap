<?php
	session_start();

	require_once '../config/rutas.php';
	require_once DIR_LIB . 'perlas.php';
	require_once DIR_LIB . 'comentarios.php';
	require_once DIR_LIB . 'usuarios.php';

	if( isset( $_POST['accion'] ) ){
		switch( $_POST['accion'] ){
			case 'Borrar comentario':
				BorrarComentario( $_POST['comentario'] );
				header( 'Location: ../../general.php?seccion=aviso&aviso=AV_COMENTARIO_BORRADO' );
				exit();
			break;
			case 'Modificar comentario':
				ModificarComentario( $_POST['comentario'], $_POST['texto'] );
				header( 'Location: ../../general.php?seccion=aviso&aviso=AV_COMENTARIO_MODIFICADO' );
				exit();
			break;
			case 'Subir comentario':
				InsertarComentario( $_POST['perla'], $_POST['texto_comentario'] );
				header( 'Location: ../../general.php?seccion=aviso&aviso=AV_COMENTARIO_SUBIDO' );
				exit();
			break;
			default:
				die( 'Accion desconocida' );
			break;
		}
	}
?>
