<?php
	// Sección para subir una perla (o actualizar una existente).
	//die( phpinfo() );
	//session_start();

	require_once 'php/config/rutas.php';
	require_once DIR_CLASES . 'usuario.php';

	$perla = new Perla;
	if( isset( $_POST[ 'titulo_perla' ] ) ){
		die( print_r( $_POST ) );
		// El usuario ha enviado un formulario con una nueva perla. Rellena
		// la perla.

		// Titulo.
		$perla->EstablecerTitulo( $_POST[ 'titulo_perla' ] );

		// Texto.
		$perla->EstablecerTexto( $_POST[ 'texto_perla' ] );

		// Imagen.
		if( $_FILES['imagen']['error'] != UPLOAD_ERR_NO_FILE ||
			(
				isset( $_POST['modificar'] )
				&& !isset( $_POST['borrar_imagen'] )
				&& file_exists( 'media/perlas/' . $_POST['modificar'] )
			)
		){
			$perla->EstablecerPerlaVisual( true );
		}else{
			$perla->EstablecerPerlaVisual( false );
		}

		// Nueva categoria. TODO: Ver como manejaba lo de las nuevas categorias.
		// $perla->EstablecerCategoria( $_POST['nueva_categoria'] );
		
		// Fecha de subida.
		$perla->EstablecerFecha( date("Y/m/d") );

		// Fecha.
		if( $_POST['fecha_perla'] != '' ){
			$perla->EstablecerFecha( "<i>{$_POST['fecha_perla']}</i>" );
		}else{
			$perla->EstablecerFecha( '<i>No especificada</i>' );
		}
		
		// Contenido informatico.
		$perla->EstablecerContenidoInformatico( isset( $_POST['contenido_informatico'] ) );

		// Humor negro.
		$perla->EstablecerHumorNegro( isset( $_POST['humor_negro'] ) );

		// Imagen.
		//$perla['imagen'] = false; // TODO: ?

		// Categoria.
		$perla->EstablecerCategoria( $_POST['categoria_perla'] );

		// Participantes.
		$perla->EstablecerParticipantes( $_POST['participantes'] );

		// Subidor. Se que suena mal, pero queria dejarlo todo en Español.
		$perla->EstablecerSubidor( $_SESSION['id'] );

		// La estructura "$perla" está completa. Ahora diferenciamos entre dos
		// casos, según si estamos subiendo una perla nueva o actualizando una
		// existente.
		if( !isset( $_POST['modificar'] ) ){
			// Vamos a insertar una perla nueva.
			try{
				//InsertarPerla( $perla );
				//SumarPerla( $perla['categoria'] );
				die( 'Perla subida correctamente' );
			}catch( Exception $e ){
				die( $e->getMessage() );
			}
		}else{
			// Estamos actualizando una perla existente. En $_POST['modificar']
			// tenemos la id de la perla en cuestión.
			//$res = ActualizarPerla( $_POST['modificar'], $perla, isset( $_POST['borrar_imagen'] ) );
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
			// El usuario quiere modificar una perla existente. La variable
			// $_GET['modificar'] contiene La id de la perla en cuestión.
			$perla->CargarDesdeBD( $_GET['modificar'], BD::ObtenerInstancia() );
		}else{
			// El usuario va a subir una perla nueva. Rellena sus campos con
			// los valores por defecto.
			$perla->EstablecerTitulo( '' );
			$perla->EstablecerTexto( '' );
			$perla->EstablecerFecha( '' );

			$perla->EstablecerContenidoInformatico( false );
			$perla->EstablecerHumorNegro( false );
			$perla->EstablecerPerlaVisual( false );
		
			$perla->EstablecerEtiquetas( '' );

			$perla->EstablecerParticipantes( "{$_SESSION['id']}" );
		}
	}
	//die( 'Categoria: ' . $perla->ObtenerCategoria() );
?>



<!-- TÍTULO -->
<h1>Subir perla</h1>

<!-- FORMULARIO PARA SUBIR/MODIFICAR UNA PERLA -->
<form action="php/controladores/perlas.php" method="post" enctype="multipart/form-data">

	<!-- Id de la perla (solo cuando se trata de una actualizacion) -->
	<?php
		if( $perla->ObtenerId() ){
			echo "<input type=\"hidden\" name=\"id\" value=\"{$perla->ObtenerId()}\" />";
		}
	?>


	<!-- ¿Título de la perla? (campo de texto) -->
	<h2>T&iacute;tulo:</h2>
	<p>
	<?php
		echo "<input type=\"text\" name=\"titulo\" id=\"titulo\" value=\"{$perla->ObtenerTitulo()}\" required />";
	?>
	</p>

	<?php /*
	<!-- Imagen (solo perlas visuales) -->
	<h2>Imagen (s&oacute;lo perlas visuales)</h2>
	<?php
		if( $perla->ObtenerPerlaVisual() ){
			echo "<img src=\"../datos/img/perlas/{$_GET['modificar']}\" width=\"100%\" >";
			echo '<input type="checkbox" name="borrar_imagen" value="" />Borrar imagen<br />';
		}
	?>
	<label for="imagen">Cargar imagen: </label>
	<input type="file" name="imagen" id="imagen" />
	<!-- <a href="Javascript:void(0)" onclick="VaciarElemento('imagen')">Resetear campo de fichero</a> --> */ ?>

	<!-- ¿Texto de la perla? (textarea) --> 
	<h2>Texto: </h2>
	<textarea name="texto" id="texto"><?php echo $perla->ObtenerTextoPlano(); ?></textarea>

	<!-- Etiquetas de la perla -->
	<h2>Etiquetas: </h2>
	<label for="etiquetas">Introduce las etiquetas separadas por comas. Por ejemplo: "pastelillo, g&eacute;minis, sub-woofer, napoleon":</label>
	<?php
		echo "<input type=\"text\" name=\"etiquetas\" id=\"etiquetas\" value=\"{$perla->ObtenerEtiquetasStr()}\" required />";
	?>

	<!-- ¿Fecha de la perla (cuándo ocurrió)? (campo de texto) -->
	<h2>Fecha de la perla (¿cu&aacute;ndo ocurri&oacute;?): </h2>
	<label for="fecha">Fecha de la perla (Si sabes el d&iacute;a concreto, ponlo como dd/mm/aaaa. Si no, pues una frase o lo que sea (p.e. "el año pasado"). Tambi&eacute;n se puede dejar vac&iacute;a: </label>
	<?php
		echo "<input type=\"text\" name=\"fecha\" id=\"fecha\" value=\"{$perla->ObtenerFecha()}\" />";
	?>

	<!-- TODO: Meter lo del contenido informatico y el humor negro (mediante etiquetas) -->
	
	<!-- Conjunto de campos "checkbox" para añadir participantes a la perla -->
	<h2>Participantes en la perla:</h2>
	<fieldset required>
		<?php 
			$usuarios = $rap->ObtenerUsuarios();

			if( !$usuarios ) die( 'Error: no se obtuvieron usuarios de la base de datos' );

			if( isset( $_GET['modificar'] ) ){
				while( $usuario = $usuarios->fetch_object() ){
					if( $usuario->nombre != $_SESSION['nombre'] ){
						echo "<input type=\"checkbox\" name=\"participantes[]\" value=\"{$usuario->id}\" ";
						if( $perla->EsParticipante( $usuario->id ) ){
							echo 'checked';
						}
						echo " />{$usuario->nombre} ({$usuario->id})<br />";
					}
				}
			}else{
				while( $usuario = $usuarios->fetch_object() ){
					if( $usuario->nombre != $_SESSION['nombre'] ){
						echo "<input type=\"checkbox\" name=\"participantes[]\" value=\"{$usuario->id}\" />{$usuario->nombre}<br />";
					}
				}
			}
			$usuarios->close();
		?>
	</fieldset>

	<!--
	<?php
		// Botón de "submit". Se llama a una función javascript que haga 
		// comprobaciones sobre el formulario antes de subir el formulario.
		echo '<input type="button" value="Subir Perla" ';
		echo "onclick=\"SubirPerla('{$_SESSION['id']}')\" ";
		echo '/>';
	?> -->

	<input type="submit" name="accion" value="Subir perla"/>
	
</form>
