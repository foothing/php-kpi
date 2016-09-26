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

    public function test_rebuild_fails_if_config_is_empty() {
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

        // Get the aggregators configuration.
        $this->aggregators->shouldReceive('getConfig')->once()->andReturn($config);

        // Get the kpi cache.
        $this->cache->shouldReceive('all')->andReturn($kpis);

        // Num configured kpis X num measurables.
        $this->aggregators->shouldReceive('store')->times(8);

        // Num aggregators X measurables.
        $this->aggregators->shouldReceive('storeBalancedAggregate')->times(6);

        $this->manager->rebuild($this->cache);
    }

    public function test_balanced_aggregate() {
        $balancedValues = [7, 9, 0, 0.5];
        $expectd = (7 + 9 + 0 + 0.5) / 4;
        $this->assertEquals($expectd, $this->manager->getBalancedAggregate($balancedValues));
    }

    protected function config() {
        return Factory::configs();
    }

    public function tearDown() {
        \Mockery::close();
    }
}