# Change Log
23 June 2019 - Only works with JQuery, only works with `bundle_search` 

# H-Google-Place-Search
Search places in list of cities/district/area from Google Place API (place search, place detail, place image) in a single call.
Google Place Search (GPSearch) API Integration for JQuery

GPSearch is a small Javascript API to search lists of places filtered 
by types & radius. This API will return results in Object listing with
seperated places response data. This API also compatible for JQuery or
non-JQuery client library. 

## Notice:
As of Google search place not support search by country name. If put
counrty name in places list, some expected results will not be shown
(as no data for country name search). For our recommendation, put key
place name (i.e. district name, city name etc.) as places list search.

## Basic Usage:
GPSearch(obj)
```
obj-> {
	key: "Google-Place-API-Key",
	search: "search type",
	radius: 10000
	//setting please refer below documentation
}
```

## Search Setting
1. Radius (radius) -> int:
  If radius is not set, it will use the default value of 10000.
  (Radius is in meters unit)

2. Places (places) -> array:
  Put list of places in an array of strings. Make sure do not
  put country name as places or it will return empty for that 
  response results.

3. Types (type) -> array:
  This values must use types provided by google. Visit links
  below for all list af types available:

  https://developers.google.com/places/web-service/supported_types
		
## RPC Call to Server
Direct request from Javascript to Google API gives you some error/warning message and break the your program (due to Cross Origin Request issue). So, this JS API will request from a local server RPC which is will this RPC will call to Google API service.
`Javascript GPSearch->RPC->Google API`

You can try it with `rpc.php` which is a simple RPC call from PHP to Google API. This `rpc.php` use CURL (you can find CURL class in `Curl.php`).


*This API seperated into two type of search:*

1. Bundle Search (bundle_search):
	This search will search all listed places with the same search 
	setting (types & radius).
	
	Example:
```
	GPlaceSearch({
		key		: "Google-Place-API-Key",
		search	: "bundle_search",
		places	: [
			"johor bahru", "kuala lumpur"
		],
		types	: [
			"restaurant", "airport", "zoo"
		]
		radius	: 10000
	});
```
	
2. Specific Search (specific_search):
	This search will search with individuals search setting that has been
	set for specified places.
	
	Example:
```
	GPlaceSearch({
		key		: "Google-Place-API-Key",
		search	: "bundle_search",
		places	: [
			{
				"keyword"	: "johor bahru",
				"radius"	: 5000,
				"types"		: [
					"restaurant"
				]
			},
			{
				"keyword"	: "kuala lumpur",
				"radius"	: 10000,
				"types"		: [
					"restaurant", "gym"
				]
			},
			{
				"keyword"	: "penang",
				"radius"	: 15000,
				"types"		: [
					"taxi_stand", "travel_agency"
				]
			}
		]
	});
```

## Sample Response
```
{
	"status"	: "success" //error if fail,
	"data"	: [
		{
			"place"		: "johor bahru",
			"radius"	: 5000
			"response"	:  [
				{
					"name"		: "restaurant"
					"results"	: [
						{
							"id"		: "google-id",
							"place_id"	: "google-place-id",
							"name"		: "Restaurant Number One",
							"address"	: "Address goes here",
							"geometry"	: {
								"latitude"	: 1.125452,
								"longitude"	: 103.628389
							},
							"rating"	: 3.5,
							"images"	: [
								"google-place-image-url"
							],
							"details":{
								//Google Place Detail API results
								//Go to link below for reference:
								//https://developers.google.com/places/web-service/details
							}
						}
					]
				}
			]
		}
	]
}
```
