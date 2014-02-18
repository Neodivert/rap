<?php
	/***
	 mostrar_top_etiquetas.php
	 Plantilla que muestra las etiquetas mas populares (las que son 
	 referenciadas en un mayor numero de perlas).
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
?>
<div id="div_etiquetas_mas_populares">
	<h3>Etiquetas m&aacute;s populares</h3>
	<ol>
	<?php
		$etiquetas_mas_populares = $rap->ObtenerEtiquetasMasPopulares( 10 );
		while( $etiqueta = $etiquetas_mas_populares->fetch_object() ){
			echo '<li>';
			if( $mostrar_etiquetas_como_enlaces ){
				echo "<a href=\"general.php?seccion=lista_perlas&etiquetas={$etiqueta->nombre}\" >{$etiqueta->nombre}</a> ({$etiqueta->n} perlas)";
			}else{
				echo "{$etiqueta->nombre} ({$etiqueta->n} perlas)";
			}
			echo '</li>';
		}
	?>
	</ol>
</div>
