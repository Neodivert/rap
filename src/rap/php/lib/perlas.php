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
	require_once 'utilidades.php';
	require_once DIR_CLASES . 'objeto_bd.php';

	class Perla extends ObjetoBD {

		// Inserta la perla en la BD.
		public function InsertarBD( $bd ){
			// Si con la perla se crea una nueva categoria, introduce la ultima
			// en la BD. Tambien se obtiene la id de la categoria.
			if( $perla['nueva_categoria'] ){
				$id_categoria = CrearCategoria( $perla['nueva_categoria'] );
			}else{
				$id_categoria = $perla['categoria'];
			}

			// Inserta la perla en la BD y obtiene su ID.
			$id_perla = $bd->Consultar( "INSERT INTO perlas (titulo, texto, fecha_subida, fecha, contenido_informatico, humor_negro, perla_visual, categoria, subidor, fecha_modificacion, modificador) VALUES( '{$this->info['titulo']}', '{$this->info['texto']}', NOW(), '{$this->info['fecha']}', '{$this->info['contenido_informatico']}', '{$this->info['humor_negro']}', '{$this->info['perla_visual']}', '$id_categoria', '{$this->info['subidor']}', NOW(), '{$this->info['subidor']}' )" );
		
			// TODO: Tener en cuenta si $id_perla == false (error al insertar).
		
			// Inserta en la BD los participantes de la perla.
			InsertarParticipantes( $id_perla, $perla['participantes'] );

			// Notifica por email.
			NotificarPorEmail( 'nueva_perla', $id_perla );

			// Trata de subir la imagen (sólo perlas visuales).
			if( $perla['perla_visual'] ){
				try{
					InsertarImagen( 'imagen', $id_perla );
				}catch( Exception $e ){
					throw $e;
				}
			}
		}

		
		// Actualiza en la BD la perla cuya id es '$id_perla'.
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


		// Determina si el usuario '$usuario' es participante de la perla.
		function EsParticipante( $usuario )
		{
			$res = ConsultarBD( "SELECT * from participantes WHERE usuario='$usuario' AND perla='{$this->info['id']}'" );

			if( $res->num_rows == 1 ) return true;
			else return false;
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

		// Obtiene los participantes de la perla cuya id es $id_perla.
		function ObtenerParticipantes( $id_perla )
		{
			return $this->bd->Consultar( "SELECT usuario FROM participantes WHERE perla=$id_perla" );
		}


		// Muestra (en la web) la perla actual. Usa los arrays auxiliares 
		// $usuarios y $categorias para mostrar, respectivamente, los nombres de 
		// los participantes y de la categoría de la perla.
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


	} // Fin de la clase Perla.
	


	// Crea una categoria de nombre '$nombre' y la inserta en la BD.
	// Valor devuelto: id de la nueva categoria en la BD.
	function CrearCategoria( $nombre )
	{
		return $bd->Consultar( "INSERT INTO categorias (nombre, num_perlas) VALUES ('$nombre', 0) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)" );
	}

	// Inserta los participantes '$participantes' de la perla 'id_perla' en la 
	// BD.
	function InsertarParticipantes( $id_perla, $participantes ){
		$bd = ConectarBD();

		foreach( $participantes as $participante ){
			$res = $bd->query( "INSERT INTO participantes (perla, usuario) VALUES ('{$id_perla}', '{$participante}' )" ) or die( $bd->error );
		}
	
		$bd->close();
	}

	

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


	// Recupera de la BD la perla cuya id es $id_perla. Se puede especificar
	// si se desea recuperar como un array asociativo ($tipo_objeto = false)
	// o como un objeto ($tipo_objeto = true).
	function ObtenerPerla( $id_perla, $tipo_objeto = false )
	{
		$consulta = "SELECT * from perlas ";
		$consulta .= 'LEFT JOIN (SELECT perla, COUNT(*) AS num_denuncias FROM denuncias_perlas GROUP BY perla) t2 ON id = t2.perla ';
		$consulta .= "LEFT JOIN (SELECT perla AS denunciada FROM denuncias_perlas WHERE usuario = {$_SESSION['id']}) denuncias ON id = denunciada WHERE id='$id_perla'";

		$res = ConsultarBD( $consulta );

		if( $res->num_rows > 0 ){
			if( !$tipo_objeto )
				return $res->fetch_array();
			else
				return $res->fetch_object();
		}else{
			return null;
		}
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
