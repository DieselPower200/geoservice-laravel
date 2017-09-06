<?php

namespace ADiesel82\GeoService;


class GeoManager
{

    private static $geo;

    public function __construct($config)
    {
        $class = $config['driver'];
        require_once implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'vendor', $class . '.php']);
        $database = implode(DIRECTORY_SEPARATOR, [$config['store_path'], $config[$class]['filename']]);

        self::$geo = new $class($database);
    }

    public function __call($name, $arguments)
    {
        $array = call_user_func_array([self::$geo, $name], $arguments);
        return json_decode(json_encode($array), false);
    }

    public static function __callStatic($name, $arguments)
    {
        $array = call_user_func_array([self::$geo, $name], $arguments);
        return json_decode(json_encode($array), false);
    }


}