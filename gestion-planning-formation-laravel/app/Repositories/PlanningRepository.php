<?php

namespace App\Repositories;

use App\Models\Planning;

class PlanningRepository extends ResourceRepository
{
    /**
     * PlanningRepository constructor.
     * @param Planning $model
     */
    public function __construct(Planning $model)
    {
        $this->model = $model;
    }

}
