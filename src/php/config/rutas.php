<?php
	/*** 
	 rutas.php
	 Constantes con las rutas a las diferentes carpetas de la RAP.
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

	define("DIR_WEB", $_SERVER['DOCUMENT_ROOT'] . '/rap/' ); 
	define("DIR_GLOBAL_LIB", $_SERVER['DOCUMENT_ROOT'] . '/lib/' );
	define("DIR_CONFIG", DIR_WEB . 'php/config/' );
	define("DIR_CONTROLADORES", DIR_WEB . 'php/controladores/' );
	define("DIR_LIB", DIR_WEB . 'php/lib/' );
	define("DIR_CLASES", DIR_WEB . 'php/clases/' );
	define("DIR_PLANTILLAS", DIR_WEB . 'php/plantillas/' );
?>
