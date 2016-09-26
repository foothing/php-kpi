<?php namespace Foothing\Kpi;

use Foothing\Kpi\Aggregator\AggregatorManager;
use Foothing\Kpi\Cache\KpiCache;
use Foothing\Kpi\Calculator\CalculatorInterface;
use Foothing\Kpi\Calculator\FormulaParser;
use Foothing\Kpi\Models\MeasurableInterface;
use Foothing\Kpi\Repositories\DatasetsManager;
use Foothing\Kpi\Repositories\KpiRepositoryInterface;
use Foothing\Kpi\Repositories\MeasurableRepositoryInterface;

class Manager {

    /**
     * @var Calculator\FormulaParser
     */
    protected $parser;

    /**
     * @var Repositories\DatasetsManager
     */
    protected $datasets;

    /**
     * @var Repositories\KpiRepositoryInterface
     */
    protected $kpis;

    /**
     * @var Repositories\MeasurableRepositoryInterface
     */
    protected $measurables;

    /**
     * @var Calculator\CalculatorInterface
     */
    protected $calculator;

    /**
     * @var Cache\KpiCache
     */
    protected $cache;

    /**
     * @var Aggregator\AggregatorManager
     */
    protected $aggregatorManager;

    public function __construct(
        FormulaParser $parser,
        DatasetsManager $datasets,
        KpiRepositoryInterface $kpis,
        MeasurableRepositoryInterface $measurables,
        CalculatorInterface $calculator,
        KpiCache $cache,
        AggregatorManager $aggregatorManager) {

        $this->parser = $parser;
        $this->datasets = $datasets;
        $this->kpis = $kpis;
        $this->measurables = $measurables;
        $this->calculator = $calculator;
        $this->cache = $cache;
        $this->aggregatorManager = $aggregatorManager;
    }

    public function refresh() {
        // Cleanup.
        $this->aggregatorManager->clearCache();

        // Fetch all configured kpis.
        $kpis = $this->kpis->all();

        // Fetch all measurable entities.
        $measurables = $this->measurables->all();

        $debug = [];

        // Evaluate kpis for each measurable.
        foreach($measurables as $measurable) {

            // Cycle kpis.
            foreach ($kpis as $kpi) {
                // Compute each kpi's value and cache.
                $value = $this->compute($kpi->getFormula(), $measurable);

                $debug[] = ['kpi' => $kpi, 'measurable' => $measurable, 'value' => $value];
//print "$kpi->name $measurable->id $value | $kpi->formula<br>";

                $this->cache->put($kpi, $measurable, $value);
            }
        }

        $this->aggregatorManager->rebuild($this->cache);

        return $debug;
    }

    public function compute($formula, MeasurableInterface $measurable) {
        // Get all variables from formula.
        $variables = $this->parser->parse($formula);

        // Get variable values.
        foreach ($variables as $variable) {
            $variable->value = $this->datasets->getData($measurable, $variable) ?: 0;
        }

        // Set parameters and compute formula.
        return $this->calculator->execute($formula, $variables);
    }
}
