<?php namespace Foothing\Kpi\Tests\Mocks;

use Foothing\Kpi\Models\KpiInterface;

class Kpi implements KpiInterface {

    protected $id, $name, $formula, $value;

    public function __construct($id, $name, $formula) {
        $this->id = $id;
        $this->name = $name;
        $this->formula = $formula;
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

    public function quantizeValue($value) {
        // TODO: Implement quantizeValue() method.
    }
}
