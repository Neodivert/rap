<?php
	// Elementos comunes a todas las secciones una vez el usuario ha iniciado sesión
	session_start();

	// El usuario no está logueado. Échalo a la pantalla de login.
	if( !isset( $_SESSION['nombre'] ) ){
		header( 'Location: index.php' );
		exit();
	}

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

	// Funciones necesarias.
	require_once 'php/config/rutas.php';
	require_once DIR_LIB . 'usuarios.php';
	require_once DIR_LIB . 'perlas.php';
	require_once DIR_LIB . 'comentarios.php';
	require_once DIR_LIB . 'notificaciones.php';
	require_once DIR_CLASES . 'rap.php';
	require_once DIR_CONFIG . 'parametros.php';

	$rap = RAP::ObtenerInstancia();
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
					<li class="titulo_submenu"><?php echo $_SESSION['nombre']; ?></li>
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
					require_once 'php/secciones/' . $_GET['seccion'] . '.php';
				?>
			</div>
		</div>
	</body>
</html>
