<?php namespace Foothing\Kpi\Aggregator;

use Foothing\Kpi\Aggregator\Exceptions\AggregatorConfigNotFoundException;
use Foothing\Kpi\Aggregator\Exceptions\KpiNotFoundException;
use Foothing\Kpi\Cache\KpiCache;
use Foothing\Kpi\Models\AggregatorConfigInterface;
use Foothing\Kpi\Models\KpiInterface;
use Foothing\Kpi\Models\TransientKpi;
use Foothing\Kpi\Repositories\AggregatorRepositoryInterface;

class AggregatorManager {

    /**
     * @var \Foothing\Kpi\Repositories\AggregatorRepositoryInterface
     */
    protected $aggregators;

    public function __construct(AggregatorRepositoryInterface $aggregators) {
        $this->aggregators = $aggregators;
    }

    /**
     * Rebuild the aggregators status.
     *
     * @param KpiCache $cache
     *
     * @throws Exceptions\KpiNotFoundException
     * @throws Exceptions\AggregatorConfigNotFoundException
     */
    public function rebuild(KpiCache $cache) {
        // Note: each entry is instance of AggregatorConfigInterface.
        $config = $this->aggregators->getConfig();

        if (! $config) {
            throw new AggregatorConfigNotFoundException();
        }

        foreach ($config as $configValue) {
            /* @var $configValue \Foothing\Kpi\Models\AggregatorConfigInterface */

            // Kpi for each measurable.
            $measurables = $cache->get($configValue->getAggregatorKpiId());
dd($measurables);
            if (! $measurables) {
                throw new KpiNotFoundException("Kpi " . $configValue->getAggregatorKpiId() . " not found in cache.");
            }

            foreach ($measurables as $measurableId => $kpis) {
                $balancedValues = [];

                foreach ($kpis as $kpi) {
                    /* @var $kpi TransientKpi */

                    // The kpi value, in quantized scale.
                    $quantized = $kpi->quantizeTransientValue();

                    // The balanced kpi value for this aggregator.
                    $balancedValues[] = $quantized * $configValue->getAggregatorKpiWeight();
                }

                // Store the value.
                $this->aggregators->store($configValue, $measurableId, $kpi, $this->getBalancedAggregate($balancedValues));
            }
        }
    }

    public function getBalancedAggregate(array $balancedValues) {
        return array_sum($balancedValues) / count($balancedValues);
    }

    public function addKpi(AggregatorConfigInterface $config, KpiInterface $kpi, $weight) {
        return $this->aggregators->addKpi($config, $kpi, $weight);
    }

    public function removeKpi(AggregatorConfigInterface $config, KpiInterface $kpi) {
        return $this->aggregators->removeKpi($config, $kpi);
    }
}
