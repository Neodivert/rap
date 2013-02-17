<?php
	//Lista de perlas. Las perlas se muestran por categorías y páginas

	// Las perlas se muestran por categorías. Categoria por defecto: 0 
	// (cualquier categoría).
	if( !isset( $_GET['categoria'] ) ){
		$_GET['categoria'] = 0;
	}

	// Las perlas se muestran por páginas. Página por defecto: 0.
	if( !isset( $_GET['pagina'] ) ){
		$_GET['pagina'] = 0;
	}
    
    // Las perlas se pueden mostrar por participantes. Participante por 
    // defecto: 0 (cualquier participante).
    if( !isset( $_GET['participante'] ) ){
		$_GET['participante'] = 0;
	}

    if( !isset( $_GET['contenido_informatico'] ) ){
		$_GET['contenido_informatico'] = 1;
	 }

    if( !isset( $_GET['humor_negro'] ) ){
		$_GET['humor_negro'] = 1;
	}

    if( !isset( $_GET['palabras'] ) ){
		$_GET['palabras'] = null;
	}
?>

<!-- TÍTULO -->
<h1>Lista de Perlas</h1>

<!-- La barra de búsqueda consiste en un formulario con un único select para
     elegir la categoría de perlas que se desea ver. -->
<div id="barra_busqueda" class="barra">
	<form id="form_busqueda" method="get">
        <h2>Buscar perlas</h2>
		<label for="categoria">Categor&iacute;a: </label>
  		<select name="categoria" id="categoria" >
		<?php
			$categorias = ObtenerCategorias();
			$total_perlas = 0;
		
			// Bucle que itera a lo largo de las categorías y va creando las
			// opciones del select.
			while( $categoria = $categorias->fetch_object() ){
				echo "<option ";
				if( $categoria->id == $_GET['categoria'] ){
					// Si la categoría coincide con la actual, muestra esta 
					// opción como preseleccionada.
					echo 'selected="selected" ';
				}
				//echo "onclick=\"CambiarCategoria('{$categoria->id}')\" >{$categoria->nombre} ({$categoria->num_perlas})</option>";
                echo "value=\"{$categoria->id}\" >{$categoria->nombre} ({$categoria->num_perlas})</option>";

				// Cada categoría en la BD tiene guardado el número de perlas
				// de la misma para evitar consultar la BD y contar. El total
				// de perlas, sin embargo, se va calculando a medida que se 
				// recuperan las categorías de la BD.
				$total_perlas += $categoria->num_perlas;
			}

			// Muestra una última opción 'Cualquiera' (categoría) en el select.
			echo "<option ";
			if( $_GET['categoria'] == 0 ){
				echo 'selected="selected" ';
			}
			echo "value=\"0\">Cualquiera ($total_perlas)</option>";
		?>
		</select>
	

	<label for="participante">Participante</label>
        <select name="participante" id="participante">
        <?php
            $usuarios = ObtenerUsuarios();
            while( $usuario = $usuarios->fetch_object() ){
                echo '<option ';
                if( $usuario->id == $_GET['participante'] ){
					// Si la categoría coincide con la actual, muestra esta 
					// opción como preseleccionada.
					echo 'selected="selected" ';
				}
                echo "value=\"{$usuario->id}\">";
                echo $usuario->nombre;
                echo '</option>';
            }

            // Muestra una última opción 'Cualquiera' (usuario) en el select.
			echo "<option ";
			if( $_GET['participante'] == 0 ){
				echo 'selected="selected" ';
			}
			echo "value=\"0\">Cualquiera</option>";
        ?>
        </select>

		<?php
			echo '<br /><input type="checkbox" name="contenido_informatico" value="0" ';
			
			if( $_GET['contenido_informatico'] == 0 ){
				echo 'checked';
			}
			echo '/>NO quiero perlas con "humor inform&aacute;tico"';
			echo '<br /><input type="checkbox" name="humor_negro" value="1" ';
			if( $_GET['humor_negro'] == 0 ){
				echo 'checked';
			}
			echo '/>NO quiero perlas con humor negro';
			
		?>
		<br />
        <input type="submit" value="Buscar Perlas" />
		</form>
</div>

<?php 
	// Obtiene los nombres de los usuarios y los mete en un array.
	$rUsuarios = ObtenerUsuarios();
	$usuarios = array();
	while( $rUsuario = $rUsuarios->fetch_object() ){
		$usuarios[$rUsuario->id] = $rUsuario->nombre;
	}

	// Obtiene los nombres de las categorias y las mete en un array.
	$rCategorias = ObtenerCategorias();
	$categorias = array();
	while( $rCategoria = $rCategorias->fetch_object() ){
		$categorias[$rCategoria->id] = $rCategoria->nombre;
	}

	// Genera un "libro" (mostrar las perlas por páginas) y muestra
	// la página seleccionada.
   // function ObtenerPerlas( $categoria = 0, $participante = 0, $contenido_informatico = 1, $humor_negro = 1, $palabras = null, $offset = 0, $n = 0 )

	//$rt = mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()")); // Total de registros
	GenerarLibro( $_GET['pagina'], array( 'ObtenerPerlas', $_GET['categoria'], $_GET['participante'], $_GET['contenido_informatico'], $_GET['humor_negro'], $_GET['palabras'] ), array( 'MostrarPerla', $usuarios, $categorias ) );

	// Libera los recursos.
	$rUsuarios->close();
	$rCategorias->close();
?> 

