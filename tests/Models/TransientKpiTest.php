<?php namespace Foothing\Kpi\Tests\Models;

use Foothing\Kpi\Models\TransientKpi;
use Foothing\Kpi\Tests\Mocks\Kpi;

class TransientKpiTest extends \PHPUnit_Framework_TestCase {

    public function test_quantize() {
        $transient = new TransientKpi(new Kpi(100, "foo", "1+1", [1, 2, 3]), 0);
        $this->assertEquals(0, $transient->quantizeTransientValue());

        $transient = new TransientKpi(new Kpi(100, "foo", "1+1", [1, 2, 3]), 1);
        $this->assertEquals(1, $transient->quantizeTransientValue());

        $transient = new TransientKpi(new Kpi(100, "foo", "1+1", [1, 2, 3]), 1.9);
        $this->assertEquals(1, $transient->quantizeTransientValue());

        $transient = new TransientKpi(new Kpi(100, "foo", "1+1", [1, 2, 3]), 2.01);
        $this->assertEquals(2, $transient->quantizeTransientValue());

        $transient = new TransientKpi(new Kpi(100, "foo", "1+1", [1, 2, 3]), 3);
        $this->assertEquals(3, $transient->quantizeTransientValue());

        $transient = new TransientKpi(new Kpi(100, "foo", "1+1", [1, 2, 3]), 3.01);
        $this->assertEquals(3, $transient->quantizeTransientValue());

        $transient = new TransientKpi(new Kpi(100, "foo", "1+1", [1, 2, 3]), 100);
        $this->assertEquals(3, $transient->quantizeTransientValue());
    }
}
