<?php namespace Foothing\Kpi\Calculator\Mossadal;

use Foothing\Kpi\Calculator\CalculatorInterface;
use Foothing\Kpi\Calculator\FormulaParser;
use MathParser\Exceptions\DivisionByZeroException;
use MathParser\Exceptions\SyntaxErrorException;
use MathParser\Interpreting\Evaluator;
use MathParser\StdMathParser;

class Calculator implements CalculatorInterface {

    /**
     * @var \Foothing\Kpi\Calculator\FormulaParser
     */
    protected $parser;

    public function __construct(FormulaParser $parser) {
        $this->parser = $parser;
    }

    public function execute($formula, array $variables = null) {
        // Compile formula replacing variables.
        if ($variables) {
            $compiled = $this->parser->compile($formula, $variables);
        } else {
            $compiled = $formula;
        }

        // Plug and execute math parser.
        $parser = new StdMathParser();
        $evaluator = new Evaluator();

        try {
            $parsed = $parser->parse($compiled);
            return $parsed->accept($evaluator);
        } catch (SyntaxErrorException $ex) {
            throw new \Exception("$formula | $compiled has syntax error.");
        } catch (DivisionByZeroException $ex) {
            throw new \Exception("$formula | $compiled division by zero.");
        }
    }
}
