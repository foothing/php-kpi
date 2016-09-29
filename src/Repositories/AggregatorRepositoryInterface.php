<?php namespace Foothing\Kpi\Repositories;

use Foothing\Kpi\Models\AggregatorConfigCollection;
use Foothing\Kpi\Models\AggregatorConfigInterface;
use Foothing\Kpi\Models\KpiInterface;
use Foothing\Kpi\Models\TransientAggregatorConfig;
use Foothing\Kpi\Models\TransientKpi;

interface AggregatorRepositoryInterface {

    /**
     * @return AggregatorConfigCollection
     */
    public function getConfig();

    /**
     * @param TransientAggregatorConfig $config
     * One entry in the aggregators config. Each entry relates
     * the kpi to the aggregator, with its weight.
     *
     * @param                           $measurableId
     * @param TransientKpi              $kpi
     * @param float                     $balancedValue
     *
     * @return mixed
     */
    public function store(TransientAggregatorConfig $config, $measurableId, TransientKpi $kpi, $balancedValue);

    public function storeBalancedAggregate(TransientAggregatorConfig $config, $measurableId, TransientKpi $kpi, $balancedAggregate);

    public function clearCache();

    // @TODO following.




    public function addKpi(AggregatorConfigInterface $config, KpiInterface $kpi, $weight);

    public function removeKpi(AggregatorConfigInterface $config, KpiInterface $kpi);
}
