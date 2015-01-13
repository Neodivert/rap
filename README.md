# RAP - Real Academia de las Perlas

La RAP es una web privada donde un grupo de amigos puede subir sus "perlas" (textos o imágenes): frases célebres, conversaciones legendarias, situaciones surrealistas... .
Este repositorio contiene todo el código necesario para montar una RAP privada y extenderla si se desea.


## Instalación manual (en localhost y en servidor remoto)

- Clonar este repositorio en local
```
git clone git@github.com:Neodivert/rap <directorio>
```

- Acceder al directorio recién instalado.
```
cd <directorio>
```

- Iniciar XAMPP.

- Acceder al panel de control de phpmyadmin.

- Crear una base de datos vacía e importar la estructura desde el fichero "bd/bd-rap.sql".

- Crea un usuario MySQL con permisos INSERT, UPDATE, SELECT y DELETE sobre la base de datos anterior.

- Crear uno o más usuarios en la base de datos anterior (nombre + contraseña).
	- (*) Las contraseñas deben guardarse codificadas en MD5. las contraseñas se pueden codificar en la página siguiente: http://pajhome.org.uk/crypt/md5/

- Abandonar el panel de control de phpmyadmin.
 
- Copiar la carpeta "web" en el servidor web, renombrándola a gusto.

- Modificar el fichero <web>/php/config/bd.php con los datos de conexión de la base de datos y usuario anteriores.

- Modificar la variable "DIR_WEB" el fichero <web>/php/config/rutas.php para que apunte al directorio donde se aloja la RAP.


## Instalación automática (sólo en localhost)

- Clonar este repositorio en local
```
git clone git@github.com:Neodivert/rap <directorio>
```

- Acceder al directorio de instalación.
```
cd <directorio>/install
```

- Ejecutar el script de instalación y seguir instrucciones.
```
sudo ./install_localhost.sh
```

## Notas

- Si se trabaja en local, al intentar acceder a "localhost/phpmyadmin" puede surgir un mensaje de error del tipo "Acceso prohibido! XAMPP nuevo concepto de seguridad: [...]". Este "error" lo solucioné en mi caso siguiendo las indicaciones del siguiente enlace: http://stackoverflow.com/questions/11630412/phpmyadmin-xampp-error
