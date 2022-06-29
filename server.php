<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

/*
// Ésto se llama autenticación HTTP y es para poner una capa de autenticación, si no se pone el user o el pwd correcto, no se mostrará nada,
// y la forma de autenticarse es modificar la URL  de la siguiente manera: curl http://eslover:1234@localhost:8000/books
// éste tipo de autenticación es poco segura porque toda la información de autenticación viaja por la URL (user y password), y es ineficiente
// porque en cada pedido se debe realizar la verificación de que la autenticación es válida.

$user = array_key_exists('PHP_AUTH_USER', $_SERVER) ? $_SERVER['PHP_AUTH_USER'] : '';
$pwd = array_key_exists('PHP_AUTH_PW', $_SERVER) ? $_SERVER['PHP_AUTH_PW'] : '';

if($user !== 'eslover' || $pwd !== '1234') {
    die;
}
*/

/*
// Ésto es autenticación vía HMAC, para que funcione se debe llamar primero el archivo generate_hash.php y se le debe proporcionar el userId
// por ejemplo: php generate_hash.php 1 en este caso el userId es 1; y para poder ver los datos usamos el comando:
// curl http://localhost:8000/books -H 'X-HASH: f8a1409e43ab8461287ad90f85f6b10234b6d26b' -H 'X-UID: 1' -H 'X-TIMESTAMP: 1656024230'

// Verificamos que los encabezados estén en el arreglo de encabezados que recibimos, si no están entonces el usuario no será autenticado
if(!array_key_exists('HTTP_X_HASH', $_SERVER) || !array_key_exists('HTTP_X_TIMESTAMP', $_SERVER) || !array_key_exists('HTTP_X_UID', $_SERVER)) {
    die;
}

list($hash, $uid, $timestamp) = [$_SERVER['HTTP_X_HASH'], $_SERVER['HTTP_X_UID'], $_SERVER['HTTP_X_TIMESTAMP'],];

// Ésta es una clave secreta que solo conoce el servidor y el cliente
$secret = 'Sh!! No se lo cuentes a nadie!';

// Se genera el nuevo hash que se construye concatenando lo que nos pasa el usuario
$newHash = sha1($uid.$timestamp.$secret);

// Se comparan el hash del usuario y el del servidor
if($newHash !== $hash) {

    die;
}
*/

/*
// Ésto es autenticación via access tokens
// Aquí verificamos si el servidor ha recibido un token del cliente
if(!array_key_exists('HTTP_X_TOKEN', $_SERVER)) {
    
    die;
}

// Ésta es la URL donde va a estar escuchando el servidor de autenticación ("auth_server.php").
$url = 'http://localhost:8001';
//$url = 'https://'.$_SERVER['HTTP_HOST'].'/auth';

// Acá estamos inicializando una llamada via curl al servidor de autenticación para validar el token
$ch = curl_init($url);

// Acá le informamos al servidor de autenticación el encabezado (token) que queremos validar, mediante un arreglo de encabezados.
curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
        "X-Token: {$_SERVER['HTTP_X_TOKEN']}",
    ]
    );
    // Acá estamos obteniendo el resultado que nos está devolviendo el servidor
curl_setopt(
    $ch,
    CURLOPT_RETURNTRANSFER,
    true
);

// Acá estamos realizando una llamada
$ret = curl_exec($ch);

if(curl_errno($ch) !=0) {
    die(curl_error($ch));
}

// Acá verificamos si el resultado es true
if($ret !== 'true') {
    http_response_code(403);

    die;
}
*/

// Definimos los recursos disponibles
$allowedResourceTypes = [
    'books',
    'authors',
    'genres',
];

// Validamos que el recurso este disponible
$resourceType = $_GET['resource_type'];

if (!in_array( $resourceType, $allowedResourceTypes ) ) {
    http_response_code(400);
    echo json_encode(
        [
            'error' => "$resourceType is unknown",
        ]
        );
    die;
}

// Defino los recursos
$books = [
    1 => [
        'titulo' => 'Lo que el viento se llevo',
        'id_autor' => 2,
        'id_genero' => 2,
    ],
    2 => [
        'titulo' => 'La Iliada',
        'id_autor' => 1,
        'id_genero' => 1,
    ],
    3 => [
        'titulo' => 'La Odisea',
        'id_autor' => 1,
        'id_genero' => 1,
    ],
];

// Levantamos el id del recurso buscado
$resourceId = array_key_exists('resource_id', $_GET) ? $_GET['resource_id'] : '';
$method = $_SERVER['REQUEST_METHOD'];

// Generamos la respuesta asumiendo que el pedido es correcto
switch (strtoupper($method)) {
    case 'GET':
        if ("books" !== $resourceType) {
            http_response_code(404);
            // Acá estamos mostrando toda la lista de los recursos con el comando: curl http://localhost:8000/books
            echo json_encode(
                [
                    'error' => $resourceType.' not yet implemented :(',
                ]
            );
            die;
        }
        
        if(!empty($resourceId)) {
            if(array_key_exists($resourceId, $books)) {
                echo json_encode($books[$resourceId]);
            } else {
                http_response_code(404);

                echo json_encode(
                    [
                        'error' => 'Book '.$resourceId.' not found :(',
                    ]
                );
            }
        } else {
                echo json_encode($books);
        }

        die;
        break;
        
    case 'POST':
        // Éste sería el comando para crear un recurso nuevo: curl -X 'POST' http://localhost:8000/books -d '{"titulo": "Nuevo libro","id_autor": 1, "id_genero": 2}'
        // Tomamos la entrada tal cual como nos la envian
        $json = file_get_contents('php://input');

        // Transformamos el json recibido a un nuevo elemento del arreglo
        $books[] = json_decode($json);

        // acá estamos imprimiendo solamente el número del id del libro que acabamos de crear
        echo array_keys($books)[count($books) - 1];
        break;
    case 'PUT':
        // Éste sería el comando para modificar un recurso existente: curl -X 'PUT' http://localhost:8000/books/1 -d '{"titulo": "Nuevo libro","id_autor": 1, "id_genero": 2}'
        // Validamos que el recurso buscado exista
        if(!empty($resourceId) && array_key_exists($resourceId, $books)) {
            // Tomamos los datos de entrada
            $json = file_get_contents('php://input');

            // Transformamos el json recibido a un nuevo elemento del arreglo
            $books[$resourceId] = json_decode($json, true);

            // Retornamos la colección modificada en formato json
            echo $resourceId;
        }
        break;
    case 'DELETE':
        // Éste sería el comando para eliminar un recurso: curl -X 'DELETE' http://localhost:8000/books/1
        // Validamos que el recurso exista
        if(!empty($resourceId) && array_key_exists($resourceId, $books)) {
            // Eliminamos el recurso
            unset($books[$resourceId]);
        }
        break;
    default:
    http_response_code(404);
    
    echo json_encode(
        [
            'error' => $method.' not yet implemented :(',
        ]
        );

        break;
}
