<?php namespace Foothing\Kpi\Calculator;

class Variable {

    static $TODATE = 0;
    static $REALTIME = 1;

    public $name;
    public $year;
    public $month;
    public $day;
    public $weekOfYear;
    public $weekOfMonth;
    public $type;

    public $value;

    public function getTimeString() {
        return $this->year . $this->month . $this->day . $this->weekOfYear . $this->weekOfMonth;
    }
}
