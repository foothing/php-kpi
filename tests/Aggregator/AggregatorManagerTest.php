<?php namespace Foothing\Kpi\Tests\Aggregator;

use Foothing\Kpi\Aggregator\AggregatorManager;
use Foothing\Kpi\Tests\Mocks\AggregatorConfig;
use Foothing\Kpi\Tests\Mocks\Factory;

class AggregatorManagerTest extends \PHPUnit_Framework_TestCase {

    protected $cache, $aggregators;

    /**
     * @var AggregatorManager
     */
    protected $manager;

    public function setUp() {
        parent::setUp();
        $this->cache = \Mockery::mock("Foothing\Kpi\Cache\KpiCache");
        $this->aggregators = \Mockery::mock("Foothing\Kpi\Repositories\AggregatorRepositoryInterface");
        $this->manager = new AggregatorManager($this->aggregators);
    }

    public function _test_rebuild_fails_if_config_is_empty() {
        $this->aggregators->shouldReceive('getConfig')->once()->andReturnNull();
        $this->setExpectedException("Foothing\Kpi\Aggregator\Exceptions\AggregatorConfigNotFoundException");
        $this->manager->rebuild($this->cache);
    }

    public function _test_rebuild_fails_if_kpi_not_found() {
        $this->aggregators->shouldReceive('getConfig')->once()->andReturn($this->config());
        $this->cache->shouldReceive("get")->once()->with(101)->andReturnNull();
        $this->setExpectedException("Foothing\Kpi\Aggregator\Exceptions\KpiNotFoundException");
        $this->manager->rebuild($this->cache);
    }

    public function test_rebuild() {
        $config = $this->config();
        $kpis = Factory::kpisFromCache();

        $this->aggregators->shouldReceive('getConfig')->once()->andReturn($config);

        $this->cache->shouldReceive('get')->once()->with(101)->andReturn($kpis);
        $this->aggregators->shouldReceive('store')->once();

        $this->cache->shouldReceive('get')->once()->with(102)->andReturn($kpis);
        $this->aggregators->shouldReceive('store')->once();

        $this->cache->shouldReceive('get')->once()->with(103)->andReturn($kpis);
        $this->aggregators->shouldReceive('store')->once();
        $this->manager->rebuild($this->cache);
    }

    public function test_balanced_aggregate() {
        $balancedValues = [7, 9, 0, 0.5];
        $expectd = (7 + 9 + 0 + 0.5) / 4;
        $this->assertEquals($expectd, $this->manager->getBalancedAggregate($balancedValues));
    }

    protected function config() {
        return [
            new AggregatorConfig(1, 101, 1),
            new AggregatorConfig(2, 102, 1),
            new AggregatorConfig(3, 103, 0.5),
        ];
    }

    public function tearDown() {
        \Mockery::close();
    }
}