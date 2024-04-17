<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Consultar mapas de fecho</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            @if($files->isEmpty())
            <p class="text-muted m-t-30 m-b-30 text-center">
                <i class="fas fa-info-circle"></i> Não existem mapas de fecho de expedição.
            </p>
            @else
                <div style="overflow-y: auto;height: 332px;border: 1px solid #ccc;">
                    <table class="table table-condensed m-0">
                        <tr>
                            <th class="bg-gray-light w-100px">Data</th>
                            <th class="bg-gray-light w-70px">Hora</th>
                            <th class="bg-gray-light w-50px">Guias</th>
                            <th class="bg-gray-light w-50px">Vols.</th>
                            <th class="bg-gray-light w-90px">Peso</th>
                            <th class="bg-gray-light w-1">Cobrança</th>
                            <th class="bg-gray-light"></th>
                        </tr>
                        @foreach($files as $file)
                            <?php $file->closed_at = new Jenssegers\Date\Date($file->closed_at) ?>
                            <tr>
                                <td>
                                    <a href="{{ route('account.shipments.selected.print', ['closed', 'date' => $file->closed_at->format('Y-m-d H:i:s')]) }}" target="_blank">
                                    {{ $file->closed_at->format('Y-m-d') }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('account.shipments.selected.print', ['closed', 'date' => $file->closed_at->format('Y-m-d H:i:s')]) }}" target="_blank">
                                        {{ $file->closed_at->format('H:i') }}
                                    </a>
                                </td>
                                <td>{{ $file->total }}</td>
                                <td>{{ $file->volumes }}</td>
                                <td>{{ money($file->weight, 'kg') }}</td>
                                <td>{{ money($file->cod, '€') }}</td>
                                <td class="text-right">
                                    <a href="{{ route('account.shipments.selected.print', ['closed', 'date' => $file->closed_at->format('Y-m-d H:i:s')]) }}" class="btn btn-xs btn-default" target="_blank">
                                        <i class="fas fa-print"></i> Imprimir
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
<div class="modal-footer text-right">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    </div>
</div>