<?php
	/*** 
	 general.php
	 Elementos comunes a todas las secciones (contentedores, menus, enlaces a 
	 hojas de estilo, etc).
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

	// La RAP original se "ubica" en Canarias. Usa una zona horaria acorde.
	date_default_timezone_set( 'Europe/London' );

	// Un usuario logueado se identifica por que existe una variable de sesion
	// 'id' con su id. Si no existe dicha variable, echa al usuario a la
	// pantalla de inicio.
	if( !isset( $_SESSION['id'] ) ){
		header( 'Location: index.php' );
		exit();
	}

	// "Requires" necesarios.
	require_once 'php/config/rutas.php';
	require_once DIR_CLASES . 'rap.php';
	require_once DIR_CLASES . 'usuario.php';

	// Obtiene la unica instancia de la clase RAP (singlenton). Si es la primera
	// vez, en RAP::ObtenerInstancia() se cargan los datos de conexion a la BD
	// y se obtienen los nombres de todos los usuarios.
	$rap = RAP::ObtenerInstancia();

	// Obtiene una instancia de la clase Usuario para el id del usuario actual.
	$usuario = new Usuario( $_SESSION['id'] );

	// La seccion actual se encuentra en $_GET['seccion']. Si no hay ninguna 
	// definida, se toma por defecto la sección 'lista_perlas'.
	if( !isset( $_GET['seccion'] ) ){
		$_GET['seccion'] = 'lista_perlas';
	}

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
?>

<!DOCTYPE html>

<html>
	<!-- CABEZA -->
	<head>
		<!-- Titulo y metadatos -->
		<title>RAP: Real Academia de las Perlas - v2</title>
		<meta charset="UTF-8" />
		<!-- Scripts -->
		<script type="text/javascript" src="js/md5.js"></script> 
		<script type="text/javascript" src="js/usuarios.js"></script>
		<!-- Hojas de estilos -->
		<link rel="stylesheet" type="text/css" href="css/general.css" />
		<link rel="stylesheet" type="text/css" href="css/perlas.css" />
		<link rel="stylesheet" type="text/css" href="css/comentarios.css" />
	</head>

	<!-- CUERPO -->
	<body>
		<div id="contenedor">
			<!-- Logo de la RAP -->
			<a href="general.php?seccion=lista_perlas"> 
				<img id="logo_index" width="339" height="179" src="media/logo.png" alt="Logo de la RAP" />
			</a>
			<!-- Menus -->
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
					<li><a href="general.php?seccion=historico">Hist&oacute;rico</a></li>
					<li><a href="general.php?seccion=perfil">Perfil</a></li>
					<li>
						<form action="php/controladores/usuarios.php" method="post" >
							<input type="submit" name="accion" value="Logout" />
						</form>
					</li>
				</ul>
			</div>

			<!-- Para cada seccion lo que cambia es el contenido del div "visor". 
			     Dicho contenido se guarda en un fichero del mismo nombre que la 
				  seccion. -->
			<div id="visor">
				<?php
					// Si se desea mostrar alguna notificacion al usuario
					// (indexada por el valor de $_GET['notificacion']), carga
					// la plantilla para mostrarla.
					if( isset( $_GET['notificacion'] ) ){
						require_once DIR_PLANTILLAS . 'notificacion.php';
					}
					// Carga la seccion a mostrar.
					require_once 'php/secciones/' . $_GET['seccion'] . '.php';
				?>
			</div>
		</div>
	</body>
</html>
