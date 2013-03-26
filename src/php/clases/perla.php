<?php
	/*** 
	 perla.php
	 Clase para la gestion de las perlas de la RAP.
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

	// TODO: librarme de esto.
	require_once DIR_LIB . 'utilidades.php';

	class Perla {
		/*** Atributos ***/
		protected $id;
		protected $titulo;
		protected $etiquetas;
		protected $nota_acumulada;
		protected $num_votos;
		protected $texto;
		protected $fecha;
		protected $subidor;
		protected $fecha_subida;
		protected $modificador;
		protected $fecha_modificacion;
		protected $imagen;

		protected $num_votos_positivos;
		protected $num_votos_negativos;
		protected $nota_media;

		protected $denunciada_por_usuario;

		protected $num_comentarios;
		protected $participantes;

		
		/*** Getters y Setters ***/
		function ObtenerId(){ return $this->id; }
		function EstablecerId( $id ){ $this->id = $id; }

		function ObtenerTitulo(){ return $this->titulo; }
		function EstablecerTitulo( $titulo ){ $this->titulo = $titulo; }

		// Las etiquetas pueden obtenerse como un array (ObtenerEtiquetas) o como
		// una string del tipo "etiqueta1, etiqueta2, ..." (ObtenerEtiquetasStr).
		function ObtenerEtiquetas(){ return $this->etiquetas; }
		function ObtenerEtiquetasStr()
		{ 
			$strEtiquetas = '';
			foreach( $this->etiquetas as $etiqueta ){
				$strEtiquetas .= $etiqueta . ', ';
			}
			$strEtiquetas = substr_replace($strEtiquetas ,"",-2);
			return $strEtiquetas;
		}
		function EstablecerEtiquetas( $etiquetas ){ $this->etiquetas = $etiquetas; }

		function ObtenerNumVotos(){ return $this->num_votos; }
		function EstablecerNumVotos( $num_votos ){ $this->num_votos = $num_votos; }

		function ObtenerTexto(){
			return $this->texto;
		}

		function ObtenerTextoPlano(){ 
			return str_replace( array( '<strong>', '</strong>', '<br />' ), '', $this->texto );
		}

		// Texto de la perla. A éste texto se le da un formateo previo, 
		// consistente en tomar las líneas de tipo 'participante: texto' y
		// resaltar (poner en negrita) la parte de 'participante'.
		function EstablecerTexto( $texto )
		{ 	
			$lineas = explode( "\n", $texto );
			$this->texto = '';

			foreach( $lineas as $linea ){
				$tokens = explode( ': ', $linea, 2 );
				if( count( $tokens ) == 2 ){
					$this->texto .= "<strong>{$tokens[0]}: </strong>{$tokens[1]}<br />";
				}else{
					$this->texto .= $linea . '<br />';
				}
			}
		}

		function ObtenerFecha()
		{ 
			if( $this->fecha != null ){
				return str_replace( array( '<i>', '</i>' ), '', $this->fecha );
			}else{
				return 'no especificado';
			}
		}
		function EstablecerFecha( $fecha ){ $this->fecha = $fecha; }

		function ObtenerSubidor(){ return $this->subidor; }
		function EstablecerSubidor( $subidor ){ $this->subidor = $subidor; }

		function ObtenerNombreSubidor(){ return $this->subidor; }
		function EstablecerNombreSubidor( $subidor ){ $this->subidor = $subidor; }

		function ObtenerFechaSubida()
		{
			// Obtiene la fecha en el formato "normal" para Espanna.
			$fecha = new DateTime( $this->fecha_subida );
			return date_format( $fecha, 'd-m-Y H:i:s' ); 
		} 
		function EstablecerFechaSubida( $fecha_subida ){ $this->fecha_subida = $fecha_subida; }
		
		function ObtenerModificador(){ return $this->modificador; }
		function EstablecerModificador( $modificador ){ $this->modificador = $modificador; }
		
		function ObtenerFechaModificacion()
		{
			// Obtiene la fecha en el formato "normal" para Espanna.
			$fecha = new DateTime( $this->fecha_modificacion );
			return date_format( $fecha, 'd-m-Y H:i:s' ); 
		}
		function EstablecerFechaModificacion( $fecha_modificacion ){ $this->fecha_modificacion = $fecha_modificacion; }
		
		function EstablecerParticipantes( $participantes ){
			$this->participantes = $participantes;
		}

		function ObtenerNumComentarios(){ return $this->num_comentarios; }
		function EstablecerNumComentarios( $num_comentarios ){ $this->num_comentarios = $num_comentarios; }

		function ObtenerNotaMedia(){ return number_format( $this->nota_media, 2 ); }
		function ObtenerNumVotosPositivos(){ return $this->num_votos_positivos; }
		function ObtenerNumVotosNegativos(){ return $this->num_votos_negativos; }
		function DenunciadaPorUsuario(){ return $this->denunciada_por_usuario; }

		function EstablecerImagen( $imagen ){ $this->imagen = $imagen; }


		/*** Otros metodos ***/

		// Constructor.
		function Perla()
		{
			// TODO: ¿Solo esto? -> Inicializar todos los campos (funcion Vaciar).
			$imagen = null;
		}


		// Carga los atributos a partir de un formulario (array $_POST).
		public function CargarDesdeFormulario( $form ){
			if( isset( $form['id'] ) ){
				$this->EstablecerId( $form['id'] );
			}
			$this->EstablecerTitulo( $form['titulo'] );
			$this->EstablecerTexto( $form['texto'] );
			$this->etiquetas = explode( ', ', $form['etiquetas'] );
			
			$this->EstablecerFecha( $form['fecha'] );

			if( isset( $form['participantes'] ) ){
				$this->participantes = $form['participantes'];
			}
		}


		// Carga los atributos a partir del registro asociativo $reg.
		public function CargarDesdeRegistro( $reg ){
			$this->EstablecerId( isset( $reg['id'] ) ? $reg['id'] : null );

			$this->titulo = $reg['titulo'];
			$this->texto = $reg['texto'];
			$this->fecha = $reg['fecha'];

			$this->subidor = $reg['subidor'];
			$this->fecha_subida = $reg['fecha_subida'];
			$this->modificador = $reg['modificador'];
			$this->fecha_modificacion = $reg['fecha_modificacion'];
		}


		// Carga la perla con id $id_perla desde la BD $bd.
		function CargarDesdeBD( $bd, $id_perla, $id_usuario )
		{
			// Carga la informacion basica (titulo, texto, subidor, etc) desde
			// la BD.
			$res = $bd->Consultar( "SELECT * FROM perlas WHERE id='$id_perla'" );
			$this->CargarDesdeRegistro( $res->fetch_assoc() );
			
			// Carga la informacion extra (participantes, votos, etc) desde la BD.
			$this->CargarInfoExtraBD( $bd, $id_usuario );
		}


		// Carga desde la BD $bd la informacion extra sobre la perla (etiquetas,
		// participantes, votos y nº de comentarios).
		function CargarInfoExtraBD( $bd, $id_usuario )
		{
			$this->CargarEtiquetasBD( $bd );
			$this->CargarParticipantesBD( $bd );
			$this->CargarVotosBD( $bd, $id_usuario );
			$this->CargarNumComentariosBD( $bd );
		}


		// Devuelve true si el usuario con id $id_usuario es quien subio la
		// perla.
		function EsSubidor( $id_usuario ){
			return ( $this->subidor == $id_usuario );
		}


		// Inserta la perla en la BD.
		function InsertarBD( $bd, $id_usuario, $borrar_imagen = false )
		{
			// Escapa las strings que introduce el usuario.
			$titulo = $bd->EscaparString( $this->titulo );
			$texto = $bd->EscaparString( $this->texto );
			$fecha = $bd->EscaparString( $this->fecha );

			// Aqui se actua segun si la perla no tiene una id asignada (es nueva
			// y por tanto se inserta en la BD por primera vez) o si tiene una id
			// asignada (ya existe en la BD y solo tenemos que actualizarla).
			if( $this->id == null ){
				// Insertar perla.
				$this->id = $bd->Consultar( "INSERT INTO perlas (titulo, texto, fecha_subida, fecha, subidor, fecha_modificacion, modificador) VALUES( '$titulo', '$texto', NOW(), '$fecha', '$id_usuario', NOW(), '$id_usuario' )" );
			}else{
				// Actualizar perla.
				$bd->Consultar( "UPDATE perlas SET titulo='$titulo', texto='$texto', fecha='$fecha', fecha_modificacion=NOW(), modificador='{$id_usuario}' WHERE id='{$this->id}' " ) or die ($bd->error);
				
				// La forma de actualizar los participantes y etiquetas de una
				// perla es a lo bruto: se borran todos los antiguos y luego se
				// insertan los nuevos.
				$this->BorrarParticipantesBD( $bd );
				$this->BorrarEtiquetasBD( $bd );
			}
			// Añade al usuario actual como participante de la perla.
			$this->participantes[] = $id_usuario;
			
			// TODO: Tener en cuenta si $id_perla == false (error al insertar).
			
			// Inserta en la BD los participantes de la perla.
			$this->InsertarParticipantesBD( $bd );

			// Inserta en la BD las etiquetas de la perla.
			$this->InsertarEtiquetasBD( $bd );
	
			// Inserta la imagen (si procede).
			if( $this->imagen != null ){
				if( $this->InsertarImagenBD() ) return -1;
			}
		}

		// Obtiene de la BD $bd el numero de comentarios de la perla.
		private function CargarNumComentariosBD( $bd ){
			$res = $bd->Consultar( "SELECT COUNT(*) as n FROM comentarios WHERE perla = {$this->id}" );
			$reg = $res->fetch_assoc();
			$this->num_comentarios = $reg['n'];
		}

		// Inserta en la BD $bd los participantes de la perla.
		private function InsertarParticipantesBD( $bd ){
			foreach( $this->participantes as $participante ){
				$bd->Consultar( "INSERT INTO participantes (perla, usuario) VALUES ('{$this->id}', '{$participante}' )" );
			}
		}

	
		// Borra de la BD los participantes de la perla.
		private function BorrarParticipantesBD( $bd ){
			$bd->Consultar( "DELETE FROM participantes WHERE perla='{$this->id}'" );
		}


		// Inserta en la BD las etiquetas de la perla.
		private function InsertarEtiquetasBD( $bd ){
			foreach( $this->etiquetas as $etiqueta ){
				// Inserta la etiqueta en la BD.
				$bd->Consultar( "INSERT IGNORE INTO etiquetas (nombre) VALUES( '$etiqueta' )" );
		
				// Obtiene la id de la etiqueta.
				$res = $bd->Consultar( "SELECT id FROM etiquetas WHERE nombre = '$etiqueta'" );
				$res = $res->fetch_array();
				$id_etiqueta = $res[0];

				// Inserta la relacion (id_perla, id_etiqueta) en la BD.
				$bd->Consultar( "INSERT INTO perlas_etiquetas (perla, etiqueta) VALUES ('{$this->id}', '{$id_etiqueta}' )" );
			}
		}


		// Borra en la BD las etiquetas de la perla.
		private function BorrarEtiquetasBD( $bd ){
			// Borra las relaciones (perla, etiqueta).
			$bd->Consultar( "DELETE FROM perlas_etiquetas WHERE perla='{$this->id}'" );

			// Consulta auxiliar: borra todas las etiquetas que se han podido quedar sin referenciar.
			$bd->Consultar( 'DELETE FROM etiquetas WHERE id not IN (SELECT etiqueta FROM perlas_etiquetas)' );
		}
		
		
		// Obtiene las etiquetas desde la BD y las carga en un array.
		function CargarEtiquetasBD( $bd ){
			// Obtiene las etiquetas desde la BD.
			$res = $bd->Consultar( "SELECT nombre FROM etiquetas JOIN perlas_etiquetas ON perlas_etiquetas.etiqueta = etiquetas.id WHERE perlas_etiquetas.perla = {$this->id}" );

			// Carga las etiquetas en un array.
			$this->etiquetas = array();
			while( $reg = $res->fetch_object() ){
				$this->etiquetas[] = $reg->nombre;
			}
		}


		// Obtiene los participantes desde la BD y lso carga en un array.
		function CargarParticipantesBD( $bd ){
			// Obtiene los participantes desde la BD.
			$res = $bd->Consultar( "SELECT usuario FROM participantes WHERE perla = {$this->id}" );

			// Carga las etiquetas en un array.
			$this->participantes = array();
			while( $reg = $res->fetch_object() ){
				$this->participantes[] = $reg->usuario;
			}
		}
		

		// Obtiene desde la BD los votos asociados a la perla.
		// TODO: eliminar los votos negativos si al final no se implementan.
		function CargarVotosBD( $bd, $id_usuario )
		{
			// Inicializaciones.
			$this->denunciada_por_usuario = false;
			$this->num_votos_positivos = 0;
			$this->num_votos_negativos = 0;
			$this->nota_media = 0;

			// Obtiene todos los votos desde la BD.
			$votos = $bd->Consultar( "SELECT usuario, nota FROM votos WHERE perla = {$this->id}" );

			// Separa los votos en positivos y negativos y los cuenta por 
			// separado. Los positivos los va sumando para obtener la nota
			// acumulada. 
			$nota_acumulada = 0;
			while( $voto = $votos->fetch_object() ){
				if( $voto->nota >= 0 ){
					$nota_acumulada += $voto->nota;
					$this->num_votos_positivos++;
				}else{
					$this->num_votos_negativos++;
					if( $voto->usuario == $id_usuario ){
						$this->denunciada_por_usuario = true;
					}
				}
			}
			
			// Obtiene la nota media.
			if( $this->num_votos_positivos > 0 ){
				$this->nota_media = $nota_acumulada / $this->num_votos_positivos;
			}else{
				$this->nota_media = 0;
			}
		}

		
		// Borra de disco la imagen asociada a la perla.
		function BorrarImagenBD()
		{
			unlink( "../../media/perlas/" . $this->id );
		}


		// Borra de la BD todos los votos asociados a la perla.
		function BorrarVotosBD( $bd )
		{
			$bd->Consultar( "DELETE FROM votos WHERE perla={$this->id}" );
		}


		// Borra de la BD todos los comentarios asociados a la perla.
		function BorrarComentariosBD( $bd )
		{
			$bd->Consultar( "DELETE FROM comentarios WHERE perla={$this->id}" );
		}

		// Borra de la BD toda la info. extra asociada a la perla (votos, 
		// participantes, etiquetas y comentarios).
		function BorrarInfoExtraBD( $bd )
		{
			$this->BorrarVotosBD( $bd );
			$this->BorrarParticipantesBD( $bd );
			$this->BorrarEtiquetasBD( $bd );
			$this->BorrarComentariosBD( $bd );
		}

		
		// Borra de la BD la perla actual.
		function BorrarBD( $bd )
		{
			// Si no se tiene una id no se puede borrar la perla.
			if( !isset( $this->id ) ){ return 1; }

			// Boorra la info. extra asociada a la perla (votos, participantes,
			// etiquetas y comentarios).
			$this->BorrarInfoExtraBD( $bd );

			// Borra la perla.
			$bd->Consultar( "DELETE FROM perlas WHERE id={$this->id}" );

			return 0;
		}


		// Devuelve true si el usuario '$usuario' es participante de la perla y
		// false en caso contrario.
		function EsParticipante( $usuario )
		{
			foreach( $this->participantes as $participante ){
				if( $usuario == $participante ) return true;
			}
			return false;
		}


		// Obtiene los participantes de la perla.
		function ObtenerParticipantes()
		{
			return $this->participantes;
		}


		// Inserta/actualiza en la BD la puntuación dada por el usuario con id 
		// $usuario a la perla cuya id es $perla y con la nota $nota.
		function PuntuarBD( $bd, $nota, $usuario )
		{
			// Al puntuar una perla se disparan unos triggers en sql que 
			// actualizan automaticamente la tabla "logros".
			// Fuente: http://dev.mysql.com/doc/refman/5.0/es/create-trigger.html

			if( $nota == 0 ){
				// Una nota de 0 implica borrar cualquier voto anterior del 
				// usuario.
				$bd->Consultar( "DELETE FROM votos WHERE perla={$this->id} AND usuario=$usuario" );
			}else{
				// Insertar o actualizar si ya existe:
				// http://mjcarrascosa.com/insertar-o-actualizar-registros-en-mysql/
				$bd->Consultar( "INSERT INTO votos (perla, usuario, nota, fecha) VALUES ({$this->id}, $usuario, $nota, NOW()) ON DUPLICATE KEY UPDATE nota=$nota, fecha=NOW()" );
			}
		}
		
		
		// Obtiene y devuelve los comentarios asociados a la perla.
		function ObtenerComentariosBD( $bd )
		{
			// Obtiene los comentarios desde la BD.
			$registros = $bd->Consultar( "SELECT * from comentarios WHERE perla={$this->id} ORDER BY fecha_subida ASC" );

			// Rellena un array de objetos Comentario con los comentarios 
			// encontrados.
			$comentarios = array();
			$i = 0;
			while( $registro = $registros->fetch_assoc() ){
				$comentarios[$i] = new Comentario;
				$comentarios[$i]->CargarDesdeRegistro( $registro );

				$i++;
			}

			// Devuelve el array resultado.
			return $comentarios;
		}


		// Carga en disco la imagen asociada a la perla.
		function InsertarImagenBD()
		{
			try{
				// Comprobaciones de formato, tamanno, etc.
				$res = ComprobarImagen( $this->imagen );
				if( $res < 0 ) return $res;

				// Mueve la imagen a la carpeta "media/perlas".
				if( !move_uploaded_file($this->imagen["tmp_name"], "../../media/perlas/" . $this->id ) ) die( 'ERROR moviendo fichero' );
				
			}catch( Exception $e ){
				die( $e->getMessage() );
			}
		}

	} // Fin de la clase Perla.
?>
