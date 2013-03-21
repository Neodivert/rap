<?php
	/*** Info: ***
	Conjunto de funciones relacionadas con las notificaciones.

	/*** Licencia ***
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
	*/
	class Notificador {

		function ObtenerPreferenciasBD( $bd, $id_usuario )
		{
			$notificaciones = array(
				'nueva_perla' => 'participante',
				'nuevo_comentario' => 'participante',
				'nueva_nota' => 'nunca',
				'nuevo_usuario' => 'siempre'
			);
	
			$notificaciones_ = $bd->Consultar( "SELECT * FROM notificaciones_email WHERE usuario='$id_usuario'" );

			$notificaciones_ = $notificaciones_->fetch_assoc();

			$notificaciones['nueva_perla'] = $notificaciones_['nueva_perla'];
			$notificaciones['nuevo_comentario'] = $notificaciones_['nuevo_comentario'];
			$notificaciones['nueva_nota'] = $notificaciones_['nueva_nota'];
			$notificaciones['nuevo_usuario'] = $notificaciones_['nuevo_usuario'];

			return $notificaciones;
		}

		function EstablecerPreferenciasBD( $bd, $id_usuario, $notificaciones )
		{
			foreach( $notificaciones as $tipo => $frecuencia ){
				if( $tipo != 'accion' ){
					$bd->Consultar( "UPDATE notificaciones_email SET $tipo='$frecuencia' WHERE usuario=$id_usuario" );
				}
			}
		}

		function NotificarNuevaPerlaBD( $bd, $id_perla )
		{
			$titulo = 'RAP - Nueva perla subida';
			$cuerpo = "Se ha subido una nueva perla a la RAP. Para verla pulsa en el siguiente enlace: \r\n";
			$cuerpo .= "http://www.neodivert.com/rap/general.php?seccion=mostrar_perla&perla=$id_perla\r\n";

			$consulta  = '(SELECT DISTINCT email FROM usuarios JOIN notificaciones_email ON usuarios.id = notificaciones_email.usuario ';
			$consulta .= 'WHERE email IS NOT NULL AND usuarios.cod_validacion_email IS NULL) ';
			$consulta .= 'UNION ';
			$consulta .= '(SELECT DISTINCT email FROM usuarios JOIN notificaciones_email ON usuarios.id = notificaciones_email.usuario ';
			$consulta .= "JOIN participantes ON notificaciones_email.usuario = participantes.usuario AND participantes.perla=$id_perla ";
			$consulta .= 'WHERE email IS NOT NULL AND usuarios.cod_validacion_email IS NULL)';

			/*
			(SELECT DISTINCT email FROM usuarios JOIN notificaciones_email ON usuarios.id = notificaciones_email.usuario WHERE email IS NOT NULL AND usuarios.cod_validacion_email IS NULL) UNION (SELECT DISTINCT email FROM usuarios JOIN notificaciones_email ON usuarios.id = notificaciones_email.usuario JOIN participantes ON notificaciones_email.usuario = participantes.usuario AND participantes.perla=186 WHERE email IS NOT NULL AND usuarios.cod_validacion_email IS NULL)
			*/

			$emails = $bd->Consultar( $consulta );

			$this->EnviarNotificacion( $emails, $titulo, $cuerpo );
		}

		private function EnviarNotificacion( $emails, $titulo, $cuerpo ){
			ini_set('sendmail_from', 'neodivert@gmail.com' );

			$destinatarios = '';
			while( $email = $emails->fetch_array() ){
				$destinatarios .= "{$email['email']}, ";
			}

			die( "Destinatarios: $destinatarios" );
			if( !mail( $destinatarios, $titulo, $cuerpo ) ){
				die( 'Error enviando las notificaciones' );
			}
		}
	}
?>
