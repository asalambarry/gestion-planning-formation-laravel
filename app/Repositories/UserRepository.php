<?php
namespace App\Repositories;

use App\Models\{User};

/**
 * Manager for User model
 *
 * @package App\Repositories
 *
 * @date 16/03/2021 12:28
 */
class UserRepository extends ResourceRepository
{

    /**
     * Constructor
     *
     * @param $model - User
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @param $type
     * @param $perPage
     * @return mixed
     */
    public function getUsersByType($type, $perPage) {
        return $this->getModel()
                ->where('type', $type)
                ->orderBy('id', 'desc')
                ->paginate($perPage);
    }

    /**
     * @param string $q
     * @param $nb
     * @return mixed
     */
    public function rechercherUser($q, $nb)
    {
        return $this->model->where(function ($query) use ($q) {

            $item = explode(' ', $q)[0];

            $query->orWhere('nom', 'LIKE', '%'.$item.'%')
                ->orWhere('prenom', 'LIKE', '%'.$item.'%')
                ->orWhere('login', 'LIKE', '%'.$item.'%');

        })->paginate($nb);

    }

    /**
     * Liste des users
     *
     * @param string $type
     * @return array
     */
    public function getUsersForSelect($type = 'all')
    {
        if ($type === 'enseignant') {
            $datas = $this->model->where('type', 'enseignant')->orderBy('nom', 'asc')->get();
        }
        else {
            $datas = $this->model->orderBy('nom', 'asc')->get();
        }

        $users = [];

        foreach ($datas as $item) {
            $users[$item->id] = $item->nom.' '.$item->prenom;
        }

        return $users;
    }

}
