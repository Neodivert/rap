<?php
	require_once DIR_CLASES . 'usuario.php';
?>

<h1>Lista de Usuarios</h1>
<div class="galeria">

<?php
	$usuarios = $rap->ObtenerUsuarios();

	foreach( $usuarios as $usuario ){
		$rap->MostrarAvatar( $usuario );
	}
?>

</div>
