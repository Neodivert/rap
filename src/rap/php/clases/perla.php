<?php
	// Conjunto de funciones relacionadas con las perlas.
	/*
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
	require_once DIR_LIB . 'utilidades.php';
	require_once DIR_CLASES . 'objeto_bd.php';

	class Perla implements ObjetoBD {
		protected $id;
		protected $titulo;
		protected $etiquetas;
		protected $nota_acumulada;
		protected $num_votos;
		protected $texto;
		protected $contenido_informatico;
		protected $humor_negro;
		protected $perla_visual;
		protected $fecha;
		protected $subidor;
		protected $fecha_subida;
		protected $modificador;
		protected $fecha_modificacion;
		protected $num_denuncias;
		protected $denunciada;
		protected $num_comentarios;
		protected $participantes;

		function CargarDesdeBD( $id, $bd )
		{
			$res = $bd->Consultar( "SELECT * FROM perlas WHERE id='$id'" );
			$this->CargarDesdeRegistro( $res->fetch_assoc() );
			$this->CargarEtiquetasBD( $bd );
			$this->CargarParticipantesBD( $bd );
		}
	
		function ObtenerId(){ return $this->id; }
		function EstablecerId( $id ){ $this->id = $id; }

		function ObtenerTitulo(){ return $this->titulo; }
		function EstablecerTitulo( $titulo ){ $this->titulo = str_replace( '"', '&quot;', $titulo ); }

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

		function ObtenerNotaMedia(){ return $this->nota_acumulada/$this->num_votos; }

		function ObtenerContenidoInformatico(){ return $this->contenido_informatico; }
		function EstablecerContenidoInformatico( $contenido_informatico ){ $this->contenido_informatico = $contenido_informatico; }

		function ObtenerHumorNegro(){ return $this->humor_negro; }
		function EstablecerHumorNegro( $humor_negro ){ $this->humor_negro = $humor_negro; }

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

		function ObtenerPerlaVisual(){ return $this->perla_visual; }
		function EstablecerPerlaVisual( $perla_visual ){ $this->perla_visual = $perla_visual; }

		function ObtenerFecha()
		{ 
			return str_replace( array( '<i>', '</i>' ), '', $this->fecha );
		}
		function EstablecerFecha( $fecha ){ $this->fecha = $fecha; }

		function ObtenerSubidor(){ return $this->subidor; }
		function EstablecerSubidor( $subidor ){ $this->subidor = $subidor; }
		
		function ObtenerFechaSubida(){ return $this->fecha_subida; }
		function EstablecerFechaSubida( $fecha_subida ){ $this->fecha_subida = $fecha_subida; }
		
		function ObtenerModificador(){ return $this->modificador; }
		function EstablecerModificador( $modificador ){ $this->modificador = $modificador; }
		
		function ObtenerFechaModificacion(){ return $this->fecha_modificacion; }
		function EstablecerFechaModificacion( $fecha_modificacion ){ $this->fecha_modificacion = $fecha_modificacion; }

		function ObtenerNumDenuncias(){ return $this->num_denuncias; }
		function EstablecerNumDenuncias( $num_denuncias ){ $this->num_denuncias = $num_denuncias; }
		
		function ObtenerDenunciada(){ return $this->denunciada; }
		function EstablecerDenunciada( $denunciada ){ $this->denunciada = $denunciada; }

		function EstablecerParticipantes( $participantes ){
			$this->participantes = $participantes;
		}

		function ObtenerNumComentarios(){ return $this->num_comentarios; }
		function EstablecerNumComentarios( $num_comentarios ){ $this->num_comentarios = $num_comentarios; }

		public function CargarDesdeFormulario( $info ){
			//die( print_r( $info ) );
			if( isset( $info['id'] ) ){
				$this->id = $info['id'];
			}
			$this->EstablecerTitulo( $info['titulo'] );
			$this->EstablecerTexto( $info['texto'] );
			$this->etiquetas = explode( ', ', $info['etiquetas'] );
			
			$this->EstablecerFecha( $info['fecha'] );
			//for
			//$this->nota_acumulada = $info['nota_acumulada'];
			//$this->num_votos = $info['num_votos'];

			//die( print_r( $info ) );
			if( isset( $info['participantes'] ) ){
				$this->participantes = $info['participantes'];
			}

			//$this->contenido_informatico = $info['contenido_informatico'];
			//$this->humor_negro = $info['humor_negro'];
			//$this->perla_visual = $info['perla_visual'];

			//$this->subidor = $info['subidor'];
			//$this->fecha_subida = $info['fecha_subida'];
			//$this->modificador = $info['modificador'];
			//$this->fecha_modificacion = $info['fecha_modificacion'];

			/*
			if( isset( $info['num_denuncias'] ) ){
				$this->num_denuncias = $info['num_denuncias'];
			}else{
				$this->num_denuncias = 0;
			}
			if( isset( $info['denunciada'] ) ){
				$this->denunciada = $info['denunciada'];
			}else{
				$this->denunciada = false;
			}
			*/
			//$this->num_comentarios = $info['num_comentarios'];
		}

		public function CargarDesdeRegistro( $registro ){
			// TODO: Faltan participantes y etiquetas.
			if( isset( $registro['id'] ) ){			
				$this->id = $registro['id'];
			}
			$this->titulo = $registro['titulo'];
			// TODO: $this->etiquetas = $registro['etiquetas'];
			//$this->nota_acumulada = $info['nota_acumulada'];
			//$this->num_votos = $info['num_votos'];
			$this->texto = $registro['texto'];
			//$this->contenido_informatico = $info['contenido_informatico'];
			//$this->humor_negro = $info['humor_negro'];
			//$this->perla_visual = $info['perla_visual'];
			$this->fecha = $registro['fecha'];

			$this->subidor = $registro['subidor'];
			$this->fecha_subida = $registro['fecha_subida'];
			$this->modificador = $registro['modificador'];
			$this->fecha_modificacion = $registro['fecha_modificacion'];

			if( isset( $registro['num_denuncias'] ) ){
				$this->num_denuncias = $registro['num_denuncias'];
			}else{
				$this->num_denuncias = 0;
			}
			if( isset( $registro['denunciada'] ) ){
				$this->denunciada = $registro['denunciada'];
			}else{
				$this->denunciada = false;
			}

			//$this->num_comentarios = $info['num_comentarios'];
		}

		// Inserta la perla en la BD.
		function InsertarBD( $bd, $id_usuario, $borrar_imagen = false ){
			//die( 'Participantes: ' .  print_r( $this->participantes ) );
			if( !isset( $this->id ) ){
				// La perla es nueva. Inserta la perla en la BD y obtiene su ID.
				$this->id = $bd->Consultar( "INSERT INTO perlas (titulo, texto, fecha_subida, fecha, subidor, fecha_modificacion, modificador) VALUES( '{$this->titulo}', '{$this->texto}', NOW(), '{$this->fecha}', '$id_usuario', NOW(), '$id_usuario' )" );
			}else{
				// La perla no es nueva. Actualiza los datos en la BD.
				$bd->Consultar( "UPDATE perlas SET titulo='{$this->titulo}', texto='{$this->texto}', fecha='{$this->fecha}', fecha_modificacion=NOW(), modificador='{$id_usuario}' WHERE id='{$this->id}' " ) or die ($bd->error);
				
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
		}
		
		function CargarEtiquetasBD( $bd ){
			$res = $bd->Consultar( "SELECT nombre FROM etiquetas JOIN perlas_etiquetas ON perlas_etiquetas.etiqueta = etiquetas.id WHERE perlas_etiquetas.perla = {$this->id}" );
			$this->etiquetas = array();
			while( $reg = $res->fetch_object() ){
				$this->etiquetas[] = $reg->nombre;
			}
		}

		function CargarParticipantesBD( $bd ){
			$res = $bd->Consultar( "SELECT id, nombre FROM usuarios JOIN participantes ON participantes.usuario = usuarios.id WHERE participantes.perla = {$this->id}" );
			$this->participantes = array();
			while( $reg = $res->fetch_object() ){
				$this->participantes[$reg->id] = $reg->nombre;
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
		
			// Trata de subir la imagen (sólo perlas visuales).
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

		// Determina si el usuario '$usuario' es participante de la perla.
		function EsParticipante( $usuario )
		{
			/*
			$res = ConsultarBD( "SELECT * from participantes WHERE usuario='$usuario' AND perla='{$this->info['id']}'" );

			if( $res->num_rows == 1 ) return true;
			else return false;
			*/
			foreach( $this->participantes as $participante => $nombre_participante ){
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
		function ObtenerParticipantes( $bd )
		{
			return $bd->Consultar( "SELECT usuario FROM participantes WHERE perla={$this->id}" );
		}


		// Muestra (en la web) la perla actual. Usa los arrays auxiliares 
		// $usuarios y $categorias para mostrar, respectivamente, los nombres de 
		// los participantes y de la categoría de la perla.
		/*
		function Mostrar( $usuarios, $categorias )
		{
			$modificable = false;

			// Título.
			echo '<div class="perla">';

			echo "<h1>{$this->info['titulo']}</h1>";
		
			// Categorías.
			echo "<span class=\"subtexto\">Categor&iacute;a: {$categorias[$this->info['categoria']]}</span>";

			// Si la perla tiene votos, muestra la nota media y el nº de votantes.
			if( $this->info['num_votos'] != 0 ){
				$nota_media = $this->info['nota_acumulada'] / $this->info['num_votos'];
				echo "<br /><span class=\"subtexto\">Nota media: $nota_media / 10 - N&uacute;mero de votos: {$this->info['num_votos']}</span>";
			}

			// Cuerpo de la perla.
			echo '<div class="cuerpo_perla">';

			// ¿Tiene contenido informático?
			if( $this->info['contenido_informatico'] ){
				echo '<span class="subtexto"><strong>Nota: La perla tiene contenido inform&aacute;tico</strong></span>';
			}

			// ¿Contiene humor negro?
			if( $this->info['humor_negro'] ){
				if( $this->info['contenido_informatico'] ) echo '<br />';
				echo '<span class="subtexto"><strong>Nota: La perla tiene humor negro y/o salvajadas</strong></span>';
			}

			// ¿Perla visual? Muestra la imagen
			if( $this->info['perla_visual'] ){
				//die( getcwd() . " - media/perlas/{$this->info['id']}" );
				echo "<img src=\"media/perlas/{$this->info['id']}\" alt=\"*** ERROR: no se encuentra la imagen ***\" width=\"100%\" alt=\"perla visual - {$this->info['titulo']}\" >";
			}

			// Texto de la perla.
			echo "<p>{$this->info['texto']}</p>";
		
			echo "<span class=\"subtexto\">";
			echo "Subida: {$this->info['fecha_subida']} por {$usuarios[$this->info['subidor']]}<br />";
			echo "&Uacute;ltima modificaci&oacute;n: {$this->info['fecha_modificacion']} por {$usuarios[$this->info['modificador']]}<br />";
			echo "</span>";

			// Participantes.
			echo "Participantes: ";

			echo '<div class="galeria">';
			$participantes = ObtenerParticipantes( $this->info['id'] );

			while( $participante = $participantes->fetch_object() ){
				if( $participante->usuario == $_SESSION['id'] ){
					// ¿El usuario actual tiene permisos para modificar la perla 
					// (es participante de la misma)?
					$modificable = true;
				}
				MostrarAvatar( $usuarios[$participante->usuario] );
				//echo "{$usuarios[$participante->usuario]}, ";
			}

			$participantes->close();
			echo '</div>';
		
		
			$hoy = date("Y-m-d H:i:s");
			$t2 = strtotime( $hoy );
			$t1 = strtotime( $this->info['fecha_subida'] );
			$minutos = ($t2 - $t1)/60;
		
			// Si el usuario actual puede modificar/borrar la perla actual, muéstrale
			// los botones para hacerlo.
		
			if( $modificable ){
				CrearCabeceraFormulario( 'php/controladores/perlas.php', 'post' );
				echo "<input type=\"hidden\" name=\"perla\" value=\"{$this->info['id']}\" />";
				echo '<input type="submit" name="accion" value="Modificar perla" />';
				echo '</form>';
			}
		

			CrearCabeceraFormulario( 'php/controladores/perlas.php', 'post', 1 );
			echo "<input type=\"hidden\" name=\"perla\" value=\"{$this->info['id']}\" />";
			if( $modificable && ($minutos < 30) ){
				echo '<input type="submit" name="accion" value="Borrar perla" />';
			}else{
				if( isset( $this->info['num_denuncias'] ) ){
					$n = 3 - $this->info['num_denuncias'];
				}else{
					$n = 3;
				}
				if( $this->info['num_denuncias'] ){
					echo "({$this->info['num_denuncias']} persona(s) ha(n) votado para eliminar esta perla - $n votos restantes)<br/>";
				}else{
					echo "(0 persona(s) ha(n) votado para eliminar esta perla - $n votos restantes)<br/>";
				}
				if( isset( $this->info['denunciada'] ) ){
					echo 'Has votado para borrar esta perla: ';
					echo '<input type="submit" name="accion" value="Cancelar voto borrado" />';
				}else{
					$denuncias = $this->info['num_denuncias'] + 1;
					echo "<input type=\"hidden\" name=\"num_denuncias\" value=\"$denuncias\" />";
					echo '<input type="submit" name="accion" value="Denunciar perla" />';
					//echo '<input type="submit" name="accion" value="Denunciar perla 2" />';
				}
			}
			echo '</form>';
			echo "<br /><a href=\"Javascript:void(0)\" onclick=\"MostrarPerla('{$this->info['id']}')\">Comentar Perla (comentarios: {$this->info['num_comentarios']})</a>";

			echo '<br />';
			// Formulario (select + botón) para votar la perla.
			GenerarFormularioVoto( $this->info['id'] );		

			echo '</div>';
			echo '</div>';
		}
		*/

		// TODO: Completar
		

	} // Fin de la clase Perla.
	


	// Crea una categoria de nombre '$nombre' y la inserta en la BD.
	// Valor devuelto: id de la nueva categoria en la BD.
	function CrearCategoria( $nombre )
	{
		return $bd->Consultar( "INSERT INTO categorias (nombre, num_perlas) VALUES ('$nombre', 0) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)" );
	}

	// Inserta los participantes '$participantes' de la perla 'id_perla' en la 
	// BD.
	

	

	// Obtiene las 10 perlas con mejor nota en orden descendente.
	function ObtenerTop10Perlas()
	{
		return ConsultarBD( "SELECT * FROM perlas LEFT JOIN (SELECT perla, COUNT(*) AS num_denuncias FROM denuncias_perlas GROUP BY perla) t2 ON id = t2.perla LEFT JOIN (SELECT perla AS denunciada FROM denuncias_perlas WHERE usuario = {$_SESSION['id']}) denuncias ON id = denunciada ORDER BY nota_acumulada DESC LIMIT 10" );
	}


	


	// Devuelve el nº de perlas de la categoría $categoria.
	// $categoria = 0 -> cualquier categoría.
	function ContarPerlas( $categoria )
	{
		return 50;
		$n = 0;
		if( $categoria == 0 ){
			// Cualquier categoría.
			// El nº de perlas totales no está precalculado. Se suma el nº de 
			// perlas de cada categoría.
			$categorias = ConsultarBD( "SELECT num_perlas FROM categorias" );
			while( $categoria = $categorias->fetch_object() ){
				$n += $categoria->num_perlas;
			}
		}else{
			$categorias = ConsultarBD( "SELECT num_perlas FROM categorias WHERE id=$categoria" );
			$categoria = $categorias->fetch_array();
			$n = $categoria[0];
		}
		return $n;
	}


	
	

	// Muestra (en la web) un formulario (select + botón) para votar por
	// la perla cuya id es $id_perla.
	function GenerarFormularioVoto( $id_perla )
	{
		CrearCabeceraFormulario( 'php/controladores/perlas.php', 'post' );
		echo "<input type=\"hidden\" name=\"id_perla\" value=\"$id_perla\" />";
		echo '<select name="nota">';
		for( $i=0; $i<=10; $i++ ){
			echo "<option value=\"$i\">$i</option>";
		}
		echo '</select>';
		echo '<input type="submit" name="accion" value="Puntuar Perla">'; 
		echo '</form>';
	}

	
	// Inserta/actualiza en la BD la puntuación de la perla cuya id es $perla
	// con la nota $nota.
	function PuntuarPerla( $perla, $nota )
	{
		// La nota acumulada y el numero de votos de una perla se
		// actualizan automáticamente en la BD gracias a una serie de 
		// disparadores creados en la misma.
		// Fuente: http://dev.mysql.com/doc/refman/5.0/es/create-trigger.html

		// Insertar o actualizar si ya existe:
		// http://mjcarrascosa.com/insertar-o-actualizar-registros-en-mysql/
		ConsultarBD( "INSERT INTO votos (perla, usuario, nota, fecha) VALUES ($perla, {$_SESSION['id']}, $nota, NOW()) ON DUPLICATE KEY UPDATE nota=$nota, fecha=NOW()" );
	}

	// Comprueba que el fichero que se ha subido es válido.
	// En caso de éxito no devuelve nada, y si hay un error lanza una excepción.
	function ComprobarImagen( $nombre )
	{
		$tipos_soportados = array( 'image/jpeg', 'image/png' );

		// ¿Hubo algún error en la subida?. El error 4 (No se subió fichero) ya
		// se tiene en cuenta antes de intentar subir el fichero.
		if( $_FILES[$nombre]['error'] > 0 ){
			throw new Exception( 'ERROR: ' . MostrarErrorFichero( $_FILES[$nombre]['error'] ) );
		}

		// Comprueba que el tipo mime de la imagen es jpeg o png.
		// Contribución de renato en la ayuda de php.
		$finfo = new finfo( FILEINFO_MIME );
		$tipo_imagen = $finfo->file( $_FILES[$nombre]['tmp_name'] );
		$tipo_mime = substr( $tipo_imagen, 0, strpos($tipo_imagen, ';') );
		//$tipo_imagen = mime_content_type( $_FILES[$nombre]['tmp_name'] );
		echo 'Tipo mime: ' . $tipo_mime . '<br />';
		if( !in_array( $tipo_mime, $tipos_soportados ) ){
			throw new Exception( 'ERROR: tipo de imagen no soportado. Tipos soportados: jpeg, png' );
		}
	}


	// Devuelve una string explicativa para el error de fichero con el código $codigo.
	// El error 4 (No se subió fichero) no se contempla.
	function MostrarErrorFichero( $codigo )
	{
		$max_tam_imagen = ini_get( 'upload_max_filesize' );
		$mensajes_error = array(
			UPLOAD_ERR_INI_SIZE => "El tama&ntilde;o del fichero sobrepasa el m&aacute;ximo definido ($max_tam_imagen)",
			UPLOAD_ERR_FORM_SIZE => 'El tama&ntilde;o del fichero sobrepasa el m&aacute;ximo definido en el formulario HTML',
			UPLOAD_ERR_PARTIAL => 'S&oacute;lo se carg&oacute; parte del archivo',
			UPLOAD_ERR_NO_TMP_DIR => 'No se encuentra el directorio temporal',
			UPLOAD_ERR_CANT_WRITE => 'No se puede escribir en disco',
			UPLOAD_ERR_EXTENSION => 'Una extensi&oacute;n PHP par&oacute; la subida del fichero'
		);
		return 'ERROR SUBIENDO FICHERO: ' . $mensajes_error[$codigo] . '<br />';
	}


	// Trata de insertar la imagen $nombre para la perla cuya id es $id_perla.
	// Si hay algún error lanza una excepción.
	function InsertarImagen( $nombre, $id_perla )
	{
		if( $_FILES['imagen']['error'] == UPLOAD_ERR_NO_FILE ){
			echo 'Sin imagen subida';
			return;
		}

		try{
			ComprobarImagen( $nombre );

			echo "Imagen: " . $_FILES["imagen"]["name"] . "<br />";
			echo "Tipo: " . $_FILES["imagen"]["type"] . "<br />";
			echo "Tamanno: " . ($_FILES["imagen"]["size"] / 1024) . " Kb<br />";

			if( !move_uploaded_file($_FILES["imagen"]["tmp_name"], "media/perlas/" . $id_perla ) ) throw new Exception( 'ERROR moviendo fichero' );
			echo "Guardada en: " . $_FILES["imagen"]["tmp_name"] . '<br />';
		}catch( Exception $e ){
			die( $e->getMessage() );
		}
	}

	function BorrarPerla( $id_perla )
	{
		$bd = ConectarBD();

		$res = $bd->query( "DELETE FROM perlas WHERE id='$id_perla'" );

		if( !$res ){
			throw new Exception( 'ERROR borrando perla: ' . $bd->error );
		}

		$bd->close();
	}
?>
