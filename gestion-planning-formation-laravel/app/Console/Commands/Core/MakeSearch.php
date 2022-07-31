<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;

/**
 * Class MakeSearch
 *
 * @package App\Console\Commands\Core
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 07/03/2020
 */
class MakeSearch extends Command
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
    protected $signature = 'core:search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make search engine for support all models tables';

    /**
     * Array of all classes
     *
     * @var array
     */
    protected $tabClasses = [];

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
        $dirname = database_path('migrations');
        try {

            $reader = new Reader($dirname);
            $this->tabClasses = $reader->getAllClasses();
            $this->process($this->tabClasses);

        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
        }

        return true;
    }

    protected function process(array $tabClasses) {

        $this->info('En cours ...');

        //Controller
        $controller_filename = app_path('Http\Controllers\Admin\SearchController.php');
        $this->checkBeforeWrite($controller_filename, $this->getControllerContent($tabClasses));

        //Repository
        $repository_filename = app_path('Repositories\SearchRepository.php');
        $this->checkBeforeWrite($repository_filename, $this->getRepositoryContent($tabClasses));

         //Views
        $view_dir = resource_path('views\admin\search');
        $this->checkAndCreateTheOutputDir($view_dir);

        $index_filename = $view_dir.'\index.blade.php';
        $noresult_filename = $view_dir.'\no_results.blade.php';
        $this->checkBeforeWrite($index_filename, $this->getIndexViewContent($tabClasses));
        $this->checkBeforeWrite($noresult_filename, $this->getNoResulViewContent());

        $this->info('Operations terminées avec succes');
    }

    /**
     * Get the controller content
     *
     * @param array $tabClasses
     * @return string
     */
    protected function getControllerContent(array $tabClasses) {

        $models = '';
        $ifs = '';
        $compacts = '';
        $i = 0;
        foreach ($tabClasses as $classeName => $datas) {

            $classeName = $this->removePlural($classeName);
            $pluralName = strtolower($classeName).'s';

            $models .= '
            $'.$pluralName.' = $rep[\''.$pluralName.'\'];';

            if($i++) {
                $ifs .= ' or ';
                $compacts .= ', ';
            }
            $ifs .= 'count($'.$pluralName.')';
            $compacts .= '\''.$pluralName.'\'';

        }
        return '<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Repositories\SearchRepository;
use Illuminate\Http\Request;
use App\Library\CustomFunction;
use Illuminate\Support\Facades\Log;

/**
 * Class SearchController
 *
 * @package App\Http\Controllers\Admin
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
class SearchController extends Controller
{
    /**
     * Utilities functions
     *
     * @trait CustomFunction
     */
    use CustomFunction;

    /**
     * @var SearchRepository
     */
    protected $searchRepository;

    /**
     * SearchController constructor.
     * @param SearchRepository $searchRepository
     */
    public function __construct(SearchRepository $searchRepository)
    {
        //Les middlewares
        $this->middleware(\'auth\');

        $this->searchRepository = $searchRepository;
    }

    /**
     * Find by term
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $texte = $request->input(\'q\');
        //Log::alert(\'q=\' . $texte);

        if($texte) {

            $rep = $this->searchRepository->findByText($texte);
            '.$models.'

            if('.$ifs.') {
                return view(\'admin.search.index\', compact('.$compacts.'));
            }
        }

        return view(\'admin.search.no_results\');

    }

}';
    }

    /**
     * Get the the content repository
     *
     * @param array $tabClasses
     * @return string
     */
    protected function getRepositoryContent(array $tabClasses) {

        $models = '';
        $clauses = '';
        $i = 0;
        $results = '';
        foreach ($tabClasses as $classeName => $datas) {

            $classeName = $this->removePlural($classeName);
            $upperName = ucfirst($classeName);
            $pluralName = strtolower($classeName).'s';

            if($i++) {
                $models .= ', ';
            }

            $models .= $upperName;
            $results .= '
                \''.$pluralName.'\' => $'.$pluralName.',';

            $clauses .= '

            //find in '.$pluralName.' table
            $'.$pluralName.' = '.$upperName.'::where(function($query) use ($q) {
               '.$this->getSearchWhere($datas).'

            })->orderBy(\''.$this->getFirstKeyName($datas).'\')->get();';

        }

        return '<?php

namespace App\Repositories;

use App\{'.$models.'};

/**
 * SearchRepository
 *
 * @package App\Repositories
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
class SearchRepository extends ResourceRepository
{

    /**
     * Find from query text
     * 
     * @param string $q
     * @return array
     */
    public function findByText($q)
    {
        $ret = [];
        if ($q) :'.$clauses.'

            $ret = ['.$results.'
            ];

        endif;

        return $ret;

    }

}';
    }

    /**
     * Get the index view content
     *
     * @param array $tabClasses
     * @return string
     */
    protected function getIndexViewContent(array $tabClasses) {

        $models = '';
        foreach ($tabClasses as $classeName => $datas) {

            $classeName = $this->removePlural($classeName);
            $upperName = ucfirst($classeName);
            $lowerName = strtolower($classeName);
            $pluralName = strtolower($classeName).'s';

           $models .= '

        <!-- List '.$upperName.'s -->
        <li class="uk-parent">

            <a href="#" class="uk-text-lead">
				<span uk-icon="icon:database; ratio:1.2"></span>
				'.$upperName.'s ({!! count($'.$pluralName.') !!})</a>

            @if(count($'.$pluralName.'))

                <div class="uk-overflow-auto">

                    <table class="uk-table uk-table-hover uk-table-striped uk-table-middle">

                        <thead>
                            <tr>
                                <th class="uk-text-success uk-text-center">
                                    <span uk-icon="icon:thumbnails; ratio:1"></span>
                                </th>
                                '.$this->getHeaderTable($datas, 2).'
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($'.$pluralName.' as $'.$lowerName.')
    
                                <tr class="uk-text-center">

                                    <td>{{ $loop->iteration }}</td>
                                    '.$this->getTdTable($classeName, $datas, 2).'
                                </tr>
    
                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif
			<hr class="uk-divider-icon">

        </li>';

        }

        return '@extends(\'layouts.admin\')

@section(\'title\', \'Résultats de la récherche\')

@section(\'contenu\')

    <h2>
        <span uk-icon="icon:list; ratio:2"></span>
        Résultats trouvés
    </h2>

    <!-- Information zone -->
    @component(\'components.alert\', [])
    @endcomponent

    <ul class="match-height uk-nav-default uk-nav-parent-icon" uk-nav="multiple: true">'.$models.'
    </ul>

@endsection';

    }

    /**
     * Get the content of no_result
     *
     * @return string
     */
    protected function getNoResulViewContent() {

        return '@extends(\'layouts.admin\')

@section(\'title\', \'Aucun résultat trouvé\')

@section(\'contenu\')

    <!-- Zone d\'information -->
    @component(\'components.alert\', [])
    @endcomponent

    <div class="match-height" uk-grid>

        <div class="uk-width-1-1@s">

            <div class="uk-card uk-card-primary uk-card-body uk-text-center">

                <h2 class="uk-card-title">
                    <span uk-icon="icon:list; ratio:2"></span>
                    0 Résultat trouvé
                </h2>
                <h4>Aucun résultat n\'a été trouvé avec ce terme.<br></h4>
                <p>Veuillez réessayer avec d\'autres mots-clés</p>

            </div>
        </div>

    </div>

@endsection';
    }

}
