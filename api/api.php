<?php

class Api {
  private $koneksi;

  public function __construct(){
    $host = getenv('BSR_DB_HOST');
    $user = getenv('BSR_DB_USER');
    $pass = getenv('BSR_DB_PASS');
    $db   = getenv('BSR_DB_NAME');

    $this->koneksi = mysqli_connect($host, $user, $pass, $db) or die(mysql_error());
  }

  public function __destroy(){
    mysqli_close($this->koneksi);
  }

  public function get_bus(){
    $data = array(
      'status' => 200,
      'message'=> 'OK'
    );

    $result = mysqli_query($this->koneksi, "SELECT * FROM perusahaan");

    while($r = mysqli_fetch_assoc($result)){
      $data['data'][] = array(
        'id' => $r['id'],
        'nama' => $r['nama']
      );
    }

    Flight::json($data);
  }
}