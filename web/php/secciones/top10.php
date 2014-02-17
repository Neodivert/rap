<?php
	/***
	 top10.php
	 Seccion que muestra las 10 perlas mejor votadas ordenadas por notas 
	 (de la mayor a la menor).
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
	
	// Se hace uso de la clase Perla.
	require_once DIR_CLASES . 'perla.php';

	// Titulo de la seccion.	
	echo '<h1>TOP 10 DE PERLAS</h1>';

	// Obtiene las 10 perlas mejor votadas.
	$perlas = $rap->ObtenerTop10Perlas();

	// Muestra el top 10 de perlas mejor votadas.
	foreach( $perlas as $perla ){
		$modificable = false;
		require DIR_PLANTILLAS . 'perla.php';
	}
?>
