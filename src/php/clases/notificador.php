<?php
	/*** 
	 notificador.php
	 Clase singlenton para la gestion de las notificaciones al email.
	 Fuente: 
	http://www.cristalab.com/tutoriales/crear-e-implementar-el-patron-de-diseno-singleton-en-php-c256l/
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

	class Notificador {

		/*** Atributos ***/

		// Instancia privada (singlenton).
   	private static $instancia;

		/*** Metodos ***/

		// Constructor privado (singlenton).
   	private function __construct(){}

		// Obtiene la instancia unica (singlenton).
   	public static function ObtenerInstancia()
		{
      	if( !self::$instancia instanceof self ){
				self::$instancia = new self;
			}
			return self::$instancia;
   	}

		// Obtiene de la BD $bd las preferencias del usuario cuya id es 
		// $id_usuario. Las preferencias se refieren a la frecuencia con la que
		// el usuario desea recibir cada uno de los tipos de notificaciones
		// existentes.
		function ObtenerPreferenciasBD( $bd, $id_usuario )
		{
			// Notificaciones por defecto (se devolveran en caso de no encontrar
			// preferencias especificas para el usuario).
			// TODO: ¿Cambiar todas a 'nunca'?.
			$notificaciones = array(
				'nueva_perla' => 'participante',
				'nuevo_comentario' => 'participante',
				'nueva_nota' => 'nunca',
				'nuevo_usuario' => 'siempre'
			);
	
			// Obtiene las preferencias de la BD.
			$notificaciones_ = $bd->Consultar( "SELECT * FROM notificaciones_email WHERE usuario='$id_usuario'" );
			$notificaciones_ = $notificaciones_->fetch_assoc();

			// Carga las notificaciones en el array asociativo $notificaciones.
			$notificaciones['nueva_perla'] = $notificaciones_['nueva_perla'];
			$notificaciones['nuevo_comentario'] = $notificaciones_['nuevo_comentario'];
			$notificaciones['nueva_nota'] = $notificaciones_['nueva_nota'];
			$notificaciones['nuevo_usuario'] = $notificaciones_['nuevo_usuario'];

			// Devuelve las preferencias.
			return $notificaciones;
		}


		// Guarda en la BD $bd las preferencias del usuario cuya id es 
		// $id_usuario. Las preferencias se refieren a la frecuencia con la que
		// el usuario desea recibir cada uno de los tipos de notificaciones
		// existentes.
		function EstablecerPreferenciasBD( $bd, $id_usuario, $notificaciones )
		{
			foreach( $notificaciones as $tipo => $frecuencia ){
				$bd->Consultar( "UPDATE notificaciones_email SET $tipo='$frecuencia' WHERE usuario=$id_usuario" );
			}
		}


		// Notifica a los usuarios pertinentes que se ha subido a la RAP una
		// perla con id $id_perla. La notificacion no se envia al usuario
		// que envio la perla (aquel con id $id_usuario).
		function NotificarNuevaPerlaBD( $bd, $id_perla, $id_usuario )
		{
			// Redacta el email a enviar.
			$titulo = 'RAP - Nueva perla subida';
			$cuerpo = "Se ha subido una nueva perla a la RAP. Para verla pulsa en el siguiente enlace: \r\n";
			$cuerpo .= "http://www.neodivert.com/rap/general.php?seccion=mostrar_perla&perla=$id_perla\r\n";
			$cuerpo .= "\r\n";

			// Prepara la consulta para obtener de la BD los usuarios que desean 
			// recibir la notificacion.
			$consulta  = 'SELECT DISTINCT email FROM usuarios ';
			$consulta .= 'WHERE email IS NOT NULL ';
			$consulta .= 'AND usuarios.cod_validacion_email IS NULL ';
			$consulta .= "AND id != $id_usuario ";
			$consulta .= 'AND id IN (SELECT usuario FROM notificaciones_email WHERE nueva_perla = \'siempre\' ';
			$consulta .= 'OR (nueva_perla = \'participante\' ';
			$consulta .= "AND usuario IN (SELECT usuario FROM participantes WHERE perla=$id_perla) ) )";

			// Lanza la consulta a la BD.
			$emails = $bd->Consultar( $consulta );

			// Envia la notificacion a los usuarios pertinentes.
			$this->EnviarNotificacion( $emails, $titulo, $cuerpo );
		}

		
		// Notifica a los usuarios pertinentes que se ha subido a la RAP un
		// comentario en la perla con id $id_perla. La notificacion no se envia 
		// al usuario que envio el comentario (aquel con id $id_usuario).
		function NotificarNuevoComentarioBD( $bd, $id_perla, $id_usuario )
		{
			// Redacta el email a enviar.
			$titulo = 'RAP - Nuevo comentario subido';
			$cuerpo = "Se ha subido un nuevo comentario a la RAP. Para verlo pulsa en el siguiente enlace: \r\n";
			$cuerpo .= "http://www.neodivert.com/rap/general.php?seccion=mostrar_perla&perla=$id_perla\r\n";
			$cuerpo .= "\r\n";

			// Prepara la consulta para obtener de la BD los usuarios que desean
			// recibir la notificacion.
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

			// Lanza la consulta.
			$emails = $bd->Consultar( $consulta );

			// Envia la notificacion a los usuarios pertinentes.
			$this->EnviarNotificacion( $emails, $titulo, $cuerpo );
		}


		// Notifica a los usuarios pertinentes que ha cambiado la nota de la
		// perla con id $id_perla. La notificacion no se envia al usuario
		// que envio la perla (aquel con id $id_usuario).
		function NotificarNuevaNotaBD( $bd, $id_perla, $id_usuario )
		{
			// Redacta el email a enviar.
			$titulo = 'RAP - Nota cambiada';
			$cuerpo = "Ha cambiado la nota de una perla en la RAP. Para verla pulsa en el siguiente enlace: \r\n";
			$cuerpo .= "http://www.neodivert.com/rap/general.php?seccion=mostrar_perla&perla=$id_perla\r\n";
			$cuerpo .= "\r\n";

			// Prepara la consulta para obtener de la BD los usuarios que desean
			// recibir la notificacion.
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

			// Lanza la consulta.
			$emails = $bd->Consultar( $consulta );

			// Envia la notificacion a los usuarios pertinentes.
			$this->EnviarNotificacion( $emails, $titulo, $cuerpo );
		}


		// Envia a las direcciones $emails el correo de titulo $titulo y
		// cuerpo $cuerpo.
		// NOTA: $emails es una estructura mysqli_result.
		private function EnviarNotificacion( $emails, $titulo, $cuerpo ){
			ini_set('sendmail_from', 'neodivert@gmail.com' );

			$cuerpo = wordwrap( $cuerpo, 70, "\r\n" );

			// Extrae los destinatarios de $emails y los pone como una string
			// de la forma "d1, d2, ...".
			$destinatarios = '';
			while( $email = $emails->fetch_assoc() ){
				$destinatarios .= "{$email['email']}, ";
			}

			// Si no hay destinatarios no se envia el mensaje.
			if( $destinatarios == '' ){
				return 0;
			}
		
			// Establece cabeceras adicionales del mensaje.
			$cabeceras = 	'From: neodivert@gmail.com' . "\r\n" .
    							'Reply-To: neodivert@gmail.com' . "\r\n" .
    							'X-Mailer: PHP/' . phpversion();

			// Envia las notificaciones.
			if( !mail( $destinatarios, $titulo, $cuerpo, $cabeceras ) ){
				die( 'Error enviando las notificaciones' );
			}
		}
	}
?>
