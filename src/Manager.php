<?php namespace Foothing\Kpi;

use Foothing\Kpi\Aggregator\AggregatorManager;
use Foothing\Kpi\Aggregator\Exceptions\KpiNotFoundException;
use Foothing\Kpi\Cache\KpiCache;
use Foothing\Kpi\Calculator\CalculatorInterface;
use Foothing\Kpi\Calculator\FormulaParser;
use Foothing\Kpi\Calculator\KpiVariable;
use Foothing\Kpi\Calculator\Variable;
use Foothing\Kpi\Models\KpiInterface;
use Foothing\Kpi\Models\MeasurableInterface;
use Foothing\Kpi\Repositories\DatasetsManager;
use Foothing\Kpi\Repositories\KpiRepositoryInterface;
use Foothing\Kpi\Repositories\MeasurableRepositoryInterface;
use MathParser\Exceptions\DivisionByZeroException;

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

    /**
     * @var array
     */
    protected $recursiveStack = [];

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
                /*$compiled = "";

                // Compute each kpi's value and cache.
                $value = $this->compute($kpi->getFormula(), $measurable, $compiled);

//\Log::debug($kpi->getName() . " " . $measurable->getName() . " " .  $value);
                $debug[] = ['kpi' => $kpi, 'measurable' => $measurable, 'compiled' => $compiled, 'value' => $value];
//print "$kpi->name $measurable->id $value | $kpi->formula<br>";

                $this->cache->put($kpi, $measurable, $value);*/

                // @TODO re-enable this when parser is done.
                $this->computeKpi($measurable, $kpi);
            }
        }

        $this->aggregatorManager->rebuild($this->cache);

        return $debug;
    }

    public function computeKpi(MeasurableInterface $measurable, KpiInterface $kpi) {
        if ($transientKpi = $this->cache->get($measurable->getId(), $kpi->getId())) {
            return $transientKpi->getTransientValue();
        }

        if (! $computed = $this->compute($kpi->getFormula(), $measurable)) {
            return null;
        }

        $computed->quantizedValue = $this->getQuantizedValue($kpi, $computed->value);
        $this->cache->put($kpi, $measurable, $computed);
        return $computed;
    }

    public function compute($formula, MeasurableInterface $measurable) {
        // Get all variables from formula.
        $variables = $this->parser->parse($formula);

        // Get variable values.
        foreach ($variables as $variable) {

            // @TODO check recursive steps.

            // Set recursive step for nested kpis.
            if ($variable instanceof KpiVariable) {

                // Limit recursive stack to 1 item.

                // @FIXME! Broken - disabled.
                //if (! empty($this->recursiveStack)) {
                //    throw new \Exception("Recursion too deep in $formula");
                //}

                // Add kpi to stack.
                $this->recursiveStack[ $variable->name ] = $variable;
                //var_dump($this->recursiveStack);

                // Find kpi and go recursive.
                $kpi = $this->kpis->findOneBy('name', $variable->name);

                if (! $kpi) {
                    throw new KpiNotFoundException("KPI $variable->name not found in recursive forumla.");
                }

                $computed = $this->computeKpi($measurable, $kpi);
                $variable->value = $computed ? $computed->quantizedValue : null;

                // Reset recursive stack.
                unset($this->recursiveStack[ $variable->name ]);
            }

            // Fetch variable value.
            else {
                $variable->value = $this->datasets->getData($measurable, $variable) ?: 0;
            }
        }

        // Set parameters and compute formula.
        try {
            return $this->calculator->execute($formula, $variables);
            //print "$compiledFormula\n";
        } catch (DivisionByZeroException $ex) {
            \Log::warning("Possible zero value for $measurable->id in $formula");
            return null;
        } /*catch (\Exception $ex) {
            \Log::error($ex->getMessage());
            return null;
        }*/

    }

    public function getQuantizedValue(KpiInterface $kpi, $value) {
        $thresholds = $kpi->getThresholds();

        for ($i = 1; $i <= count($thresholds); $i++) {
            $threshold = (float)$thresholds[$i - 1];

            if ($kpi->isThresholdReverse() && $value > $threshold) {
                return $i;
            }

            elseif (! $kpi->isThresholdReverse() && $value < $threshold) {
                return $i;
            }
        }

        return $i;
    }
}
