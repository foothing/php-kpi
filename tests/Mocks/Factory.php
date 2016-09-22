<?php namespace Foothing\Kpi\Tests\Mocks;

class Factory {

    public static function kpis() {
        return [
            new Kpi(101, "KPI1", "1+1"),
            new Kpi(102, "KPI2", "1+2"),
            new Kpi(103, "KPI3", "1+3"),
            new Kpi(104, "KPI4", "1+4"),
            new Kpi(105, "KPI5", "1+5"),
        ];
    }

    public static function kpisFromCache() {
        return [
            "1" => new Kpi(101, "KPI1", "1+1"),
            "2" => new Kpi(102, "KPI2", "1+2"),
            "3" => new Kpi(103, "KPI3", "1+3"),
            "4" => new Kpi(104, "KPI4", "1+4"),
            "5" => new Kpi(105, "KPI5", "1+5"),
        ];
    }

    public function measurables() {
        return [
            new Team(1),
            new Team(2),
            new Team(3),
            new Team(4),
            new Team(5),
        ];
    }
}
