<?php
	// Conjunto de funciones relacionadas con las perlas.
	include_once 'utilidades.php';

	// Obtiene la lista de categorías de la base de datos (por orden ascendente 
	// de nombres).
	function ObtenerCategorias()
	{
		return ConsultarBD( "SELECT * from categorias ORDER BY nombre ASC" );
	}


	// Inserta la perla definida por el array asociativo '$perla' en la BD.
	// Devuelve null en caso de éxito o una string explicativa en caso de 
	// error.
	function InsertarPerla( $perla )
	{
		$bd = ConectarBD();

		// Inserta la perla en la BD.
		$res = $bd->query( "INSERT INTO perlas (titulo, texto, fecha_subida, fecha, contenido_informatico, humor_negro, perla_visual, categoria, subidor, fecha_modificacion, modificador) VALUES( '{$perla['titulo']}', '{$perla['texto']}', NOW(), '{$perla['fecha']}', '{$perla['contenido_informatico']}', '{$perla['humor_negro']}', '{$perla['perla_visual']}', '{$perla['categoria']}', '{$perla['subidor']}', NOW(), '{$perla['subidor']}' )" );

		if( !$res ){
			throw new Exception( 'ERROR subiendo perla: ' . $bd->error );
		}
		
		// Obtiene el id de la nueva perla en la BD.
		$id_perla = $bd->insert_id;
		
		// Inserta en la BD los participantes de la perla.
		foreach( $perla['participantes'] as $participante ){
			$res = $bd->query( "INSERT INTO participantes (perla, usuario) VALUES ('{$id_perla}', '{$participante}' )" );
			if( !$res ){
				throw new Exception( 'ERROR subiendo participantes: ' . $bd->error );
			}
		}

		$bd->close();

		// Trata de subir la imagen (sólo perlas visuales).
		if( $perla['perla_visual'] ){
			try{
				InsertarImagen( 'imagen', $id_perla );
			}catch( Exception $e ){
				throw $e;
			}
		}
	}


	// Actualiza en la BD la perla cuya id es '$id' y cuyos nuevos datos están 
	// en el array asociativo '$perla'.
	// Devuelve null en caso de éxito o una string explicativa en caso de 
	// error.
	function ActualizarPerla( $id, $perla, $borrar_imagen = false )
	{
		$bd = ConectarBD();
		$res = $bd->query( "SELECT categoria FROM perlas WHERE id=$id" );
		$res = $res->fetch_array();
		$categoria_anterior = $res[0];

		echo 'Categoria anterior: (' . $categoria_anterior . ')';
		RestarPerla( $categoria_anterior );
		SumarPerla( $perla['categoria'] );

		$res = $bd->query( "UPDATE perlas SET titulo='{$perla['titulo']}', texto='{$perla['texto']}', fecha='{$perla['fecha']}', contenido_informatico='{$perla['contenido_informatico']}', humor_negro='{$perla['humor_negro']}', perla_visual='{$perla['perla_visual']}', categoria='{$perla['categoria']}', fecha_modificacion=NOW(), modificador='{$perla['subidor']}' WHERE id='{$id}' " );

		if( !$res ){
			return $bd->error;
		}
		
		$res = ConsultarBD( "DELETE FROM participantes WHERE perla='$id'" );
		if( !$res ){
			return $bd->error;
		}

		foreach( $perla['participantes'] as $participante ){
			$res = $bd->query( "INSERT INTO participantes (perla, usuario) VALUES ('{$id}', '{$participante}' )" );
			if( !$res ){
				return $bd->error;
			}
		}
		
		// Trata de subir la imagen (sólo perlas visuales).
		if( $_FILES['imagen']['error'] != UPLOAD_ERR_NO_FILE ){
			try{
				InsertarImagen( 'imagen', $id );
			}catch( Exception $e ){
				throw $e;
			}
		}else{
			// No se subió una imagen. Esto puede indicar que la perla contenía
			// una imagen pero el usuario quiere borrarla. Comprobamos si es así.
			if( ($borrar_imagen == true) && file_exists( "../datos/img/perlas/" . $id ) ){
				unlink( "../datos/img/perlas/" . $id );
			}
		}

		$bd->close();
	
		return null;
	}


	// Determina si el usuario '$usuario' es participante de la perla cuyo id
	// es '$perla'.
	function EsParticipante( $usuario, $perla )
	{
		$res = ConsultarBD( "SELECT * from participantes WHERE usuario='$usuario' AND perla='$perla'" );

		if( $res->num_rows == 1 ) return true;
		else return false;
	}


	function ObtenerTop10Perlas()
	{
		return ConsultarBD( "SELECT * FROM perlas ORDER BY nota_acumulada DESC LIMIT 10" );

		/*
		return $res;
		
		$aux = $res->fetch_all( MYSQLI_ASSOC );
		$res->close();

		return $aux;
		*/
	}

	// Obtiene de la BD las perlas de la categoría '$categoria'. En caso de
	// que se quiera recuperar sólo una "página" de los datos, se puede
	// especificar el '$offset' o nº de registro a partir del cual se recupera
	// y cuantos registros se recuperan ($n).
	function ObtenerPerlas( $categoria = 0, $participante = 0, $contenido_informatico = 1, $humor_negro = 1, $palabras = null, $offset = 0, $n = 0 )
	{
		$consulta = 'SELECT SQL_CALC_FOUND_ROWS * from perlas ';
		$and = false;

		if( $participante ){
			$consulta .= "INNER JOIN participantes ON perlas.id=participantes.perla AND participantes.usuario='$participante' ";
		}
		
		//$consulta .= CrearSubCategoria( $categoria, $participante, $contenido_informatico, $humor_negro, $palabras );
		//if( $offset ){
		//	$consulta .= "LIMIT $offset, $n";
		//}
		if( $categoria || !$contenido_informatico || !$humor_negro ){
			$consulta .= 'WHERE ';
			if( $categoria ){
				$consulta .= "categoria='$categoria' ";
				$and = true;
			}
	
			if( !$contenido_informatico ){
				if( $and ) $consulta .= 'AND ';
				$consulta .= 'contenido_informatico=0 ';
				$and = true;
			}
			
			if( !$humor_negro ){
				if( $and ) $consulta .= 'AND ';
				$consulta .= 'humor_negro=0 ';
				$and = true;
			}			
		}

		$consulta .= 'ORDER BY id DESC ';

		if( $n != 0 ){
			$consulta .= "LIMIT $offset, $n";
		}

		$res = ConsultarBD( $consulta, true );

		if( !$res ) return null;
		return $res;

		/*
		$aux = $res->fetch_all( MYSQLI_ASSOC );
		$res->close();

		return $aux;
		*/
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
		$res = ConsultarBD( "SELECT * from perlas WHERE id='$id_perla'" );

		if( $res->num_rows > 0 ){
			if( !$tipo_objeto )
				return $res->fetch_array();
			else
				return $res->fetch_object();
		}else{
			return null;
		}
	}


	// En la BD, suma 1 al nº de perlas de la categoría $categoria.
	function SumarPerla( $categoria )
	{
		$res = ConsultarBD( "SELECT num_perlas FROM categorias WHERE id=$categoria" );
		$n = $res->fetch_array();
		$res->close();

		$n = $n[0]+1;

		ConsultarBD( "UPDATE categorias SET num_perlas={$n} WHERE id=$categoria" );
	}


	// En la BD, resta 1 al nº de perlas de la categoría $categoria.
	function RestarPerla( $categoria )
	{
		$res = ConsultarBD( "SELECT num_perlas FROM categorias WHERE id=$categoria" );
		$n = $res->fetch_array();
		$res->close();

		$n = $n[0]-1;

		ConsultarBD( "UPDATE categorias SET num_perlas={$n} WHERE id=$categoria" );
	}

	
	// Obtiene los participantes de la perla cuya id es $id_perla.
	function ObtenerParticipantes( $id_perla )
	{
		return ConsultarBD( "SELECT usuario FROM participantes WHERE perla=$id_perla" );
	}

	
	// Muestra (en la web) la perla '$perla'. Usa los arrays auxiliares 
	// $usuarios y $categorias para mostrar, respectivamente, los nombres de 
	// los participantes y de la categoría de la perla.
	function MostrarPerla( $perla, $usuarios, $categorias )
	{
		$modificable = false;

		// Título.
		echo '<div class="perla">';
		echo "<h1>{$perla['titulo']}</h1>";
		
		// Categorías.
		echo "<span class=\"subtexto\">Categor&iacute;a: {$categorias[$perla['categoria']]}</span>";

		// Si la perla tiene votos, muestra la nota media y el nº de votantes.
		if( $perla['num_votos'] != 0 ){
			$nota_media = $perla['nota_acumulada'] / $perla['num_votos'];
			echo "<br /><span class=\"subtexto\">Nota media: $nota_media / 10 - N&uacute;mero de votos: {$perla['num_votos']}</span>";
		}

		// Cuerpo de la perla.
		echo '<div class="cuerpo_perla">';

		// ¿Tiene contenido informático?
		if( $perla['contenido_informatico'] ){
			echo '<span class="subtexto"><strong>Nota: La perla tiene contenido inform&aacute;tico</strong></span>';
		}

		// ¿Contiene humor negro?
		if( $perla['humor_negro'] ){
			if( $perla['contenido_informatico'] ) echo '<br />';
			echo '<span class="subtexto"><strong>Nota: La perla tiene humor negro y/o salvajadas</strong></span>';
		}

		// ¿Perla visual? Muestra la imagen
		if( $perla['perla_visual'] ){
			//die( getcwd() . " - media/perlas/{$perla['id']}" );
			echo "<img src=\"media/perlas/{$perla['id']}\" alt=\"*** ERROR: no se encuentra la imagen ***\" width=\"100%\" >";
		}

		// Texto de la perla.
		echo "<p>{$perla['texto']}</p>";
		echo "<span class=\"subtexto\">";
		echo "Subida: {$perla['fecha_subida']} por {$usuarios[$perla['subidor']]}<br />";
		echo "&Uacute;ltima modificaci&oacute;n: {$perla['fecha_modificacion']} por {$usuarios[$perla['modificador']]}<br />";
	
		// Participantes.
		echo "Participantes: ";

		$participantes = ObtenerParticipantes( $perla['id'] );

		while( $participante = $participantes->fetch_object() ){
			if( $participante->usuario == $_SESSION['id'] ){
				// ¿El usuario actual tiene permisos para modificar la perla 
				// (es participante de la misma)?
				$modificable = true;
			}
			echo "{$usuarios[$participante->usuario]}, ";
		}

		$participantes->close();

		echo "</span>";

		// Si el usuario actual puede modificar la perla actual, muéstrale el
		// botón para hacerlo.
		if( $modificable ){
			echo "<form><input type=\"button\" onclick=\"ModificarPerla('{$perla['id']}' )\" value=\"Modificar perla\" /></form>";
		}	

		echo "<br /><a href=\"Javascript:void(0)\" onclick=\"MostrarPerla('{$perla['id']}')\">Comentar Perla (comentarios: {$perla['num_comentarios']})</a>";

		echo '<br />';
		// Formulario (select + botón) para votar la perla.
		GenerarFormularioVoto( $perla['id'] );		

		echo '</div>';
		echo '</div>';
	}


	// Muestra (en la web) un formulario (select + botón) para votar por
	// la perla cuya id es $id_perla.
	function GenerarFormularioVoto( $id_perla )
	{
		echo '<form method="post">';
		echo "<input type=\"hidden\" name=\"id_perla\" value=\"$id_perla\" />";
		echo '<select name="nota">';
		for( $i=0; $i<=10; $i++ ){
			echo "<option value=\"$i\">$i</option>";
		}
		echo '</select>';
		echo '<input type="submit" value="Puntuar">'; 
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
?>
