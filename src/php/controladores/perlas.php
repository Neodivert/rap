<?php
	/*** 
	 perlas.php
	 Script php que procesa las peticiones de los usuarios relacionadas con las
	 perlas.
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
	require_once DIR_CLASES . 'perla.php';
	require_once DIR_CLASES . 'rap.php';
	require_once DIR_CLASES . 'notificador.php';
	require_once DIR_LIB . 'utilidades.php';

	// Inicializa la RAP estableciendo los datos de conexion a la BD.
	$rap = RAP::ObtenerInstancia();

	// Comprueba que realmente hubo una peticion por parte del usuario.
	if( isset( $_POST['accion'] ) ){
		// Diferencia el tipo de peticion segun el valor de $_POST['accion'].
		switch( $_POST['accion'] ){


			// El usuario desea subir una perla (nueva o modificada).
			case 'Subir perla':
				// Carga la perla desde el formulario (registro $_POST).
				$perla = new Perla;
				$perla->CargarDesdeFormulario( $_POST );

				// Diferencia si esta subiendo una perla nueva (aun no tiene id
				// asignada) o se trata de una perla modificada.
				$nueva_perla = ($perla->ObtenerId() == null);

				// Si se trata de una perla visual se obtiene la imagen asociada.
				if( !$_FILES['imagen']['error'] ){
					$perla->EstablecerImagen( $_FILES['imagen'] );
				}else{
					if( $_FILES['imagen']['error'] != 4 ){
						MostrarErrorFichero( $_FILES['imagen']['error'] );
					}
				}

				// Si se esta modificando una perla y el usuario lo pide borramos
				// de disco la imagen asociada.
				if( isset( $_POST['borrar_imagen'] ) ){
					$perla->BorrarImagenBD();
				}

				// Intenta insertar la perla en la BD.
				if( !$perla->InsertarBD( BD::ObtenerInstancia(), $_SESSION['id'] ) ){
					// La perla se inserto correctamente en la BD.
					if( $nueva_perla ){
					 	// Se trata de una perla nueva. Se notifica por email a 
						// los usuarios pertinentes que se ha subido una perla.
						$notificador = Notificador::ObtenerInstancia();
						$notificador->NotificarNuevaPerlaBD( BD::ObtenerInstancia(), $perla->ObtenerId(), $_SESSION['id'] );
					}

					// Redirige al usuario a la ultima direccion visitada y le
					// muestra una notificacion de que todo fue bien.
					RedirigirUltimaDireccion( 'OK_PERLA_SUBIDA' );
				}else{
					// Error subiendo la perla. Redirigue al usuario a la ultima
					// direccion visitada y se le notifica el error.
					RedirigirUltimaDireccion( 'ERROR_SUBIENDO_PERLA' );
				}
			break;


			// El usuario desea modificar una perla.
			case 'Modificar perla':
				// Redirige al usuario a la pagina para modificar la perla.
				header( "Location: ../../general.php?seccion=subir_perla&modificar={$_POST['perla']}" );
				exit();
			break;


			// El usuario desea borrar una perla.
			case 'Borrar perla':
				// Obtiene la id de la perla y la borra de la BD.
				// TODO: Usar un metodo estatico Perla::BorrarBD?.
				$perla = new Perla;
				$perla->EstablecerId( $_POST['perla'] );
				$perla->BorrarBD( BD::ObtenerInstancia() );

				// Redirige al usuario a la lista de perlas y le notifica que
				// la perla se ha borrado correctamente.
				header( "Location: ../../general.php?seccion=lista_perlas&notificacion=OK_PERLA_BORRADA" );
				exit();
			break;

			// El usuario desea puntuar una perla.
			case 'Puntuar Perla':
				// Obtiene la id de la perla y sube la puntuacion a la BD.
				// TODO: Usar un metodo estatico Perla::PuntuarBD?.
				$perla = new Perla;
				$perla->EstablecerId( $_POST['perla'] );
				$perla->PuntuarBD( BD::ObtenerInstancia(), $_POST['nota'], $_SESSION['id'] );

				// Notifica por email a los usuarios pertinentes que la nota de la
				// perla ha cambiado.
				$notificador = Notificador::ObtenerInstancia();
				$notificador->NotificarNuevaNotaBD( BD::ObtenerInstancia(), $perla->ObtenerId(), $_SESSION['id'] );

				// Redigire al usuario a la ultima direccion visitada y le muestra
				// una notificacion de que todo ha ido bien.
				RedirigirUltimaDireccion( 'OK_PERLA_PUNTUADA' );
			break;

			
			// Error: accion desconocida.
			default:
				die( 'Accion desconocida' );
			break;
		}
		exit();
	}else{
		die( 'Accion desconocida (2)' );
	}
?>
