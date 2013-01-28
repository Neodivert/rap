<?php
	//include_once 'php/funciones/perlas.php';
	echo '<h1>TOP 10 DE PERLAS</h1>';

	// Obtiene los nombres de los usuarios y los mete en un array.
	$rUsuarios = ObtenerUsuarios();
	$usuarios = array();
	while( $rUsuario = $rUsuarios->fetch_object() ){
		$usuarios[$rUsuario->id] = $rUsuario->nombre;
	}

	// Obtiene los nombres de las categorias y las mete en un array.
	$rCategorias = ObtenerCategorias();
	$categorias = array();
	while( $rCategoria = $rCategorias->fetch_object() ){
		$categorias[$rCategoria->id] = $rCategoria->nombre;
	}

	$perlas = ObtenerTop10Perlas();

	while( $perla = $perlas->fetch_assoc() ){
		MostrarPerla( $perla, $usuarios, $categorias );
	}

	$rUsuarios->close();
	$rCategorias->close();
?>
