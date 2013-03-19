<?php
	ini_set("default_charset", "UTF-8");
	// Elementos comunes a todas las secciones una vez el usuario ha iniciado sesión
	session_start();

	if( !isset( $_SESSION['id'] ) ){
		header( 'Location: index.php' );
		exit();
	}

	// Funciones necesarias.
	require_once 'php/config/rutas.php';
	require_once DIR_CLASES . 'usuario.php';
	require_once DIR_CLASES . 'perla.php';
	require_once DIR_CLASES . 'comentario.php';
	require_once DIR_CLASES . 'notificador.php';
	require_once DIR_CLASES . 'rap.php';
	require_once DIR_CONFIG . 'parametros.php';

	

	$rap = RAP::ObtenerInstancia();

	$usuario = Usuario::ObtenerInstancia( $_SESSION['id'] );

	//$usuario = new Usuario( $_SESSION['id'], $_SESSION['nombre'] );

	// El usuario no está logueado. Échalo a la pantalla de login.
	

	// El usuario intenta desconectarse. Destruye la sesión y ve a la pantalla de login.
	if( isset( $_POST['logout'] ) ){
		unset( $_SESSION['nombre'] );
		unset( $_SESSION['id'] );

		header( 'Location: index.php' );
		exit();
	}

	// El usuario quiere ver el perfil.
	/*if( isset( $_POST['perfil'] ) ){
		header( 'Location: general.php?seccion=perfil' );
		exit();
	}*/

	// La seccion actual se encuentra en $_GET['seccion']. Si no hay ninguna 
	// definida, se toma por defecto la sección 'lista_perlas'.
	if( !isset( $_GET['seccion'] ) ){
		$_GET['seccion'] = 'lista_perlas';
	}

	date_default_timezone_set( 'Europe/London' );

	// Se guarda la ultima direccion visitada por el usuario para volver a ella
	// cuando se haga algun procesamiento. La direccion no se guarda si la 
	// ultima seccion visitada es "subir_perla"
	if( $_GET['seccion'] != 'subir_perla' ){
		$_SESSION['ultima_dir'] = $_SERVER["REQUEST_URI"];
	}else{
		if( !isset( $_SESSION['ultima_dir'] ) ){
			$_SESSION['ultima_dir'] = '/general.php';
		}
	}

	

	$rap->CargarUsuarios();
?>

<!DOCTYPE html>

<html>
	<!-- CABEZA -->
	<head>
		<title>RAP: Real Academia de las Perlas - v2</title>
		<meta charset="UTF-8" />
		<script type="text/javascript" src="js/md5.js"></script> 
		<script type="text/javascript" src="js/usuarios.js"></script> 
		<script type="text/javascript" src="js/perlas.js"></script> 
		<script type="text/javascript" src="js/utilidades.js"></script> 
		<link rel="stylesheet" type="text/css" href="css/general.css" />
		<link rel="stylesheet" type="text/css" href="css/perlas.css" />
		<link rel="stylesheet" type="text/css" href="css/comentarios.css" />
	</head>

	<!-- CUERPO -->
	<body>
		<div id="contenedor">
			<a href="general.php?seccion=lista_perlas"> 
				<img id="logo_index" width="339" height="179" src="media/logo.png" alt="Logo de la RAP" />
			</a>
			<div id="contenedor_menus">
				<ul id="menu_perlas" class="menu">
					<li class="titulo_submenu">Perlas</li>
					<li><a href="general.php?seccion=lista_perlas">Lista de Perlas</a></li>
					<li><a href="general.php?seccion=top10">Top 10</a></li>
					<li><a href="general.php?seccion=subir_perla">Subir Perla</a></li>
					
				</ul>
				<ul id="menu_usuarios" class="menu">
					<li class="titulo_submenu">Usuarios</li>
					<li><a href="general.php?seccion=lista_usuarios">Lista de usuarios</a></li>
					<li><a href="general.php?seccion=ranking_raper@s">Ranking de raper@s</a></li>
				</ul>
				<ul id="menu_usuario" class="menu">
					<li class="titulo_submenu"><?php echo $usuario->ObtenerNombre(); ?></li>
					<li><a href="general.php?seccion=notificaciones">Notificaciones</a></li>
					<li><a href="general.php?seccion=perfil">Perfil</a></li>
					<li>
						<form action="general.php" method="post" >
							<input type="submit" name="logout" value="Logout" />
						</form>
					</li>
				</ul>
			</div>

			<!-- Para cada sección lo que cambia es el contenido del div "visor". 
			     Dicho contenido se guarda en un fichero del mismo nombre de la sección. -->
			<div id="visor">
				<?php
					if( isset( $_GET['notificacion'] ) ){
						require_once DIR_PLANTILLAS . 'notificacion.php';
					}
					require_once 'php/secciones/' . $_GET['seccion'] . '.php';
				?>
			</div>
		</div>
	</body>
</html>