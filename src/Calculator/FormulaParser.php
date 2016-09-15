<?php namespace Foothing\Kpi\Calculator;

use Foothing\Kpi\Calculator\Exceptions\InvalidFormulaException;

class FormulaParser {

    protected static $REGEX_VARIABLE = "/\{([a-zA-z_]+)\((TD|RT)(,[0-9]{4}|,CUR|,PREV)(,[0-9]{2})?(:?,[0-9]{2})?(:?,[0-9])?(:?,[0-9]{1,2})?\)\}/";

    /**
     * Parse variables from a math formula. Variables are expected to be
     * in the form of {NAME(SAMPLE,YEAR,MONTH,DAY,WEEKOFYEAR,WEEKOFMONTH)} where
     * - SAMPLE sample type, TD (to date) or RT (real time)
     * - NAME is [a-zA-z_]+
     * - YEAR is 4 digit or CUR (current year) or PREV (previous year)
     * - MONTH 2 digits
     * - DAY 2 digits
     * - WEEKOFYEAR 2 digits
     * - WEEKOFMONTH 2 digits
     *
     * @param $formula
     *
     * @return array|null
     * @throws Exceptions\InvalidFormulaException
     */
    public function parse($formula) {

        if (! $formula) {
            throw new InvalidFormulaException("Formula is empty.");
        }

        //$sample = "{AUTO_RC(TD,2015,09,12,0,0)}";
        preg_match_all(self::$REGEX_VARIABLE, $formula, $result);
//var_dump($result);
        // No variables in this formula.
        if (! $result) {
            return null;
        }

        // Parsed variables array.
        $variables =[];

        // Each record contains N matches, one for each variable.
        // Note offset $result[0] is the global regex match (the whole thing).
        $matches = count($result[1]);

        // Build the variables.
        for ($i = 0; $i < $matches; $i++) {
            $variables[$i] = new Variable();
            $variables[$i]->raw = $result[0][$i];
            $variables[$i]->name = $result[1][$i];
            $variables[$i]->type = $this->parseType($result[2][$i]);
            $variables[$i]->year = $this->parseYear(ltrim($result[3][$i], ","));
            $variables[$i]->month = ltrim($result[4][$i], ",");
            $variables[$i]->day = ltrim($result[5][$i], ",");
            $variables[$i]->weekOfYear = ltrim($result[6][$i], ",");
            $variables[$i]->weekOfMonth = ltrim($result[7][$i], ",");
        }

        return $variables;
    }

    public function compile($formula, array $variables) {
        foreach ($variables as $variable) {
            $formula = str_replace($variable->raw, $variable->value, $formula);
        }

        return $formula;
    }

    public function parseYear($year) {
        if ($year == "CUR") {
            return date('Y');
        }

        if ($year == "PREV") {
            return date('Y') - 1;
        }

        return $year;
    }

    public function parseType($type) {
        if ($type == "TD") {
            return Variable::$TODATE;
        }

        if ($type == "RT") {
            return Variable::$REALTIME;
        }

        return null;
    }
}
