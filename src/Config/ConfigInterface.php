<?php namespace Foothing\Kpi\Config;

interface ConfigInterface {

    public function get($key, $defaultValue = null);
    public function set($key, $value);

}
