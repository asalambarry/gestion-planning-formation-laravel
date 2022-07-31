<?php

namespace App\Http\Controllers;

use App\Repositories\FormationRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{

    /**
     * RegisterController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param FormationRepository $formationRepository
     * @return mixed
     */
    public function index(FormationRepository $formationRepository) {

        $formations = $formationRepository->getFormationForSelect();

        return view('register', compact('formations'));
    }

    public function register(Request $request, UserRepository $repository) {

        $request->validate([
            'nom' => ['nullable', 'string', 'max:40'],
            'prenom' => ['nullable', 'string', 'max:40'],
            'login' => ['required', 'string', 'max:30', Rule::unique('users')],
            'mdp' =>  ['required', 'string', 'max:60'],
            'formation_id' =>  ['nullable', 'integer', Rule::exists('formations', 'id')],
        ]);

        $data = $request->all();
        $data['mdp'] = Hash::make($data['mdp']);
        $data['type'] = null;
        $user = $repository->store($data);

        return redirect()
            ->route('login')
            ->with('info', 'Votre compte est crée, veuillez vous connecter lorsque votre compte sera activé');
    }
}
