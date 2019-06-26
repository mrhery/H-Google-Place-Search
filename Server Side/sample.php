<?php
//example:
include_once("Curl.php");
include_once("GPSearch.php");
try{
    GPSearch::$key = "your-google-api-key";
    
    $x = new GPSearch("bundle_search");
    $x->places = ["johor"];
    $x->types = ["restaurant"];
    $x->execute();
    
    echo $x->getJson();
}catch(Exception $e){
	echo $e->getMessage();
}
    
