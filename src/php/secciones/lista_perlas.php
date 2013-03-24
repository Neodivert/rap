<?php
	// Lista de perlas. Las perlas se muestran por categorías y páginas
	/***
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

	// Las perlas se muestran por etiquetas.
	$etiquetas = isset( $_GET['etiquetas'] ) ? $_GET['etiquetas'] : '';

	// Las perlas se muestran por páginas. Página por defecto: 0.
	$pagina_actual = isset( $_GET['pagina'] ) ? $_GET['pagina'] : 0;
    
	// Las perlas se pueden mostrar por participantes. Participante por 
  	// defecto: 0 (cualquier participante).
	$participante = isset( $_GET['participante'] ) ? $_GET['participante'] : 0;

	require_once DIR_CLASES . 'perla.php';
?>

<!-- TÍTULO -->

<h1>Lista de Perlas</h1>

<!--                           Barra de busqueda                            -->
<h2>Buscar perlas</h2>
<div id="barra_busqueda" class="barra">

	<!-- TODO: Completar 
	<input list="browsers">

	<datalist id="browsers">
	  <option value="Internet Explorer">
	  <option value="Firefox">
	  <option value="Chrome">
	  <option value="Opera">
	  <option value="Safari">
	</datalist> -->
	<p>Introduce una palabra o una frase sencilla. Si quieres ver todas las perlas, deja el campo de texto vac&iacute;o y pulsa en "Buscar perlas".</p>
	<form action="general.php" method="get">
		<input list="etiquetas" name="etiquetas">
		<input type="submit" value="Buscar perlas" />
	</form>
</div>


<!--                           Lista de perlas                              -->
<?php 
	if( $etiquetas != '' ){
		echo "<h2>Resultados de la b&uacute;squeda para la etiqueta '$etiquetas'</h2>";
	}else{
		echo "<h2>Todas las perlas</h2>";
	}
	

	$bd = BD::ObtenerInstancia();

	// Obtiene las perlas de la pagina actual.
	$perlas = $rap->ObtenerPerlas( $_SESSION['id'], $etiquetas, $participante, $pagina_actual*5, 5 );

	// Obtiene el numero de perlas.
	$nElementos = $bd->ObtenerNumFilasEncontradas();
	
	// Establece el numero de perlas por cada pagina.
	$nElementosPorPagina = 5;
	
	// Muestra cada perla.
	foreach( $perlas as $perla ){
		require DIR_PLANTILLAS . 'perla.php';
	} // Fin del while que recorre las perlas.
	
	// Crea los enlaces a las otras paginas.
	require DIR_PLANTILLAS . 'selector_paginas.php';
?>
