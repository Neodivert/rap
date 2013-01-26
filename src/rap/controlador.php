<?php
	session_start();

	require_once 'secciones/aviso.php';
	require_once 'recursos/config.php';
	require_once DIR_LIB . 'perlas.php';

	if( isset( $_POST['accion'] ) ){
		switch( $_POST['accion'] ){
			case 'Modificar perla':
				header( "Location: general.php?seccion=subir_perla&modificar={$_POST['perla']}" );
				exit();
			break;
			case 'Denunciar perla':
				if( $_POST['num_denuncias'] >= 3 ){
					BorrarPerla( $_POST['perla'] );
					header( 'Location: general.php?seccion=aviso&mensaje=AV_PERLA_BORRADA' );
					exit();
				}else{
					DenunciarPerla( $_SESSION['id'], $_POST['perla'] );
					header( 'Location: general.php?seccion=aviso&mensaje=AV_PERLA_DENUNCIADA' );
					exit();
				}
			break;
			case 'Borrar perla':
				BorrarPerla( $_POST['perla'] );
				header( 'Location: general.php?seccion=aviso&mensaje=AV_PERLA_BORRADA' );
				exit();
			break;
			case 'Cancelar voto borrado':
				CancelarDenunciaPerla( $_SESSION['id'], $_POST['perla'] );
				header( 'Location: general.php?seccion=aviso&mensaje=AV_DENUNCIA_ELIMINADA' );
				exit();
			break;
			default:
				die( 'Accion desconocida' );
			break;
		}
	}
?>
