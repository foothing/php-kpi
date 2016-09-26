<?php namespace Foothing\Kpi\Models;

class TransientAggregatorConfig {

    protected $aggregatorId;

    protected $kpis = [];

    public function __construct($aggregatorId) {
        $this->aggregatorId = $aggregatorId;
    }

    public function getAggregatorId() {
        return $this->aggregatorId;
    }

    public function add($kpiId, $weight) {
        $this->kpis[$kpiId] = $weight;
        return $this;
    }

    public function get($kpiId) {
        if (! isset($this->kpis[$kpiId])) {
            throw new \Exception("Kpi $kpiId not configured in aggregator $this->aggregatorId");
        }

        return $this->kpis[$kpiId];
    }

    public function has($kpiId) {
        return isset($this->kpis[$kpiId]);
    }
}
