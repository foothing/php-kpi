<?php namespace Foothing\Kpi\Tests;

use Foothing\Kpi\Calculator\Exceptions\InvalidFormulaException;
use Foothing\Kpi\Calculator\FormulaParser;
use Foothing\Kpi\Calculator\Variable;

class FormulaParserTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Calculator
     */
    protected $parser;

    public function setUp() {
        $this->parser = new FormulaParser();
    }

    public function testParse_empty_formula_throws_exception() {
        $this->setExpectedException(InvalidFormulaException::class);
        $this->parser->parse("");
    }

    public function testParse_single_variable() {
        $formula = "{AUTO_RC(TD,2015,09,12,0,0)}";
        $variable = $this->parser->parse($formula)[0];
        $this->assertEquals("{AUTO_RC(TD,2015,09,12,0,0)}", $variable->raw);
        $this->assertEquals("AUTO_RC", $variable->name);
        $this->assertEquals(2015, $variable->year);
        $this->assertEquals("09", $variable->month);
        $this->assertEquals("12", $variable->day);
        $this->assertEquals("0", $variable->weekOfYear);
        $this->assertEquals("0", $variable->weekOfYear);
        $this->assertEquals(Variable::$TODATE, $variable->type);
    }

    public function testParse_real_world_example() {
        $formula = "{AUTO_RC(TD,CUR,09,12,0,0)} / {AUTO_RC(TD,PREV,09,12,0,0)}";
        $variables = $this->parser->parse($formula);
        $this->assertEquals("AUTO_RC", $variables[0]->name);
        $this->assertEquals(date('Y'), $variables[0]->year);
        $this->assertEquals("09", $variables[0]->month);
        $this->assertEquals("12", $variables[0]->day);
        $this->assertEquals("0", $variables[0]->weekOfYear);
        $this->assertEquals("0", $variables[0]->weekOfYear);
        $this->assertEquals(Variable::$TODATE, $variables[0]->type);
        $this->assertEquals("AUTO_RC", $variables[1]->name);
        $this->assertEquals(date('Y') - 1, $variables[1]->year);
        $this->assertEquals("09", $variables[1]->month);
        $this->assertEquals("12", $variables[1]->day);
        $this->assertEquals("0", $variables[1]->weekOfYear);
        $this->assertEquals("0", $variables[1]->weekOfYear);
        $this->assertEquals(Variable::$TODATE, $variables[1]->type);
    }

    public function test_compile() {
        $formula = "{AUTO_RC(TD,CUR,09,12,0,0)} / {AUTO_RC(TD,PREV,09,12,0,0)}";

        $variables0 = new Variable();
        $variables0->raw = "{AUTO_RC(TD,CUR,09,12,0,0)}";
        $variables0->value = 100;

        $variables1 = new Variable();
        $variables1->raw = "{AUTO_RC(TD,PREV,09,12,0,0)}";
        $variables1->value = 0.5;

        $compiled = $this->parser->compile($formula, [$variables0, $variables1]);
        $this->assertEquals("100 / 0.5", $compiled);
    }

    public function test_parseYear() {
        $this->assertEquals(2015, $this->parser->parseYear(2015));
        $this->assertEquals(date('Y'), $this->parser->parseYear("CUR"));
        $this->assertEquals(date('Y') - 1, $this->parser->parseYear("PREV"));
    }

    public function test_parseType() {
        $this->assertNull($this->parser->parseType(""));
        $this->assertNull($this->parser->parseType("X"));
        $this->assertEquals(Variable::$REALTIME, $this->parser->parseType("RT"));
        $this->assertEquals(Variable::$TODATE, $this->parser->parseType("TD"));
    }
}
