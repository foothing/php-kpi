<?php namespace Foothing\Kpi\Calculator;

class KpiVariable {

    public $raw;
    public $name;

    public function __construct($name) {
        $this->name = $name;
    }
}
