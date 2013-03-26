<?php
	/***
	 mostrar_usuario.php
	 (Seccion) Perfil publico del usuario con id $_GET['usuario'].
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

	// Obtiene una instancia de la clase Usuario a partir de la id solicitada.
	$rapero = new Usuario( $_GET['usuario'] );
?>

<!-- Titulo de la seccion -->
<h1>Perfil p&uacute;blico de <?php echo $rapero->ObtenerNombre(); ?></h1>

<!-- Cuerpo de la seccion -->

<!-- Avatar del usuario -->
<div class="galeria">
	<?php
		$rap->MostrarAvatar( $rapero->ObtenerId() );
	?>
</div>

<!-- Informaci&oacute;n general -->
<h2>Informaci&oacute;n general</h2>
<ul>
<?php
	// Muestra las fechas de registro y de ultima conexion.
	echo "<li>Fecha de registro: {$rapero->ObtenerFechaRegistro()}</li>";
	echo "<li>Fecha de &uacute;ltima conexi&oacute;n: {$rapero->ObtenerFechaUltimaConexion()}</li>";
?>
</ul>

<!-- Estad&iacute;sticas -->
<h2>Estad&iacute;sticas</h2>
<ul>
<?php
	// Obtiene las estadisticas del usuario como un array de pares 
	// (titulo_estadistica, valor_estadistica).
	$estadisticas = $rapero->ObtenerEstadisticasBD( BD::ObtenerInstancia() );

	// Muestra cada una de las estadisticas.
	foreach( $estadisticas as $estadistica ){
		echo "<li>{$estadistica[0]}: {$estadistica[1]}</li>";
	}
?>
</ul>

<!-- Perlas -->
<h2>Perlas</h2>
<ul>
<?php
	// Muestra un enlace para buscar todas las perlas subidas por el usuario.
	echo "<li><a href=\"general.php?seccion=lista_perlas&subidor={$rapero->ObtenerId()}\">Ver perlas subidas por {$rapero->ObtenerNombre()}</a></li>";
	//TODO: echo "<li><a href=\"general.php?seccion=lista_perlas&subidor={$rapero->ObtenerId()}\">Ver perlas en las que participa {$rapero->ObtenerNombre()}</li></a>";
?>
</ul>
