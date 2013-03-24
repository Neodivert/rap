<?php
	/*** 
	 parametros.php
	 Parametros de configuracion de la RAP.
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

	// Tiempo que tarda una perla subida en publicarse (en horas).
	// TODO: usarla.
	define( 'TIEMPO_PUBLICACION', 24 );

	// Notificaciones de acciones realizadas correctamente al usuario.
	$notificaciones_buenas = array(
		'OK_PERLA_BORRADA'	=> 'La perla ha sido correctamente borrada',
		'OK_PERLA_SUBIDA' => 'La perla se ha subido correctamente',
		'OK_PERLA_PUNTUADA'=> 'La perla se ha puntuado correctamente',
		'OK_PERLA_DENUNCIADA'=> 'Tu voto para eliminar la perla se ha recibido correctamente',
		'OK_DENUNCIA_ELIMINADA' => 'Tu voto para eliminar la perla se ha eliminado correctamente',
		'OK_EMAIL_ESTABLECIDO' => 'Email establecido correctamente',
		'OK_EMAIL_VALIDADO' => 'Email validado correctamente',
		'OK_NOTIFICACIONES_ESTABLECIDAS' => 'Las notificaciones por email se han establecido correctamente',
		'OK_COMENTARIO_BORRADO' => 'El comentario se ha borrado correctamente',
		'OK_COMENTARIO_MODIFICADO' => 'El comentario ha sido modificado correctamente',
		'OK_COMENTARIO_SUBIDO' => 'El comentario se ha subido correctamente',
		'OK_CONTRASENNA_CAMBIADA' => 'La contraseña se ha cambiado correctamente',
		'OK_AVATAR_CAMBIADO' => 'El avatar se ha cambiado correctamente',
		'OK_AVATAR_BORRADO' => 'El avatar se ha borrado correctamente',
		'OK_NOTIFICACIONES_CAMBIADAS' => 'Las preferencias sobre las notificaciones email se han cambiado correctamente'
	);

	// Notificaciones de error posibles al usuario.
	$notificaciones_malas = array(
		'ERROR_VALIDANDO_EMAIL' => 'Error validando el email',
		'ERROR_CAMBIANDO_AVATAR' => 'Error cambiando el avatar',
		'ERROR_SUBIENDO_PERLA' => 'Hubo algún error subiendo la perla'
	);
?>
