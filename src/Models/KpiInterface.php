<?php namespace Foothing\Kpi\Models;

interface KpiInterface {

    public function getId();
    public function getName();
    public function getFormula();
    public function getThresholds();

    // @TODO move in trait?
    public function quantizeValue($value);

}
