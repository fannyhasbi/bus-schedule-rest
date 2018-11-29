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

  private function response($data = null, $status = 200, $message = "OK"){
    $status = (int) $status;
    $message= (string) $message;

    Flight::json([
      'status' => $status,
      'message'=> $message,
      'data'   => $data
    ]);
  }

  private function response400(){
    Flight::json([
      'status' => 400,
      'message'=> 'Bad Request',
      'data'   => null
    ]);
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

  public function add_bus(){
    $perusahaan = Flight::request()->data->perusahaan;

    if(!(isset($perusahaan) && !empty($perusahaan))){
      $this->response400();
    }
    else {
      $query = "INSERT INTO perusahaan (nama) VALUES ('$perusahaan')";
      $result= mysqli_query($this->koneksi, $query);

      $this->response();
    }
  }
}