<?php
	session_start();

	require_once '../config/rutas.php';
	//require_once DIR_LIB . 'perlas.php';
	require_once DIR_CLASES . 'rap.php';
	require_once DIR_CLASES . 'usuario.php';
	require_once DIR_CLASES . 'bd.php';

	$rap = RAP::ObtenerInstancia();
	

	//die( "USUARIOS: " . print_r( $_POST ) );

	if( isset( $_POST['accion'] ) ){
		switch( $_POST['accion'] ){
			case 'Entrar':	
				if( !Usuario::Loguear( $_POST['nombre'], $_POST['contrasenna'] ) ){
					header( 'Location: ../../general.php' );
				}else{
					die( 'Usuario o contraseña incorrectos' );
				}
			break;
			/*
			case 'Establecer email':
				$usuario = Usuario::ObtenerInstancia( $_SESSION['id'] );

				EstablecerEmail( $_SESSION['id'], $_POST['email'] );
				header( 'Location: ../../general.php?seccion=aviso&aviso=AV_EMAIL_ESTABLECIDO' );
				exit();
			break;
			*/
			case 'Cambiar contraseña':
				$usuario = Usuario::ObtenerInstancia( $_SESSION['id'] );
				$usuario->CambiarContrasennaBD( BD::ObtenerInstancia(), $_POST['contrasenna'] );

				RedirigirUltimaDireccion( 'OK_CONTRASENNA_CAMBIADA' );
				//CambiarContrasenna( $_POST['contrasenna'] );
				// Si la contraseña pudo cambiarse, muestra un aviso al usuario.
				// CONFIRMAR QUE SE CAMBIA Y TENER EN CUENTA ERRORES.
				//MostrarAviso( 'Contrasenna cambiada!' );
			break;
			/*
			case 'Validar email':
				$usuario = Usuario::ObtenerInstancia( $_SESSION['id'] );
				if( ValidarEmail( $_SESSION['id'], $_POST['cod_validacion_email'] ) ){
					header( 'Location: ../../general.php?seccion=aviso&aviso=AV_EMAIL_VALIDADO' );
				}else{
					header( 'Location: ../../general.php?seccion=aviso&error=ERROR_VALIDANDO_EMAIL' );
				}
				exit();
			break;
			case 'Establecer notificaciones':
				$usuario = Usuario::ObtenerInstancia( $_SESSION['id'] );
				EstablecerNotificacionesEmail( $_SESSION['id'], $_POST );
				header( 'Location: ../../general.php?seccion=aviso&aviso=AV_NOTIFICACIONES_ESTABLECIDAS' );
				exit();
			break;
			*/
			default:
				die( "Accion desconocida ({$_POST['accion']})" );
			break;
		}
	}

	die( print_r( $_POST ) );
?>

