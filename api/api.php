<?php

class Api {
  public function getBus(){
    $data = array(
      'status' => 200,
      'message'=> 'OK',
      'data' => null
    );

    Flight::json($data);
  }
}