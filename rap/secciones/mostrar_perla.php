<?php
	// Sección para mostrar una perla y sus comentarios

	// Comprueba que se ha elegido una perla para mostrar (variable GET).
	if( !isset( $_GET['perla'] ) ){
		die( 'No se ha seleccionado una perla' );
	}

	// El usuario ha enviado un comentario.
	if( isset( $_POST['texto_comentario'] ) ){
		InsertarComentario( $_GET['perla'], $_POST['texto_comentario'] );
	}

	if( isset( $_POST['borrar_comentario'] ) ){
		BorrarComentario( $_POST['comentario'] );
		//echo "Borrando comentario ({$_POST['comentario']})";
	}

	if( isset( $_POST['modificar_comentario'] ) ){
		//echo "Modificando el comentario ({$_POST['comentario']}) con el texto {$_POST['texto']}";
		ModificarComentario( $_POST['comentario'], $_POST['texto'] );
		//echo "Borrando comentario ({$_POST['comentario']})";
	}

	// El usuario quiere puntuar una perla (DUPLICADO).
	if( isset( $_POST['nota'] ) ){
		PuntuarPerla( $_POST['id_perla'], $_POST['nota'] );
	}
?>

<!-- MUESTRA LA PERLA -->
<?php
	// Obtiene los nombres de los usuarios en un array.
	$rUsuarios = ObtenerUsuarios();
	$usuarios = array();
	while( $rUsuario = $rUsuarios->fetch_object() ){
		$usuarios[$rUsuario->id] = $rUsuario->nombre;
	}

	// Obtiene los nombres de las categorias en un array.
	// ESTO SE PUEDE OPTIMIZAR, pidiendo solo el nombre
	// de la categoría de la perla a la BD.
	$rCategorias = ObtenerCategorias();
	$categorias = array();
	while( $rCategoria = $rCategorias->fetch_object() ){
		$categorias[$rCategoria->id] = $rCategoria->nombre;
	}

	// Obtiene la perla de la BD y la muestra usando los arrays de usuarios
	// y de categorias como apoyo.
	$perla = ObtenerPerla( $_GET['perla'] );
	MostrarPerla( $perla, $usuarios, $categorias );
?>

<!-- MUESTRA LOS COMENTARIOS -->
<h1>Comentarios</h1>
<?php
	$comentarios = ObtenerComentarios( $_GET['perla'] );

	// La variable "$par" se usa para saber si el comentario actual es par o
	// impar. El interés radica en dar a los comentarios pares e impares
	// estilos distintos.
	$par = true;
	while( $comentario = $comentarios->fetch_object() ){
		if( $par )
			echo "<div id=\"c_{$comentario->id}\" class=\"comentario_par\">";
		else
			echo "<div id=\"c_{$comentario->id}\" class=\"comentario_impar\">";

		echo "<p>{$comentario->texto}</p>";
		echo "<span class=\"fecha\">";
		echo $usuarios[$comentario->usuario];
		echo "<br /> subido: {$comentario->fecha_subida} - modificado: {$comentario->fecha_modificacion}";
		echo '</span>';

		if( $comentario->usuario == $_SESSION['id'] ){
			echo "<form action=\"general.php?seccion=mostrar_perla&perla={$perla->id}\" method=\"post\" onsubmit=\"return confirm('Esta seguro/a de querer borrar este comentario?');\" >";
			echo "<input type=\"hidden\" name=\"comentario\" value=\"{$comentario->id}\" />";
			echo '<input type="submit" name="borrar_comentario" value="Borrar comentario" />';
			echo "<input type=\"button\" value=\"Modificar comentario\" onclick=\"ModificarComentario('{$comentario->id}', '{$comentario->texto}')\" />";
			echo '</form>';
		}

		echo '</div>';
		
		// Alterna de un comentario par a uno impar o viceversa.
		$par = !$par;
	}
?>

<!-- FORMULARIO PARA UN NUEVO COMENTARIO -->
<h2>Nuevo comentario</h2>
<form method="post" >
	<label for="texto_comentario">Texto: </label>
	<textarea name="texto_comentario" id="texto_comentario"></textarea>
	<br />
	<input type="submit" value="Subir comentario" />
</form>
