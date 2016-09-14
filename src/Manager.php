<?php namespace Foothing\Kpi;

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

    public function __construct(
        FormulaParser $parser,
        DatasetsManager $datasets,
        KpiRepositoryInterface $kpis,
        MeasurableRepositoryInterface $measurables,
        CalculatorInterface $calculator) {

        $this->parser = $parser;
        $this->datasets = $datasets;
        $this->kpis = $kpis;
        $this->measurables = $measurables;
        $this->calculator = $calculator;
    }

    public function refresh() {
        // Fetch all configured kpis.
        $kpis = $this->kpis->all();

        // Fetch all measurable entities.
        $measurables = $this->measurables->all();

        foreach($measurables as $measurable) {

            foreach ($kpis as $kpi) {
                // Compute each kpi's value and cache.
                $value = $this->compute($kpi->getFormula(), $measurable);

                //$this->kpis->store($kpi, $value);
            }
        }
    }

    public function compute($formula, MeasurableInterface $measurable) {
        // Get all variables from formula.
        $variables = $this->parser->parse($formula);

        // Get variable values.
        foreach ($variables as $variable) {
            $variable->value = $this->datasets->getData($measurable, $variable);
        }

        // Set parameters and compute formula.
        return $this->calculator->execute($formula, $variables);
    }
}
