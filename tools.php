<?php
class Infinity {
  public $responce = [
    'res' => 'ok',
    'code' => 200,
    'request' => null,
    'message' => null,
    'data' => null,
  ];
  // ESTRUCTURA LAS RESPUESTAS DEL SERVIDOR
  public function responce($res = 'ok', $code = 200, $request = null, $message = null, $data = null) {
    $this->$responce = [
      'res' => $res,
      'code' => $code,
      'request' => $request,
      'message' => $message,
      'data' => $data,
    ];
  }
}