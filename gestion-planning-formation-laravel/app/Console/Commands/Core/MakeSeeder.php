<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;

/**
 * Construire un outil de génération de controlleur
 *
 * @package App\Console\Commands\Core
 * @date 11/07/2019
 * @author Jerome Dh <jdieuhou@gmail.com>
 */
class MakeSeeder extends Command
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
    protected $signature = 'core:seeder
                            {--c|classe=all : To generated the seeder for the classes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make all seeders classes for migrations files';

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
        $output_dir = database_path('seeds');

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

            $this->tabClasses = $reader->getAllClasses();

            //Write all commons methods in CommonForSeeders file
            $output_file = $output_dir.'/CommonForSeeders.php';
            $this->info('En cours ...');
            $this->checkBeforeWrite($output_file, $this->getContentCommonForSeeders($tabClasses));
            $this->info('Operations terminées avec succes');

            //Make all seeders
            $this->traitement($tabClasses, $output_dir, 'seeder');

            //Write all classes in DatabaseSeeder file
            $output_file = $output_dir.'/DatabaseSeeder.php';
            $this->info('En cours ...');
            $this->checkBeforeWrite($output_file, $this->getContentDatabaseSeeder($tabClasses));
            $this->info('Operations terminées avec succes');

        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
        }

        return true;
    }

    /**
     * Get the DatabaseSeeder file content
     *
     * @param array $datas
     * @return string
     */
    protected function getContentDatabaseSeeder(array $datas) {

        $content = '';
        foreach ($datas as $classeName => $data) {
            $content .= '
        $this->call('.ucfirst($this->removePlural($classeName)).'Seeder::class);';
        }

        return '<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application\'s database.
     *
     * @return void
     */
    public function run()
    {'.$content.'
    }

}';
    }

    /**
     * Get the content of specific seeder
     *
     * @param $name
     * @param array $datas
     * @return string
     */
    protected function getContent($name, array $datas) {

        $lowerName = strtolower($name);
        $upperName = ucfirst($name);
        $myModel = $this->isPivotTable($name, $this->tabClasses) ? '' : 'use App\\{'.$upperName.'};';
        $createInsertStatement = $this->isPivotTable($name, $this->tabClasses)
            ? 'DB::table(\''.$lowerName.'\')->insert($'.$lowerName.');'
            : $upperName.'::create($'.$lowerName.');';
        $importDBFacade = $this->isPivotTable($name, $this->tabClasses) ? '
use Illuminate\Support\Facades\DB;
' : '';
        $relationalsTablesNames = $this->getRelationalTableName($datas);
        $putComma = $myModel && $relationalsTablesNames ? ', ' : '';

        return '<?php

use Illuminate\Database\Seeder;
'.$myModel.''.$importDBFacade.'
/**
 * Class '.$upperName.'Seeder
 *
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
class '.$upperName.'Seeder extends Seeder
{
    /**
     * Trait CommonForSeeders
     */
     use CommonForSeeders;
     
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        for($i = 0; $i < 20; $i++) {
            $'.$lowerName.' = $this->get'.$upperName.'();
            '.$createInsertStatement.'
        }
    }
    
}';

    }


}
