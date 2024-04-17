<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Certificados de Aceitação Guardados</h4>
</div>
<div class="modal-body p-10">
    @if(!$certificates->isEmpty())
        <div style="height: 235px; overflow: scroll; border: 1px solid #ddd;">

            <table class="table table-condensed">
                <tr>
                    <th class="w-150px">Criado em</th>
                    <th>Cliente</th>
                    <th class="w-90px"></th>
                </tr>
                <?php
                $today     = \Carbon\Carbon::today()->format('Y-m-d');
                $yesterday = \Carbon\Carbon::yesterday()->format('Y-m-d');
                ?>
                @foreach($certificates as $file)
                    <?php
                    $date = $file->created_at;
                    $date = $date->format('Y-m-d');
                    $hour = $file->created_at->format('H:i:s');
                    ?>
                    <tr>
                        <td>
                            @if($date == $today)
                                Hoje, {{ $hour }}
                            @elseif($date == $yesterday)
                                Ontem, {{ $hour }}
                            @else
                                {{ $file->created_at }}
                            @endif
                        </td>
                        <td>{{ @$file->customer->name ? @$file->customer->name : 'Vários Clientes' }}</td>
                        <td>
                            <a href="{{ asset($file->filepath) }}" target="_blank">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </td>

                    </tr>
                @endforeach
            </table>
        </div>
    @else
        <div class="m-t-20 m-b-20 text-center text-muted">
            <i class="fas fa-info-circle"></i> Não existem ficheiros para download.
        </div>
    @endif
</div>
<div class="modal-footer">
    <div class="pull-left">
        <p class="text-red m-t-5 m-b-0" id="modal-feedback"></p>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>
