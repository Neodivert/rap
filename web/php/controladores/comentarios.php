<?php
	/*** 
	 comentarios.php
	 Script php que procesa las peticiones de los usuarios relacionadas con los
	 comentarios.
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
	require_once DIR_CLASES . 'comentario.php';
	require_once DIR_CLASES . 'notificador.php';

	// Inicializa la RAP estableciendo los datos de conexion a la BD.
	$rap = RAP::ObtenerInstancia();

	// Comprueba que realmente hubo una peticion por parte del usuario.
	if( isset( $_POST['accion'] ) ){
		// Hubo peticion por parte del usuario, crea un objeto Comentario.
		$comentario = new Comentario;

		// Diferencia el tipo de peticion segun el valor de $_POST['accion'].
		switch( $_POST['accion'] ){
			// El usuario desea subir un comentario.
			case 'Subir comentario': 
				// Carga la informacion desde el formulario (registro $_POST).
				$comentario->CargarDesdeRegistro( $_POST );

				// Inserta el comentario en la BD.
				$comentario->InsertarBD( BD::ObtenerInstancia(), $_SESSION['id'] );

				// Notifica por email a los usuarios pertinentes que se ha subido
				// un nuevo comentario.
				$notificador = Notificador::ObtenerInstancia();
				$notificador->NotificarNuevoComentarioBD( BD::ObtenerInstancia(), $comentario->ObtenerPerla(), $_SESSION['id'] );

				// Procesamiento completado. Redirige a la pagina de la perla en 
				// cuestion (solo se puede comentar desde la pagina de una perla).
				header( "Location: ../../general.php?seccion=mostrar_perla&perla={$comentario->ObtenerPerla()}&notificacion=OK_COMENTARIO_SUBIDO" );
				exit();
			break;

			// El usuario desea modificar un comentario.
			case 'Modificar comentario':
				// Carga la informacion desde el formulario (registro $_POST).
				$comentario->CargarDesdeRegistro( $_POST );

				// Actualiza el comentario en la BD.
				$comentario->InsertarBD( BD::ObtenerInstancia(), $_SESSION['id'] );

				// Procesamiento completado. Redirige a la pagina de la perla en 
				// cuestion (solo se puede comentar desde la pagina de una perla).
				header( "Location: ../../general.php?seccion=mostrar_perla&perla={$_POST['perla']}&notificacion=OK_COMENTARIO_MODIFICADO" );
				exit();
			break;

			// El usuario desea borrar un comentario.
			case 'Borrar comentario':
				// Obtiene la id del comentario a borrar y lo borra de la BD.
				$comentario->EstablecerId( $_POST['comentario'] );
				$comentario->BorrarBD( BD::ObtenerInstancia() );

				// Procesamiento completado. Redirige a la pagina de la perla en 
				// cuestion (solo se puede borrar comentarios desde la pagina de 
				// una perla).
				header( "Location: ../../general.php?seccion=mostrar_perla&perla={$_POST['perla']}&notificacion=OK_COMENTARIO_BORRADO" );
				exit();
			break;
			default:
				die( 'Accion desconocida' );
			break;
		}
	}
?>
