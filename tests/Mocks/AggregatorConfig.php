<?php namespace Foothing\Kpi\Tests\Mocks;

use Foothing\Kpi\Models\AggregatorConfigInterface;

class AggregatorConfig implements AggregatorConfigInterface {

    protected $id, $kpi, $weight;

    public function __construct($id, $kpi, $weight) {
        $this->id = $id;
        $this->kpi = $kpi;
        $this->weight = $weight;
    }

    public function getAggregatorId() {
        return $this->id;
    }

    public function getAggregatorKpiId() {
        return $this->kpi;
    }

    public function getAggregatorKpiWeight() {
        return $this->weight;
    }
}
