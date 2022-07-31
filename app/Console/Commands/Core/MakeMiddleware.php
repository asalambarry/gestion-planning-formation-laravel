<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;

/**
 * Class MakeMiddleware
 *
 * @package App\Console\Commands\Core
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 07/03/2020
 */
class MakeMiddleware extends Command
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
    protected $signature = 'core:middleware';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make the middlewares classes';

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
        $output_dir = app_path('Http\Middleware');

        $admin_file = $output_dir.'\\Admin.php';
        $reader_file = $output_dir.'\\Reader.php';

        $this->info('En cours ...');
        $this->checkBeforeWrite($admin_file, $this->getAdminContent());
        $this->checkBeforeWrite($reader_file, $this->getReaderContent());
        $this->info('Operations terminées avec succes');

        return true;
    }


    protected function getAdminContent() {

        return '<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Middleware Admin
 *
 * @package App\Http\Middleware
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
    
        if($request->user() and $request->user()->role == config(\'custum.user_role.admin\')) {
            return $next($request);
        } else {
            return redirect(url(\'not_authorize\'));
        }
    }
}';

    }

    protected function getReaderContent() {

        return '<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Middleware Reader
 *
 * @package App\Http\Middleware
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
class Reader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
    
       if($request->user() and $request->user()->role >= config(\'custum.user_role.reader\')) {
            return $next($request);
        } else {
            return redirect(url(\'not_authorize\'));
        }

    }
}';
    }

}
