<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;

/**
 * Class MakeRoutes
 *
 * @package App\Console\Commands\Core
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 07/03/2020
 */
class MakeRoutes extends Command
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
    protected $signature = 'core:routes
                            {--c|classe=all : To generated the routes for a resources class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Making all necessary routes for migrations files';

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
            //Retrieve the argument value
            $classe = $this->option('classe');
            $reader = new Reader($dirname);

            if ($classe == 'all') {
                $tabClasses = $reader->getAllClasses();
            } else {
                $tabClasses = $reader->getOnlyClasses([$classe]);
            }

            $this->info('En cours ...');
            $this->writeOthersRoutes();
            $this->writeWebRoute($tabClasses);
            $this->writeApiRoute($tabClasses);
            $this->info('Operations terminées avec succes');

        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
        }

        return true;
    }

    /**
     * Writing others routes
     */
    protected function writeOthersRoutes() {

        $content = '
Route::get(\'/\', function () {
    return view(\'welcome\');
});

Route::get(\'about\', function(){
	return view(\'about\');
});

Route::get(\'aide\', function() {
	return view(\'aide\');
});

Route::get(\'confidentialite\', function() {
	return view(\'confidentialite\');
});

Route::get(\'conditions-generales-d-utilisation\', function() {
	return view(\'cgu\');
});

Route::get(\'contact\', function() {
	return view(\'contact\');
});

Route::get(\'not_authorize\', function() {
	return view(\'errors.not_authorize\');
});

Auth::routes();

Route::get(\'/home\', \'HomeController@index\')->name(\'home\');';

        $output_file = base_path('routes\web.php');
        $this->appendInFile($output_file, $content);

    }

    /**
     * Generating all web routes
     *
     * @param array $tabClasses
     */
    protected function writeWebRoute(array $tabClasses) {

        $content = '

// ===================================================================================================
//
//      Admins zone
//
// ===================================================================================================

Route::prefix(\'admin\')->namespace(\'Admin\')->name(\'admin.\')->group(function () {
';

                foreach ($tabClasses as $classeName => $values) {

                    $classeName = $this->removePlural($classeName);
                    $lowerName = strtolower($classeName);
                    $upperName = ucfirst($classeName);

                    $content .= '
    //'.$lowerName.'s
    Route::resource(\''.$lowerName.'s\',            \''.$upperName.'Controller\');
    Route::get(\'test'.$upperName.'\',              \''.$upperName.'Controller@test\');
';

                }

                $content .= '
    //Recherche
    Route::get(\'search\',              \'SearchController@index\')->name(\'search\');

});';
        $output_file = base_path('routes\web.php');
        $this->appendInFile($output_file, $content);

    }

    /**
     * Generating all web routes
     *
     * @param array $tabClasses
     */
    protected function writeApiRoute(array $tabClasses) {

        $content = '
        
// ===================================================================================================
//
//      Api zone
//
// ===================================================================================================

//======== Create/login
Route::post(\'create\',         \'UserController@create\');
Route::post(\'login\',          \'UserController@login\');

Route::namespace(\'Api\')->name(\'api.\')->middleware([\'jwt.verify\'])->group(function () {
';

                foreach ($tabClasses as $classeName => $values) {

                    $classeName = $this->removePlural($classeName);
                    $lowerName = strtolower($classeName);
                    $upperName = ucfirst($classeName);

                    $content .= '
    //======= '.$lowerName.'s routes =======
	Route::group([\'prefix\' => \''.$lowerName.'s\'], function () {

		Route::post(\'getAll\',             \''.$upperName.'Controller@getAll\');
		Route::post(\'create\', 			  \''.$upperName.'Controller@create\');
		Route::post(\'show\',               \''.$upperName.'Controller@show\');
		Route::post(\'update\',             \''.$upperName.'Controller@update\');
		Route::post(\'destroy\',            \''.$upperName.'Controller@destroy\');
		Route::post(\'test\',               \''.$upperName.'Controller@test\');

	});
';
                }

                $content .= '
});';
        $output_file = base_path('routes\api.php');
        $this->appendInFile($output_file, $content);

    }

    /**
     * Append text in web routes file
     *
     * @param string $output_file
     * @param string $content
     */
    protected function appendInFile(string $output_file, string $content) {

        if(file_exists($output_file)) {

            $header1 = '
// ===================================================================================================
//
//      Generated Zone
//
// ===================================================================================================';

            $header2 = '

//
// ========================= End Generated Zone ======================================================
//
';

            $handle = fopen($output_file, 'a');
            fwrite($handle, $header1);
            fwrite($handle, $content);
            fwrite($handle, $header2);
            fclose($handle);
        }
    }

}
