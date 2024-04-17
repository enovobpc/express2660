<?php $method = @$resolution->history->shipment->webservice_method; ?>
{{ Form::model($resolution, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body modal-incidences">
    @if($resolution->shipment_history_id)
        {{ Form::hidden('shipment_history_id', $resolution->shipment_history_id) }}

        @if(@$resolution->history->provider_agency_code && @$resolution->history->provider_agency->code)
            <div style="margin: -15px -15px 15px -15px;padding: 10px 15px;background: #e0e0e0;border-bottom: 1px solid #ccc;">
                <b>{{ @$resolution->history->provider_agency->code }} {{ @$resolution->history->provider_agency->name }}</b>
                <span class="pull-right">
                <i class="fas fa-phone" data-toggle="tooltip" title="Última atualização ao contacto em {{ @$resolution->history->provider_agency->updated_at }}"></i>
                    {{ @$resolution->history->provider_agency->phone }}
            </span>
            </div>
        @endif
        @if(@$resolution->history->incidence->name || @$resolution->history->obs)
            <div style="margin: -15px -15px 15px -15px;
    background: #f2f2f2;
    border-bottom: 1px solid #ddd;
    padding: 15px;">
                <h4 class="m-0 bold">{{ @$resolution->history->incidence->name }}</h4>
                @if(@$resolution->history->obs)
                    <p class="text-muted m-t-3 m-b-0">{{ @$resolution->history->obs }}</p>
                @endif
            </div>
        @endif
    @else
        <div class="form-group is-required">
            {{ Form::label('shipment_history_id', 'Resolução para a incidência') }}
            {{ Form::select('shipment_history_id', $incidences, null, ['class' => 'form-control select2', 'required']) }}
        </div>
    @endif
    <div class="form-group is-required">
        {{ Form::label('resolution_type_id', trans('account/incidences.create.action')) }}<br/>
        {!! Form::selectWithData('resolution_type_id', $resolutionsTypes, null, ['class' => 'form-control select2', 'required']) !!}
    </div>
    <div class="form-group">
        {{ Form::label('obs', trans('account/incidences.create.details')) }}
        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3, 'required']) }}
    </div>
</div>
<div class="modal-footer">
    {{--<div class="extra-options" style="display: none">
        <div class="input-group input-group-email pull-left" style="width: 280px; margin-top: -1px" data-toggle="tooltip" title="Enviar um e-mail automático com a informação de resolução para a agência que originou a incidência.">
            <div class="input-group-addon">
                <i class="fas fa-envelope"></i>
                {{ Form::checkbox('send_email', 1, @$resolution->history->provider_agency->email ? true : false) }}
            </div>
            {{ Form::text('email', @$resolution->history->provider_agency->email, ['class' => 'form-control pull-left nospace lowercase', 'placeholder' => 'E-mail do fornecedor']) }}
        </div>
        <div class="clearfix"></div>
    </div>--}}
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
        <button type="submit" class="btn btn-primary"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> {{ trans('account/global.word.loading') }}...">{{ trans('account/global.word.submit') }}
        </button>
    </div>
</div>
<style>
    .modal-incidences .select2-container {
        max-width: 100%;
    }
</style>
@if(@$method && in_array($method, \App\Models\ShipmentIncidenceResolution::AVAILABLE_PROVIDERS))
    <div style="display: none">
        {{ Form::checkbox('submit_webservice', 1, $resolution->submited_at ? false : true) }}
    </div>
@endif
{{ Form::close() }}

{{ Html::script(asset('vendor/devbridge-autocomplete/dist/jquery.autocomplete.js')) }}
<script>
    $('#modal-remote .select2').select2(Init.select2());

    /**
     * SEARCH SENDER
     * ajax method
     */
    $('[name="email"]').autocomplete({
        serviceUrl: "{{ route('admin.address-book.search') }}",
        minChars: 2,
        onSearchStart: function () {

        },
        /* beforeRender: function (container, suggestions) {
             container.find('.autocomplete-suggestion').each(function(key, suggestion, data){
                 $(this).append('<div class="autocomplete-address">' + suggestions[key].address + ' - ' + suggestions[key].zip_code + ' ' +  suggestions[key].city + '</div>')
             });
         },*/
        onSelect: function (suggestion) {
        },
    });

    $('[name="resolution_type_id"]').on('change', function(e){
        var status = $(this).find(':selected').data('status');

        $('.status-alert').hide();
        if(status != '' && typeof status !== 'undefined') {
            $('.status-alert').show().find('span').html(status);
        }
    });

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-incidence-resolution').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                Growl.success(data.feedback)
                $('#modal-remote').modal('hide');
                $('.table-incidence-resolutions').replaceWith(data.html);

                if(typeof oTableIncidences !== 'undefined') {
                    oTableIncidences.draw();
                }

            } else {
                Growl.error(data.feedback)
            }

        }).fail(function () {
            Growl.error500()
        }).always(function(){
            $button.button('reset');
        })
    });
</script>