<?php namespace Foothing\Kpi\Calculator;

interface CalculatorInterface {

    public function execute($formula, array $variables);

}