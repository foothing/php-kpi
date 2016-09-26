<?php namespace Foothing\Kpi\Tests\Models;

use Foothing\Kpi\Models\AggregatorConfigCollection;
use Foothing\Kpi\Tests\Mocks\AggregatorConfig;

class AggregatorConfigCollectionTest extends \PHPUnit_Framework_TestCase {

    public function test_add() {
        $collection = new AggregatorConfigCollection();
        $collection->add(new AggregatorConfig(1, 100), 0.75);

        /* @var $config \Foothing\Kpi\Models\TransientAggregatorConfig */
        $config = $collection->items()[1];

        $this->assertEquals(1, $config->getAggregatorId());
        $this->assertEquals(0.75, $config->get(100));
    }

    public function test_items() {
        $collection = new AggregatorConfigCollection();
        $this->assertEmpty($collection->items());
        $collection->add(new AggregatorConfig(1,0,0), 1);
        $this->assertEquals(1, count($collection->items()));
    }

}