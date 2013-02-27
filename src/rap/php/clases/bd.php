<?php
	/* Info:
	Clase singlenton para el manejo de la base de datos.
	Fuente: 
	http://www.cristalab.com/tutoriales/crear-e-implementar-el-patron-de-diseno-singleton-en-php-c256l/
	// TODO: Seguir el enlace para convertir la BD en una verdadera singlenton.
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

	require_once DIR_CONFIG . 'bd.php';

	class BD {
   	private static $instancia;

		private $host;
		private $usuario;
		private $contrasenna;
		private $bd;

		// Filas encontradas en la ultima busqueda.
		private $numFilas;

		// Constructor privado.
   	private function __construct(){}

		// Obtiene la instancia unica.
   	public static function ObtenerInstancia()
		{
      	if( !self::$instancia instanceof self ){
				self::$instancia = new self;
			}
			return self::$instancia;
   	}

		// Configura los datos de conexion a la BD.
		public function Configurar( $host, $usuario, $contrasenna, $bd )
		{
			$this->host = $host;
			$this->usuario = $usuario;
			$this->contrasenna = $contrasenna;
			$this->bd = $bd;
		}

		// Conecta a la BD y devuelve el objeto de la conexion (mysqli).
		public function Conectar()
		{
			$bd = new mysqli( $this->host, $this->usuario, $this->contrasenna, 
									$this->bd );

			if( $bd->connect_errno ){
				die( "Error conectando a BD (".$bd->connect_errno.") - ".$bd->connect_error );
			}

			if( !$bd->set_charset( 'utf8' ) ){
				die( $bd->error );
			}

			return $bd;
		}

		// Lanza la consulta '$consulta' a la BD.
		public function Consultar( $consulta )
		{
			// Conecta a la BD.
			$bd = $this->Conectar();

			// Lanza la consulta a la BD.
			$res = $bd->query( $consulta ) 
				or die( "Error con consulta [$consulta]: {$bd->error}" );

			if( strpos($consulta, 'SQL_CALC_FOUND_ROWS') !== false ){
				$res1 = $bd->query( 'SELECT FOUND_ROWS()' );
				$this->numFilas = $res1->fetch_row();
				$this->numFilas = $this->numFilas[0];
				//die( "numFilas == " . print_r( $this->numFilas ) );
			}

			// Si la consulta es de tipo INSERT y fue bien, devuelve el id
			// del objeto insertado.
			if( (strpos($consulta, 'INSERT') !== false) && ($res == true) ){
				$res = $bd->insert_id;
			}
		
			// Cierra la conexion y devuelve el resultado.
			$bd->close();
			return $res;
		}
		
		// Devuelve el numero de filas encontradas en la ultima consulta SELECT.
		// Fuente: http://quenerapu.com/mysql/me-encanta-select-found_rows/
		public function ObtenerNumFilasEncontradas()
		{
			return $this->numFilas;
		}

	} // Final de la definicion de la clase BD.
?>
