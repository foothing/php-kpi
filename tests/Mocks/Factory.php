<?php namespace Foothing\Kpi\Tests\Mocks;

use Foothing\Kpi\Models\AggregatorConfigCollection;
use Foothing\Kpi\Models\TransientAggregatorConfig;
use Foothing\Kpi\Models\TransientKpi;

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
        $cache = [

            // Entries for measurable 1
            "1" => [
                101 => new TransientKpi(new Kpi(101, "KPI1", "1+1"), 2),
                102 => new TransientKpi(new Kpi(102, "KPI1", "1+2"), 3),
                //103 => new TransientKpi(new Kpi(102, "KPI1", "1+3"), 4),
                //104 => new TransientKpi(new Kpi(102, "KPI1", "1+4"), 5),
                //105 => new TransientKpi(new Kpi(102, "KPI1", "1+5"), 6),
            ],

            // Entries for measurable 2
            "2" => [
                101 => new TransientKpi(new Kpi(101, "KPI1", "1+1"), 22),
                102 => new TransientKpi(new Kpi(102, "KPI1", "1+2"), 33),
                //103 => new TransientKpi(new Kpi(102, "KPI1", "1+3"), 44),
                //104 => new TransientKpi(new Kpi(102, "KPI1", "1+4"), 55),
                //105 => new TransientKpi(new Kpi(102, "KPI1", "1+5"), 66),
            ],

        ];

        return $cache;
    }

    public static function configs() {
        $collection = new AggregatorConfigCollection();
        $collection->add(new AggregatorConfig(1, 101, 0.75), 1);
        $collection->add(new AggregatorConfig(1, 102, 1.1), 1);
        $collection->add(new AggregatorConfig(2, 101, 1), 1);
        $collection->add(new AggregatorConfig(3, 101, 1), 1);

        return $collection;
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
