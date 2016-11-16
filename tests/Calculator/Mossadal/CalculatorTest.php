<?php namespace Foothing\Kpi\Tests\Calculator\Mossadal;

use Foothing\Kpi\Calculator\FormulaParser;
use Foothing\Kpi\Calculator\Mossadal\Calculator;
use Foothing\Kpi\Calculator\Variable;
use MathParser\Interpreting\Evaluator;
use MathParser\StdMathParser;

class CalculatorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider provideFormulas
     */
    public function test_execute_without_variables($formula, $result) {
        $calculator = new Calculator(new FormulaParser());
        $this->assertEquals($result, $calculator->execute($formula));
    }

    public function testa() {
        $p = new StdMathParser();
        $c = new Evaluator();

        $parsed = $p->parse("1+a");
        $c->setVariables(["a" => -1]);
        $parsed->accept($c);
    }

    /**
     * @dataProvider provideValues
     */
    public function test_execute_with_variables($formula, $variables, $result) {
        $calculator = new Calculator(new FormulaParser());
        $this->assertEquals($result, $calculator->execute($formula, $variables));
    }

    /**
     * @dataProvider provideReplaceVariables
     */
    public function test_replace_variables($formula, $variables, $result) {
        $calculator = new Calculator(new FormulaParser());
        $this->assertEquals($result, $calculator->replaceVariables($formula, $variables)->formula);
    }

    public function provideFormulas() {
        return [
            ["0", 0],
            ["0+0", 0],
            ["-1+1", 0],
            ["0*1", 0],
            ["0/1", 0],
            ["-1 * 0.4", -0.4],
            ["+1 -1", 0],
            ["+1 -1", 0],
        ];
    }

    public function provideValues() {
        $variables = [
            new Variable("{A(CUR,09,12,0,0,TD)}", 1),
            new Variable("{B(CUR,09,12,0,0,TD)}", -1),
            new Variable("{C(CUR,09,12,0,0,TD)}", 0),
            new Variable("{D(CUR,09,12,0,0,TD)}", 0.55),
        ];

        return [
            ["0 - 1", $variables, -1],
            ["{A(CUR,09,12,0,0,TD)} - 1", $variables, 0],
            ["{A(CUR,09,12,0,0,TD)} - {B(CUR,09,12,0,0,TD)} + 1", $variables, 3],
            ["{A(CUR,09,12,0,0,TD)} - {C(CUR,09,12,0,0,TD)} + 1", $variables, 2],
            ["{A(CUR,09,12,0,0,TD)} * {D(CUR,09,12,0,0,TD)} + 1", $variables, 1.55],
            ["{A(CUR,09,12,0,0,TD)} / {D(CUR,09,12,0,0,TD)} + 1", $variables, 1 / 0.55 + 1],
        ];
    }

    public function provideReplaceVariables() {
        $variables = [
            new Variable("{A(CUR,09,12,0,0,TD)}", 0),
            new Variable("{B(CUR,09,12,0,0,TD)}", 0),
            new Variable("{C(CUR,09,12,0,0,TD)}", 0),
            new Variable("{D(CUR,09,12,0,0,TD)}", 0),
            new Variable("{E(CUR,09,12,0,0,TD)}", 0),
        ];

        return [
            ["0 - 1", $variables, "0 - 1"],
            ["{A(CUR,09,12,0,0,TD)} - 1", $variables, "a - 1"],
            ["{A(CUR,09,12,0,0,TD)} - {A(CUR,09,12,0,0,TD)} + 1", $variables, "a - a + 1"],
            ["{A(CUR,09,12,0,0,TD)} - {B(CUR,09,12,0,0,TD)} + 1", $variables, "a - b + 1"],
        ];
    }
}
