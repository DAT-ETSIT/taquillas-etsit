<?php
$realm = 'Taquillas DAT';

//user => password
$users = array(
    "nombre.apellido@alumnos.upm.es",
    // Añadir los correos UPM de todos los administradores aquí
);

$mensajeDenegado = "Acceso Denegado";
$mensajeDenegado1 = "Error en servidor";
$mensajeDenegado2 = "No hay user";
$mensajeDenegado3 = "Invalid response";


/***********************************************************************************
/* NO TOCAR DE AQUI PARA ABAJO
/***********************************************************************************/

if (empty($_SERVER['REMOTE_USER']))
    die($mensajeDenegado2);

if (!in_array($_SERVER['REMOTE_USER'], $users))
    die($mensajeDenegado);

// function to parse the http auth header
function http_digest_parse($txt)
{
    // protect against missing data
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));

    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

    foreach ($matches as $m) {
        $data[$m[1]] = $m[3] ? $m[3] : $m[4];
        unset($needed_parts[$m[1]]);
    }

    return $needed_parts ? false : $data;
}
?>
