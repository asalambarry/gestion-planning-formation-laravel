<?php

namespace App\Http\Controllers;

use App\Repositories\CoursRepository;
use App\Repositories\PlanningRepository;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanningController extends Controller
{
    /**
     * @var PlanningRepository
     */
    protected $planningRepository;

    /**
     * @var int
     */
    protected $nbPerPage = 10;

    /**
     * PlanningController constructor.
     * @param  PlanningRepository  $planningRepository
     */
    public function __construct(PlanningRepository $planningRepository)
    {
        $this->middleware('auth');
        $this->middleware(['adminenseignant'])->only('index', 'create', 'store', 'edit', 'update', 'destroy');
        $this->middleware(['etudiantenseignant'])->only('monPlanning');

        $this->planningRepository = $planningRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return  mixed
     */
    public function index()
    {
        $plannings = $this->planningRepository->paginate('date_fin', 'desc', $this->nbPerPage);

        $title = 'Liste des plannings';

        return view('plannings.index', compact('plannings', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param CoursRepository $coursRepository
     * @return  mixed
     */
    public function create(CoursRepository $coursRepository)
    {
        $cours = $coursRepository->getCoursForSelect();

        return view('plannings.create', compact('cours'));
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
            'cours_id' => ['required', 'numeric', Rule::exists('cours', 'id')],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date'],
        ]);

        $data = $request->all();

        $this->planningRepository->store($data);

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
        $planning = $this->planningRepository->getById($id);

        $this->authorize('view', $planning);

        return view('plannings.show', compact('planning'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param CoursRepository $coursRepository
     * @return  mixed
     * @throws AuthorizationException
     */
    public function edit($id, CoursRepository $coursRepository)
    {
        $planning = $this->planningRepository->getById($id);

        $this->authorize('update', $planning);

        $cours = $coursRepository->getCoursForSelect();

        return view('plannings.edit', compact('planning', 'cours'));
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
            'cours_id' => ['required', 'numeric', Rule::exists('cours', 'id')],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date'],
        ]);

        $data = $request->all();

        $this->planningRepository->update($id, $data);

        return back()->with('info', 'Modification effectuée avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return  mixed
     * @throws AuthorizationException
     */
    public function destroy($id)
    {
        $planning = $this->planningRepository->getById($id);

        $this->authorize('delete', $planning);

        $this->planningRepository->destroy($id);

        return redirect()->route('plannings.index')->with('info', 'Suppression effectuée avec succès !');
    }

    /**
     * Mon planning
     *
     * @param Request $request
     * @param CoursRepository $coursRepository
     * @param PlanningRepository $planningRepository
     * @return mixed
     * @throws \Exception
     */
    public function monPlanning(Request $request, CoursRepository $coursRepository, PlanningRepository $planningRepository)
    {
        $type = $request->query('type');

        if ($type == 'cours')
        {
            $cours_id = $request->query('intitule');

            if ($cours_id)
            {
                $cour = $coursRepository->getById($cours_id);
                $title = 'Planning du cours <strong>&#171; '.$cour->intitule.' &#187;</strong>';
            }
            else {
                $cour = null;
                $title = 'Choisissez un cours';
            }

            $cours = $this->getMyCours();

            return view('plannings.mon_planning', compact('cours', 'cour', 'title', 'type'));
        }
        elseif ($type == 'semaine')
        {
            $debut = $request->query('debut');
            $semaine = null;
            $cours = null;
            $datas = null;

            if ($debut) {

                $d = strtotime($debut);

                $semaine = new Carbon($d);

                $cours = $this->getMyCours();

                $datas = [];

                $days = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];

                $week = clone $semaine;

                for ($i = 0; $i < 7; $i++) {

                    $tmp = [];

                    foreach ($cours as $cour) {

                        $planning = $planningRepository->getModel()->where('cours_id', $cour->id)
                            ->whereDate('date_debut', $week->format('Y-m-d'))
                            ->get();

                        if ($planning->count()) {
                            $tmp[] = [
                                'cour' => $cour,
                                'planning' => $planning,
                            ];
                        }

                    }

                    $datas[$days[$i]] = $tmp;

                    $week->addDay();
                }

                $title = 'Planning de la semaine du <strong>&#171; '.date('d/m/Y', $d).' &#187;</strong>';
            }
            else {
                $title = 'Plannings par semaine';
            }

            $firstDays = now()->startOfYear();

            $begin_date = $firstDays->subDays($firstDays->dayOfWeek)->addDays(1);

            return view('plannings.mon_planning', compact('cours', 'title', 'type', 'begin_date', 'semaine', 'datas'));
        }
        else {

            $cours = $this->getMyCours();

            $title = 'Planning Intégral';

            return view('plannings.mon_planning', compact('cours', 'title', 'type'));
        }

    }

    /**
     * Recuperer les cours du user
     *
     * @return mixed
     */
    private function getMyCours()
    {
        if (auth()->user()->isEtudiant())
        {
            $c1 = auth()->user()->cours;
            $cours = $c1->concat(auth()->user()->formation->cours);
        }
        else {
            $cours = auth()->user()->coursEnseignant;
        }

        return $cours;
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
