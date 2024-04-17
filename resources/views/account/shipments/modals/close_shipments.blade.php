{{ Form::open(array('route' => 'account.shipments.close.store', 'class' => 'form-ajax', 'method' => 'POST')) }}
<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Fechar Expedição</h4>
</div>
<div class="modal-body">
    @if($shipments->isEmpty())
        <div class="row row-5">
            <div class="col-sm-12">
                <p class="text-muted m-t-30 m-b-30 text-center">
                    <i class="fas fa-info-circle"></i> Não existem serviços para fechar.
                </p>
            </div>
        </div>
    @else
        <div class="row row-5">
            <div class="col-sm-12">
                <h4 class="message">
                    Confirma o fecho de {{ $shipments->count() }} serviços?
                </h4>
                <div style="overflow-y: scroll;height: 270px;border: 1px solid #ccc;margin-top: 20px;">
                <table class="table table-condensed m-0">
                    <tr>
                        <th class="w-90px">Serviço</th>
                        <th>Destino</th>
                        <th class="w-30px">Vol</th>
                        <th class="w-70px">Peso</th>
                        <th class="w-70px">Cob.</th>
                    </tr>
                    @foreach($shipments as $shipment)
                        <tr>
                            <td>
                                {{ $shipment->tracking_code }}
                                {{ Form::hidden('ids[]', $shipment->id) }}
                            </td>
                            <td>{{ $shipment->recipient_name }}</td>
                            <td>{{ $shipment->volumes }}</td>
                            <td>{{ money($shipment->weight) }}</td>
                            <td>{{ $shipment->charge_price ? money($shipment->charge_price) : ''}}</td>
                        </tr>
                    @endforeach

                </table>
                </div>
                <ul class="list-unstyled list-inline pull-right m-t-5 bold">
                    <li>{{ $shipments->count() }} Expedições</li>
                    <li>{{ $shipments->sum('volumes') }} Vol.</li>
                    <li>{{ money($shipments->sum('weight'), 'kg') }}</li>
                    <li>{{ money($shipments->sum('charge_price'), Setting::get('app_currency')) }}</li>
                </ul>
                <div class="clearfix"></div>
            </div>
        </div>
    @endif
</div>
<div class="modal-footer text-right">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        @if(!$shipments->isEmpty())
        <button type="submit" class="btn btn-primary btn-submit" data-loading-text="Aguarde...">Fechar Expedição</button>
        @endif
    </div>
</div>
</div>
{{ Form::close() }}

<script>

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-ajax').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                oTable.draw(); //update datatable
                $('#modal-remote').modal('hide');
                Growl.success(data.feedback);

                if (window.open(data.printManifest, '_blank')) {
                    $('#modal-remote').modal('hide');
                } else {
                    $('#modal-remote').modal('show');
                    $('#modal-remote').find('.modal-content').html(data.popupDenied);
                }
            } else {
                Growl.error(data.feedback);
            }

        }).fail(function () {
            Growl.error500();
        }).always(function(){
            $button.button('reset');
        })
    });
</script>