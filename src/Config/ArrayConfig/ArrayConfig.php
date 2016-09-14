<?php namespace Foothing\Kpi\Config\ArrayConfig;

use Foothing\Kpi\Config\ConfigInterface;

class ArrayConfig implements ConfigInterface {

    public function get($key, $defaultValue = null) {
        return isset(self::$config[$key]) ? self::$config[$key] : $defaultValue;
    }

    public function set($key, $value) {
        return self::$config[$key] = $value;
    }

    protected static $config = [

        "datasets.variable.AUTO_RC" => "dataset1",
        "datasets.variable.AUTO_X" => "dataset1",
        "datasets.variable.AUTO_Y" => "dataset2",

    ];
}
