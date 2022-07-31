{{-- List --}}
@extends('layouts.auth')

@section('title', $title)

@section('contenu')

    <h2 class="m-4">
        {!! $title !!}
    </h2>

    <div class="overflow-auto">

        @if ($type == 'cours')

            <form action="{{ url('mon-planning') }}">

                <input type="hidden" name="type" value="cours"/>

                <label class="m-4"> Choisissez un cours

                    <select name="intitule" class="p-2"
                        onchange="if(this.options.selectedIndex !== 0) this.form.submit()" required>

                        <option>Choisissez</option>

                        @foreach($cours as $item)

                            <option
                                @if(isset($cour) and $cour->id === $item->id) selected @endif
                                value="{{ $item->id }}">
                                {{ ucfirst($item->intitule) }}
                            </option>

                        @endforeach

                    </select>

                </label>

            </form>

            @isset($cour)

                <table class="table table-hover table-striped table-middle table-bordered">

                    <thead>
                        <tr class="head-table">
                            <th class="text-success text-center">Num</th>
                            <th class="text-success text-center">Début</th>
                            <th class="text-success text-center">Fin</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($cour->plannings as $planning)

                            <tr class="text-center">

                                <td>
                                    {!! $loop->iteration !!}
                                </td>

                                <td class="text-muted">
                                    {{ optional($planning->date_debut)->format('d/m/Y H:i') }}
                                </td>

                                <td class="text-muted">
                                    {{ optional($planning->date_fin)->format('d/m/Y H:i') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="3" class="text-center">
                                    Aucun planning pour ce cours
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            @endisset

        @elseif($type == 'semaine')

            <form action="{{ url('mon-planning') }}">

                <input type="hidden" name="type" value="semaine"/>

                <label class="m-4"> Choisissez une semaine

                    <select name="debut" class="p-2"
                        onchange="if(this.options.selectedIndex !== 0) this.form.submit()" required>

                        <option>Choisissez</option>

                        @for($i = 1; $i <= 52; $i++)

                            <option
                                @if(isset($semaine) and $semaine->format('Y/m/d') === $begin_date->format('Y/m/d')) selected @endif
                                value="{{ $begin_date->format('Y/m/d') }}">
                                {{ $begin_date->format('d/m/Y') }}
                            </option>

                            @php
                                $begin_date->addWeeks(1);
                            @endphp

                        @endfor

                    </select>

                </label>

            </form>

            @isset($semaine)

                <table class="table table-hover table-striped table-middle table-bordered">

                    <thead>

                        <tr class="head-table">
                            <th class="text-success">Jours semaine</th>
                            <th class="text-success">Cours</th>
                            <th class="text-success">Début/Fin</th>
                        </tr>

                    </thead>

                    <tbody>

                        <tr class="">

                            @component('plannings.item-semaine', [
                                'name' => 'Lundi',
                                'date' => $semaine,
                                'datas' => $datas['lundi'],
                            ])
                            @endcomponent

                        </tr>

                         <tr>

                             @component('plannings.item-semaine', [
                                'name' => 'Mardi',
                                'date' => $semaine->addDay(),
                                'datas' => $datas['mardi'],
                             ])
                             @endcomponent

                        </tr>

                         <tr>

                             @component('plannings.item-semaine', [
                                'name' => 'Mercredi',
                                'date' => $semaine->addDay(),
                                'datas' => $datas['mercredi'],
                             ])
                             @endcomponent

                         </tr>

                         <tr>

                             @component('plannings.item-semaine', [
                                'name' => 'Jeudi',
                                'date' => $semaine->addDay(),
                                'datas' => $datas['jeudi'],
                             ])
                             @endcomponent

                         </tr>

                        <tr>

                            @component('plannings.item-semaine', [
                               'name' => 'Vendredi',
                               'date' => $semaine->addDay(),
                               'datas' => $datas['vendredi'],
                            ])
                            @endcomponent

                         </tr>

                        <tr>

                            @component('plannings.item-semaine', [
                               'name' => 'Samedi',
                               'date' => $semaine->addDay(),
                               'datas' => $datas['samedi'],
                            ])
                            @endcomponent

                        </tr>

                        <tr>

                             @component('plannings.item-semaine', [
                               'name' => 'Dimanche',
                               'date' => $semaine->addDay(),
                               'datas' => $datas['dimanche'],
                             ])
                             @endcomponent

                        </tr>

                    </tbody>

                </table>

            @endisset

        @else

            <table class="table  table-striped table-middle table-bordered">

                <thead>
                    <tr class="head-table">
                        <th class="text-success text-center">Num</th>
                        <th class="text-success">Cours</th>
                        <th class="text-success" colspan="2">Planning: Début --> Fin</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($cours as $cour)

                        <tr>

                            <td style="vertical-align: middle" class="text-center">
                                {!! $loop->iteration !!} <br>
                            </td>

                            <td style="vertical-align: middle">
                                <a href="{{ route('cours.show', [$cour->id]) }}" title="Voir">
                                    {{ ucfirst($cour->intitule) }}
                                </a>
                            </td>

                            <td colspan="2" class="text-muted">

                                @forelse($cour->plannings as $planning)

                                    {{ optional($planning->date_debut)->format('d/m/Y H:i') }}
                                    -->
                                    {{ optional($planning->date_fin)->format('d/m/Y H:i') }}

                                    <hr/>

                                @empty

                                    Aucun planning

                                @endforelse

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        @endif

    </div>

@endsection
