<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Resource Repository class
 *
 * @package App\Repositories
 *
 * 16/03/2021 12:28
 */
abstract class ResourceRepository
{
    /**
     * @var Model
     */
	protected $model;

    /**
     * Paginate
     *
     * @param $by
     * @param $order
     * @param $nbPerPage
     * @return mixed
     */
	public function paginate($by, $order, $nbPerPage)
    {
        return $this->model->orderBy($by, $order)->paginate($nbPerPage);
    }

    /**
     * @param array $inputs
     * @return mixed
     */
	public function store(array $inputs)
	{
		return $this->model->create($inputs);
	}

    /**
     * @param $id
     * @return mixed
     */
	public function getById($id)
	{
		return $this->model->findOrFail($id);
	}

    /**
     * @param $id
     * @param array $inputs
     * @return void
     */
	public function update($id, array $inputs)
	{
		$m = $this->getById($id);
		$m->update($inputs);
	}

    /**
     * @param $id
     * @return void
     */
	public function destroy($id)
	{
		$m = $this->getById($id);
		$m->delete();
	}

    /**
     * Récupérer le model en cours
     *
     * @return Model
     */
	public function getModel()
	{
		return $this->model;
	}

}
