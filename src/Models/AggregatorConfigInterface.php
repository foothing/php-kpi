<?php namespace Foothing\Kpi\Models;

interface AggregatorConfigInterface {

    public function getAggregatorId();
    public function getAggregatorKpiId();
    public function getAggregatorKpiWeight();

}
