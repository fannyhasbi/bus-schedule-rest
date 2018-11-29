<?php

class Api {
  private $koneksi;

  public function __construct(){
    // Mengatasi isu CORS
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: x-requested-with, x-requested-by");
    header("Access-Control-Expose-Headers: Access-Control-Allow-Origin");
    header("Access-Control-Allow-Methods: *");
    header("Content-Type: application/json");
    header( "Access-Control-Allow-Credentials: true");
    // header( "Access-Control-Max-Age: 604800");
    header( "Access-Control-Request-Headers: x-requested-with");
    
    date_default_timezone_set('Asia/Jakarta');

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

  private function response500(){
    Flight::json([
      'status' => 500,
      'message'=> 'Internal Server Error',
      'data'   => null
    ]);
  }

  public function get_bus(){
    $result = mysqli_query($this->koneksi, "SELECT * FROM perusahaan");

    $data = array();
    while($r = mysqli_fetch_assoc($result)){
      $data[] = [
        'id' => (int) $r['id'],
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
        'id'   => (int) $r['id'],
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
        'id'          => (int) $r['id'],
        'id_tujuan'   => (int) $r['id_tujuan'],
        'nama_tujuan' => $r['nama_tujuan'],
        'id_asal'     => (int) $r['id_asal'],
        'nama_asal'   => $r['nama_asal'],
        'berangkat'   => $r['berangkat'],
        'sampai'      => $r['sampai']
      ];
    }

    $this->response($data);
  }

  public function add_departure(){
    $input = Flight::request()->data;

    if(!(
      isset($input->id_perusahaan) &&
      isset($input->id_tujuan) &&
      isset($input->id_asal) &&
      isset($input->berangkat) &&
      isset($input->sampai)
    )){
      $this->response400();
    }
    else {
      $id_perusahaan = (int) $input->id_perusahaan;
      $id_tujuan     = (int) $input->id_tujuan;
      $id_asal       = (int) $input->id_asal;

      // client time input is HH:mm
      // so it has to be concated by the date
      $berangkat = date('Y-m-d') .' '. $input->berangkat;
      $sampai    = date('Y-m-d') .' '. $input->sampai;

      $query = "INSERT INTO keberangkatan VALUES (null, $id_perusahaan, $id_tujuan, $id_asal, '$berangkat', '$sampai')";
      mysqli_query($this->koneksi, $query) or $this->response500();

      $this->response();
    }
  }

  public function get_arrival(){
    $query = "
      SELECT k.id,
        p.nama AS nama_perusahaan,
        k.id_tujuan,
        (SELECT nama FROM tempat WHERE id = k.id_tujuan) AS nama_tujuan,
        k.id_asal,
        (SELECT nama FROM tempat WHERE id = k.id_asal) AS nama_asal,
        k.berangkat,
        k.datang
      FROM kedatangan k
      INNER JOIN perusahaan p
        ON k.id_perusahaan = p.id
    ";

    $result = mysqli_query($this->koneksi, $query);

    $data = array();
    while($r = mysqli_fetch_assoc($result)){
      $data[] = [
        'id'          => (int) $r['id'],
        'id_tujuan'   => (int) $r['id_tujuan'],
        'nama_tujuan' => $r['nama_tujuan'],
        'id_asal'     => (int) $r['id_asal'],
        'nama_asal'   => $r['nama_asal'],
        'berangkat'   => $r['berangkat'],
        'datang'      => $r['datang']
      ];
    }

    $this->response($data);
  }

  public function add_arrival(){
    $input = Flight::request()->data;

    if(!(
      isset($input->id_perusahaan) &&
      isset($input->id_tujuan) &&
      isset($input->id_asal) &&
      isset($input->berangkat) &&
      isset($input->datang)
    )){
      $this->response400();
    }
    else {
      $id_perusahaan = (int) $input->id_perusahaan;
      $id_tujuan     = (int) $input->id_tujuan;
      $id_asal       = (int) $input->id_asal;

      // client time input is HH:mm
      // so it has to be concated by the date
      $berangkat = date('Y-m-d') .' '. $input->berangkat;
      $datang    = date('Y-m-d') .' '. $input->datang;

      $query = "INSERT INTO kedatangan VALUES (null, $id_perusahaan, $id_tujuan, $id_asal, '$berangkat', '$datang')";
      mysqli_query($this->koneksi, $query) or $this->response500();

      $this->response();
    }
  }
}