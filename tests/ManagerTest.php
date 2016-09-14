<?php namespace Foothing\Kpi\Tests;

use Foothing\Kpi\Calculator\Variable;
use Foothing\Kpi\Manager;
use Foothing\Kpi\Tests\Mocks\Kpi;
use Foothing\Kpi\Tests\Mocks\Team;

class ManagerTest extends \PHPUnit_Framework_TestCase {

    protected $manager, $kpis, $measurables, $parser, $datasets, $calculator;

    public function setUp() {
        $this->kpis = \Mockery::mock("Foothing\Kpi\Repositories\KpiRepositoryInterface");
        $this->measurables = \Mockery::mock("Foothing\Kpi\Repositories\MeasurableRepositoryInterface");
        $this->parser = \Mockery::mock("Foothing\Kpi\Calculator\FormulaParser");
        $this->datasets = \Mockery::mock("Foothing\Kpi\Repositories\DatasetsManager");
        $this->calculator = \Mockery::mock("Foothing\Kpi\Calculator\CalculatorInterface");
        $this->manager = new Manager($this->parser, $this->datasets, $this->kpis, $this->measurables, $this->calculator);
    }

    public function testRefresh() {
        $manager = \Mockery::mock("Foothing\Kpi\Manager[compute]", [$this->parser, $this->datasets, $this->kpis, $this->measurables, $this->calculator]);

        $this->kpis->shouldReceive('all')->once()->andReturn($this->kpis());
        $this->measurables->shouldReceive('all')->once()->andReturn($this->measurables());

        $manager->shouldReceive('compute')->times(10)->andReturnNull();

        $manager->refresh();
    }

    public function testCompute() {
        $measurable = \Mockery::mock("Foothing\Kpi\Models\MeasurableInterface");
        $variables = $this->variables();

        $this->parser->shouldReceive('parse')->once()->andReturn($variables);

        $this->datasets->shouldReceive('getData')->once()->with($measurable, $variables[0])->andReturn(100);
        $this->datasets->shouldReceive('getData')->once()->with($measurable, $variables[1])->andReturn(200);
        $this->calculator->shouldReceive('execute')->once()->with("foo bar", $variables)->andReturn(300);

        $this->manager->compute("foo bar", $measurable);
    }

    public function tearDown() {
        \Mockery::close();
    }

    protected function variables() {
        $variables0 = new Variable();
        $variables0->raw = "{AUTO_RC(CUR,09,12,0,0,TD)}";
        $variables0->value = 100;

        $variables1 = new Variable();
        $variables1->raw = "{AUTO_RC(PREV,09,12,0,0,TD)}";
        $variables1->value = 0.5;

        return [$variables0, $variables1];
    }

    protected function kpis() {
        return [
            new Kpi(1, "KPI1", "1+1"),
            new Kpi(2, "KPI2", "1+2"),
            new Kpi(3, "KPI3", "1+3"),
            new Kpi(4, "KPI4", "1+4"),
            new Kpi(5, "KPI5", "1+5"),
        ];
    }

    protected function measurables() {
        return [
            new Team(),
            new Team()
        ];
    }

}