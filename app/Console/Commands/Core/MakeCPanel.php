<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;

/**
 * Class MakeCPanel
 *
 * @package App\Console\Commands\Core
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 28/03/2020
 */
class MakeCPanel extends Command
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
    protected $signature = 'core:cpanel
                            {--c|classe=all : To generated the cpanel for the classe}\';';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generated c-panel based on classe(s) given';

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
            $menu_file = $output_dir.'\\cpanel.blade.php';

            $this->checkAndCreateTheOutputDir($output_dir);

            $this->info('En cours ...');
            $this->checkBeforeWrite($menu_file, $this->getCPanelContent($tabClasses));
            $this->info('Operations terminées avec succes');

        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
        }

        return true;
    }

    /**
     * Get the cpanel content
     *
     * @param array $tabClasses
     * @return string
     */
    public function getCPanelContent(array $tabClasses) {

        $content = '
<div>

    <!-- Group Ressources Humaines -->
    <div class="uk-card uk-card-default uk-width-1-1@m c-panel">
        <div class="uk-card-header">
            <div class="uk-grid-small uk-flex-middle" uk-grid>
                <div class="uk-width-auto">
                    <img class="uk-border-circle" width="60" height="60" src="{{ asset(\'storage/rh1.png\') }}" alt="icone">
                </div>
                <div class="uk-width-expand">
                    <h3 class="uk-card-title uk-margin-remove-bottom uk-text-primary">Ressources Humaines</h3>
                    <p class="uk-text-meta uk-margin-remove-top">Gérez les employés, équipes de travail, missions, congés, ..</p>
                </div>
            </div>
        </div>

        <div class="uk-card-body">

            <div class="uk-flex uk-flex-left uk-flex-row uk-flex-wrap uk-flex-wrap-around uk-background-muted">
';

        foreach ($tabClasses as $classeName => $values) {

            $classeName = $this->removePlural($classeName);
            $lowername = strtolower($classeName);
            $pluralName = $lowername.'s';
            $upperName = ucfirst($classeName);

            $content .= '
                <!-- CPanel '.$upperName.' -->
                <div class="uk-card uk-card-default uk-card-body uk-flex-auto">

                    <h3 class="uk-card-title"><span uk-icon="icon:nut; ratio:1"></span> '.formatDisplayName($upperName.'s').'</h3>

                    <div>
                        <span uk-icon="icon:list; ratio:1"></span>
                        <a href="{{ route(\'admin.'.$pluralName.'.index\') }}" class="uk-button uk-button-text">
                            Liste des '.formatDisplayName($upperName.'s').'
                        </a>

                        <br/>
                        <span uk-icon="icon:plus-circle; ratio:1"></span>
                        <a href="{{ route(\'admin.'.$pluralName.'.create\') }}" class="uk-button uk-button-text">
                            Ajouter un '.formatDisplayName($upperName).'
                        </a>
                    </div>

                    <div class="uk-text-meta uk-float-right">
                        <span>?-</span>
                        <a href="#" class="uk-button uk-button-text" uk-toggle="target: #aide-'.$lowername.'">
                            Aide
                        </a>
                    </div>

                    <!-- Modal to help for '.formatDisplayName($upperName.'s').' -->
                    <div id="aide-'.$lowername.'" uk-modal>
                        <div class="uk-modal-dialog uk-modal-body">
                            <button class="uk-modal-close-default" type="button" uk-close></button>
                            <h2 class="uk-modal-title">'.formatDisplayName($upperName.'s').'</h2>

                            <p>A propos de &#171; '.formatDisplayName($upperName.'s').' &#187;</p>
                            <ul>
                                <li>
                                    Pour enregistrer un nouveau '.formatDisplayName($lowername).', cliquer sur <strong>&#171; Ajouter un '.formatDisplayName($lowername).' &#187;</strong>
                                </li>
                                <li>
                                    Pour voir la liste ou modifier les '.formatDisplayName($lowername.'s').' enregistrés, cliquer sur <strong>&#171; Liste des '.formatDisplayName($pluralName).' &#187;</strong>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
';

        }

        $content .= '

            </div>
        </div>

        <div class="uk-card-footer">
            <a href="javascript:void(0)" class="uk-button uk-button-text">Dernière opération:</a> <small class="uk-text-meta">Listing</small>
        </div>

    </div>
';

        for($i = 0; $i < 3; ++$i) {
            $content .= '
    <!-- Group Ressources Humaines -->
    <div class="uk-card uk-card-default uk-width-1-1@m c-panel uk-margin">

        <div class="uk-card-header">
            <div class="uk-grid-small uk-flex-middle" uk-grid>
                <div class="uk-width-auto">
                    <img class="uk-border-circle" width="60" height="60" src="{{ asset(\'storage/rh1.png\') }}" alt="icone">
                </div>
                <div class="uk-width-expand">
                    <h3 class="uk-card-title uk-margin-remove-bottom uk-text-primary">Ressources Humaines</h3>
                    <p class="uk-text-meta uk-margin-remove-top">Gérez les employés, équipes de travail, missions, congés, ..</p>
                </div>
            </div>
        </div>

        <div class="uk-card-body">

            <div class="uk-flex uk-flex-left uk-flex-row uk-flex-wrap uk-flex-wrap-around uk-background-muted">

            </div>
        </div>

        <div class="uk-card-footer">
            <a href="javascript:void(0)" class="uk-button uk-button-text">Dernière opération:</a> <small class="uk-text-meta">Listing</small>
        </div>

    </div>
 ';
        }

    $content .= '
</div>';

        return $content;
    }

}
