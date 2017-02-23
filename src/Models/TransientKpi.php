<?php namespace Foothing\Kpi\Models;

use Foothing\Kpi\Calculator\Result;
use Foothing\Kpi\Models\Traits\QuantizeValues;

class TransientKpi {

    /**
     * @var KpiInterface
     */
    protected $kpi;

    /**
     * @var float
     */
    protected $value;

    /**
     * @var float
     */
    protected $quantizedValue;

    /**
     * @var string
     */
    protected $computedFormula;

    /**
     * @var array
     */
    protected $variables;

    /**
     * @var float
     */
    protected $balancedValue;

    public function __construct(KpiInterface $kpi, Result $result) {
        $this->setKpi($kpi);
        $this->setTransientValue($result->value);
        $this->quantizedValue = $result->quantizedValue;
        $this->computedFormula = $result->formula;
        $this->variables = $result->variables;
    }

    public function setKpi(KpiInterface $kpi) {
        $this->kpi = $kpi;
    }

    public function getKpi() {
        return $this->kpi;
    }

    public function setTransientValue($value) {
        $this->value = $value;
    }

    public function getTransientValue() {
        return $this->value;
    }

    public function getQuantizedValue() {
        return $this->quantizedValue;
    }

    public function setBalancedValue($balancedValue) {
        $this->balancedValue = $balancedValue;
    }

    public function getBalancedValue() {
        return $this->balancedValue;
    }
}
