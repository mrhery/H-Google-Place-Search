<?php
include_once("Curl.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$url = Input::post("url") . "?";

$acc = ["key", "location", "address", "type", "radius", "placeid", "fields", "maxwidth", "maxheight", "photoreference"];

foreach($_POST as $key => $value){
  if(in_array($key, $acc)){
    if($key != "url"){
      $url .= $key . "=" . urlencode($value) . "&";
    }
  }
}

try{
  $c = new Curl($url);

  echo $c->getResponse();
}catch(Exception $e){
  echo json_encode([
    "status"	=> "error",
    "message"	=> "Fail executing request",
    "data"		=> $e
  ]);
}
