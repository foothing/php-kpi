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

    public function execute($formula, array $variables = null, &$compiled = null) {
        // Compile formula replacing variables.
        $compiled = $this->replaceVariables($formula, $variables);

        // Plug and execute math parser.
        $parser = new StdMathParser();
        $evaluator = new Evaluator();

        try {
            $parsed = $parser->parse($compiled->formula);
            $evaluator->setVariables($compiled->variables);
            //print("$formula / $compiled / $parsed<br>");
            //\Log::debug("$formula / $compiled / $parsed");
            return $parsed->accept($evaluator);
        } catch (SyntaxErrorException $ex) {
            throw new \Exception("$formula | $compiled has syntax error.");
        } catch (DivisionByZeroException $ex) {
            throw new \Exception("$formula | $compiled division by zero.");
        }
    }

    public function replaceVariables($formula, $variables) {
        if (! $variables) {
            return (object)[
                "formula" => $formula,
                "originalFormula" => $formula,
                "variables" => null,
            ];
        }

        $originalFormula = $formula;
        $currentChar = "a";
        $outputVariables = [];

        foreach ($variables as $variable) {
            $formula = str_replace($variable->raw, $currentChar, $formula, $count);

            if ($count) {
                $outputVariables[ $currentChar ] = $variable->value;
                $currentChar++;
            }
        }

        return (object)[
            "formula" => $formula,
            "originalFormula" => $originalFormula,
            "variables" => $outputVariables,
        ];
    }
}
