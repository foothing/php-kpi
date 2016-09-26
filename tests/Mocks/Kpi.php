<?php namespace Foothing\Kpi\Tests\Mocks;

use Foothing\Kpi\Models\KpiInterface;

class Kpi implements KpiInterface {

    protected $id, $name, $formula, $value, $thresholds;

    public function __construct($id, $name, $formula, $thresholds = []) {
        $this->id = $id;
        $this->name = $name;
        $this->formula = $formula;
        $this->thresholds = $thresholds;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getFormula() {
        return $this->formula;
    }

    public function getThresholds() {
        return $this->thresholds;
    }

    public function quantizeTransientValue() {
        // TODO: Implement quantizeTransientValue() method.
    }

    public function setTransientValue($value) {
        $this->value = $value;
    }

    public function getTransientValue() {
        return $this->value;
    }
}
