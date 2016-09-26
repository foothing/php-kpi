<?php namespace Foothing\Kpi\Tests\Cache;

use Foothing\Kpi\Cache\KpiCache;
use Foothing\Kpi\Tests\Mocks\Factory;

class KpiCacheTest extends \PHPUnit_Framework_TestCase {

    public function test_all_empty() {
        $cache = new KpiCache();
        $this->assertEmpty($cache->all());
    }

    public function test_all() {
        $cache = new KpiCache();
        $kpi = Factory::kpis()[0];
        $measurables = Factory::measurables();

        // Set 3 kpis on one measurable.
        $cache->put($kpi, $measurables[0], 100);

        $value = $cache->get($measurables[0]->id, $kpi->getId());
        $this->assertEquals(100, $value->getTransientValue());

        $cache->flush();

        // Kpis on more measurables.
        $cache->put($kpi, $measurables[0], 100);
        $cache->put($kpi, $measurables[1], 120);

        $this->assertEquals(100, $cache->get($measurables[0]->id, $kpi->getId())->getTransientValue());
        $this->assertEquals(120, $cache->get($measurables[1]->id, $kpi->getId())->getTransientValue());
    }

    public function test_flush() {
        $cache = new KpiCache();
        $kpi = Factory::kpis()[0];
        $measurables = Factory::measurables();

        $cache->put($kpi, $measurables[0], 100);
        $this->assertEquals(100, $cache->get($measurables[0]->id, $kpi->getId())->getTransientValue());

        $cache->flush();
        $this->assertNull($cache->get($measurables[0]->id, $kpi->getId()));
    }

    public function test_is_empty() {
        $cache = new KpiCache();
        $kpi = Factory::kpis()[0];
        $measurables = Factory::measurables();

        $this->assertTrue($cache->isEmpty());
        $cache->put($kpi, $measurables[0], 100);
        $this->assertFalse($cache->isEmpty());
    }
}
