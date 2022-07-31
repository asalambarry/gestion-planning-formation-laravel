
@if( ! count($plannings))

    <h3>Aucun planning n'a été trouvé !</h3>

@else

    {!! $plannings->links() !!}

    <div class="overflow-auto">

        <table class="table table-hover table-striped table-middle">

            <thead>
                <tr class="head-table">
                    <th class="text-success text-center">Num</th>
                    <th class="text-success text-center">Cours</th>
                    <th class="text-success text-center">Début --> Fin</th>
                    <th class="text-success text-center">Opération</th>
                </tr>
            </thead>

            <tbody>

            @foreach($plannings as $planning)

                <tr>

                    <td class="text-center">{!! $loop->iteration !!}</td>

                    <td>
                        <a href="{{ route('plannings.show', [$planning->id]) }}" title="Voir tous les détails">
                            {{ ucfirst($planning->cours->intitule) }}
                        </a>
                    </td>

                    <td class="text-center">
                        {{ optional($planning->date_debut)->format('d/m/Y') }}
                        -->
                        {{ optional($planning->date_fin)->format('d/m/Y') }}
                    </td>

                    <td class="text-center">

                        @if( ! auth()->user()->isEtudiant())

                            @component('components.update-buttons', [
                               'id' => $planning->id,
                               'edit_route' => 'plannings.edit',
                               'destroy_route' => 'plannings.destroy',
                               'name' => 'planning',
                               'buttons' => false,
                               'back_button' => false,
                            ])
                            @endcomponent

                        @endif

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    </div>

    {!! $plannings->links() !!}

@endif
