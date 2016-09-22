<?php namespace Foothing\Kpi\Repositories;

use Foothing\Kpi\Models\AggregatorConfigInterface;
use Foothing\Kpi\Models\KpiInterface;
use Foothing\Kpi\Models\TransientKpi;

interface AggregatorRepositoryInterface {

    public function getConfig();

    /**
     * @param AggregatorConfigInterface $config
     * One entry in the aggregators config. Each entry relates
     * the kpi to the aggregator, with its weight.
     *
     * @param                           $measurableId
     * @param TransientKpi              $kpi
     * @param                           $balancedAggregate
     * The balanced aggregator aggregate value.
     *
     * @return mixed
     */
    public function store(AggregatorConfigInterface $config, $measurableId, TransientKpi $kpi, $balancedAggregate);

    public function addKpi(AggregatorConfigInterface $config, KpiInterface $kpi, $weight);

    public function removeKpi(AggregatorConfigInterface $config, KpiInterface $kpi);
}
