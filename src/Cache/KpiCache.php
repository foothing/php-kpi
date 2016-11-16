<?php namespace Foothing\Kpi\Cache;

use Foothing\Kpi\Models\KpiInterface;
use Foothing\Kpi\Models\MeasurableInterface;
use Foothing\Kpi\Models\TransientKpi;

class KpiCache {

    protected $cache = [];

    public function put(KpiInterface $kpi, MeasurableInterface $measurable, $value) {
        $this->cache[ $measurable->getId() ][ $kpi->getId() ] = new TransientKpi($kpi, $value);
    }

    public function get($measurableId, $kpiId) {
        return isset($this->cache[$measurableId]) && isset($this->cache[$measurableId][$kpiId]) ? $this->cache[$measurableId][$kpiId] : null;
    }

    public function all() {
        return $this->cache;
    }

    public function flush() {
        $this->cache = [];
    }

    public function isEmpty() {
        return empty($this->cache);
    }
}
