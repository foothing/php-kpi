<?php namespace Foothing\Kpi\Tests;

use Foothing\Kpi\Calculator\KpiVariable;
use Foothing\Kpi\Calculator\Variable;
use Foothing\Kpi\Manager;
use Foothing\Kpi\Tests\Mocks\Factory;
use Foothing\Kpi\Tests\Mocks\Kpi;
use Foothing\Kpi\Tests\Mocks\Team;

class ManagerTest extends \PHPUnit_Framework_TestCase {

    protected $manager, $kpis, $measurables, $parser, $datasets, $calculator, $kpiCache, $aggregatorManager;

    public function setUp() {
        $this->kpis = \Mockery::mock("Foothing\Kpi\Repositories\KpiRepositoryInterface");
        $this->measurables = \Mockery::mock("Foothing\Kpi\Repositories\MeasurableRepositoryInterface");
        $this->parser = \Mockery::mock("Foothing\Kpi\Calculator\FormulaParser");
        $this->datasets = \Mockery::mock("Foothing\Kpi\Repositories\DatasetsManager");
        $this->calculator = \Mockery::mock("Foothing\Kpi\Calculator\CalculatorInterface");
        $this->kpiCache = \Mockery::mock("Foothing\Kpi\Cache\KpiCache");
        $this->aggregatorManager = \Mockery::mock("Foothing\Kpi\Aggregator\AggregatorManager");
        $this->manager = new Manager(
            $this->parser,
            $this->datasets,
            $this->kpis,
            $this->measurables,
            $this->calculator,
            $this->kpiCache,
            $this->aggregatorManager);
    }

    // @TODO re-enable and refactor.
    public function _testRefresh() {
        $manager = \Mockery::mock("Foothing\Kpi\Manager[compute]", [
            $this->parser,
            $this->datasets,
            $this->kpis,
            $this->measurables,
            $this->calculator,
            $this->kpiCache,
            $this->aggregatorManager]);

        $this->aggregatorManager->shouldReceive('clearCache')->once();
        $this->kpis->shouldReceive('all')->once()->andReturn($this->kpis());
        $this->measurables->shouldReceive('all')->once()->andReturn($this->measurables());

        $manager->shouldReceive('compute')->times(10)->andReturnNull();
        $this->kpiCache->shouldReceive('put')->times(10);
        $this->aggregatorManager->shouldReceive('rebuild')->once();

        $manager->refresh();
    }

    public function test_computeKpi() {
        $manager = \Mockery::mock("Foothing\Kpi\Manager[compute]", [
            $this->parser,
            $this->datasets,
            $this->kpis,
            $this->measurables,
            $this->calculator,
            $this->kpiCache,
            $this->aggregatorManager]);

        $this->kpiCache->shouldReceive('get')->once()->andReturnNull();
        $manager->shouldReceive('compute')->once();
        $this->kpiCache->shouldReceive('put');
        $manager->computeKpi($this->measurables()[0], $this->kpis()[0]);
    }

    public function test_compute_only_variables() {
        $measurable = \Mockery::mock("Foothing\Kpi\Models\MeasurableInterface");
        $variables = $this->variables();

        $this->parser->shouldReceive('parse')->once()->andReturn($variables);

        $this->datasets->shouldReceive('getData')->once()->with($measurable, $variables[0])->andReturn(100);
        $this->datasets->shouldReceive('getData')->once()->with($measurable, $variables[1])->andReturn(200);
        $this->calculator->shouldReceive('execute')->once()->with("foo bar", $variables, $foo = 0)->andReturn(300);

        $this->manager->compute("foo bar", $measurable);
    }

    public function test_compute_only_kpis() {
        $manager = \Mockery::mock("Foothing\Kpi\Manager[computeKpi]", [
            $this->parser,
            $this->datasets,
            $this->kpis,
            $this->measurables,
            $this->calculator,
            $this->kpiCache,
            $this->aggregatorManager]);

        $measurable = \Mockery::mock("Foothing\Kpi\Models\MeasurableInterface");
        $kpis = [new KpiVariable("foo")];

        $this->parser->shouldReceive('parse')->once()->andReturn($kpis);
        $this->kpis->shouldReceive('findOneBy')->with('name', 'foo')->once()->andReturn($this->kpis()[0]);
        $manager->shouldReceive('computeKpi')->once();
        $manager->compute("foo bar", $measurable);
    }

    public function test_compute_recursive_steps() {
        $manager = \Mockery::mock("Foothing\Kpi\Manager[computeKpi]", [
            $this->parser,
            $this->datasets,
            $this->kpis,
            $this->measurables,
            $this->calculator,
            $this->kpiCache,
            $this->aggregatorManager]);

        $testKpi = new KpiVariable("TEST");

        $this->parser->shouldReceive('parse')->once()->andReturn([$testKpi]);
        $this->kpis->shouldReceive('findOneBy')->with('name', 'TEST')->once()->andReturn($this->kpis()[0]);
        $manager->shouldReceive("computeKpi")->once();

        $manager->compute("foo bar", $this->measurables()[0]);
    }

    // @TODO tests to check for values

    public function _test_compute_recursive() {
        $testKpi = new KpiVariable("TEST");

        $this->parser->shouldReceive('parse')->once()->andReturn([$testKpi]);
        $this->kpis->shouldReceive('findOneBy')->with('name', 'TEST')->once()->andReturn($this->kpis()[0]);
        $this->kpiCache->shouldReceive('get')->once()->andReturn(0.48);
        $result = $this->manager->compute("{KPI(TEST,CUR)} + 1", $this->measurables()[0]);

        $this->assertEquals(1.48, $result);
    }

    // broken
    public function ____test_compute_recursive_is_limited() {
        $manager = \Mockery::mock("Foothing\Kpi\Manager[computeKpi]", [
            $this->parser,
            $this->datasets,
            $this->kpis,
            $this->measurables,
            $this->calculator,
            $this->kpiCache,
            $this->aggregatorManager]);

        $testKpi1 = new KpiVariable("TEST1");
        $testKpi2 = new KpiVariable("TEST2");

        $this->parser->shouldReceive('parse')->once()->andReturn([$testKpi1]);
        $this->parser->shouldReceive('parse')->once()->andReturn([$testKpi2]);
        $this->kpis->shouldReceive('findOneBy')->twice()->andReturn($this->kpis()[0]);
        $manager->shouldReceive("computeKpi")->once();

        $this->setExpectedException("Exception");
        $manager->compute("foo bar", $this->measurables()[0]);
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
        return Factory::kpis();
    }

    protected function measurables() {
        return [
            new Team(1),
            new Team(2)
        ];
    }

}