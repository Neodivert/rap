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
		protected $nota;

		protected $denunciada_por_usuario;

		protected $num_comentarios;
		protected $participantes;

		
		/*** Getters y Setters ***/
		function ObtenerId(){ return $this->id; }
		function EstablecerId( $id ){ $this->id = $id; }

		function ObtenerTitulo(){ return $this->titulo; }
		function EstablecerTitulo( $titulo ){ $this->titulo = $titulo; } // TODO: He quitado un replace de '"' que usaba para escapar el titulo y poder meterlo en la BD. Comprobar que funciona bien.

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
			return str_replace( array( '<i>', '</i>' ), '', $this->fecha );
		}
		function EstablecerFecha( $fecha ){ $this->fecha = $fecha; }

		function ObtenerSubidor(){ return $this->subidor; }
		function EstablecerSubidor( $subidor ){ $this->subidor = $subidor; }

		function ObtenerNombreSubidor(){ return $this->subidor; }
		function EstablecerNombreSubidor( $subidor ){ $this->subidor = $subidor; }

		function ObtenerFechaSubida(){ return $this->fecha_subida; }
		function EstablecerFechaSubida( $fecha_subida ){ $this->fecha_subida = $fecha_subida; }
		
		function ObtenerModificador(){ return $this->modificador; }
		function EstablecerModificador( $modificador ){ $this->modificador = $modificador; }
		
		function ObtenerFechaModificacion(){ return $this->fecha_modificacion; }
		function EstablecerFechaModificacion( $fecha_modificacion ){ $this->fecha_modificacion = $fecha_modificacion; }
		
		function EstablecerParticipantes( $participantes ){
			$this->participantes = $participantes;
		}

		function ObtenerNumComentarios(){ return $this->num_comentarios; }
		function EstablecerNumComentarios( $num_comentarios ){ $this->num_comentarios = $num_comentarios; }

		function ObtenerNota(){ return $this->nota; }
		function ObtenerNumVotosPositivos(){ return $this->num_votos_positivos; }
		function ObtenerNumVotosNegativos(){ return $this->num_votos_negativos; }
		function DenunciadaPorUsuario(){ return $this->denunciada_por_usuario; }

		function EstablecerImagen( $imagen ){ $this->imagen = $imagen; }


		/*** Otros metodos ***/

		// Constructor.
		function Perla()
		{
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

		// Carga los atributos a partir del registro $reg. Tambien establece
		// al usuario con id $id_usuario como subidor y ultimo modificador
		// de la perla.
		public function CargarDesdeRegistro( $reg, $id_usuario ){
			$this->EstablecerId( isset( $reg['id'] ) ? $reg['id'] : null );

			// TODO: Voy por aqui comentando.
			$this->titulo = $reg['titulo'];
			$this->texto = $reg['texto'];
			$this->fecha = $reg['fecha'];

			$this->subidor = $reg['subidor'];
			$this->fecha_subida = $reg['fecha_subida'];
			$this->modificador = $reg['modificador'];
			$this->fecha_modificacion = $reg['fecha_modificacion'];
		}

		function CargarDesdeBD( $bd, $id_perla, $id_usuario )
		{
			$res = $bd->Consultar( "SELECT * FROM perlas WHERE id='$id_perla'" );
			$this->CargarDesdeRegistro( $res->fetch_assoc(), $id_usuario );
			
			$this->CargarInfoExtraBD( $bd, $id_usuario );
		}

		function CargarInfoExtraBD( $bd, $id_usuario )
		{
			$this->CargarEtiquetasBD( $bd );
			$this->CargarParticipantesBD( $bd );
			$this->CargarVotosBD( $bd, $id_usuario );
			$this->CargarNumComentariosBD( $bd );
			// TODO: Falta los comentarios (nº de comentarios).
		}

		function EsSubidor( $id_usuario ){
			return ( $this->subidor == $id_usuario );
		}

		// Inserta la perla en la BD.
		function InsertarBD( $bd, $id_usuario, $borrar_imagen = false ){

			$titulo = $bd->EscaparString( $this->titulo );
			$texto = $bd->EscaparString( $this->texto );
			$fecha = $bd->EscaparString( $this->fecha );

			if( $this->id == null ){
				// La perla es nueva. Inserta la perla en la BD y obtiene su ID.
				$this->id = $bd->Consultar( "INSERT INTO perlas (titulo, texto, fecha_subida, fecha, subidor, fecha_modificacion, modificador) VALUES( '$titulo', '$texto', NOW(), '$fecha', '$id_usuario', NOW(), '$id_usuario' )" );
			}else{
				// La perla no es nueva. Actualiza los datos en la BD.
				$bd->Consultar( "UPDATE perlas SET titulo='$titulo', texto='$texto', fecha='$fecha', fecha_modificacion=NOW(), modificador='{$id_usuario}' WHERE id='{$this->id}' " ) or die ($bd->error);
				
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
	
			/*
			if( $_FILES['imagen']['error'] != UPLOAD_ERR_NO_FILE ||
				(
				isset( $_POST['modificar'] )
				&& !isset( $_POST['borrar_imagen'] )
				&& file_exists( 'media/perlas/' . $_POST['modificar'] )
				)
){
$perla['perla_visual'] = true;
}else{
$perla['perla_visual'] = false;
}
*/
			
			// 
			if( $this->imagen != null ){
				if( $this->InsertarImagenBD() ) return -1;
			}
			// TODO: Notifica por email.
			//NotificarPorEmail( 'nueva_perla', $id_perla );

			// Trata de subir la imagen (sólo perlas visuales).
			/*
			if( $perla['perla_visual'] ){
				try{
					InsertarImagen( 'imagen', $id_perla );
				}catch( Exception $e ){
					throw $e;
				}
			}
			*/
		}

		private function CargarNumComentariosBD( $bd ){
			$res = $bd->Consultar( "SELECT COUNT(*) as n FROM comentarios WHERE perla = {$this->id}" );
			$reg = $res->fetch_assoc();
			$this->num_comentarios = $reg['n'];
		}

		private function InsertarParticipantesBD( $bd ){
			foreach( $this->participantes as $participante ){
				$bd->Consultar( "INSERT INTO participantes (perla, usuario) VALUES ('{$this->id}', '{$participante}' )" );
			}
		}

		private function BorrarParticipantesBD( $bd ){
			$bd->Consultar( "DELETE FROM participantes WHERE perla='{$this->id}'" );
		}


		private function InsertarEtiquetasBD( $bd ){
			foreach( $this->etiquetas as $etiqueta ){
				$bd->Consultar( "INSERT IGNORE INTO etiquetas (nombre) VALUES( '$etiqueta' )" );
				$res = $bd->Consultar( "SELECT id FROM etiquetas WHERE nombre = '$etiqueta'" );
				$res = $res->fetch_array();
				$id_etiqueta = $res[0];
				$bd->Consultar( "INSERT INTO perlas_etiquetas (perla, etiqueta) VALUES ('{$this->id}', '{$id_etiqueta}' )" );
			}
		}

		private function BorrarEtiquetasBD( $bd ){
			$bd->Consultar( "DELETE FROM perlas_etiquetas WHERE perla='{$this->id}'" );

			// Consulta auxiliar: borra todas las etiquetas que se han podido quedar sin referenciar.
			$bd->Consultar( 'DELETE FROM etiquetas WHERE id not IN (SELECT etiqueta FROM perlas_etiquetas)' );
		}
		
		function CargarEtiquetasBD( $bd ){
			$res = $bd->Consultar( "SELECT nombre FROM etiquetas JOIN perlas_etiquetas ON perlas_etiquetas.etiqueta = etiquetas.id WHERE perlas_etiquetas.perla = {$this->id}" );
			$this->etiquetas = array();
			while( $reg = $res->fetch_object() ){
				$this->etiquetas[] = $reg->nombre;
			}
		}

		function CargarParticipantesBD( $bd ){
			$res = $bd->Consultar( "SELECT usuario FROM participantes WHERE perla = {$this->id}" );
			$this->participantes = array();
			while( $reg = $res->fetch_object() ){
				$this->participantes[] = $reg->usuario;
			}
		}
		

		function CargarVotosBD( $bd, $id_usuario )
		{
			$this->denunciada_por_usuario = false;
			$this->num_votos_positivos = 0;
			$this->num_votos_negativos = 0;
			$this->nota = 0;

			$votos = $bd->Consultar( "SELECT usuario, nota FROM votos WHERE perla = {$this->id}" );
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
			
			if( $this->num_votos_positivos > 0 ){
				$this->nota = $nota_acumulada / $this->num_votos_positivos;
			}else{
				$this->nota = 0;
			}
		}

		// Actualiza en la BD la perla cuya id es '$id_perla'.
		/*
		function ActualizarBD( $id_perla, $borrar_imagen = false )
		{
			// Si con la perla se crea una nueva categoria, introduce esta en la BD.
			// Tambien se obtiene la id de la categoria.
			if( $perla['nueva_categoria'] != "" ){
				$id_categoria = CrearCategoria( $perla['nueva_categoria'] );
			}else{
				$id_categoria = $perla['categoria'];
			}

			// Actualiza la perla en la BD.
			$res = $bd->Consultar( "UPDATE perlas SET titulo='{$this->info['titulo']}', texto='{$this->info['texto']}', fecha='{$this->info['fecha']}', contenido_informatico='{$this->info['contenido_informatico']}', humor_negro='{$this->info['humor_negro']}', perla_visual='{$this->info['perla_visual']}', categoria='$id_categoria', fecha_modificacion=NOW(), modificador='{$this->info['subidor']}' WHERE id='{$id_perla}' " ) or die ($bd->error);

			// Borra los participantes antiguos en la BD (por si se modificaron).		
			$res = $bd->ConsultarBD( "DELETE FROM participantes WHERE perla='$id'" ) or die( $bd->error );

			// Introduce los nuevos participantes en la BD.
			InsertarParticipantes( $id_perla, $perla['participantes'] );
		
			// Trata de subir la imagen (sólo perlas visuales). // TODO: NO BORRAR HASTA QUE HAGA DE NUEVO LO DE LAS PERLAS VISUALES.
			if( $_FILES['imagen']['error'] != UPLOAD_ERR_NO_FILE ){
				try{
					InsertarImagen( 'imagen', $id );
				}catch( Exception $e ){
					throw $e;
				}
			}else{
				// No se subio una imagen. Esto puede indicar que la perla contenía
				// una imagen pero el usuario quiere borrarla. Comprobamos si es así.
				if( ($borrar_imagen == true) && file_exists( "../datos/img/perlas/" . $id ) ){
					unlink( "../datos/img/perlas/" . $id );
				}
			}

			$bd->close();
		}
		*/

		function BorrarImagenBD()
		{
			unlink( "../../media/perlas/" . $this->id );
		}

		function BorrarVotosBD( $bd )
		{
			$bd->Consultar( "DELETE FROM votos WHERE perla={$this->id}" );
		}


		function BorrarInfoExtraBD( $bd )
		{
			$this->BorrarVotosBD( $bd );
			$this->BorrarParticipantesBD( $bd );
			$this->BorrarEtiquetasBD( $bd );

			// TODO: Borrar comentarios.
		}

		function BorrarBD( $bd )
		{
			if( !isset( $this->id ) ){ return 1; }

			$this->BorrarVotosBD( $bd );
			$this->BorrarParticipantesBD( $bd );
			$this->BorrarEtiquetasBD( $bd );

			$bd->Consultar( "DELETE FROM perlas WHERE id={$this->id}" );

			return 0;
		}


		// Determina si el usuario '$usuario' es participante de la perla.
		function EsParticipante( $usuario )
		{
			foreach( $this->participantes as $participante ){
				if( $usuario == $participante ) return true;
			}
			return false;
		}


		// Inserta en la BD una denuncia (voto de borrado) del usuario '$usuario'
		// contra la perla.
		function Denunciar( $usuario )
		{	
			ConsultarBD( "INSERT INTO denuncias_perlas (usuario, perla) VALUES ($usuario, {$this->info['id']})" );
		}


		// Borra en la BD la denuncia (voto de borrado) del usuario '$usuario' 
		// contra la perla.
		function CancelarDenuncia( $usuario )
		{
			ConsultarBD( "DELETE FROM denuncias_perlas WHERE usuario='$usuario' AND perla='{$this->info['id']}'" );
		}

		// Obtiene los participantes de la perla.
		function ObtenerParticipantes()
		{
			//die( "Participantes({$this->participantes})" );
			return $this->participantes;
		}

		// Inserta/actualiza en la BD la puntuación de la perla cuya id es $perla
		// con la nota $nota.
		function PuntuarBD( $bd, $nota, $usuario )
		{
			// Al puntuar una perla se disparan unos triggers en sql que 
			// actualizan automaticamente la tabla "logros".
			// Fuente: http://dev.mysql.com/doc/refman/5.0/es/create-trigger.html

			// Insertar o actualizar si ya existe:
			// http://mjcarrascosa.com/insertar-o-actualizar-registros-en-mysql/
			if( $nota == 0 ){
				$bd->Consultar( "DELETE FROM votos WHERE perla={$this->id} AND usuario=$usuario" );
			}else{
				$bd->Consultar( "INSERT INTO votos (perla, usuario, nota, fecha) VALUES ({$this->id}, $usuario, $nota, NOW()) ON DUPLICATE KEY UPDATE nota=$nota, fecha=NOW()" );
			}
		}
		

		function ObtenerComentariosBD( $bd )
		{
			$registros = $bd->Consultar( "SELECT * from comentarios WHERE perla={$this->id} ORDER BY fecha_subida ASC" );
			$comentarios = array();
			$i = 0;
			while( $registro = $registros->fetch_assoc() ){
				$comentarios[$i] = new Comentario;
				$comentarios[$i]->CargarDesdeRegistro( $registro );

				$i++;
			}

			return $comentarios;
		}


		// Trata de insertar la imagen $nombre para la perla cuya id es $id_perla.
		// Si hay algún error lanza una excepción.
		function InsertarImagenBD()
		{
			try{
				$res = ComprobarImagen( $this->imagen );

				if( $res < 0 ) return $res;

				//echo getcwd() . '<br />../../media/perlas/<br/>';
				if( !move_uploaded_file($this->imagen["tmp_name"], "../../media/perlas/" . $this->id ) ) die( 'ERROR moviendo fichero' );
				
			}catch( Exception $e ){
				die( $e->getMessage() );
			}
		}

	} // Fin de la clase Perla.
?>
