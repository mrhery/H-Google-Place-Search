<?php

class GPSearch{
    public static 
        $key = "", 
        $image_max_width = 1200
    ;
    
    private 
        $search_type = [
            "bundle_search", "specific_search"    
        ],
        $type = "",
        $results = []
    ;
    
    public 
        $types = [
            "restaurants"
        ],
        $places = [], 
        $fields = [
            "name", "rating", "formatted_phone_number", "opening_hours", "website", "reviews", "international_phone_number", "photos"
        ],
        $radius = 10000,
        $coords = []
    ;
    
    public function __construct($type = "", $obj = []){
        if(empty(self::$key)){
            throw new Exception('Google API Key is empty. Please set GPSearch::$key = "your-google-api-key"');
        }
        
        if(empty($type)){
            throw new Exception('Search type is not define. Please set search type at the GPSearch($type) constructor.');
        }
        
        if(!in_array($type, $this->search_type)){
            throw new Exception('Search type is not supported. Please set a correct search type at the GPSearch($type) constructor.');
        }
        
        $this->type = $type;
        
    }
    
    public function execute(){
        switch($this->type){
            case "bundle_search":
                
                foreach($this->places as $place){
                    $result = [
                        "name"      => $place,
                        "results"   => []
                    ];
                    $c = $this->getCoordinate($place);
                    $this->coords[] = $c;
                    
                    $result["results"] = $this->listPlaces($c);
                    $this->results[] = $result;
                }
            break;
            
            case "specific_search":
                if(!count($obj)){
                    throw new Exception("Your search object is empty. Please put your list specific search at second parameter in GPSearch() constructor.");
                }
            break;
        }
    }
    
    public function listPlaces($coord = [], $types = "", $radius = ""){
        if(empty($types)){
            $types = $this->types;
        }
        
        if(empty($radius)){
            $radius = $this->radius;
        }
        
        $g = "https://maps.googleapis.com/maps/api/place/nearbysearch/json";
        
        if(is_array($coord)){
            if(isset($coord["latitude"], $coord["longitude"]) && !empty($coord["latitude"]) && !empty($coord["longitude"])){
                $result = [];
                
                foreach($types as $type){
                    $res = [
                        "name"      => $type,
                        "results"   => []
                    ];
                    
                    $c = new Curl(self::queryBuilder($g, [
                        "location"  => $coord["latitude"] . "," . $coord["longitude"],
                        "type"      => $type,
                        "radius"    => $radius
                    ]));
                    
                    $obj = json_decode($c->getResponse());
                    
                    foreach($obj->results as $r){
                        $x = [
                            "place_id"  => $r->place_id,
                            "name"      => $r->name,
                            "address"   => $r->vicinity,
                            "geometry"  => [
                                "latitude"  => $r->geometry->location->lat,
                                "longitude" => $r->geometry->location->lng
                            ],
                            "rating"    => isset($r->rating) ? $r->rating : null,
                            "images"    => [],
                            "details"   => isset($r->place_id) ? $this->placeDetails($r->place_id, $this->fields) : null
                        ];
                        
                        if(isset($x["details"]->photos)){
                            foreach($x["details"]->photos as $photo){
                                $x["images"][] = self::placeImage($photo->photo_reference);
                            }
                        }
                        
                        $res["results"][] = $x;
                    }
                    
                    $result[] = $res;
                }
                
                return $result;
            }else{
                throw new Exception("Coordinate not complete. Make sure to supply 'latitude' and 'longitude' data.");
            }
        }else{
            throw new Exception("Coordinates not found in listing places at listPlaces() method. Example: [name => '', latitude => '', longitude => '']");
        }
    }
    
    public static function placeDetails($place_id = "", $fields = ""){
        if(empty($place_id)){
            throw new Exception("Fail executing placeDetails() method as the ID is empty.");
        }
        
        if(empty($fields)){
            $fields = $this->fields;
        }
        
        $g = "https://maps.googleapis.com/maps/api/place/details/json";
        
        $c = new Curl(self::queryBuilder($g, [
            "placeid"   => $place_id,
            "fields"    => implode(",", $fields)
        ]));
        
        $obj = json_decode($c->getResponse());
        
        return $obj->result;
    }
    
    public static function placeImage($photo_reference = "", $maxwidth = ""){
        if(empty($photo_reference)){
            throw new Exception("Photo reference cannot be empty on placeImage() method.");
        }
        
        if(empty($maxwidth)){
            $maxwidth = self::$image_max_width;
        }
        
        return self::queryBuilder("https://maps.googleapis.com/maps/api/place/photo",
            [
                "maxwidth"          => $maxwidth,
                "photoreference"    => $photo_reference
            ]
        );
    }
    
    public function getCoordinate($place = ""){
        $g = "https://maps.googleapis.com/maps/api/geocode/json";
        
        if(is_array($place)){
            if(!count($place)){
                throw new Exception("Your place list is empty in getCoordinate() method.");
            }
            $result = [];
            
            foreach($place as $p){
                 $c = new Curl($this->queryBuilder($g, [
                    "address"  => $p
                ]));
                
                $obj = json_decode($c->getResponse());
                
                //print_r($obj);
                
                if(isset($obj->results[0]->geometry->location->lng)){
                    $result[] = [
                        "name"      => $place,
                        "latitude"  => $obj->results[0]->geometry->location->lat,
                        "longitude" => $obj->results[0]->geometry->location->lng
                    ];
                }else{
                    $result[] = [
                        "name"      => $place,
                        "latitude"  => "",
                        "longitude" => ""
                    ];
                }
            }
            
            $this->coords = $result;
        }else{
            if(empty($place)){
                throw new Exception("Your place is empty in getCoordinate() method.");
            }
            
            $c = new Curl($this->queryBuilder($g, [
                "address"  => $place
            ]));
            
            $obj = json_decode($c->getResponse());
            
            //print_r($obj);
            
            if(isset($obj->results[0]->geometry->location->lng)){
                return [
                    "name"      => $place,
                    "latitude"  => $obj->results[0]->geometry->location->lat,
                    "longitude" => $obj->results[0]->geometry->location->lng
                ];
            }else{
                return (object)[
                    "name"      => $place,
                    "latitude"  => "",
                    "longitude" => ""
                ];
            }
        }
    }
    
    public static function queryBuilder($url = "", $setting = []){
        if(empty($url)){
            throw new Exception("Query builder cannot accept empty url string in queryBuilder() method.");
        }
        
        $url = $url . "?key=" . GPSearch::$key;
        
        foreach($setting as $key => $value){
            $url .= "&" . $key . "=" . urlencode($value);
        }
        
        return $url;
    }
    
    public function getJson(){
        return json_encode($this->results);
    }
}