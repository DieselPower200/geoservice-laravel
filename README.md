# sypexgeo-laravel
GeoIP Laravel 5.5 service

For the current moment SyperGeo service implemented only: https://sypexgeo.net

Plaese follow next steps for install:

**1) add dependence:**
```
composer require adiesel82/sypexgeo-laravel
```

**2) add next items into *config/app.php***
```
'providers' => [
    ADiesel82\GeoService\GeoServiceProvider::class,
]
 
'aliases' => [
    'Geo' => ADiesel82\GeoService\GeoServiceFacade::class,
]
```

**3) publish config with artisan:**
```
php artisan vendor:publish
```
Type 0 to publish all or a digit near *ADiesel82\GeoService\GeoServiceProvider*
```
[8 ] Provider: ADiesel82\GeoService\GeoServiceProvider
```
It is *8* in example below and hit enter.

As result you can find *geo.php* in the config folder.


For the current moment SyperGeo service supported only.

**Ready**

###Usage example:
~~~php
$result = \Geo::get(\request()->ip());
dd($result);
~~~
As result:
~~~php
{#129 ▼
  +"city": {#128 ▼
    +"id": 524901
    +"lat": 55.75222
    +"lon": 37.61556
    +"name_ru": "Москва"
    +"name_en": "Moscow"
  }
  +"country": {#130 ▼
    +"id": 185
    +"iso": "RU"
  }
}
~~~

###Auto update database with composer
~~~
"post-install-cmd": [
  "ADiesel82\\GeoService\\ComposerScripts::postInstall"
],
"post-update-cmd": [
  "ADiesel82\\GeoService\\ComposerScripts::postUpdate"
],
~~~
