<?php

/**
 * Construire un outil de génération de Resource
 *
 * @date 01/02/2020
 *
 * @author Jerome Dh <jdieuhou@gmail.com>
 */

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Class MakeResource
 *
 * @package App\Console\Commands\Core
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 07/03/2020
 */
class MakeResource extends Command
{

    /**
     * Trait des utilitaires
     *
     * @Trait Helpers
     */
    use Helpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:resource
							{--c|classe=all : To generated the resource for classe}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a resource class';

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
        $output_dir = app_path('Http\Resources');

        $dirname = database_path('migrations');
        try {
            //Retrieve the argument value
            $classe = $this->option('classe');
            $reader = new Reader($dirname);

            if ($classe == 'all') {
                $tabClasses = $reader->getAllClasses();
            } else {
                $tabClasses = $reader->getOnlyClasses([$classe]);
            }

            $this->traitement($tabClasses, $output_dir, 'resource');

        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
        }

        return true;
    }

    /**
     * Get the class content
     *
     * @param $name - Class name
     * @param array $datas - Datas
     *
     * @return string
     */
    protected function getContent($name, array $datas)
    {
        $lowerName = strtolower($name);
        $upperName = ucfirst($name);

        $othersResources = $this->getOthersResources($datas);
        $content = $othersResources ? '
        $array = parent::toArray($request);'.$othersResources.'
        return $array;'
            : '
        return parent::toArray($request);';

        return '<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource '.$upperName.'
 *
 * @package App\Http\Resources
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
class '.$upperName.'Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
	 *
     * @return array
     */
    public function toArray($request)
    {'.$content.'
    }
}';

    }

    /**
     * Get the relationnal associated tables for model
     *
     * @param array $datas
     * @return string
     */
    protected function getOthersResources(array $datas) : string {

        $ret = '';

        foreach($this->getRelationalsTablesNames($datas) as $otherClasse) {

            $ret .= '
        $array[\''.$otherClasse.'\'] = $this->'.$otherClasse.';';
        }

        return $ret;
    }


}
