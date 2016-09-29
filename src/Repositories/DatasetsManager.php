<?php namespace Foothing\Kpi\Repositories;

use Foothing\Kpi\Calculator\Variable;
use Foothing\Kpi\Config\ConfigInterface;
use Foothing\Kpi\Models\DataInterface;
use Foothing\Kpi\Models\MeasurableInterface;

class DatasetsManager {

    protected $cache = [];

    /**
     * @var DatasetRepositoryInterface
     */
    protected $datasets;

    /**
     * @var \Foothing\Kpi\Config\ConfigInterface
     */
    protected $config;

    public function __construct(DatasetRepositoryInterface $datasets, ConfigInterface $config) {
        $this->datasets = $datasets;
        $this->config = $config;
    }

    public function getData(MeasurableInterface $measurable, Variable $variable) {
        // $key = AUTO_RC
        // $time = 2015,09,16,0,0 - year, month, day, week of month, week of year
        // $type = "to date" | "realtime"

        $key = $variable->name;
        //$time = $variable->time;
        $type = $variable->type;

        // @TODO add optional data-mapping.
        // For example, data AUTO_RC might be in a certain table column
        // and should be pointed.
        // The actual implementation expects data to be in a single
        // table instead.

        if ($cached = $this->cacheGet($variable, $measurable->getId())) {
            return $cached;
        }

        // Assuming
        $data = $this->datasets->findByTime($variable);
//dd($data);
        // Cache all $key | $time values, then return the one
        // for the requested measurable entity.
        foreach ($data as $row) {
            $this->cacheSet($variable, $row);
        }
//var_dump($this->cache);
        return $this->cacheGet($variable, $measurable->getId());
    }

    protected function cacheSet(Variable $variable, DataInterface $data) {
        //print "cache set $variable->name for " . $data->getMeasurableId() . "<br>";
        return $this->cache[$variable->name][ $variable->getTimeString() ][ $data->getMeasurableId() ] = $data->getValue($variable->name);
    }

    protected function cacheGet(Variable $variable, $measurableId) {
        if (! $this->cacheHas($variable, $measurableId)) {
            return null;
        }

        return $this->cache[$variable->name][ $variable->getTimeString() ][$measurableId];
    }

    protected function cacheHas(Variable $variable, $measurableId) {
        return
            isset($this->cache[ $variable->name ])
            && isset($this->cache[ $variable->name ][ $variable->getTimeString() ])
            && isset($this->cache[ $variable->name ][ $variable->getTimeString() ][ $measurableId ]);
    }
}
