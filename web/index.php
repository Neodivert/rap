<?php
	/*** 
	 index.php
	 Pantalla de inicio / login de la RAP.
	 Copyright (C) Moises J. Bonilla Caraballo 2012 - 2013.
	****
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
	***/
	
	// Permite el uso de variables de sesion.
	session_start();

	// Establece UTF-8 como conjunto de caracteres por defecto. Necesario para
	// la interaccion con la BD.
	ini_set("default_charset", "UTF-8");
	
	// "Requires" necesarios.
	require_once 'php/config/rutas.php';
	require_once DIR_CLASES . 'rap.php';
	require_once DIR_CLASES . 'usuario.php';

	// Obtiene la unica instancia de la clase RAP (singlenton). Si es la primera
	// vez, en RAP::ObtenerInstancia() se cargan los datos de conexion a la BD
	// y se obtienen los nombres de todos los usuarios.
	$rap = RAP::ObtenerInstancia();

	// El usuario ya estaba logueado. Salta a php/general.php.
	if( isset( $_SESSION['id'] ) ){
		header( 'Location: general.php' );
		exit();
	}
?>

<!DOCTYPE html>

<html>
	<!-- CABEZA -->
	<head>
		<!-- Titulo y metadatos -->
		<title>RAP: Real Academia de las Perlas - v2</title>
		<meta charset="UTF-8" />
		<!-- Hojas de estilo -->
		<link rel="stylesheet" type="text/css" href="css/general.css" />
		<link rel="stylesheet" type="text/css" href="css/login.css" />
		<!-- Scripts necesarios -->
		<script type="text/javascript" src="js/md5.js"></script> 
		<script type="text/javascript" src="js/usuarios.js"></script> 
	</head>

	<!-- CUERPO - FORMULARIO DE LOGIN -->
	<body>
		<img id="logo_index" width="339" height="179" src="media/logo.png" />
		<div id="panel_login">
			<h2>Login:</h2>
			<form id="form_login" action="php/controladores/usuarios.php" method="post" onSubmit="return ValidarLogin();" >
					<p>Nombre: <input type="text" id="nombre" name="nombre" required /></p>
					<p>Contrase&ntilde;a: <input type="password" id="contrasenna" name="contrasenna" required /></p>
					<input type="submit" name="accion" value="Entrar" />
			</form>
		</div>
	</body>
</html>
