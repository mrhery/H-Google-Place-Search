
var data;
function GPlaceSearch(obj){
	if(obj.key == undefined){
		console.log("GPlace_Key is empty. Please place GPlace_Key or send key obj with your Google API Key.");
	}else{
		r = 10000;
		data = [];
		switch(obj.search){
			case "bundle_search":
				if(obj.types.length > 0){
					if(obj.radius != undefined){
						r = obj.radius;
					}
					
					if($){
						//Jquery Exists
						obj.places.forEach(function($i){
							console.log("Starting Request for " + $i);
							var robj = {
								place		: $i,
								radius		: r,
								response	: []
							};
							
							console.log("Getting Geo-Information for " + $i);
							$.ajax({
								method		: "POST",
								url			: obj.rpc,
								async		: false,
								cache		: false,
								data		: {
									url			: "https://maps.googleapis.com/maps/api/geocode/json",
									key			: obj.key,
									address		: $i
								},
								dataType	: "json"
							}).done(function(res){
								//console.log(res);
								console.log("Geo-Information form " + $i + " received.");
								
								clong = res.results[0].geometry.location.lng;
								clat = res.results[0].geometry.location.lat;
								robj.geometry = {
									latitude	: clat,
									longitude	: clong
								};
								
								console.log("Fetching Data By Types");
								obj.types.forEach(function($t){
									console.log("== " + $t);
									
									var iobj = {
										name	: $t,
										results	: []
									};
									
									$.ajax({
										method		: "POST",
										url			: obj.rpc,
										async		: false,
										data:{
											url			: "https://maps.googleapis.com/maps/api/place/nearbysearch/json",
											key			: obj.key,
											location	: clat +","+ clong,
											type		: $t,
											radius		: r
										},
										dataType	: "json"
									}).done(function(res){										
										if(res.results.length > 0){
											console.log("Data Received:");
											//console.log(res);
											
											res.results.forEach(function($o){
												console.log("Found " + $o.name + " in " + $i);
												resultx = {
													place_id	: $o.place_id,
													name		: $o.name,
													address		: $o.vincity,
													geometry	: {
														latitude	: $o.geometry.location.lat,
														longitude	: $o.geometry.location.lng
													},
													rating		: $o.rating,
													images		: [],
													details		: {}
												};
												
												$.ajax({
													method	: "POST",
													url		: obj.rpc,
													async	: false,
													data	: {
														url		: "https://maps.googleapis.com/maps/api/place/details/json",
														key		: obj.key,
														placeid	: $o.place_id,
														fields	: obj.fields.toString()
													},
													dataType: "json"
												}).done(function(res){
													resultx.details = res.result;
													
													///*
													if(res.result.photos != undefined){
														console.log("== Getting Images");
														res.result.photos.forEach(function($p){
															
															resultx.images.push("https://maps.googleapis.com/maps/api/place/photo?maxwidth=1200&photoreference=" + $p.photo_reference + "&key=" + obj.key);
														});
														console.log("==== Images added");
													}
													//*/
												});
												
												iobj.results.push(resultx);
												console.log("Data id: " + $o.place_id + " added");
												console.log("+++++");
											});
										}
										robj.response.push(iobj);
										console.log("Data " + $i + " for "+ $t +" created");
									});
								});
								data.push(robj);
								console.log("Information for " + $i + " recorded.");
							}).fail(function(){
								console.log("FAIL");
							});
						});
					}else{
						//Jquery  Not Exists
					}
				}
			break;
			
			case "specific_search":
			
			break;
			
			default:
				console.log("Your search type is not specified. Please choose between bundle_search or specific_search.");
			break;
		}
		
		
	}
	
	console.log(data.length + " data set created");
	
	return data;
}
