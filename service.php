<?php
/**
 * PARA CONSULTAR LA API LA SOLICITUD LA DEBES SOLICITAR DE LA SIGUIENTE FORMA
 * service.php/[version]/[servicio]/[recurso]
 * PUEDES UTILIZAR CUALQUIERA DE LOS METODOS, TODO LO QUE UTILICES DESPUÉS DEL SERVICIO
 * SE ENVIARÁ COMO PARTE DE LOS DATOS QUE SE PUEDEN UTILIZAR EN EL MÉTODO
 * 
 * EJEMPLO: SIN DEFINIR EL RECURSO
 * http://localhost/api/service.php/v1/crypt/
 * EN ESTE EJEMPLO DEPENDIENDO DEL MÉTODO VA A EJECUTAR UNO U OTRA FUNCIÓN
 * 
 * EJEMPLO: DEFINIENDO EL RECURSO
 * http://localhost/api/service.php/v1/survey/
 */
header('Content-Type: application/json');
error_reporting(0);
require('tools.php');
$nf = new Infinity;
$security['res'] = 'ok';
if ($security['res'] === 'ok') {
  $query = strstr($_SERVER["REQUEST_URI"], 'service.php');
  if (preg_match_all('/\/([^\/]+)/', $query, $matches)) {
    $version = $matches[1][0];
    $service = $matches[1][1];
    $request = $matches[1];
    array_splice($request, 0, 2);
    $variables = [
      'type' => strtoupper($_SERVER['REQUEST_METHOD']),
      'request' => $request,
      'data' => file_get_contents('php://input')
    ];
    switch ($version) {
      case 'v1':
        switch ($service) {
          case 'crypt':
            require 'crypt/crypt.v1.php';
            $crypt = new Crypt($variables);
            $nf->$responce = $crypt->$responce;
          break;
          case 'survey':
            require 'survey/survey.v1.php';
            $crypt = new Survey($variables);
            $nf->$responce = $crypt->$responce;
          break;
          /*
            EN ESTA SECCIÓN SE PONEN TODOS LOS REDIRECCIONAMIENTOS A LAS DIFERENTES CLASES QUE PROCESARÁN LA INFORMACIÓN
          */
          default:
            $nf->responce('error', 501, $service, 'servicio no identificado');
          break;
        }
      break;
      default:
        $nf->responce('error', 501, $version, 'no se tiene disponible la ' . $version);
      break;
    }
  } else {
    $nf->responce('error', 404, $query, 'api rest no reconocida');
  }
} else {
  $nf->responce('error', 401, $security['res'], 'usuario no autorizado');
}
http_response_code($nf->$responce['code']);
echo json_encode($nf->$response);
