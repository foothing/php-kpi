<?php namespace Foothing\Kpi\Models;

class AggregatorConfigCollection {

    protected $items = [];

    public function add(AggregatorConfigInterface $config, $weight) {
        // Initialize transient value.
        if (! isset($this->items[ $config->getAggregatorId() ])) {
            $this->items[ $config->getAggregatorId() ] = new TransientAggregatorConfig($config->getAggregatorId());

        }

        // Set value.
        return $this->items[ $config->getAggregatorId() ]->add($config->getAggregatorKpiId(), $weight);
    }

    public function items() {
        return $this->items;
    }
}
