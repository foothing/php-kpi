<?php namespace Foothing\Kpi\Tests\Calculator\Mossadal;

use Foothing\Kpi\Calculator\FormulaParser;
use Foothing\Kpi\Calculator\Mossadal\Calculator;
use Foothing\Kpi\Calculator\Variable;

class CalculatorTest extends \PHPUnit_Framework_TestCase {

    public function test_execute_without_variables() {
        $calculator = new Calculator(new FormulaParser());
        $this->assertEquals(0, $calculator->execute("0"));
        $this->assertEquals(0, $calculator->execute("0+0"));
        $this->assertEquals(0, $calculator->execute("-1+1"));
        $this->assertEquals(0, $calculator->execute("0*1"));
        $this->assertEquals(0, $calculator->execute("0/1"));
    }

    public function test_execute_with_variables() {
        $calculator = new Calculator(new FormulaParser());

        $variables0 = new Variable();
        $variables0->raw = "{AUTO_RC(CUR,09,12,0,0,TD)}";
        $variables0->value = 100;

        $variables1 = new Variable();
        $variables1->raw = "{AUTO_RC(PREV,09,12,0,0,TD)}";
        $variables1->value = 0.5;

        $formula = "$variables0->raw / $variables1->raw * 0.5";

        $this->assertEquals(100/0.5*0.5, $calculator->execute($formula, [$variables0, $variables1]));
    }
}
