<?php namespace Foothing\Kpi\Repositories;

use Foothing\Kpi\Calculator\Variable;

interface DatasetRepositoryInterface {

    /**
     * The interface implementation should determine
     * which dataset contains the requested variable.
     *
     * @param Variable $variable
     *
     * @return array|Collection the whole measurables
     * variable dataset.
     */
    public function findByTime(Variable $variable);

}
