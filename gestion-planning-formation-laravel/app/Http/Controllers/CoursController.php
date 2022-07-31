<?php

namespace App\Http\Controllers;

use App\Repositories\CoursRepository;
use App\Repositories\FormationRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CoursController extends Controller
{
    /**
     * @var CoursRepository
     */
    protected $coursRepository;

    /**
     * @var int
     */
    protected $nbPerPage = 10;

    /**
     * CoursController constructor.
     * @param  CoursRepository  $coursRepository
     */
    public function __construct(CoursRepository $coursRepository)
    {
        $this->middleware('auth');
        $this->middleware('admin')->only('create', 'store', 'edit', 'update', 'destroy', 'coursParEnseignant');
        $this->middleware('etudiant')->only('inscription', 'desinscription', 'storeInscription');
        $this->middleware(['etudiantenseignant'])->only('mesCours');

        $this->coursRepository = $coursRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return  mixed
     */
    public  function  index()
    {
        $cours = $this->coursRepository->paginate('intitule', 'asc', $this->nbPerPage);

        $title = 'Liste des cours';

        return view('cours.index', compact('cours', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param UserRepository $userRepository
     * @param FormationRepository $formationRepository
     * @return  mixed
     */
    public function create(UserRepository $userRepository, FormationRepository $formationRepository)
    {
        $users = $userRepository->getUsersForSelect('enseignant');

        $formations = $formationRepository->getFormationForSelect();

        return view('cours.create', compact('users', 'formations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return  mixed
     */
    public function store(Request $request)
    {
        $request->validate([
            'intitule' => ['required', 'string', 'max:50'],
            'user_id' => ['required', 'numeric', Rule::exists('users', 'id')],
            'formation_id' => ['nullable', 'numeric', Rule::exists('formations', 'id')],
        ]);

        $data = $request->all();

        $this->coursRepository->store($data);

        return back()->with('info', 'Enregistrement effectuée avec succès !');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return  mixed
     */
    public function show($id)
    {
        $cour = $this->coursRepository->getById($id);

        return view('cours.show', compact('cour'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param UserRepository $userRepository
     * @param FormationRepository $formationRepository
     * @return  mixed
     */
    public function edit($id, UserRepository $userRepository, FormationRepository $formationRepository)
    {
        $cour = $this->coursRepository->getById($id);

        $users = $userRepository->getUsersForSelect('enseignant');

        $formations = $formationRepository->getFormationForSelect();

        return view('cours.edit', compact('cour', 'users', 'formations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return  mixed
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'intitule' => ['required', 'string', 'max:50'],
            'user_id' => ['required', 'numeric', Rule::exists('users', 'id')],
            'formation_id' => ['nullable', 'numeric', Rule::exists('formations', 'id')],
        ]);

        $data = $request->all();

        $this->coursRepository->update($id, $data);

        return back()->with('info', 'Modification effectuée avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return  mixed
     */
    public function destroy($id)
    {
        $cours = $this->coursRepository->getById($id);

        $cours->users()->detach();

        if ($cours->plannings()->count() > 0) {
            $title = 'Impossible de supprimer, au moins un élement est lié !';
        }
        else {
            $title = 'Suppression effectuée avec succès !';
            $this->coursRepository->destroy($id);
        }

        return redirect()->route('cours.index')->with('info', $title);
    }

    /**
     * Inscription a un cours
     *
     * @param $id
     * @return mixed
     */
    public function inscription($id)
    {
        $cour = $this->coursRepository->getById($id);

        return view('cours.inscription', compact('cour'));
    }

    /**
     * Se désinscrire d'un cours
     *
     * @param $id
     * @return mixed
     */
    public function desinscription($id)
    {
        auth()->user()->cours()->detach($id);

        return back()->with('info', 'Désinscription effectuée avec succès !');
    }

    /**
     * Stocker l'inscription a un cours
     *
     * @param Request $request
     * @return mixed
     */
    public function storeInscription(Request $request) {

        $request->validate([
            'cours_id' =>  ['required', 'numeric', Rule::exists('cours', 'id')],
        ]);

        $cours_id = $request->input('cours_id');

        auth()->user()->cours()->attach($cours_id);

        return redirect()
            ->route('cours.show', [$cours_id])
            ->with('info', 'Inscription au cours effectuée avec succes !');
    }

    /**
     * Mes cours
     *
     * @param Request $request
     * @return mixed
     */
    public function mesCours(Request $request)
    {

        if ($request->query('formation')) {

            if ( ! auth()->user()->isEtudiant()) {
                abort(403);
            }

            if ( ! auth()->user()->formation) {
                abort(404);
            }

            $cours = auth()->user()->formation->cours()->paginate($this->nbPerPage);

            $title = 'Liste des cours de la formation <strong>&#171; '.auth()->user()->formation->intitule.' &#187;</strong>';
        }
        else {

            $title = 'Mes cours';

            if (auth()->user()->isEtudiant()) {
                $cours = auth()->user()->cours()->paginate($this->nbPerPage);
            }
            else {
                $cours = auth()->user()->coursEnseignant()->paginate($this->nbPerPage);
            }
        }

        return view('cours.index', compact('cours', 'title'));
    }

    /**
     * Recherche de cours
     *
     * @param  Request  $request
     * @return  mixed
     */
    public function rechercheCours(Request $request) {

        $q = $request->query('q');

        $cours = $this->coursRepository->rechercherCours($q, $this->nbPerPage);

        $title = 'Résultats de la recherche pour <strong>&#171; '.$q.' &#187;</strong>';

        return view('cours.index', compact('cours', 'title'));
    }

    /**
     * Cours par enseignant
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @return mixed
     */
    public function coursParEnseignant(Request $request, UserRepository $userRepository) {

        $login = $request->query('login');

        $cours = $this->coursRepository->coursParEnseignant($login, $this->nbPerPage);

        $enseignants = $userRepository->getUsersForSelect('enseignant');

        $title = 'Cours par enseignant';

        if ($login and $login !== 'tous') {
            $user = $userRepository->getById($login);
            $title = 'Cours de l\'enseignant <strong>&#171; '.$user->nom.' '.$user->prenom.' &#187;</strong>';
        }

        return view('cours.index', compact('cours', 'title', 'enseignants', 'login'));
    }

    /**
     * Test
     *
     * @param  Request  $request
     * @return  mixed
     */
    public function test(Request $request)
    {
        $url = $request->fullUrl();
        return response($url);
    }
}
