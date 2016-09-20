<?php namespace Foothing\Kpi\Repositories;

use Foothing\Kpi\Models\KpiInterface;

interface KpiRepositoryInterface {

    public function all();
    public function store(KpiInterface $kpi, $value);

}
