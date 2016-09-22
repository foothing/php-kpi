<?php namespace Foothing\Kpi\Models\Traits;

/**
 * Requires class to implement KpiInterface.
 *
 * Trait QuantizeValues
 * @package Foothing\Kpi\Models\Traits
 */
trait QuantizeValues {

    public function quantizeTransientValue() {
        $thresholds = $this->getThresholds();

        for ($i = 1; $i < count($thresholds) - 1; $i++) {
            $threshold = (float)$thresholds[$i];

            if ($this->getTransientValue() < $threshold) {
                return $i;
            }
        }

        return $i;
    }
}
