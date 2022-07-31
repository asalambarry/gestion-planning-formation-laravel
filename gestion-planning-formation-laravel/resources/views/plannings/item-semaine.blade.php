
<td>{{ $name }} <span class="small-date">{!! $date->format('d/m/Y') !!}</span></td>

@if(count($datas))

    <td>
        @foreach($datas as $item)

            {{ $item['cour']->intitule }}
            <hr>

        @endforeach
    </td>

    <td>
        @foreach($datas as $item)

            @foreach($item['planning'] as $planning)
                {{ $planning->date_debut->format('H:i') }} /
                {{ $planning->date_fin->format('H:i') }} <br>
            @endforeach

            <hr>

        @endforeach
    </td>

@else

    <td colspan="2" class="text-muted">Aucun planning</td>

@endif
