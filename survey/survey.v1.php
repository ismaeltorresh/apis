<?php
require_once('./tools.php');
class Survey extends Infinity {
  var $request;
  const SOURCE = array(
    'recurso1',
    'recurso2'
  );

  public function __constructor($request) {
    $this->request = $request;
    $this->exec();
  }

  public function exec() {
    $source = $this->request['request'][0];
    if (in_array($source, self::SOURCE)) {
      switch (strtoupper($this->request['type'])) {
        case 'GET':
          if ($source === 'recurso1') {
            $this->getRecurso1();
          } else if ($source === 'recurso2') {
            $this->getRecurso2();
          }
          $this->get();
        break;
        case 'POST':
        break;
        case 'DELETE':
        break;
        case 'PUT':
        break;
      }
    } else {
      $this->responce(
        'error',
        404,
        $source,
        'el recurso' . $source . ' no se encuentra en el catálogo',
      );
    }
  }
  private function getRecurso1() {
    /**
     * Aquí todo el procesamiento de la información
     */
    $this->responce('ok');
  }
  private function getRecurso2() {
    /**
     * Aquí todo el procesamiento de la información
     */
    $this->responce('ok');
  }
}
