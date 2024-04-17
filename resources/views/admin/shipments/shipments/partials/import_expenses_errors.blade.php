<p class="text-red">
    <i class="fas fa-exclamation-triangle"></i> <b>{{ $totalSuccess }}</b> registos importados com sucesso. <b>{{ $totalErrors }}</b> registos não importados.
</p>
<div style="height: 235px; overflow: scroll; border: 1px solid #ddd;">
    <table class="table table-condensed">
        <tr>
            <th class="w-1">#</th>
            <th>TRK Original</th>
            <th>Motivo</th>
        </tr>
        @foreach($errors as $key => $error)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $error['code'] }}</td>
                <td>{{ $error['message'] }}</td>
            </tr>
        @endforeach
    </table>
</div>
