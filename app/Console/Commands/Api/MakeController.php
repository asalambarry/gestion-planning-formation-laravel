<?php

namespace App\Console\Commands\Api;

use App\Console\Commands\Core\Helpers;
use App\Console\Commands\Core\Reader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Construire un outil de génération de controlleur
 *
 * @package App\Console\Commands\Api
 * @date 11/07/2019
 * @author Jerome Dh <jdieuhou@gmail.com>
 */
class MakeController extends Command
{

	/**
	 * Trait des utilitaires
	 *
	 * Trait Helpers
	 */
	 use Helpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:controller
							{--c|classe=all : To generated the controllers for the classes}';

    /**
     * The console command description.
     *
     * @var string
     */
	protected $description = 'Create controllers for Api';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $output_dir = app_path('Http\Controllers\Api');

        $dirname = database_path('migrations');
        try {
            //Retrieve the argument value
            $classe = $this->option('classe');
            $reader = new Reader($dirname);

            if($classe == 'all') {
                $tabClasses = $reader->getAllClasses();
            } else {
                $tabClasses = $reader->getOnlyClasses([$classe]);
            }
            $this->traitement($tabClasses, $output_dir, 'controller');
        }
        catch (\RuntimeException $e) {
            $this->error($e->getMessage());
        }

        return true;
    }

	/**
	 * Get the controller content
	 *
	 * @param $nom - Nom de la classe
	 * @param $tab - Tableau
	 *
	 * @return string
	 */
	protected function getContent($nom, array $tab)
	{
		$lowerNom = strtolower($nom);
		$upperNom = ucfirst($nom);

		$classe = '<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

use App\Repositories\\'.$upperNom.'Repository;
use App\Repositories\ResponseRepository;
use App\Http\Resources\\'.$upperNom.'Resource;

use App\Library\CustomFunction;

use App\Http\Controllers\Controller;

/**
 * Class '.$upperNom.'Controller
 *
 * @package App\Http\Controllers\Api
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
class '.$upperNom.'Controller extends Controller
{
    /**
	 * Trait for utilities functions
	 *
	 * @trait CustomFunction
	 */
	use CustomFunction;

    /**
	 * The repository for the '.$upperNom.' model
	 *
	 * @var '.$upperNom.'Repository
	 */
	protected $'.$lowerNom.'Repository;

	/**
	 * The response repository
	 *
	 * @var ResponseRepository
	 */
    protected $fullResponse;

	/**
	 * The table name
	 *
	 * @var string
	 */
	protected $table = \''.$lowerNom.'s\';

	/**
	 * Constructor
	 *
	 * @param '.$upperNom.'Repository $'.$lowerNom.'Repository
	 * @param ResponseRepository $fullResponse
	 */
    public function __construct(
		'.$upperNom.'Repository $'.$lowerNom.'Repository,
		ResponseRepository $fullResponse)
	{

  		$this->'.$lowerNom.'Repository = $'.$lowerNom.'Repository;
  		$this->fullResponse = $fullResponse;

    }

	/**
	 * Find the '.$lowerNom.'s
	 *
	 * @param $request - Request with datas
	 *			! [q] - query String
	 *			! [sort] - Field to do the sorting
	 *			! [order] - ASC/DESC
	 *			! [per] - Max result per page
	 *			! [page] - Current number page
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function getAll(Request $request)
    {
        $url = $request->fullUrl();
		$content = \'\';
		$error = \'\';
		$code = 200;

		$ret = $this->validerRecherche(
			$request->all(),
			$this->'.$lowerNom.'Repository->getModel()->getAttrs()
		);

		if($ret !== true) {

			$error = $ret;
			$code = 400;
		}
		else {
			//Requête
			$result = $this->'.$lowerNom.'Repository
					->find'.$upperNom.'(
						$request->input(\'q\') ?? \'\',
						$request->input(\'sort\'),
						$request->input(\'order\'),
						$request->input(\'per\'),
						$request->input(\'page\')
					);

			$content = '.$upperNom.'Resource::collection($result);

		}

		return $this->fullResponse->make($content, $error, $code, $url);

    }

	/**
	 * Save a '.$lowerNom.'
	 *
	 * @param $request - Request with datas
	 *'.
$this->getDonneesParams($tab).'
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function create(Request $request)
    {
        return $this->saveOrUpdate($request, $create = true);
    }

	/**
     * Show a '.$lowerNom.'
     *
	 * @param $request - Request with datas
	 *
	 *			! [id] - Identifier
	 *
	 * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
		return $this->obtainOrDelete($request, $obtain = true);
    }

	/**
     * Update a '.$lowerNom.'
     *
	 * @param $request - Request with datas
	 *
	 *			! [id] - Identifiant'.
$this->getDonneesParams($tab).'
	 *
	 * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        return $this->saveOrUpdate($request, $create = false);
    }

	/**
     * Delete a '.$lowerNom.'
     *
     * @param $request - Request with datas
	 *
	 *			! [id] - Identifier
	 *
	 * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
		return $this->obtainOrDelete($request, $obtain = false);
    }

	/**
	 * Create or update a '.$lowerNom.'
	 *
	 * @param Request $request
	 * @param $create - Create/Update
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function saveOrUpdate(Request $request, $create)
	{
		$url = $request->fullUrl();
		$content = \'\';
		$error = \'\';
		$code = 200;

		$id = $request->input(\'id\');

		$data = $request->all();

		$valid = $this->valider'.$upperNom.'($data, $create, $id);

		if($valid === true)
		{
			if($create) {

				$'.$lowerNom.' = $this->'.$lowerNom.'Repository->store($data);

				if($'.$lowerNom.') {
					$content = $'.$lowerNom.';
				}
				else {
					$error = \'Echec d\\\'enregistrement\';
					$code = 500;
				}
			}
			else {
				$this->'.$lowerNom.'Repository->update($id, $data);
				$content = \'Mise à jour effectuée avec succès\';
			}
		}
		else {
			$error = $valid;
			$code = 400;
		}

		return $this->fullResponse->make($content, $error, $code, $url);

	}

	/**
	 * Get or delete a '.$lowerNom.'
	 *
	 * @param Request $request
	 * @param $obtain - Get/Delete
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function obtainOrDelete(Request $request, $obtain)
	{
		$url = $request->fullUrl();
		$content = \'\';
		$error = \'\';
		$code = 200;

		$id = $request->input(\'id\');
		$valid = $this->validerId($id, $this->table);

		if($valid !== true) {
			$error = $valid;
			$code = 400;
		}
		else
		{
			if($obtain) {
				$'.$lowerNom.' = $this->'.$lowerNom.'Repository->getById($id);
				$content = [
					\''.$lowerNom.'\' => $'.$lowerNom.','.$this->getRelationnalData($lowerNom, $tab).'
				];
			}
			else {
				$this->'.$lowerNom.'Repository->destroy($id);
				$content = \'Suppression effectuée avec succès\';
			}
		}

		return $this->fullResponse->make($content, $error, $code, $url);
	}

	/**
     * Validate a '.$upperNom.'
     *
     * @param array $data
     * @param boolean $create - Create/Update
     * @param $id - Existing identifier
     *
     * @return mixed
     */
    protected function valider'.$upperNom.'(array $data, $create, $id = 0)
    {
        //Check the existence of $id in table
        if( ! $create) {
            $validId = $this->validerId($id, $this->table);

            if($validId !== true) {
                return $validId;
            }
            '.$this->getUniqueRules($tab, 1).'
        }
        else {
            '.$this->getUniqueRules($tab, 2).'
        }
        '.$this->getSaveDatas($tab).'

        $tab = array_merge($tab1, $tab2);

        $validator = Validator::make($data, $tab);

        return $validator->fails() ? $validator->errors() : true;

    }

	/**
	 * Zone to remove, just for the tests
	 *
	 * @param Request $request
     * @return \Illuminate\Http\JsonResponse
	 */
	public function test(Request $request)
	{
		$url = $request->fullUrl();
		$content = \'fullWork\';
		return $this->fullResponse->make($content, \'\', 200, $url);
	}

}';

	return $classe;

	}

	/**
	 * Récuperer les données du tableau
	 *
	 * @param $datas
	 *
	 * @return string
	 */
	protected function getDonneesParams(array $datas)
	{
		$ch = '';

        foreach($datas as $field => $attrs)
		{
            if( ! empty($field) and $field != 'id') {
                $ch .= '
	 *			! [' . trim($field) . '] - ' . $this->valOfType($attrs);
            }
		}

		return $ch;

	}

	/**
	 * Trouver et faire correspondre un type de données
	 *
	 * @param $attrs
	 *
	 * @return string
	 */
	protected function valOfType($attrs)
	{
        switch ($attrs['type']) {
            case 'email':
                $ret = 'Email valide';
                break;
            case 'date':
                $ret = 'Date valide';
                break;
            case 'dateTime':
                $ret = 'Datetime';
                break;
            case 'integer':
            case 'double':
                $ret = 'Nombre';
                break;
            case 'char':
                $ret = 'Char';
                break;
            case 'text':
                $ret = 'Text';
                break;
            default:
                $ret = 'Chaine';
        }

		return $ret;

	}

    /**
     * Get the relationnal associated tables
     *
     * @param $tableName
     * @param array $datas
     * @return string
     */
    protected function getRelationnalData($tableName, array $datas) : string {
        $ret = '';
        foreach($datas as $name => $attrs) {
            if( ! empty($name) and $name != 'id' and \Str::endsWith($name, '_id')) {
                $className = substr($name, 0, stripos($name, '_id'));
                $ret .= '
                    \''.$className.'\' => $'.$tableName.'->'.$className.'()->first(),';
            }
        }

        return $ret;
    }

}
