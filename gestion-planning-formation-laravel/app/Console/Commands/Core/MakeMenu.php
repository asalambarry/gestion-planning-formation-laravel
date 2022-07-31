<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;

/**
 * Class MakeMenu
 *
 * @package App\Console\Commands\Core
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 09/03/2020
 */
class MakeMenu extends Command
{
    /**
     * Trait: helpers
     */
    use Helpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:menu
                            {--c|classe=all : To generated the menu for the classe}\';';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Making all menus of resources for admin';

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

            $output_dir = resource_path('views\auth');
            $menu_file = $output_dir.'\\menu.blade.php';

            $this->checkAndCreateTheOutputDir($output_dir);

            $this->info('En cours ...');
            $this->checkBeforeWrite($menu_file, $this->getMenuContent($tabClasses));
            $this->info('Operations terminées avec succes');

        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
        }

        return true;
    }

    /**
     * Get the menu content
     *
     * @param array $tabClasses
     * @return string
     */
    protected function getMenuContent(array $tabClasses) {
        $content = '
<ul class="menu-admin match-height uk-nav-default" uk-nav="multiple: true">
';

        foreach ($tabClasses as $classeName => $values) {

            $classeName = $this->removePlural($classeName);
            $pluralName = strtolower($classeName).'s';
            $upperName = ucfirst($classeName);

            $content .= '
    <!-- '.formatDisplayName($pluralName).' -->
    <li class="uk-parent">
        <a href="#">'.formatDisplayName($upperName.'s').'</a>
        <ul class="uk-nav-sub">
            <li><a href="{{ route(\'admin.'.$pluralName.'.index\') }}">
                    Liste des '.formatDisplayName($upperName.'s').'</a>
            </li>
            <li><a href="{{ route(\'admin.'.$pluralName.'.create\') }}">
                    Ajouter un '.formatDisplayName( $upperName).'</a>
            </li>
        </ul>
    </li>
';

        }

        $content .= '
</ul>';

        return $content;

    }

}
