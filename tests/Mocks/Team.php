<?php namespace Foothing\Kpi\Tests\Mocks;

use Foothing\Kpi\Models\MeasurableInterface;

class Team implements MeasurableInterface {

    public function getId() {
        return 1;
    }

}
