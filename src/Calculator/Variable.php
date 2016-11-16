<?php namespace Foothing\Kpi\Calculator;

class Variable {

    static $TODATE = 0;
    static $REALTIME = 1;
    static $TYPE_DATA = 0;
    static $TYPE_KPI = 1;

    public $type;
    public $name;
    public $year;
    public $month;
    public $day;
    public $weekOfYear;
    public $weekOfMonth;
    public $sampleType;

    public $value;

    public function __construct($raw = null, $value = null) {
        $this->raw = $raw;
        $this->value = $value;
    }

    public function getTimeString() {
        return $this->year . $this->month . $this->day . $this->weekOfYear . $this->weekOfMonth;
    }

    public function setName($name) {
        $this->name = $name;

        if (strtolower(trim($name)) == 'kpi') {
            $this->type = self::$TYPE_KPI;
        } else {
            $this->type = self::$TYPE_DATA;
        }

        return $this->type;
    }
}
