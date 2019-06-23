<?php
//example:
include_once("Curl.php");
include_once("GPSearch.php");
try{
    GPSearch::$key = "AIzaSyC5v_959WshV3Cjs1F4zVaV9B1qZbIdmXU";
    
    $x = new GPSearch("bundle_search");
    $x->places = ["johor"];
    $x->types = ["restaurant"];
    $x->execute();
    
    echo $x->getJson();
}catch(Exception $e){
	echo $e->getMessage();
}
    