<?php namespace Foothing\Kpi\Cache;

use Foothing\Kpi\Models\KpiInterface;
use Foothing\Kpi\Models\MeasurableInterface;
use Foothing\Kpi\Models\TransientKpi;

class KpiCache {

    protected $cache = [];

    public function put(KpiInterface $kpi, MeasurableInterface $measurable, $value) {
        $this->cache[ $kpi->getId() ][ $measurable->getId() ][] = new TransientKpi($kpi, $value);
    }

    public function get($kpiId) {
        return isset($this->cache[$kpiId]) ? $this->cache[$kpiId] : null;
    }

    public function flush() {
        $this->cache = [];
    }

    public function isEmpty() {
        return empty($this->cache);
    }
}
