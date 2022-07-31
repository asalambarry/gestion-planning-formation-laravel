<?php

namespace App\Console\Commands\Admin;

use App\Console\Commands\Core\Helpers;
use App\Console\Commands\Core\Reader;
use Illuminate\Console\Command;

/**
 * Class MakeController
 *
 * @package App\Console\Commands\Admin
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 07/02/2020
 */
class MakeController extends Command
{
    /**
     * Trait Helpers
     */
    use Helpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:controller
                            {--c|classe=all : To generated the controllers for the classes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create controllers for admin';

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
        $output_dir = app_path('Http\Controllers\Admin');

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
     * The content for specific controller
     *
     * @param $name
     * @param array $datas
     * @return string
     */
    protected function getContent($name, array $datas) {

        $lowerNom = strtolower($name);
        $upperNom = ucfirst($name);

        return '<?php
namespace App\Http\Controllers\Admin;

use App\Library\CommonForUsers;
use App\Library\CustomFunction;

use App\Repositories\\'.$upperNom.'Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Http\Controllers\Controller;

/**
 * Class '.$upperNom.'Controller
 *
 * @package App\Http\Controllers\Admin
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
class '.$upperNom.'Controller extends Controller
{
    /**
     * Trait of utilities functions
     *
     * @trait CustomFunction
     */
    use CustomFunction;

    /**
     * Trait for commons operations
     *
     * @trait CommonForUsers
     */
    use CommonForUsers;

    /**
     * The repository for the model '.$upperNom.'
     *
     * @var '.$upperNom.'Repository
     */
    protected  $'.$lowerNom.'Repository;

    /**
     * Name of the DB table
     * @var string
     */
    protected $table = \''.$lowerNom.'s\';

    /**
     * Path and base dir
     */
    protected $base_view_dir = \'admin.\';
    protected $base_path = \'admin/\';

    /**
     * Number of pages elements
     * @var int
     */
    protected $nbPerPage = 10;

    /**
     * Constructor
     *
     * @param  '.$upperNom.'Repository  $'.$lowerNom.'Repository
     */
    public function __construct('.$upperNom.'Repository $'.$lowerNom.'Repository)
    {
        //The middlewares
        $this->middleware(\'auth\');
        $this->middleware(\'admin\');

        $this->'.$lowerNom.'Repository = $'.$lowerNom.'Repository;
    }

    /**
     * List of '.$lowerNom.'s
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
		// Log::alert(\'index: \');
        $'.$lowerNom.'s = $this->'.$lowerNom.'Repository->getPaginateByOrder(\'id\', $this->nbPerPage);
        return view($this->base_view_dir.\''.$lowerNom.'s.index\', compact(\''.$lowerNom.'s\'));
    }

    /**
     * Show the form to create a '.$upperNom.'
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
		// Log::alert(\'create: \');'
       .$this->getStatementsForOthersSelect($lowerNom, $datas).'
        return view($this->base_view_dir.\''.$lowerNom.'s.create\''.$this->getStatementsForCompact($datas, true).');
    }

    /**
     * Save a '.$upperNom.'
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
		// Log::alert(\'store: \');
		return $this->saveOrUpdate($request, true, 0);
    }

    /**
     * Show a specific '.$upperNom.'
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show($id)
    {
		// Log::alert(\'show: \' . $id);
        return $this->obtainOrDelete(true, $id);
    }

    /**
     * Show a '.$upperNom.' for update
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
		// Log::alert(\'edit: \' . $id);
        $validator = $this->validerIdSession($id, $this->table);

        if($validator === true)
        {
            $'.$lowerNom.' = $this->'.$lowerNom.'Repository->getById($id);
            '.$this->getStatementsForOthersSelect($lowerNom, $datas).'
            return view($this->base_view_dir.\''.$lowerNom.'s.edit\',
                compact(\''.$lowerNom.'\''.$this->getStatementsForCompact($datas, false).'));
        }
        else
        {
            $info = \''.$upperNom.' introuvable !\';
            return back()
                ->with(\'info\', $info);
        }

    }

    /**
     * Update a '.$upperNom.'
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
		// Log::alert(\'update: \' . $id);
        return $this->saveOrUpdate($request, false, $id);
    }

    /**
     * Delete a '.$upperNom.'
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function destroy($id)
    {
        // Log::alert(\'destroy: \' . $id);
        return $this->obtainOrDelete(false, $id);
    }

    /**
     * Save or update a '.$upperNom.'
     *
     * @param Request $request
     * @param $create
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function saveOrUpdate(Request $request, $create, $id)
    {
        $data = $request->all();
        $validator = $this->valider'.$upperNom.'($data, $create, $id);

        $uri = $create ? \'create\' : $id.\'/edit\';

        if($validator === true)
        {
             $data = array_merge(
                $data,
                [\'author_id\' => $request->user()->id],
                $this->checkAndSaveImage($data)
            );

            if($create)
            {
                $'.$lowerNom.' = $this->'.$lowerNom.'Repository->store($data);
                $info = ( ! is_null($'.$lowerNom.')) ? \'Sauvegarde effectuée avec succès !\'
                    : \'Une erreur est survenue lors de la sauvegarde, veuillez réessayer !\';
            }
            else
            {
                $this->'.$lowerNom.'Repository->update($id, $data);
                $info = \'Mise à jour effectuée avec succès\';
            }

            return redirect(url($this->base_path.\''.$lowerNom.'s/\'.$uri))
            ->with(\'info\', $info);
        }
        else
        {
            //Echec de validation
            return redirect(url($this->base_path.\''.$lowerNom.'s/\'.$uri))
                ->withErrors($validator)
                ->withInput();
        }

    }

    /**
     * Get or delete a '.$upperNom.'
     *
     * @param $obtain
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    protected function obtainOrDelete($obtain, $id)
    {
        $validator = $this->validerIdSession($id, $this->table);

        if($validator === true)
        {
            if($obtain)
            {
                $'.$lowerNom.' = $this->'.$lowerNom.'Repository->getById($id);
                return view($this->base_view_dir.\''.$lowerNom.'s.show\', compact(\''.$lowerNom.'\'));
            }
            else
            {
                $this->'.$lowerNom.'Repository->destroy($id);
                $info = \'Suppression effectuée avec succès\';
                return redirect(route($this->base_view_dir.\''.$lowerNom.'s.index\'))
					->with(\'info\', $info);
            }
        }
        else
        {
            $info = \''.$upperNom.' introuvable !\';
            return back()
                ->with(\'info\', $info);
        }

    }

    /**
     * Validate a '.$upperNom.'
     *
     * @param array $data
     * @param boolean $create - Create/Update
     * @param $id - an exists identifier
     *
     * @return bool|\Illuminate\Contracts\Validation\Validator
     */
    protected function valider'.$upperNom.'(array $data, $create, $id = 0)
    {
        //Check the existence of $id in table
        if( ! $create)
        {
            $validId = $this->validerIdSession($id, $this->table);

            if($validId !== true)
            {
                return $validId;
            }
            '.$this->getUniqueRules($datas, 1).'
        }
        else
        {
            '.$this->getUniqueRules($datas, 2).'
        }
        '.$this->getSaveDatas($datas).'

        $tab = array_merge($tab1, $tab2);
        $validator = Validator::make($data, $tab);

        return $validator->fails() ? $validator : true;

    }

    /**
     * Zone to remove, just for the tests
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function test(Request $request)
    {
        $url = $request->fullUrl();
        return response($url);
    }

}';

    }


}
