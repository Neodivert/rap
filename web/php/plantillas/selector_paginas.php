<?php
	/***
	 selector_paginas.php
	 Plantilla que muestra al usuario una fila de enlaces a las demas paginas
	 (pe. en la lista de perlas).
	 NOTA: esta plantilla requiere la existencia previa de las siguientes 
	 variables:
	  - $nElementos: total de elementos (entre todas las paginas).
	  - $nElementosPorPagina: cantidad de elementos que se quieren mostrar por
		 pagina.
	  - $pagina_actual: pagina actual en la que se encuentra el usuario.
	  variables $nElementos
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

	// Los enlaces a las otras paginas se crean a partir de la url actual.
	// Elimina la variable $_GET['notificacion'] para que no se propage a las
	// otras paginas.
	$get = $_GET;
	if( isset( $get['notificacion'] ) ){
		unset( $get['notificacion'] );
	}
?>

<!-- Selector de paginas -->
<div id="seleccion_paginas">
	<?php
		$nPaginas = $nElementos / $nElementosPorPagina;
	for( $pagina=0; $pagina<$nPaginas; $pagina++ ){
		$get['pagina'] = $pagina;
		
		$getArray = http_build_query( $get );
		if( $pagina != $pagina_actual ){
			echo "<a href=\"" . $_SERVER["PHP_SELF"] . '?' . $getArray . "\" >";
			echo "$pagina</a> ";
		}else{
			echo $pagina . ' ';
		}
	}
	?>
</div>
