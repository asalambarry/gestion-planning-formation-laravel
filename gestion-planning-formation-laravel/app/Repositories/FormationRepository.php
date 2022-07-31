<?php


namespace App\Repositories;

use App\Models\Formation;

class FormationRepository extends ResourceRepository
{
    /**
     * FormationRepository constructor.
     * @param Formation $model
     */
    public function __construct(Formation $model)
    {
        $this->model = $model;
    }

    public function getFormationForSelect() {

        $datas = $this->model->orderBy('intitule', 'asc')->get();

        $formations = [];

        foreach ($datas as $item) {
            $formations[$item->id] = $item->intitule;
        }

        return $formations;
    }
}
