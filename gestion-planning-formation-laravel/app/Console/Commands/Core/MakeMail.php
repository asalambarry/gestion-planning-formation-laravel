<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;

/**
 * Class MakeMail
 *
 * @package App\Console\Commands\Core
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 07/03/2020
 */
class MakeMail extends Command
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
    protected $signature = 'core:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make all mails classes';

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
        $output_dir = app_path('Mail');
        $contact_file = $output_dir.'\\ContactMail.php';
        $newsletter_file = $output_dir.'\\ContentNewsletter.php';
        $user_file = $output_dir.'\\UserMail.php';

        $this->checkAndCreateTheOutputDir($output_dir);

        $this->info('En cours ...');
        $this->checkBeforeWrite($contact_file, $this->getContactContent());
        $this->checkBeforeWrite($newsletter_file, $this->getNewsletterContent());
        $this->checkBeforeWrite($user_file, $this->getUserMailContent());
        $this->info('Operations terminées avec succes');

        return true;
    }

    protected function getContactContent() {

        return '<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Contact Mail class
 *
 * @package App\Mail
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

	/**
	 * @var
	 */
	protected $sub;
	protected $message;
	protected $fr;
	protected $name;

    /**
     * Create a new message instance.
     *
     * @param $fr
     * @param $name
     * @param $sub
     * @param $message
     */
    public function __construct($fr, $name, $sub, $message)
    {
        $this->fr = $fr;
        $this->name = $name;
        $this->sub = $sub;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->sub)
                    ->from([\'address\' => $this->fr, \'name\' => $this->name])
                    ->markdown(\'emails.contact\',
                        [\'name\' => $this->name, \'message\' => $this->message]);

    }
}';

    }
     protected function getNewsletterContent() {

         return '<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Newsletter Mail class
 *
 * @package App\Mail
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 07/03/2020 10:43
 */
class ContentNewsletter extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The markdown content
     *
     * @var
     */
    public $content;

    protected $sub;

    /**
     * Create a new message instance.
     *
     * @param $sub
     * @param $content
     */
    public function __construct($sub, $content)
    {
        $this->sub = $sub;
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$this->writeIntoNewsletterFile($this->content);

		return $this->from(config(\'mail.from\'))
            ->subject($this->sub)
            ->markdown(\'emails.newsletter\');
    }

    /**
     * Write the content into newsletter core template
     *
     * @param $content
     *
     * @return void
     */
	protected function writeIntoNewsletterFile($content) {

		$file = base_path(\'resources/views/emails/newsletter.blade.php\');
		if(file_exists($file)) {
			file_put_contents($file, $content);
		}
	}

}';

    }

     protected function getUserMailContent() {

        return '<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * User Mail class
 *
 * @package App\Mail
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
class UserMail extends Mailable
{
    use Queueable, SerializesModels;

	/**
	 * Le user
	 *
	 * @var User
	 */
	protected $user;

	/**
	 * Le token
	 *
	 * @var string
	 */
	protected $token;

	/**
	 * Le type de message à envoyer
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Le password
	 *
	 * @var string
	 */
	protected $password = \'\';

	/**
	 * The command name
	 *
	 * @var string
	 */
	protected $commande = \'\';

    /**
     * @var string
     */
	static public $NEW_COMMANDE = \'new_commande\';

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

	/**
	 * Le token
	 *
	 * @param string $token - Le token de validation du mail
	 *
	 * @return $this
	 */
	public function setToken(string $token)
	{
		$this->token = $token;
		return $this;
	}

	/**
	 * Le type de message
	 *
	 * @param string $type - Le type
	 *
	 * @return $this
	 */
	public function setType(string $type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Le mot de passe à envoyer
	 *
	 * @param string $password - Le type
	 *
	 * @return $this
	 */
	public function setPassword($password)
	{
		$this->password = $password;

		return $this;
	}
	
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		//Le type de message
		switch($this->type)
		{
			case \'confirmation_email\':
				$url = action(\'UserController@valider\', [\'token\'=>$this->token]);
				$ret = $this->subject(\'Confirmation de l\\\'adresse email\')
						->markdown(\'emails.confirmation\',
							[\'user\' => $this->user, \'url\' => $url]);
			break;

			case \'details_compte\':

				$url = url(\'/\');
				$ret = $this->subject(\'Details de votre compte\')
						->markdown(\'emails.details_compte\',
							[	\'user\' => $this->user,
								\'url\' => $url,
								\'password\'=>$this->password
							]);
				break;

			case \'new_admin\':

				$url = url(\'/\');
				$ret = $this->subject(\'Votre mot de passe Administrateur\')
						->markdown(\'emails.admin.new_admin\',
							[\'user\' => $this->user, \'url\' => $url]);
				break;

            case self::$NEW_COMMANDE:

				$ret = $this->subject(\'Commande enregistrée avec succès\')
						->markdown(\'emails.new-commande\',
							[\'user\' => $this->user,
							\'command_name\' => $this->commande]);
				break;

			default:

				$ret = $this->subject(\'Bienvenue sur \'.config(\'app.name\'))
						->markdown(\'emails.welcome\', [\'user\' => $this->user]);

			break;
		}

		return $ret;
	}
	
}';

    }

}
