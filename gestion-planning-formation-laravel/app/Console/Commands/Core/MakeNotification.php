<?php
namespace App\Console\Commands\Core;

use Illuminate\Console\Command;

/**
 * Class MakeNotification
 *
 * @package App\Console\Commands\Core
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 07/03/2020
 */
class MakeNotification extends Command
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
    protected $signature = 'core:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make all notifications';

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
        $output_dir = app_path('Notifications');
        $user_file = $output_dir.'\\UserNotification.php';

        $this->checkAndCreateTheOutputDir($output_dir);

        $this->info('En cours ...');
        $this->checkBeforeWrite($user_file, $this->getUserNotifContent());
        $this->info('Operations terminées avec succes');

        return true;
    }

    /**
     * @return string
     */
    protected function getUserNotifContent() {

        return '<?php
namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * User Notification class
 *
 * @package App\Notifications
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
class UserNotification extends Notification
{
    use Queueable;
	
	/**
	 * The notification title
	 * 
	 * @var string
	 */
	protected $title;
	
	/**
	 * The notification text
	 * 
	 * @var string
	 */
	protected $texte;
	
	/**
	 * The user
	 * 
	 * @var User
	 */
	protected $user;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @param $title
     * @param $texte
     */
    public function __construct(User $user, $title, $texte)
    {
        $this->user = $user;
        $this->title = $title;
        $this->texte = $texte;
    }

    /**
     * Get the notification\'s delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [\'mail\'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
			->subject($this->title)
			->markdown(\'notifs.users\',
				[\'user\' => $this->user, \'texte\' => $this->texte]
		);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}';
    }

}
