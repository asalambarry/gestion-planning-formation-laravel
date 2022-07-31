@if( ! $cour->plannings()->count())
    <strong>Aucun planning !</strong>
@else
    <div class="overflow-auto">

        <table class="table table-hover table-striped table-middle">

            <thead>
                <tr class="head-table">
                    <th class="text-success text-center">Num</th>
                    <th class="text-success text-center">Date début</th>
                    <th class="text-success text-center">Date fin</th>
                </tr>
            </thead>

            <tbody>

                @foreach($cour->plannings as $planning)

                    <tr class="text-center">

                        <td>{!! $loop->iteration !!}</td>

                        <td class="text-muted">{{ optional($planning->date_debut)->format('d/m/Y H:i') }}</td>

                        <td class="text-muted">{{ optional($planning->date_fin)->format('d/m/Y H:i') }}</td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

@endif
