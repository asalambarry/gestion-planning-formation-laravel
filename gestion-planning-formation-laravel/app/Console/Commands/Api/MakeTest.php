<?php

namespace App\Console\Commands\Api;

use App\Console\Commands\Core\Helpers;
use App\Console\Commands\Core\Reader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Class MakeTest
 * Construire un outil de génération de test
 *
 * @package App\Console\Commands\Api
 * @date 11/07/2019
 * @author Jerome Dh
 */
class MakeTest extends Command
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
    protected $signature = 'api:test
							{--c|classe=all : To generated the units tests for classes}';

    /**
     * The console command description.
     *
     * @var string
     */
	protected $description = 'create units testing classes for api';

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
        $output_dir = base_path('tests\Api');

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

            //Write all datas in DatabaseSeeder file
            if ($classe == 'all') {
                $output_file = $output_dir . '/CommonForTest.php';
                $this->info('En cours ...');
                $this->checkBeforeWrite($output_file, $this->getContentCommonForTest());
                $this->info('Operations terminées avec succes');
            }

            $this->tabClasses = $reader->getAllClasses();
            $this->traitement($tabClasses, $output_dir, 'test');

        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
        }

        return true;
    }

    /**
     * Get content CommonForTest
     * @return string
     */
    protected function getContentCommonForTest()
    {
        return '<?php
namespace Tests\Api;

use JWTAuth;
use App\{User};

/**
 * Trait CommonForTest
 *
 * @package Tests\Api
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
trait CommonForTest
{
	/**
     * @Trait CommonForSeeders
     */
    use \CommonForSeeders;

	/**
	 * Base url
	 *
	 * @var String
	 */
	protected $base_url = \'http://127.0.0.1:8000/api\';

	/**
	 * Print an response
	 *
	 * @param $response
	 */
	protected function printResponse($response)
	{
		print_r($response->baseResponse->original);
	}

    /**
     * JSON Call with headers
     *
     * @param $uri
     * @param $content
     *
     * @return mixed
     *
     * @throws \Exception
     */
	protected function makeJSON($uri, $content)
	{
		// return $this->call($method, $uri, $parameters, $files, $server, $content);
        $token = JWTAuth::fromUser($this->getUserForToken());
		return $this->call(
			\'POST\',
			$uri,
			[],
			[],
			[],
			$headers = [
				//\'HTTP_CONTENT_LENGTH\' => mb_strlen($payload, \'8bit\'),
				\'CONTENT_TYPE\' => \'application/json\',
				\'HTTP_ACCEPT\' => \'application/json\',
                \'Authorization\' => "Bearer $token",
			],
			$json = json_encode($content)
		);
	}

	/**
	 * Insers some users into "users" table
	 */
	protected function insertUsers()
	{
		User::truncate();

		for($i = 0; $i < 10; ++$i) {
			User::create($this->getUserSmall());
		}
	}

    /**
     * Get a first App\User in "users" table
     *
     * @return mixed
     * @throws \Exception
     */
	protected function getUserForToken() {

	    return User::firstOrCreate($this->getUserSmall());
    }

}';
    }

	/**
	 * Get the class content
	 *
	 * @param $name - class name
	 * @param $tab - array of attributes
	 *
	 * @return string
	 */
	protected function getContent($name, array $tab)
	{
        $lowerName = strtolower($name);
        $upperName = ucfirst($name);
        $pluralName = $lowerName.'s';

        $relationnalModelsNames = $this->getRelationalTableName($tab);
        $relationnalModelsNames = $relationnalModelsNames ? ', ' . $relationnalModelsNames : '';

        return '<?php
namespace Tests\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use App\\{'.$upperName.''.$relationnalModelsNames.'};

/**
 * Class '.$upperName.'Test
 *
 * @package Tests\Api
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
class '.$upperName.'Test extends TestCase
{
    /**
     * @trait CommonForTest
     */
    use CommonForTest;

	/**
	 * Refresh database
	 */
	use RefreshDatabase;

	/**
	 * Test without middleware
	 */
	use WithoutMiddleware;

	 /**
     * @var string - Table name
     */
    protected $table = \''.$pluralName.'\';

    /**
    * Before every test
    */
    public function setUp() : void
    {
        parent::setUp();
        '.$upperName.'::truncate();'.$this->getRelationalTruncate($tab).'
    }

    /**
     * After every test
     * @throws \Throwable
     */
    public function tearDown() : void
    {
        parent::tearDown();
    }

    /**
     * Test de fonctionnement du controlleur
     *
     * @return void
     */
    public function testWorkingController()
    {
        $response = $this->post($this->base_url.\'/'.$pluralName.'/test\');
        $response->assertStatus(200);
    }

    /**
     * Test create a '.$upperName.'
     *
     * @return void
     * @throws \Exception
     */
    public function testCreate()
    {
        $url = $this->base_url.\'/'.$pluralName.'/create\';

        //Données erronées
        $d = $this->get'.$upperName.'();
        $d[\''.$this->getFirstKeyName($tab).'\'] = null;
        $response = $this->json(\'POST\', $url, $d);
        $response->assertStatus(400);

        //Données partielles
        $d = array_slice($this->get'.$upperName.'(), 1);
        $response = $this->json(\'POST\', $url, $d);
        $response->assertStatus(400);

        //Données correctes
        $d = $this->get'.$upperName.'();
        $response = $this->json(\'POST\', $url, $d);
        $response->assertStatus(200);

        //Test statut de la BD
        $this->assertDatabaseHas($this->table, $d);

        // $response->dumpHeaders();
        // $response->dump();

    }

    /**
     * Test search
     *
     * @return void
     * @throws \Exception
     */
    public function testGetAll()
    {
        $url = $this->base_url.\'/'.$pluralName.'/getAll\';

        //Données manquantes
        $response = $this->json(\'POST\', $url);
        $response->assertStatus(400);

        //Données erronées
        $datas[\'order\'] = \'foo\';
        $response = $this->json(\'POST\', $url, $datas);
        $response->assertStatus(400);

        //Données erronées, <q> invalide
        $d = [
            \'q\'=> \'\',
            \'sort\'=> \''.$this->getFirstKeyName($tab).'\',
            \'order\'=> \'ASC\',
            \'per\'=> \'10\',
            \'page\'=> \'1\',
        ];

        $response = $this->json(\'POST\', $url, $d);
        $response->assertStatus(400);

        //Données valides, avec <q>
        $'.$lowerName.' = '.$upperName.'::create($this->get'.$upperName.'());
        $d = [
            \'q\'=> $'.$lowerName.'->'.$this->getFirstKeyName($tab).',
            \'sort\'=> \''.$this->getFirstKeyName($tab).'\',
            \'order\'=> \'ASC\',
            \'per\'=> \'10\',
            \'page\'=> \'1\',
        ];

        $response = $this->json(\'POST\', $url, $d);
        $response->assertStatus(200);
        $tab = $response->baseResponse->original[\'response\'];
        $this->assertTrue(count($tab) == 1);

        //Données valides, sans <q>
        '.$upperName.'::create($this->get'.$upperName.'());
        $d = [
            \'sort\'=> \''.$this->getFirstKeyName($tab).'\',
            \'order\'=> \'ASC\',
            \'per\'=> \'10\',
            \'page\'=> \'1\',
        ];

        $response = $this->json(\'POST\', $url, $d);
        $response->assertStatus(200);

        // $response->dumpHeaders();
        // $response->dump();

    }

    /**
     * Test show
     *
     * @return void
     * @throws \Exception
     */
    public function testShow()
    {
        $url = $this->base_url.\'/'.$pluralName.'/show\';

        //Données manquantes
        $response = $this->json(\'POST\', $url);
        $response->assertStatus(400);

        //Données erronées, <id> inconnu
        $d = [\'id\' => 99999];
        $response = $this->json(\'POST\', $url, $d);
        $response->assertStatus(400);

        //Données valides
        $'.$lowerName.' = '.$upperName.'::create($this->get'.$upperName.'());
        $d[\'id\'] = $'.$lowerName.'->id;
        $response = $this->json(\'POST\', $url, $d);
        $response->assertStatus(200)
        ->assertJson([
            \'error\' => \'\',
        ]);

        // $response->dumpHeaders();
        // $response->dump();

    }

    /**
     * Test update
     *
     * @return void
     * @throws \Exception
     */
    public function testUpdate()
    {
        $url = $this->base_url.\'/'.$pluralName.'/update\';

        //Données erronées, <id> invalide
        $d = $this->get'.$upperName.'();
        $d[\'id\'] = 0;
        $response = $this->json(\'POST\', $url, $d);
        $response->assertStatus(400);

        //Données erronées, premier champs absent
        $d = array_slice($this->get'.$upperName.'(), 1);
        $'.$lowerName.' = '.$upperName.'::create($this->get'.$upperName.'());
        $d[\'id\'] = $'.$lowerName.'->id;
        $response = $this->json(\'POST\', $url, $d);
        $response->assertStatus(400);

        //Données manquantes: champs <id> absent
        $d = $this->get'.$upperName.'();
        $response = $this->json(\'POST\', $url, $d);
        $response->assertStatus(400);

        //Données correctes
        $d = $this->get'.$upperName.'();
        $'.$lowerName.' = '.$upperName.'::create($this->get'.$upperName.'());
        $d[\'id\'] = $'.$lowerName.'->id;
        $response = $this->json(\'POST\', $url, $d);
        $response->assertStatus(200)
        ->assertJson([
            \'error\' => \'\',
        ]);

        //Test statut de la BD
        $this->assertDatabaseHas($this->table, $d);

        // $response->dumpHeaders();
        // $response->dump();

    }

    /**
     * Test destroy
     *
     * @return void
     * @throws \Exception
     */
    public function testDestroy()
    {
        $url = $this->base_url.\'/'.$pluralName.'/destroy\';

        //Données manquantes
        $response = $this->json(\'POST\', $url);
        $response->assertStatus(400);

        //Données erronées, <id> érroné
        $d[\'id\'] = 99999;
        $response = $this->json(\'POST\', $url, $d);
        $response->assertStatus(400);

        //Données valides
        $'.$lowerName.' = '.$upperName.'::create($this->get'.$upperName.'());
        $d[\'id\'] = $'.$lowerName.'->id;
        $response = $this->json(\'POST\', $url, $d);
        $response->assertStatus(200);

        // $response->dumpHeaders();
        // $response->dump();

    }

}';

	}

}
