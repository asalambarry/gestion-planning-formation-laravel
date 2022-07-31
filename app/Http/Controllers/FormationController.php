<?php

namespace App\Http\Controllers;

use App\Repositories\FormationRepository;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    /**
     * @var FormationRepository
     */
    protected $formationRepository;

    /**
     * @var int
     */
    protected $nbPerPage = 10;

    /**
     * FormationController constructor.
     * @param  FormationRepository  $formationRepository
     */
    public function __construct(FormationRepository $formationRepository)
    {
        $this->middleware('auth');
        $this->middleware('admin')->only('create', 'store', 'edit', 'update', 'destroy');
        $this->middleware('etudiant')->only('maFormation');

        $this->formationRepository = $formationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return  mixed
     */
    public function index()
    {
        $formations = $this->formationRepository->paginate('intitule', 'asc', $this->nbPerPage);

        $title = 'Liste des formations';

        return view('formations.index', compact('formations', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  mixed
     */
    public function create()
    {
        return view('formations.create');
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
            'intitule' => ['required', 'string', 'max:50', 'min:3']
        ]);

        $data = $request->all();

        $this->formationRepository->store($data);

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
        $formation = $this->formationRepository->getById($id);

        return view('formations.show', compact('formation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return  mixed
     */
    public function edit($id)
    {
        $formation = $this->formationRepository->getById($id);
        return view('formations.edit', compact('formation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return  mixed
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'intitule' => ['required', 'string', 'max:50', 'min:3']
        ]);

        $data = $request->all();

        $this->formationRepository->update($id, $data);

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
        $formation = $this->formationRepository->getById($id);

        if ($formation->users()->count() > 0 or $formation->cours()->count() > 0) {
            $title = 'Impossible de supprimer, au moins un élement est lié !';
        }
        else {
            $title = 'Suppression effectuée avec succès !';
            $this->formationRepository->destroy($id);
        }

        return redirect()->route('formations.index')->with('info', $title);
    }

    /**
     * Ma formation
     *
     * @return mixed
     */
    public function maFormation()
    {
        $formation = auth()->user()->formation;

        if ( ! $formation) {
            abort(404);
        }

        return view('formations.show', compact('formation'));
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
