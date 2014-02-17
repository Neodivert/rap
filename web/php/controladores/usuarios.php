<?php
	/*** 
	 usuarios.php
	 Script php que procesa las peticiones de los usuarios relacionadas con los
	 propios usuarios.
	 Copyright (C) Moises J. Bonilla Caraballo 2012 - 2013.
	****
	 This file is part of RAP.

	 RAP is free software: you can redistribute it and/or modify
	 it under the terms of the GNU General Public License as published by
	 the Free Software Foundation, either version 3 of the License, or
	 (at your option) any later version.

	 RAP is distributed in the hope that it will be useful,
	 but WITHOUT ANY WARRANTY; without even the implied warranty of
	 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 GNU General Public License for more details.

	 You should have received a copy of the GNU General Public License
	 along with RAP.  If not, see <http://www.gnu.org/licenses/>.
	***/

	// Permite el uso de variables de sesion.
	session_start();

	// "Requires" necesarios.
	require_once '../config/rutas.php';
	require_once DIR_CLASES . 'rap.php';
	require_once DIR_CLASES . 'usuario.php';
	require_once DIR_CLASES . 'bd.php';
	require_once DIR_CLASES . 'notificador.php';

	// Inicializa la RAP estableciendo los datos de conexion a la BD. 
	$rap = RAP::ObtenerInstancia();

	// Comprueba que realmente hubo una peticion por parte del usuario.
	if( isset( $_POST['accion'] ) ){
		// Hubo peticion por parte del usuario. Diferencia el tipo de peticion
		// segun el valor de $_POST['accion'].
		switch( $_POST['accion'] ){


			// El usuario desea entrar (hacer login) en la RAP.
			case 'Entrar':	
				// Trata de loguear al usuario comprobando su contrasenna en la BD.
				if( !Usuario::Loguear( $_POST['nombre'], $_POST['contrasenna'] ) ){
					// Login correcto. Redirige a la pagina principal.
					header( 'Location: ../../general.php' );
				}else{
					// Error entrando. Muestra error.
					die( 'Usuario o contraseña incorrectos' );
				}
			break;


			// El usuario desea cambiar su avatar.
			case 'Cambiar avatar':
				// Obtiene la informacion del usuario actual.
				$usuario = Usuario::ObtenerInstancia( $_SESSION['id'] );

				// Intenta escribir en disco el nuevo avatar del usuario.
				$res = $usuario->InsertarAvatarBD( $_FILES['avatar'] );

				// Redirige al usuario a su perfil y muestrale una notificacion
				// u otra segun si todo ha ido bien o hubo algun error.
				if( !$res ){ 
					header( 'Location: ../../general.php?seccion=perfil&notificacion=OK_AVATAR_CAMBIADO' );
				}else{
					header( 'Location: ../../general.php?seccion=perfil&notificacion=ERROR_CAMBIANDO_AVATAR' );
				}
			break;

		
			// El usuario desea borrar su avatar.
			case 'Borrar avatar':
				// Obtiene la informacion del usuario actual.
				$usuario = Usuario::ObtenerInstancia( $_SESSION['id'] );

				// Borra de disco el avatar del usuario actual.
				$usuario->BorrarAvatarBD();

				// Redirige al usuario a su perfil y le notifica que todo fue bien.
				header( 'Location: ../../general.php?seccion=perfil&notificacion=OK_AVATAR_BORRADO' );
			break;


			// El usuario desea establecer su email.
			case 'Establecer email':
				// Obtiene la informacion del usuario actual.
				$usuario = Usuario::ObtenerInstancia( $_SESSION['id'] );

				// Inserta el email en la BD y envia un correo al usuario con el
				// codigo de validacion.
				$usuario->EstablecerEmailBD( BD::ObtenerInstancia(), $_POST['email'] );

				// Redirige al usuario a su perfil y le notifica que todo fue bien.
				header( 'Location: ../../general.php?seccion=perfil&notificacion=OK_EMAIL_ESTABLECIDO' );
				exit();
			break;


			// El usuario desea cambiar su contrasenna.
			case 'Cambiar contraseña':
				// Obtiene la informacion del usuario actual.
				$usuario = Usuario::ObtenerInstancia( $_SESSION['id'] );

				// Cambia la contrasenna del usuario en la BD.
				$usuario->CambiarContrasennaBD( BD::ObtenerInstancia(), $_POST['contrasenna'] );

				// Redirige al usuario a su perfil y le notifica que todo fue bien.
				RedirigirUltimaDireccion( 'OK_CONTRASENNA_CAMBIADA' );
			break;

			
			// El usuario desea validar su email.
			case 'Validar email':
				// Obtiene la informacion del usuario actual.
				$usuario = Usuario::ObtenerInstancia( $_SESSION['id'] );

				// Intenta validar el email del usuario. Redirige al usuario a su
				// perfil y le muestra una notificacion u otra segun si todo ha
				// ido bien o hubo algun error.
				if( !$usuario->ValidarEmailBD( BD::ObtenerInstancia(), $_POST['cod_validacion_email'] ) ){
					header( 'Location: ../../general.php?seccion=perfil&notificacion=OK_EMAIL_VALIDADO' );
				}else{
					header( 'Location: ../../general.php?seccion=perfil&notificacion=ERROR_VALIDANDO_EMAIL' );
				}
				exit();
			break;


			// El usuario desea establecer sus preferencias sobre notificaciones
			// email.
			case 'Establecer notificaciones':
				// Obtiene la informacion del usuario actual.
				$usuario = Usuario::ObtenerInstancia( $_SESSION['id'] );

				// Borra la variable $_POST['accion'], dejando unicamente las 
				// preferencias del usuario en cuanto a las notificaciones por 
				// email.
				unset( $_POST['accion'] );

				// Actualiza en la BD las preferencias del usuario actual.
				$notificador = Notificador::ObtenerInstancia();
				$notificador->EstablecerPreferenciasBD( BD::ObtenerInstancia(), $usuario->ObtenerId(), $_POST );

				// Redirige al usuario a su perfil y le muestra una notificacion
				// de que todo fue bien.
				header( 'Location: ../../general.php?seccion=perfil&notificacion=OK_NOTIFICACIONES_CAMBIADAS' );
				exit();
			break;


			// El usuario desea desconectarse (hacer logout).
			case 'Logout':
				// Destruye la sesion.
				unset( $_SESSION['id'] );

				// Vuelve a la pagina inicial (pantalla de login).
				header( 'Location: ../../index.php' );
				exit();
			break;
			default:
				die( "Accion desconocida ({$_POST['accion']})" );
			break;
		}
	}
	exit();
	die( print_r( $_POST ) );
?>

