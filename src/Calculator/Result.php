<?php namespace Foothing\Kpi\Calculator;

class Result {

    /**
     * The raw formula result.
     * @var float
     */
    public $value;

    /**
     * The kpi quantized value.
     * @var float
     */
    public $quantizedValue;

    /**
     * The kpi computed formula.
     * @var string
     */
    public $originalFormula;

    /**
     * The kpi formula with variables replacement.
     * @var
     */
    public $formula;

    /**
     * Variables values.
     * @var
     */
    public $variables;

    public function __construct($formula = null, $originalFormula = null, $variables = null) {
        $this->formula = $formula;
        $this->originalFormula = $originalFormula;
        $this->variables = $variables;
    }
}