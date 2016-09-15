<?php namespace Foothing\Kpi\Models;

interface DataInterface {

    public function getMeasurableId();
    public function getValue($variableName);

}
