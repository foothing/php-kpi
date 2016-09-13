<?php namespace Foothing\Kpi\Tests;

use FormulaParser\FormulaParser;
use MathParser\Interpreting\Evaluator;
use MathParser\Lexing\TokenType;
use MathParser\StdMathParser;

class FooTest extends \PHPUnit_Framework_TestCase {

    public function testFoo() {}

    public function _testDenissimon() {

        $AUTO_AC = "A";
        $AUTO_AP = "B";

        $kpi_incassi_danni = "A/B-1";

        $parser = new FormulaParser($kpi_incassi_danni, 2);
        $parser->setValidVariables(['A', 'B']);
        $parser->setVariables([
            'A' => 1,
            'B' => 1
        ]);
        var_dump($parser->getResult());
    }

    public function _testMossadal() {
        $parser = new StdMathParser();
        $evaluator = new Evaluator();
        $parsed = $parser->parse("A/A+(1/2)");
        $evaluator->setVariables(['A' => 10, 'AB' => 1]);
        $result = $parsed->accept($evaluator);
        var_dump($parser->getTokenList());

        foreach($parser->getTokenList() as $token) {
            if ($token->getType() == TokenType::Identifier) {
                print $token->getValue() . ": identifier\n";
            }
        }
    }

    public function formula() {
        $kpi0 = "{AUTO_RC(2015,09,12,0,0)}";
    }
}