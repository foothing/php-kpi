<?php namespace Foothing\Kpi\Models;

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
     * @var float
     */
    protected $balancedValue;

    public function __construct(KpiInterface $kpi, $value) {
        $this->setKpi($kpi);
        $this->setTransientValue($value);
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

    public function quantizeTransientValue() {
        $thresholds = $this->kpi->getThresholds();

        for ($i = 1; $i <= count($thresholds); $i++) {
            $threshold = (float)$thresholds[$i];

            if ($this->getTransientValue() < $threshold) {
                return $i;
            }
        }

        return $this->quantizedValue = $i;
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
