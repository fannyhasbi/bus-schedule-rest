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
    $result = mysqli_query($this->koneksi, "SELECT * FROM perusahaan");

    $data = array();
    while($r = mysqli_fetch_assoc($result)){
      $data[] = [
        'id' => $r['id'],
        'nama' => $r['nama']
      ];
    }

    $this->response($data);
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

  public function get_place(){
    $result = mysqli_query($this->koneksi, "SELECT * FROM tempat");

    $data = array();
    while($r = mysqli_fetch_assoc($result)){
      $data[] = [
        'id'   => $r['id'],
        'nama' => $r['nama']
      ];
    }

    $this->response($data);
  }

  public function get_departure(){
    $query = "
      SELECT k.id,
        p.nama AS nama_perusahaan,
        k.id_tujuan,
        (SELECT nama FROM tempat WHERE id = k.id_tujuan) AS nama_tujuan,
        k.id_asal,
        (SELECT nama FROM tempat WHERE id = k.id_asal) AS nama_asal,
        k.berangkat,
        k.sampai
      FROM keberangkatan k
      INNER JOIN perusahaan p
        ON k.id_perusahaan = p.id
    ";

    $result = mysqli_query($this->koneksi, $query);

    $data = array();
    while($r = mysqli_fetch_assoc($result)){
      $data[] = [
        'id'          => $r['id'],
        'id_tujuan'   => $r['id_tujuan'],
        'nama_tujuan' => $r['nama_tujuan'],
        'id_asal'     => $r['id_asal'],
        'nama_asal'   => $r['nama_asal'],
        'berangkat'   => $r['berangkat'],
        'sampai'      => $r['sampai']
      ];
    }

    $this->response($data);
  }
}