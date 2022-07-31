<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;

/**
 * Class MakeLibrary
 *
 * @package App\Console\Commands\Core
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 07/03/2020
 */
class MakeLibrary extends Command
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
    protected $signature = 'core:library';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make all usables libraries';

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
        $output_dir = app_path('Library');
        $user_file = $output_dir.'\\CommonForUsers.php';
        $custum_file = $output_dir.'\\CustomFunction.php';

        $this->checkAndCreateTheOutputDir($output_dir);

        $this->info('En cours ...');
        $this->checkBeforeWrite($user_file, $this->getCommonsUserContent());
        $this->checkBeforeWrite($custum_file, $this->getCustomsFunctionsContent());
        $this->info('Operations terminées avec succes');

        return true;
    }

    protected function getCommonsUserContent() {

        return '<?php

namespace App\Library;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\{User};
use App\Mail\UserMail;

use Carbon\Carbon;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Custums functions for all users
 *
 * @package App\Library
 *
 * @author Jerome Dh <jdieuhou@gmail.com>
 *
 * @date '.$this->now().'
 */
trait CommonForUsers
{

    /**
     * Envoyer le mail de confirmation de l\'adresse email
     *
     * @param User $user - L\'utilisateur
     * @return void
     * @throws \Exception
     */
	public function mailConfirmation(User $user)
	{
		//== Le token de vérification
		$token = $this->userRepository->getNextToken();
		$user->remember_token = $token;

		//== Le temps de validation: 24 heures max
		$d = new \Datetime();
		$d = $d->setTimestamp(time() + 24 * 3600);
		$user->email_verified_at = $d->getTimestamp();
		$user->save();

		//== Envoyer le mail
		$userMail = new UserMail($user);
		$userMail->setType(\'confirmation_email\')
				->setToken($token);

		Mail::to($user->email)
			->send($userMail);

	}

	/**
     * Valider la confirmation du mail d\'un utilisateur
     *
	 * @param string $token - le token
	 *
     * @return User|false
     */
	public function validerEmail(string $token)
	{
		$ret = false;

		if($token)
		{
			$user = $this->userRepository->findByToken($token);

			if($user)
			{
				$now = Carbon::now();
				if($now <= $user->email_verified_at)
				{
					//Enlever les données de vérification
					$user->remember_token = \'\';
					$user->statut = config(\'custum.statut.activate\');

					//Sauver en BD
					$user->save();

					$ret = $user;

				}
			}
		}

		return $ret;

	}

	/**
     * Envoyer les détails de connexion
     *
	 * @param User $user
	 * @param string $password - Le mot de
	 *
     * @return true|false
     */
	public function sendDetailsAccount(User $user, $password=\'\')
	{
		//Envoyer le mail
		$userMail = new UserMail($user);
		$userMail->setType(\'details_compte\')
				->setPassword($password);

		Mail::to($user->email)
			->send($userMail);

		return true;

	}

	/**
     * Envoyer les détails de compte d\'un user
     *
	 * @param string $token
	 * @param boolean $password - True s\'il faut générer un mot de passe
	 * @param boolean $matricule
	 *
     * @return array
     */
	public function gererTokenEmail(string $token, $password = false, $matricule = false)
	{
		$ret = [\'content\' => \'\', \'error\' => \'\', \'code\' => 200];

		if(($user = $this->validerEmail($token)))
		{
			//Générer un mot de passe et le matricule
			$generatedPassword = \'\';
			if($password) {
                $generatedPassword = $this->gererPassword($user);
                $user->password = Hash::make($generatedPassword);
            }

			if($matricule)
				$user->matricule = $this->userRepository->getNextMatricule();

			if($password or $matricule)
				$user->save();

			$this->sendDetailsAccount($user, $generatedPassword);

            $ret[\'user\'] = $user;

			$ret[\'content\'] = \'Adresse email validée avec succès, \'.
				\'un message contenant les données de connexion a été \'.
				\'envoyé !\';

		}
		else
		{
			$ret[\'error\'] = \'Code de vérification invalide ou expiré\';
			$ret[\'code\'] = 400;
		}

		return $ret;

	}

	/**
	 * Générer le mot de passe d\'un user
	 *
	 * @param User $user
	 *
	 * @return string - Le mot de passe
	 */
	protected function gererPassword(User $user)
	{

		$password = str_shuffle($user->first_name.\'\'.time());

		return $password;

	}

	/**
	 * Notification d\'activation/désactivation de compte
	 *
	 * @param User $user
	 *
	 * @return string - Le mot de passe
	 */
	protected function changeStatutNotification(User $user)
	{

	}

	/**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return array
     */
    protected function respondWithToken($token)
    {
        return [
            \'access_token\' => $token,
            \'token_type\' => \'bearer\',
            \'expires_in\' => auth()->factory()->getTTL() * 60
        ];
    }

	/**
	 * Supprimer toutes les données d\'un utilisateur
	 *
	 * @param $id - Identifiant du user
	 */
	protected function deleteAllDataForUser($id)
	{
		$user = $this->userRepository->getById($id);
	}

}';
    }

    /**
     * @return string
     */
    protected function getCustomsFunctionsContent() {

        return '<?php

namespace App\Library;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

/**
 * Trait CustumFunction
 *
 * @package App\Library
 *
 * @author Jerome Dh <jdieuhou@gmail.com>
 *
 * @date 07/03/2020 10:29
 */
trait CustomFunction
{
    /**
     * The dir of uploads
     *
     * @var string
     */
    protected $dir_images = \'public\';

	/**
	 * Vérifier les termes clés de la recherche
	 *
	 * @param array $datas - Tableau devant contenir
	 *
	 *			! [sort] - Champs du classement
	 *			! [order] - ASC/DESC
	 *			! [per] - Nombre de résultats
	 *			! [page] - Numéro de page
	 *
	 * @param array $attr - Liste des attributs
	 *
	 * @return true|array
	 *
	 */
	public function validerRecherche(array $datas, array $attr = [])
	{
		$ret = true;

		$sort = ($attr) ? [\'required\', Rule::in($attr)] : \'required|string\';

		$tab = [
			\'q\' => \'filled\',
			\'sort\' => $sort,
			\'order\' => [
				\'required\',
				Rule::in([\'asc\', \'desc\', \'ASC\', \'DESC\']),
			],
			\'per\' => \'required|integer\',
			\'page\' => \'required|integer\',
		];

		$validator = Validator::make($datas, $tab);

		if($validator->fails())
		{
			$ret = $validator->errors();
		}

		return $ret;

	}

	/**
	 * Vérifier l\'existence d\'un id dans une table
	 *
	 * @param $id - Identifiant
	 * @param $table - Nom de la table
	 *
	 * @return array|true - Tableau d\'erreurs ou true
	 */
	protected function validerId($id, $table) {

		if($id) {
			$validator = Validator::make(
			[\'id\' => $id],
			[
				\'id\' => [
					\'required\',
					\'numeric\',
					Rule::exists($table)->where(function ($query) use ($id) {
						$query->where(\'id\', $id);
					}),
				],
			]);

			// ==== Vérifier que le token et l\'identifiant correspondent pour le cas des users

			//=========================

			if($validator->fails()) {
				$ret = $validator->errors();
			}
			else {
				$ret = true;
			}
		}
		else {
			$ret = [\'id\' => \'Champ [id] doit avoir une valeur\'];
		}

		return $ret;

	}

    /**
     * Vérifier l\'existence d\'un id dans une table pour une session
     *
     * @param $id - Identifiant
     * @param $table - Nom de la table
     *
     * @return Validator|true - Tableau d\'erreurs ou true
     */
    protected function validerIdSession($id, $table)
    {
        $validator = Validator::make(
            [\'id\' => $id],
            [
                \'id\' => [
                    \'required\',
                    \'numeric\',
                    Rule::exists($table)->where(function ($query) use ($id) {
                        $query->where(\'id\', $id);
                    }),
                ],
            ]);

        return $validator->fails() ? $validator : true;

    }

    /**
     * Vérifier la validité d\'une plage des dates
     *
     * @param \Datetime $debut
     * @param \Datetime @fin
     *
     * @return true|false
     * @throws \Exception
     */
	protected function validePlage($debut, $fin)
	{
		$tab = [
			\'debut\' => $debut,
			\'fin\' => $fin,
		];
		$validator = Validator::make($tab, [
			\'debut\' => \'required|date\',
			\'fin\' => \'required|date\',
		]);

		if( ! $validator->fails()) {

			$d = new Carbon($debut);
			$f = new Carbon($fin);

			return $d < $f;
		}

		return false;

	}

    /**
     * Vérifier un tableau de produit
     *
     * @param array $produits
     *
     * @return bool|\Illuminate\Support\MessageBag
     */
	protected function validerTableauProduits(array $produits)
	{
		$tab = [
			\'produits\' => \'required|array\',
			\'produits.*.id\' => [
				\'required\',
				\'integer\',
				\'exists:produits,id\',
			],
			\'produits.*.qte\' => [
				\'required\',
				\'integer\',
				\'min:1\',
			],
		];

		$validator = Validator::make($produits, $tab);

		if($validator->fails()) {
			$ret = $validator->errors();
		}
		else {
			$ret = true;
		}

		return $ret;

	}

	public $listNationalite = [
		\'Afghanistan\',
		\'Albania\',
		\'Algeria\',
		\'American Samoa\',
		\'Andorra\',
		\'Angola\',
		\'Anguilla\',
		\'Antarctica\',
		\'Antigua and Barbuda\',
		\'Argentina\',
		\'Armenia\',
		\'Aruba\',
		\'Australia\',
		\'Austria\',
		\'Azerbaijan\',
		\'Bahamas\',
		\'Bahrain\',
		\'Bangladesh\',
		\'Barbados\',
		\'Belarus\',
		\'Belgium\',
		\'Belize\',
		\'Benin\',
		\'Bermuda\',
		\'Bhutan\',
		\'Bolivia\',
		\'Bosnia and Herzegovina\',
		\'Botswana\',
		\'Bouvet Island\',
		\'Brazil\',
		\'British Indian Ocean Territory\',
		\'Brunei Darussalam\',
		\'Bulgaria\',
		\'Burkina Faso\',
		\'Burundi\',
		\'Cambodia\',
		\'Cameroon\',
		\'Canada\',
		\'Cape Verde\',
		\'Cayman Islands\',
		\'Central African Republic\',
		\'Chad\',
		\'Chile\',
		\'China\',
		\'Christmas Island\',
		\'Cocos (Keeling) Islands\',
		\'Colombia\',
		\'Comoros\',
		\'Congo\',
		\'Congo, The Democratic Republic of The\',
		\'Cook Islands\',
		\'Costa Rica\',
		\'Cote D\\\'ivoire\',
        \'Croatia\',
        \'Cuba\',
        \'Cyprus\',
        \'Czech Republic\',
        \'Denmark\',
        \'Djibouti\',
        \'Dominica\',
        \'Dominican Republic\',
        \'Ecuador\',
        \'Egypt\',
        \'El Salvador\',
        \'Equatorial Guinea\',
        \'Eritrea\',
        \'Estonia\',
        \'Ethiopia\',
        \'Falkland Islands (Malvinas)\',
        \'Faroe Islands\',
        \'Fiji\',
        \'Finland\',
        \'France\',
        \'French Guiana\',
        \'French Polynesia\',
        \'French Southern Territories\',
        \'Gabon\',
        \'Gambia\',
        \'Georgia\',
        \'Germany\',
        \'Ghana\',
        \'Gibraltar\',
        \'Greece\',
        \'Greenland\',
        \'Grenada\',
        \'Guadeloupe\',
        \'Guam\',
        \'Guatemala\',
        \'Guinea\',
        \'Guinea-bissau\',
        \'Guyana\',
        \'Haiti\',
        \'Heard Island and Mcdonald Islands\',
        \'Holy See (Vatican City State)\',
        \'Honduras\',
        \'Hong Kong\',
        \'Hungary\',
        \'Iceland\',
        \'India\',
        \'Indonesia\',
        \'Iran, Islamic Republic of\',
        \'Iraq\',
        \'Ireland\',
        \'Israel\',
        \'Italy\',
        \'Jamaica\',
        \'Japan\',
        \'Jordan\',
        \'Kazakhstan\',
        \'Kenya\',
        \'Kiribati\',
        \'Korea, Democratic People\\\'s Republic of\',
        \'Korea, Republic of\',
        \'Kuwait\',
        \'Kyrgyzstan\',
        \'Lao People\\\'s Democratic Republic\',
		\'Latvia\',
		\'Lebanon\',
		\'Lesotho\',
		\'Liberia\',
		\'Libyan Arab Jamahiriya\',
		\'Liechtenstein\',
		\'Lithuania\',
		\'Luxembourg\',
		\'Macao\',
		\'Macedonia, The Former Yugoslav Republic of\',
		\'Madagascar\',
		\'Malawi\',
		\'Malaysia">Malaysia\',
		\'Maldives\',
		\'Mali\',
		\'Malta\',
		\'Marshall Islands\',
		\'Martinique\',
		\'Mauritania\',
		\'Mauritius\',
		\'Mayotte\',
		\'Mexico\',
		\'Micronesia, Federated States of\',
		\'Moldova, Republic of\',
		\'Monaco\',
		\'Mongolia\',
		\'Montserrat\',
		\'Morocco\',
		\'Mozambique\',
		\'Myanmar\',
		\'Namibia\',
		\'Nauru\',
		\'Nepal\',
		\'Netherlands\',
		\'Netherlands Antilles\',
		\'New Caledonia\',
		\'New Zealand\',
		\'Nicaragua\',
		\'Niger\',
		\'Nigeria\',
		\'Niue\',
		\'Norfolk Island\',
		\'Northern Mariana Islands\',
		\'Norway\',
		\'Oman\',
		\'Pakistan\',
		\'Palau\',
		\'Palestinian Territory, Occupied\',
		\'Panama\',
		\'Papua New Guinea\',
		\'Paraguay\',
		\'Peru">Peru\',
		\'Philippines\',
		\'Pitcairn\',
		\'Poland\',
		\'Portugal\',
		\'Puerto Rico\',
		\'Qatar\',
		\'Reunion\',
		\'Romania\',
		\'Russian Federation\',
		\'Rwanda\',
		\'Saint Helena\',
		\'Saint Kitts and Nevis\',
		\'Saint Lucia\',
		\'Saint Pierre and Miquelon\',
		\'Saint Vincent and The Grenadines\',
		\'Samoa\',
		\'San Marino\',
		\'Sao Tome and Principe\',
		\'Saudi Arabia\',
		\'Senegal\',
		\'Serbia and Montenegro\',
		\'Seychelles\',
		\'Sierra Leone\',
		\'Singapore\',
		\'Slovakia\',
		\'Slovenia\',
		\'Solomon Islands\',
		\'Somalia\',
		\'South Africa\',
		\'South Georgia and The South Sandwich Islands\',
		\'Spain\',
		\'Sri Lanka\',
		\'Sudan\',
		\'Suriname\',
		\'Svalbard and Jan Mayen\',
		\'Swaziland\',
		\'Sweden\',
		\'Switzerland\',
		\'Syrian Arab Republic\',
		\'Taiwan, Province of China\',
		\'Tajikistan\',
		\'Tanzania, United Republic of\',
		\'Thailand\',
		\'Timor-leste\',
		\'Togo\',
		\'Tokelau\',
		\'Tonga\',
		\'Trinidad and Tobago\',
		\'Tunisia\',
		\'Turkey\',
		\'Turkmenistan\',
		\'Turks and Caicos Islands\',
		\'Tuvalu\',
		\'Uganda\',
		\'Ukraine\',
		\'United Arab Emirates\',
		\'United Kingdom\',
		\'United States\',
		\'United States Minor Outlying Islands\',
		\'Uruguay\',
		\'Uzbekistan\',
		\'Vanuatu\',
		\'Venezuela\',
		\'Viet Nam\',
		\'Virgin Islands, British\',
		\'Virgin Islands, U.S.\',
		\'Wallis and Futuna\',
		\'Western Sahara\',
		\'Yemen\',
		\'Zambia\',
		\'Zimbabwe\',
	];

    /**
     * Enregistrer une photo
     *
     * @param $datas
     *
     * @return string|false
     */
    public function createImage(array $datas)
    {
        $image = $datas[\'image\'];
        // Log::error($image);

        if ($image->isValid()) {
            //Générer un unique id
            $ret = $image->store($this->dir_images);

			//Soustraire le prefix "public/"
			$ret = substr($ret, strlen($this->dir_images.\'/\'));
        }
        else {
            $ret = false;
        }

        return $ret;

    }

    /**
     * Supprimer une image dans le localStorage
     *
     * @param string $name
     * @return bool
     */
    public function deleteImage(string $name)
    {
        if(Storage::exists(($this->dir_images.\'/\'.$name))) {
            Storage::delete($this->dir_images.\'/\'.$name);
            $ret = true;
        }
        else {
            $ret = false;
        }

        return $ret;

    }

    /**
     * Check the request and save associated image
     * @param Request $request
     * @return array
     */
    public function checkAndSaveImage(array $data) {

        $ret  = [];
        if(array_key_exists(\'image\', $data) and $data[\'image\'])  {

            $name = $this->createImage([\'image\' => $data[\'image\']]);
            if($name != false) {
                $ret = [\'image\' => $name];
            }
        }

        return $ret;

    }

}';

    }


}
