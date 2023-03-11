<?php

require_once('./tools.php');
class Crypt extends Infinity {
  private $request;
  private $clave;

  public function __construct($request){
    $this->request = $request;
    $this->clave = $request['request'][0];
    $this->exec();
  }

  public function exec() {
    switch (strtoupper($this->request['type'])) {
      case 'GET':
        $this->decrypt($this->request['data']);
      break;
      case 'POST':
        $this->encrypt($this->request['data']);
      break;
      default:
        $this->responce('error', 400, $this->request['type'], 'este recurso no acepta el metodo ' . $this->request['type']);
      break;
    }
  }

  public function encrypt($objeto) {
    if (!$this->clave) {
      $this->responce(
        'error',
        401,
        $this->clave,
        'no se recibió la clave',
      );
    } else if (!$objeto || $objeto === "") {
      $this->responce(
        'error',
        400,
        $objeto,
        'no se recibió información para encriptar',
      );
    } else {
      if (gettype($objeto) === 'string') {
        $objeto = json_decode($objeto);
      }
      $datos = serialize($objeto);
      $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
      $iv = openssl_random_pseudo_bytes($ivlen);
      $ciphertext_raw = openssl_encrypt($datos, $cipher, $this->clave, $options=OPENSSL_RAW_DATA, $iv);
      $hmac = hash_hmac('sha256', $ciphertext_raw, $this->clave, $as_binary=true);
      $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
      $this->responce(
        'ok',
        200,
        $objeto,
        'la encriptación se realizó con éxito',
        $ciphertext
      );
    }
  }

  public function decrypt($textoEncriptado) {
    if (!$this->clave) {
      $this->responce(
        'error',
        401,
        $this->clave,
        'no se recibió la clave',
      );
    } else if (!$textoEncriptado || $textoEncriptado === "") {
      $this->responce(
        'error',
        400,
        $objeto,
        'no se recibió la información encriptada',
      );
    } else {
      $c = base64_decode($textoEncriptado);
      $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
      $iv = substr($c, 0, $ivlen);
      $hmac = substr($c, $ivlen, $sha2len=32);
      $ciphertext_raw = substr($c, $ivlen+$sha2len);
      $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $this->clave, $options=OPENSSL_RAW_DATA, $iv);
      $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->clave, $as_binary=true);
      if (hash_equals($hmac, $calcmac)) {
        $objeto = unserialize($plaintext);
        $this->responce(
          'ok',
          200,
          $textoEncriptado,
          'decriptado realizado',
          $objeto
        );
      } else {
        $this->responce(
          'error',
          406,
          $textoEncriptado,
          'la clave y la cadena encripatada no corresponden',
        );
      }
    }
  }
}