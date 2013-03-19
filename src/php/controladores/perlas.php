<?php
	session_start();

	require_once '../config/rutas.php';
	require_once DIR_CLASES . 'perla.php';
	require_once DIR_CLASES . 'rap.php';
	require_once DIR_LIB . 'utilidades.php';

	$rap = RAP::ObtenerInstancia();

	if( isset( $_POST['accion'] ) ){
		switch( $_POST['accion'] ){
			case 'Subir perla':
				$perla = new Perla;
				$perla->CargarDesdeFormulario( $_POST );

				if( !$_FILES['imagen']['error'] ){
					$perla->EstablecerImagen( $_FILES['imagen'] );
				}else{
					if( $_FILES['imagen']['error'] != 4 ){
						MostrarErrorFichero( $_FILES['imagen']['error'] );
					}
				}
				if( isset( $_POST['borrar_imagen'] ) ){
					$perla->BorrarImagenBD();
				}

				$perla->InsertarBD( BD::ObtenerInstancia(), $_SESSION['id'] );
				
				RedirigirUltimaDireccion( 'OK_PERLA_SUBIDA' );
			break;
			case 'Modificar perla':
				header( "Location: ../../general.php?seccion=subir_perla&modificar={$_POST['perla']}" );
				exit();
			break;
			/*
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
			*/
			case 'Borrar perla':
				$perla = new Perla;
				$perla->EstablecerId( $_POST['perla'] );
				$perla->BorrarBD( BD::ObtenerInstancia() );
				//BorrarPerla( $_POST['perla'] );
				header( "Location: ../../general.php?seccion=lista_perlas&notificacion=OK_PERLA_BORRADA" );
				exit();
			break;
			/*
			case 'Cancelar voto borrado':
				CancelarDenunciaPerla( $_SESSION['id'], $_POST['perla'] );
				header( 'Location: ../../general.php?seccion=aviso&aviso=AV_DENUNCIA_ELIMINADA' );
				exit();
			break;
			*/
			// El usuario quiere puntuar una perla.
			case 'Puntuar Perla':
				$perla = new Perla;
				$perla->EstablecerId( $_POST['perla'] );
				$perla->PuntuarBD( BD::ObtenerInstancia(), $_POST['nota'], $_SESSION['id'] );
				RedirigirUltimaDireccion( 'OK_PERLA_PUNTUADA' );
			break;
			default:
				die( 'Accion desconocida' );
			break;
		}
		exit();
	}else{
		die( 'Accion desconocida (2)' );
	}
?>
