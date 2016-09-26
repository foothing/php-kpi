<?php namespace Foothing\Kpi\Tests\Mocks;

use Foothing\Kpi\Models\MeasurableInterface;

class Team implements MeasurableInterface {

    public $id;

    public function __construct($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {

    }

}
