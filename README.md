# H-Google-Place-Search
Search places in list of cities/district/area from Google Place API (place search, place detail, place image) in a single call.

This API developed into two enironment. Server Side (in PHP) and Client Side (in Javascript -> JQuery). You might choose one to use.

Base on my experience, for large datas with hundreds of cities with 5 types (restaurant, airport, hospital etc) will take time so long. So we provide a backend service in PHP, running it in background (or prallel) so it won't break your main application.

This PHP version is good for data digging process, but as of 23 June 2019, this PHP and JS API doesn't suppot for crawling every page yet. It will be implemented in some time in future.
