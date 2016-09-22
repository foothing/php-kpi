<?php namespace Foothing\Kpi\Tests\Cache;

use Foothing\Kpi\Cache\KpiCache;
use Foothing\Kpi\Tests\Mocks\Factory;

class KpiCacheTest extends \PHPUnit_Framework_TestCase {

    public function test_get_empty() {
        $cache = new KpiCache();
        $this->assertNull($cache->get(1));
    }

    public function test_get() {
        $cache = new KpiCache();
        $kpi = Factory::kpis()[0];
        $measurables = Factory::measurables();

        // Set 3 kpis on one measurable.
        $cache->put($kpi, $measurables[0], 100);
        $cache->put($kpi, $measurables[0], 90);
        $cache->put($kpi, $measurables[0], 120);

        $values = $cache->get($kpi->getId());
        $this->assertEquals(1, count($values));
        $this->assertEquals(100, $values[1][0]->getTransientValue());
        $this->assertEquals(90, $values[1][1]->getTransientValue());
        $this->assertEquals(120, $values[1][2]->getTransientValue());

        $cache->flush();

        // Kpis on more measurables.
        $cache->put($kpi, $measurables[0], 100);
        $cache->put($kpi, $measurables[0], 90);
        $cache->put($kpi, $measurables[1], 120);

        $values = $cache->get($kpi->getId());
        $this->assertEquals(2, count($values));
        $this->assertEquals(100, $values[1][0]->getTransientValue());
        $this->assertEquals(90, $values[1][1]->getTransientValue());
        $this->assertEquals(120, $values[2][0]->getTransientValue());
    }

    public function test_flush() {
        $cache = new KpiCache();
        $kpi = Factory::kpis()[0];
        $measurables = Factory::measurables();

        $cache->put($kpi, $measurables[0], 100);
        $values = $cache->get($kpi->getId());
        $this->assertEquals(1, count($values));

        $cache->flush();
        $values = $cache->get($kpi->getId());
        $this->assertEquals(0, count($values));
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
