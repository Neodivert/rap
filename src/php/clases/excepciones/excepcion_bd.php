<?php
	/*** 
	 excepcion_bd.php
	 Subclase de RuntimeException que se lanza cuando se da un error con la BD.
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


	class ExcepcionBD extends RuntimeException {

		protected $consulta;

		function __construct( $consulta ) 
		{
		    $this->consulta = $property;
		    parent::__construct( "Error de la Base de Datos - Consulta: $consulta" );
		}

	} // Final de la definicion de la clase ExcepcionBD.
?>
