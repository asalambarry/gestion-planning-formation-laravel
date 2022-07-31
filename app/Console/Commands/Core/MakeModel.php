<?php

/**
 * Construire un outil de génération de model
 *
 * @date 16/07/2019
 *
 * @author Jerome Dh <jdieuhou@gmail.com>
 */

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Class MakeModel
 *
 * @package App\Console\Commands\Core
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 07/03/2020
 */
class MakeModel extends Command
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
    protected $signature = 'core:model
							{--c|classe=all : To generated the model(s) for classe(s)}';

    /**
     * The console command description.
     *
     * @var string
     */
	protected $description = 'Create a model class';

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
        $output_dir = app_path();

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
            $this->traitement($tabClasses, $output_dir, 'model');

        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
        }

        return true;

    }

	/**
	 * Récupérer la chaine à écrire dans le fichier
	 *
	 * @param $name - Nom de la classe
	 * @param array $datas - Tableau
	 *
	 * @return string
	 */
	protected function getContent($name, array $datas)
	{
		$lowerNom = strtolower($name);
		$upperNom = ucfirst($name);

        return '<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class '.$upperNom.'
 *
 * @package App
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
class '.$upperNom.' extends Model
{
    /**
     * Associated table name.
     *
     * @var string
     */
    protected $table = \''.$lowerNom.'s\';

    /**
     * Mass assignables attributes.
     *
     * @var array
     */
    protected $fillable = ['.
        $this->getAttrs($datas).'
        \'author_id\',
    ];

    /**
     * Attributes list
     *
     * @return array
     */
    public function getAttrs()
    {
        return $this->fillable;
    }

	/**
     * Get the author who create/update this resource
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author() {
        return $this->belongsTo(\'App\\User\', \'author_id\', \'id\');
    }
    '. $this->getRelationalTable($datas) .'
}';

	}

	/**
	 * Récuperer les données du tableau
	 *
	 * @param $datas
	 *
	 * @return string
	 */
	protected function getAttrs(array $datas)
	{
		$ret = '';
        foreach($datas as $name => $attrs) {
            if( ! empty($name) and $name != 'id') {
                $ret .= '
        \''.$name.'\',';
            }
        }

		return $ret;

	}

    /**
     * Get the relationnal associated tables for model
     *
     * @param array $datas
     * @return string
     */
	protected function getRelationalTable(array $datas) : string {

	    $ret = '';

	    foreach($this->getRelationalsTablesNames($datas) as $otherClasse) {

            $ret .= '
    /**
     * Get the '.$otherClasse.' relational
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function '.$otherClasse.'() {
        return $this->belongsTo(\'App\\'.ucfirst($otherClasse).'\', \''.$otherClasse.'_id\', \'id\');
    }
';
        }

        return $ret;
    }

}
