<?php namespace Foothing\Kpi\Tests\Models;

use Foothing\Kpi\Models\TransientAggregatorConfig;

class TransientAggregatorConfigTest extends \PHPUnit_Framework_TestCase {

    public function test_add() {
        $config = new TransientAggregatorConfig(1);
        $config->add(100, 0.75);
        $config->add(101, 0.5);

        $this->assertEquals(0.75, $config->get(100));
        $this->assertEquals(0.5, $config->get(101));
    }

    public function test_get_invalid_kpi() {
        $this->setExpectedException("Exception");
        $config = new TransientAggregatorConfig(1);
        $config->get(100);
    }

    public function test_has() {
        $config = new TransientAggregatorConfig(1);
        $config->add(100, 0.75);

        $this->assertTrue($config->has(100));
        $this->assertFalse($config->has(101));
    }

}
