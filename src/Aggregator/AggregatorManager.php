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
     * @throws Exceptions\AggregatorConfigNotFoundException
     * @throws \Exception
     */
    public function rebuild(KpiCache $cache) {
        /* var $config Foothing\Kpi\Models\AggregatorConfigCollection */
        $config = $this->aggregators->getConfig();

        if (! $config) {
            throw new AggregatorConfigNotFoundException();
        }

        // Kpi for each measurable.
        $measurables = $cache->all();

        if (! $measurables) {
            throw new \Exception("rename cache exception");
        }

        // Begin the iterations, for each configured aggregator.
        foreach ($config->items() as $configValue) {
            /* @var $configValue \Foothing\Kpi\Models\TransientAggregatorConfig */

            // Cycle all measurables. Note that each measurable
            // is being get from kpi cache, so it will be
            // present only if it has associated kpis.
            foreach ($measurables as $measurableId => $kpis) {
                $balancedValues = [];

                // @TODO
                // error if kpis don't match expectations, or not found.

                // Now cycle only the signifiative kpis and balance them.
                foreach ($kpis as $kpi) {
                    /* @var $kpi TransientKpi */

                    // Skip if current config doesn't have $kpi.
                    if (! $configValue->has($kpi->getKpi()->getId())) {
                        continue;
                    }

                    // The balanced kpi value for this aggregator.
                    $balancedValue = $this->getBalancedValue($kpi, $configValue->get($kpi->getKpi()->getId()));

                    // The quantized value after the balance.
                    $quantizedValue = $this->getQuantizedValue($kpi, $balancedValue);
                    //\Log::debug("B:" . $kpi->getKpi()->id . "/" . $measurableId . "/" . $kpi->getTransientValue() . "/" . $balancedValue . "/" . $quantizedValue);
                    //\Log::debug("Q:" . $kpi->getKpi()->id . "/" . $measurableId . "/" . $quantizedValue);
                    $balancedValues[] = $quantizedValue;

                    // Store aggregated value.
                    $this->aggregators->store($configValue, $measurableId, $kpi, $quantizedValue);
                }

                // Store the global balanced value.
                $this->aggregators->storeBalancedAggregate($configValue, $measurableId, $kpi, $this->getBalancedAggregate($balancedValues));
            }
        }
    }

    /**
     * @param TransientKpi $kpi
     * @param float        $value
     * The balanced kpi value.
     *
     * @return int
     */
    public function getQuantizedValue(TransientKpi $kpi, $value) {
        $thresholds = $kpi->getKpi()->getThresholds();

        for ($i = 1; $i <= count($thresholds); $i++) {
            $threshold = (float)$thresholds[$i];

            if ($value < $threshold) {
                return $i;
            }
        }

        return $i;
    }

    /**
     * @param TransientKpi $kpi
     * @param float        $weight
     * 0 <= $weight <= 1
     *
     * @return float
     */
    public function getBalancedValue(TransientKpi $kpi, $weight) {
        return $kpi->getTransientValue() * $weight;
    }

    public function getBalancedAggregate(array $balancedValues) {
        return round(array_sum($balancedValues) / count($balancedValues), 0);
    }

    public function clearCache() {
        $this->aggregators->clearCache();
    }

    public function addKpi(AggregatorConfigInterface $config, KpiInterface $kpi, $weight) {
        return $this->aggregators->addKpi($config, $kpi, $weight);
    }

    public function removeKpi(AggregatorConfigInterface $config, KpiInterface $kpi) {
        return $this->aggregators->removeKpi($config, $kpi);
    }
}
