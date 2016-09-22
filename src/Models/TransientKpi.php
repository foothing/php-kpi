<?php namespace Foothing\Kpi\Models;

use Foothing\Kpi\Models\Traits\QuantizeValues;

class TransientKpi {

    use QuantizeValues;

    /**
     * @var float
     */
    protected $value;

    /**
     * @var KpiInterface
     */
    protected $kpi;

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

    public function getThresholds() {
        return $this->kpi->getThresholds();
    }
}
