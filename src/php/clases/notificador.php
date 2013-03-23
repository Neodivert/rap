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

		function NotificarNuevaPerlaBD( $bd, $id_perla, $id_usuario )
		{
			$titulo = 'RAP - Nueva perla subida';
			$cuerpo = "Se ha subido una nueva perla a la RAP. Para verla pulsa en el siguiente enlace: \r\n";
			$cuerpo .= "http://www.neodivert.com/rap/general.php?seccion=mostrar_perla&perla=$id_perla\r\n";
			$cuerpo .= "\r\n";

			$consulta  = 'SELECT DISTINCT email FROM usuarios ';
			$consulta .= 'WHERE email IS NOT NULL ';
			$consulta .= 'AND usuarios.cod_validacion_email IS NULL ';
			$consulta .= "AND id != $id_usuario ";
			$consulta .= 'AND id IN (SELECT usuario FROM notificaciones_email WHERE nueva_perla = \'siempre\' ';
			$consulta .= 'OR (nueva_perla = \'participante\' ';
			$consulta .= "AND usuario IN (SELECT usuario FROM participantes WHERE perla=$id_perla) ) )";

			$emails = $bd->Consultar( $consulta );

			$this->EnviarNotificacion( $emails, $titulo, $cuerpo );
		}

		function NotificarNuevoComentarioBD( $bd, $id_perla, $id_usuario )
		{
			$titulo = 'RAP - Nuevo comentario subido';
			$cuerpo = "Se ha subido un nuevo comentario a la RAP. Para verlo pulsa en el siguiente enlace: \r\n";
			$cuerpo .= "http://www.neodivert.com/rap/general.php?seccion=mostrar_perla&perla=$id_perla\r\n";
			$cuerpo .= "\r\n";

			$consulta  = 'SELECT DISTINCT email FROM usuarios ';
			$consulta .= 'WHERE email IS NOT NULL ';
			$consulta .= 'AND usuarios.cod_validacion_email IS NULL ';
			$consulta .= "AND id != $id_usuario ";
			$consulta .= 'AND id IN (SELECT usuario FROM notificaciones_email WHERE nuevo_comentario = \'siempre\' ';
			$consulta .= ' OR ( ';
			$consulta .= '  nuevo_comentario = \'participante\' ';
			$consulta .= '  AND ( ';
			$consulta .= "   usuario IN (SELECT usuario FROM participantes WHERE perla=$id_perla) ";
			$consulta .= "   OR usuario IN (SELECT usuario FROM comentarios WHERE perla=$id_perla) ";
			$consulta .= "   OR usuario IN (SELECT usuario FROM votos WHERE perla=$id_perla) ";
			$consulta .= "  ) ";
			$consulta .= " ) ";
			$consulta .= ") ";

			$emails = $bd->Consultar( $consulta );

			$this->EnviarNotificacion( $emails, $titulo, $cuerpo );
		}

		function NotificarNuevaNotaBD( $bd, $id_perla, $id_usuario )
		{
			$titulo = 'RAP - Nota cambiada';
			$cuerpo = "Ha cambiado la nota de una perla en la RAP. Para verla pulsa en el siguiente enlace: \r\n";
			$cuerpo .= "http://www.neodivert.com/rap/general.php?seccion=mostrar_perla&perla=$id_perla\r\n";
			$cuerpo .= "\r\n";

			$consulta  = 'SELECT DISTINCT email FROM usuarios ';
			$consulta .= 'WHERE email IS NOT NULL ';
			$consulta .= 'AND usuarios.cod_validacion_email IS NULL ';
			$consulta .= "AND id != $id_usuario ";
			$consulta .= 'AND id IN (SELECT usuario FROM notificaciones_email WHERE nueva_nota = \'siempre\' ';
			$consulta .= ' OR ( ';
			$consulta .= '  nueva_nota = \'participante\' ';
			$consulta .= '  AND ( ';
			$consulta .= "   usuario IN (SELECT usuario FROM participantes WHERE perla=$id_perla) ";
			$consulta .= "   OR usuario IN (SELECT usuario FROM comentarios WHERE perla=$id_perla) ";
			$consulta .= "   OR usuario IN (SELECT usuario FROM votos WHERE perla=$id_perla) ";
			$consulta .= "  ) ";
			$consulta .= " ) ";
			$consulta .= ") ";

			$emails = $bd->Consultar( $consulta );

			$this->EnviarNotificacion( $emails, $titulo, $cuerpo );
		}

		private function EnviarNotificacion( $emails, $titulo, $cuerpo ){
			ini_set('sendmail_from', 'neodivert@gmail.com' );

			$cuerpo = wordwrap( $cuerpo, 70, "\r\n" );

			$destinatarios = '';
			while( $email = $emails->fetch_assoc() ){
				$destinatarios .= "{$email['email']}, ";
			}

			if( $destinatarios == '' ){
				return 0;
			}

			$cabeceras = 	'From: neodivert@gmail.com' . "\r\n" .
    							'Reply-To: neodivert@gmail.com' . "\r\n" .
    							'X-Mailer: PHP/' . phpversion();

			if( !mail( $destinatarios, $titulo, $cuerpo, $cabeceras ) ){
				die( 'Error enviando las notificaciones' );
			}
		}
	}
?>
