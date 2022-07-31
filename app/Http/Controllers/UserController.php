<?php

namespace App\Http\Controllers;

use App\Repositories\FormationRepository;
use App\Repositories\UserRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var int
     */
    protected $nbPerPage = 10;

    /**
     * UserController constructor.
     * @param  UserRepository  $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->middleware('auth');
        $this->middleware('admin')->only('index', 'create', 'store', 'destroy', 'rechercheUser');

        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return  mixed
     */
    public function index(Request $request)
    {
        $type = $request->query('type');

        if (in_array($type, ['etudiant', 'enseignant', 'admin'])) {
            $title = 'Liste des '.ucfirst($type).'s';
            $users = $this->userRepository->getUsersByType($type, $this->nbPerPage);
        }
        elseif ($type == 'auto-crees') {
            $title = 'Utilisateurs auto-crées';
            $users = $this->userRepository->getUsersByType(null, $this->nbPerPage);
        }
        else {
            $title = 'Liste des utilisateurs';
            $users = $this->userRepository->paginate('id', 'desc', $this->nbPerPage);
        }

        $typeExists = true;

        return view('users.index', compact('users', 'title', 'type', 'typeExists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param FormationRepository $formationRepository
     * @return  mixed
     */
    public function create(FormationRepository $formationRepository)
    {
        $formations = $formationRepository->getFormationForSelect();
        return view('users.create', compact('formations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return  mixed
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => ['nullable', 'string', 'max:40'],
            'prenom' => ['nullable', 'string', 'max:40'],
            'login' => ['required', 'string', 'max:30', Rule::unique('users')],
            'mdp' => ['required', 'string', 'max:60'],
            'formation_id' => ['nullable', 'integer', Rule::exists('formations', 'id')],
            'type' => ['required', Rule::in(config('user_role'))],
        ]);

        $data = $request->all();

        $data['mdp'] = Hash::make($data['mdp']);

        $this->userRepository->store($data);

        return back()->with('info', 'Enregistrement effectuée avec succès !');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return  mixed
     * @throws AuthorizationException
     */
    public function show($id)
    {
        $user = $this->userRepository->getById($id);

        $this->authorize('view', $user);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param FormationRepository $formationRepository
     * @return  mixed
     * @throws AuthorizationException
     */
    public function edit($id, FormationRepository $formationRepository)
    {
        $user = $this->userRepository->getById($id);

        $this->authorize('update', $user);

        $formations = $formationRepository->getFormationForSelect();

        return view('users.edit', compact('user', 'formations'));
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
            'nom' => ['nullable', 'string', 'max:40'],
            'prenom' => ['nullable', 'string', 'max:40'],
            'login' => ['required', 'string', 'max:30', Rule::unique('users')->ignore($id)],
            'mdp' => ['nullable', 'string', 'max:60'],
            'formation_id' => ['nullable', 'integer', Rule::exists('formations', 'id')],
            'type' => ['nullable', Rule::in(config('user_role'))],
        ]);

        $data = $request->all();

        if (isset($data['mdp'])) {
            $data['mdp'] = Hash::make($data['mdp']);
        }
        else {
            $user = $this->userRepository->getById($id);
            $data['mdp'] = $user->mdp;
        }

        $this->userRepository->update($id, $data);

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
        $user = $this->userRepository->getById($id);

        if ($user->coursEnseignant()->count()) {
            $title = 'Impossible de supprimer, au moins un élement est lié !';
        }
        else {
            $title = 'Suppression effectuée avec succès !';
            $user->cours()->detach();
            $this->userRepository->destroy($id);
        }

        return redirect()->route('users.index')->with('info', $title);
    }

    /**
     * Recherche de users
     *
     * @param Request $request
     * @return mixed
     */
    public function rechercheUser(Request $request) {

        $q = trim($request->query('q'));

        $users = $this->userRepository->rechercherUser($q, $this->nbPerPage);

        $title = 'Résultats de la recherche pour <strong>&#171; '.$q.' &#187;</strong>';

        return view('users.index', compact('users', 'title'));
    }

    /**
     * Refuser un compte auto-créé
     *
     * @param $id
     * @return mixed
     */
    public function refuserInscription($id)
    {
        $user = $this->userRepository->getById($id);

        if ($user->type == null) {
            $user->delete();
            $info = 'Compte réfusé avec succès !!!';
        }
        else {
            $info = 'Opération impossible.';
        }

        return back()->with('info', $info);
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
