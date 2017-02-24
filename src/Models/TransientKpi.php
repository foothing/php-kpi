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
    protected $balancedValue;

    /**
     * @var \Foothing\Kpi\Calculator\Result
     */
    protected $result;

    public function __construct(KpiInterface $kpi, Result $result) {
        $this->setKpi($kpi);
        $this->setTransientValue($result->value);
        $this->result = $result;
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
        return $this->result->quantizedValue;
    }

    public function setBalancedValue($balancedValue) {
        $this->balancedValue = $balancedValue;
    }

    public function getBalancedValue() {
        return $this->balancedValue;
    }

    public function getComputedFormula() {
        return $this->result->formula;
    }

    public function getVariables() {
        return $this->result->variables;
    }

    public function getResult() {
        return $this->result;
    }
}
