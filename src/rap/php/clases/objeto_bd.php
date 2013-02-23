<?php
	/* Info:
	Clase abstracta que representa un ente que puede leerse y/o escribirse
	en la BD. 
	/*
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
	*/

	require_once 'bd.php';

	abstract class ObjetoBD {
		protected $info;

		public function EstablecerAtributo( $atributo, $valor )
		{
			return $this->info[$atributo];
		}

		public function OBtenerAtributo( $atributo, $valor )
		{
			$this->info[$atributo] = $valor;
		}

		public function CargarDatos( $info )
		{
			$this->info = $info;
		}

		abstract public function InsertarBD( $bd );
	} // Final de la definicion de la clase BD.
?>
