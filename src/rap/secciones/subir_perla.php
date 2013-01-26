<?php
	// Sección para subir una perla (o actualizar una existente).
	//die( phpinfo() );

	require_once DIR_LIB . 'usuarios.php';

	if( isset( $_POST[ 'titulo_perla' ] ) ){
		// El usuario ha enviado un formulario con una nueva perla. Rellena
		// un array asociativo "$perla" con los datos.

		// Titulo.
		$perla['titulo'] = $_POST[ 'titulo_perla' ];

		// Texto de la perla. A éste texto se le da un formateo previo, 
		// consistente en tomar las líneas de tipo 'participante: texto' y
		// resaltar (poner en negrita) la parte de 'participante'.
		$lineas = explode( "\n", $_POST[ 'texto_perla' ] );
		$perla['texto'] = '';

		foreach( $lineas as $linea ){
			$tokens = explode( ': ', $linea, 2 );
			if( count( $tokens ) == 2 ){
				$perla['texto'] .= "<strong>{$tokens[0]}: </strong>{$tokens[1]}<br />";
			}else{
				$perla['texto'] .= $linea . '<br />';
			}
		}

		// Imagen.
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

		// Nueva categoria.
		$perla['nueva_categoria'] = $_POST['nueva_categoria'];
		
		// Fecha de subida.
		$perla['fecha_subida'] = date("Y/m/d");

		// Fecha.
		if( $_POST['fecha_perla'] != '' ){
			$perla['fecha'] = "<i>{$_POST['fecha_perla']}</i>";
		}else{
			$perla['fecha'] = '<i>No especificada</i>';
		}
		
		// Contenido informatico.
		$perla['contenido_informatico'] = isset( $_POST['contenido_informatico'] );

		// Humor negro.
		$perla['humor_negro'] = isset( $_POST['humor_negro'] );

		// Imagen.
		$perla['imagen'] = false;

		// Categoria.
		$perla['categoria'] = $_POST['categoria_perla'];

		// Participantes.
		// Los participantes se guardan en una string de la forma 
		// "p1,p2,...,pn,". Quitamos la última ',' y "rompemos" la string en
		// un array de participantes.
		$_POST['participantes'] = substr_replace( $_POST['participantes'], "", -1 );
		$perla['participantes'] = explode( ',', $_POST['participantes'] );

		// Subidor. Se que suena mal, pero queria dejarlo todo en Español.
		$perla['subidor'] = $_SESSION['id'];

		// La estructura "$perla" está completa. Ahora diferenciamos entre dos
		// casos, según si estamos subiendo una perla nueva o actualizando una
		// existente.
		if( !isset( $_POST['modificar'] ) ){
			// Vamos a insertar una perla nueva.
			try{
				InsertarPerla( $perla );
				//SumarPerla( $perla['categoria'] );
				die( 'Perla subida correctamente' );
			}catch( Exception $e ){
				die( $e->getMessage() );
			}
		}else{
			// Estamos actualizando una perla existente. En $_POST['modificar']
			// tenemos la id de la perla en cuestión.
			$res = ActualizarPerla( $_POST['modificar'], $perla, isset( $_POST['borrar_imagen'] ) );
			if( $res == null ){
				die( 'Perla subida correctamente' );
			}else{
				die( $res );
			}
		}		
	}else{
		// El usuario no ha enviado nada, sólo acaba de entrar en esta sección.
		// ¿Quiere subir una perla nueva o modificar una existente?

		if( isset( $_GET['modificar'] ) ){
			echo "Aqui<br/>";
			print_r( $_POST );
			if( isset( $_POST['borrar_perla'] ) ){
				echo "Aqui 2<br/>";
				BorrarPerla( $_GET['modificar'] );
				die( "Perla {$_GET['modificar']} borrada\n" );
				//header( 'Location: general.php?seccion=lista_perlas' );
			}
			// El usuario quiere modificar una perla existente. La variable
			// $_GET['modificar'] contiene La id de la perla en cuestión.

			// Comprueba que el usuario es participante de la perla (y por 
			// tanto tiene permiso para modificarla).
			if( !EsParticipante( $_SESSION['id'], $_GET['modificar'] ) ){
				die( 'No tienes permisos para modificar esta perla' );
			}

			// Obtiene la perla de la base de datos. Algunos campos de la misma
			// tienen un formato (tienen etiquetas html), conviértelos en
			// texto plano.
			$perla = ObtenerPerla( $_GET['modificar'] );

			$v = array( "<strong>", "</strong>", "<br />" );
			$perla['texto'] = str_replace( $v, "", $perla['texto'] );

			$v = array( "<i>", "</i>" );
			$perla['fecha'] = str_replace( $v, "", $perla['fecha'] );

			// Rellena un array con los participantes de la perla.
			$rParticipantes = ObtenerParticipantes( $perla['id'] );
	
			$perla['participantes'] = array();
			while( $rParticipante = $rParticipantes->fetch_object() ){
				$perla['participantes'][] = $rParticipante->usuario;
			}
		}else{
			// El usuario va a subir una perla nueva. Rellena sus campos con
			// los valores por defecto.
			$perla['titulo'] = $perla['texto'] = $perla['fecha'] = '';

			$perla['contenido_informatico'] = $perla['humor_negro'] = $perla['perla_visual'] = false;

			$perla['categoria'] = 13; // 13 - Sin categoria
		
			$perla['participantes'] = array( $_SESSION['id'] );
		}
	}
?>

<!-- TÍTULO -->
<h1>Subir perla</h1>

<!-- FORMULARIO PARA SUBIR/MODIFICAR UNA PERLA -->
<form id="form_subir_perla" action="general.php?seccion=subir_perla" method="post" enctype="multipart/form-data">

	<!-- ¿Título de la perla? (campo de texto) -->
	<p>
	<label for="titulo_perla">T&iacute;tulo: </label>
	<?php
		echo "<input type=\"text\" name=\"titulo_perla\" id=\"titulo_perla\" value=\"{$perla['titulo']}\" />";
	?>
	</p>


	<!-- ¿Categoría de la perla? (campo select) -->
	<p>
	<label for="categoria_perla">Categoria de la perla: </label>
	<select name="categoria_perla" id="categoria_perla" />
	<?php
		$categorias = ObtenerCategorias();
		
		while( $categoria = $categorias->fetch_object() ){
			echo "<option value=\"{$categoria->id}\")\" ";
			if( $categoria->id == $perla['categoria'] ){
				echo "selected=\"selected\" ";
			}
			echo ">{$categoria->nombre}</option>";
		}

		$categorias->close();
	?>
	</select>
	

	<!-- <p id="p_nueva_categoria"> -->
	<label for="nueva_categoria">---> O introduce una nueva categor&iacute;a: </label>
	<input type="text" name="nueva_categoria" id="nueva_categoria" value="" />
	<!-- </p> -->
	</p>
	<!-- Imagen (solo perlas visuales) -->
	<?php
		if( $perla['perla_visual'] ){
			echo "<img src=\"../datos/img/perlas/{$_GET['modificar']}\" width=\"100%\" >";
			echo '<input type="checkbox" name="borrar_imagen" value="" />Borrar imagen<br />';
		}
	?>
	<label for="imagen">Imagen (s&oacute;lo perlas visuales): </label>
	<input type="file" name="imagen" id="imagen" />
	<!-- <a href="Javascript:void(0)" onclick="VaciarElemento('imagen')">Resetear campo de fichero</a> -->

	<!-- ¿Texto de la perla? (textarea) --> 
	<p>
	<label for="texto_perla">Texto: </label>
	
	<textarea name="texto_perla" id="texto_perla"><?php echo $perla['texto']; ?></textarea>
	</p>

	<input type="hidden" id="participantes" name="participantes" value="" />
	<?php
		if( isset( $_GET['modificar'] ) ){
			echo "<input type=\"hidden\" name=\"modificar\" value=\"{$_GET['modificar']}\" />";
		}
	?>

	<!-- ¿Fecha de la perla (cuándo ocurrió)? (campo de texto) -->
	<p>
	<label for="fecha_perla">Fecha de la perla (Si sabes el d&iacute;a concreto, ponlo como dd/mm/aaaa. Si no, pues una frase o lo que sea (p.e. "el año pasado"). Tambi&eacute;n se puede dejar vac&iacute;a: </label>
	<?php
		echo "<input type=\"text\" name=\"fecha_perla\" id=\"fecha_perla\" value=\"{$perla['fecha']}\" />";
	?>
	</p>

	<!-- ¿Contenido informático? / ¿Humor negro? (checkboxes) -->
	<?php
		echo '<input type="checkbox" name="contenido_informatico" value="" ';
		if( $perla['contenido_informatico'] ){
			echo 'checked';
		}
		echo '/>';
		echo '<strong>Contenido Inform&aacute;tico</strong><br />';

		echo '<input type="checkbox" name="humor_negro" value="" ';
		if( $perla['humor_negro'] ){
			echo 'checked';
		}
		echo '/><strong>Humor negro / salvajada</strong><br />';
	?>
	
	<!-- Obtiene un array con los nombres de los usuarios para luego mostrar 
		 los participantes de la perla -->
	<?php
		$rUsuarios = ObtenerUsuarios();
		$usuarios = array();
		while( $rUsuario = $rUsuarios->fetch_object() ){
			$usuarios[$rUsuario->id] = $rUsuario->nombre;
		}
		$rUsuarios->close();
	?>

	<!-- Participantes actuales de la perla. Al lado de cada participante
		 se muestra una [x] para eliminar el participante (por javascript). -->
	<p>Participantes en la perla: </p>
	<ul id="lista_participantes">
		<?php 
			foreach( $perla['participantes'] as $participante ){
				echo "<li id=\"p_{$participante}\" >{$usuarios[$participante]} ";
				echo "<a href=\"javascript:void(0)\" onClick=\"EliminarParticipante('{$participante}')\">[x]</a>";
				echo '</li>';
			}
		?>
	</ul>

	<!-- Campo select para añadir participantes a la perla. Cuando se eligue un
		 participante, éste se añadi a la lista anterior por medio de javascript -->
	<?php
		$usuarios = ObtenerUsuarios();

		if( !$usuarios ) die( 'Error: no se obtuvieron usuarios de la base de datos' );

		echo '<p>';
		echo '<label for="seleccion_usuarios">A&ntilde;adir participante: </label>';
		echo '<select id="seleccion_usuarios">';
		while( $usuario = $usuarios->fetch_object() ){
			echo "<option value=\"{$usuario->id}\" onclick=\"AnnadirParticipante('{$usuario->id}', '{$usuario->nombre}')\">";
			echo $usuario->nombre;
			echo '</option>';
		}
		echo '</select>';
		echo '</p>';

		$usuarios->close();

		// Botón de "submit". Se llama a una función javascript que haga 
		// comprobaciones sobre el formulario antes de subir el formulario.
		echo '<input type="button" value="Subir Perla" ';
		echo "onclick=\"SubirPerla('{$_SESSION['id']}')\" ";
		echo '/>';
	?>
	
</form>
