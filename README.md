#API_REST_con_PHP
####Aquí encontrarás los archivos que utilicé para implementar los servicios REST, guiándome del curso de Platzi.
#### Features
- El archivo server.php hace el papel de la base de datos donde encontrarás los recursos que se pueden visualizar, modificar, agregar recursos nuevos y eliminar los recursos existentes; también aporta la lógica para implementar los servicios REST.
- El archivo generate_hash.php se debe llamar en caso de que se requiera usar la autenticación vía HMAC
- El archivo auth_server.php se usa para la autenticaión vía access tokens.
- El archivo client.php nos sirve para ver los códigos de error del lado del cliente
- l archivo router.php es el servidor que vamos a levantar, está directamente enlazado con el archivo server.php y su función es permitir el funcionamiento con URL's "amigables"
- El archivo index.html es el punto de entrada, y nos permite ver la interfaz en el navegador.
- El archivo xkcd.php es un script que nos permite obtener las URL's de las imágenes que le digamos en la página xkcd.com.

😎👍
