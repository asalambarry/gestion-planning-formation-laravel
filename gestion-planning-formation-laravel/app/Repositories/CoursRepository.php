<?php


namespace App\Repositories;

use App\Models\Cours;

class CoursRepository extends ResourceRepository
{

    /**
     * CoursRepository constructor.
     * @param Cours $model
     */
    public function __construct(Cours $model)
    {
        $this->model = $model;
    }

    /**
     * @param $q
     * @param $nb
     * @return mixed
     */
    public function rechercherCours($q, $nb) {
        return $this->model->where('intitule', 'LIKE', '%'.$q.'%')->paginate($nb);
    }

    /**
     * @param $login
     * @param $perPage
     * @return mixed
     */
    public function coursParEnseignant($login, $perPage) {

        if ($login and $login !== 'tous') {
            return $this->getModel()->where('user_id', $login)->orderBy('intitule', 'asc')->paginate($perPage);
        }
        else {
            return $this->getModel()->orderBy('intitule', 'asc')->paginate($perPage);
        }
    }

    /**
     * Liste de cours
     *
     * @return array
     */
    public function getCoursForSelect()
    {
        if (auth()->user()->isEnseignant())
        {
            $datas = auth()->user()->coursEnseignant()->orderBy('intitule', 'asc')->get();
        }
        else {
            $datas = $this->model->orderBy('intitule', 'asc')->get();
        }

        $cours = [];

        foreach ($datas as $item) {
            $cours[$item->id] = $item->intitule;
        }

        return $cours;
    }

}
