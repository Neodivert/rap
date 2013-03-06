<?php
	session_start();

	require_once '../config/rutas.php';
	require_once DIR_CLASES . 'perla.php';
	require_once DIR_CLASES . 'rap.php';

	$rap = RAP::ObtenerInstancia();

	if( isset( $_POST['accion'] ) ){
		switch( $_POST['accion'] ){
			case 'Subir perla':
				$perla = new Perla;
				$perla->CargarDesdeFormulario( $_POST );
				$perla->InsertarBD( BD::ObtenerInstancia(), $_SESSION['id'] );

				header( "Location: ../../general.php?seccion=lista_perlas&notificacion=OK_PERLA_SUBIDA" );
				exit();
			break;
			case 'Modificar perla':
				header( "Location: ../../general.php?seccion=subir_perla&modificar={$_POST['perla']}" );
				exit();
			break;
			case 'Denunciar perla':
				if( $_POST['num_denuncias'] >= 3 ){
					BorrarPerla( $_POST['perla'] );
					header( 'Location: ../../general.php?seccion=aviso&aviso=AV_PERLA_BORRADA' );
					exit();
				}else{
					DenunciarPerla( $_SESSION['id'], $_POST['perla'] );
					header( 'Location: ../../general.php?seccion=aviso&aviso=AV_PERLA_DENUNCIADA' );
					exit();
				}
			break;
			case 'Borrar perla':
				BorrarPerla( $_POST['perla'] );
				header( 'Location: ../../general.php?seccion=aviso&aviso=AV_PERLA_BORRADA' );
				exit();
			break;
			case 'Cancelar voto borrado':
				CancelarDenunciaPerla( $_SESSION['id'], $_POST['perla'] );
				header( 'Location: ../../general.php?seccion=aviso&aviso=AV_DENUNCIA_ELIMINADA' );
				exit();
			break;
			// El usuario quiere puntuar una perla.
			case 'Puntuar Perla':
				PuntuarPerla( $_POST['id_perla'], $_POST['nota'] );
				header( 'Location: ../../general.php?seccion=lista_perlas' );
				exit();
			break;
			default:
				die( 'Accion desconocida' );
			break;
		}
	}else{
		die( 'Accion desconocida (2)' );
	}
?>
