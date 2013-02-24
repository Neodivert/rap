<?php
	ini_set("default_charset", "UTF-8");
	session_start();

	require_once 'php/config/rutas.php';
	require_once DIR_CLASES . 'rap.php';
	require_once DIR_CLASES . 'usuario.php';

	$rap = RAP::ObtenerInstancia();
	$bd = BD::ObtenerInstancia();

	// El usuario ya estaba logueado. Salta a php/general.php.
	if( isset( $_SESSION['nombre'] ) ){
		header( 'Location: general.php' );
		exit();
	}

	// El usuario intenta loguearse.
	if( isset( $_POST['nombre'] ) ){
		$usuario = new Usuario;
		if( !$usuario->Logear( $_POST['nombre'], $_POST['contrasenna'] ) ){
			$_SESSION['nombre'] = $_POST['nombre'];
			$_SESSION['id'] = $usuario->ObtenerId();
			header( 'Location: general.php' );
			exit();
		}
	}
?>

<!DOCTYPE html>

<html>
	<!-- CABEZA -->
	<head>
		<title>RAP: Real Academia de las Perlas - v2</title>
		<meta charset="UTF-8" />
		<link rel="stylesheet" type="text/css" href="css/general.css" />
		<link rel="stylesheet" type="text/css" href="css/login.css" />
		<script type="text/javascript" src="js/md5.js"></script> 
		<script type="text/javascript" src="js/usuarios.js"></script> 
	</head>

	<!-- CUERPO - FORMULARIO DE LOGIN -->
	<body>
		<img id="logo_index" width="339" height="179" src="media/logo.png" />
		<div id="panel_login">
			<h2>Login:</h2>
			<form id="form_login" action="index.php" method="post" onSubmit="return ValidarLogin();" >
					<p>Nombre: <input type="text" id="nombre" name="nombre" /></p>
					<p>Contrase&ntilde;a: <input type="password" id="contrasenna" name="contrasenna" /></p>
					<input type="submit" value="login" />
			</form>
		</div>
	</body>
</html>
