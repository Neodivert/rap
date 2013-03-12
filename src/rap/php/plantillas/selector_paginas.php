<?php $get = $_GET; ?>
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
