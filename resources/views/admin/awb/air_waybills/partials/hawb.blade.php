<div class="table-hawb">
    @if(@$hawbs)
    <table class="table table-condensed m-0 ">
        <tr class="bg-gray-light">
            <th class="w-80px">Data</th>
            <th class="w-1">Carga</th>
            <th>HAWB Nº</th>
            <th>Ref.</th>
            <th>Expedidor</th>
            <th>Consignatário</th>
            <th class="w-60px"></th>
        </tr>
        @foreach($hawbs as $hawb)
            <tr>
                <td>{{ $hawb->date->format('Y-m-d') }}</td>
                <td>
                    <span class="label" style="background: {{ @$hawb->goodType->color }}">{{ @$hawb->goodType->name }}</span><br/>
                </td>
                <td>
                    <a href="{{ route('admin.air-waybills.hawb.edit', $hawb->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
                        {{ $hawb->awb_no }}
                    </a>
                </td>
                <td>{{ $hawb->reference }}</td>
                <td>{{ $hawb->sender_name }}</td>
                <td>{{ $hawb->consignee_name }}</td>
                <td>
                    <a href="{{ route('admin.air-waybills.hawb.edit', $hawb->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
                        <i class="fas fa-pencil-alt text-green"></i>
                    </a>
                    <a href="{{ route('admin.air-waybills.hawb.destroy', $hawb->id) }}" class="delete-hawb m-l-5">
                        <i class="fas fa-trash-alt text-red"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </table>
    @endif
</div>