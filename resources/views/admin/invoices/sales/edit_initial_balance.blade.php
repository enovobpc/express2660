{{ Form::open(['route' => ['admin.invoices.initial-balance.store'], 'method' => 'POST']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">@trans('Gerir saldos iniciais')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-9">
            <div class="form-group">
                {{ Form::label('filter', __('Filtrar na lista'), ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('filter', null, ['class' => 'form-control']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('doc_date', __('Data novos saldos'), ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('doc_date', $docDate, ['class' => 'form-control datepicker']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-12">
            <div style="height: 480px;
            overflow-x: scroll;
            border: 1px solid #ccc;">
            <table class="table table-condensed m-0">
                <thead>
                    <tr>
                        <th class="bg-gray w-1">@trans('Código')</th>
                        <th class="bg-gray">@trans('Designação')</th>
                        <th class="bg-gray w-110px">@trans('NIF')</th>
                        <th class="bg-gray w-110px">@trans('Data Saldo')</th>
                        <th class="bg-gray w-140px">@trans('Débito Inicial') (+)</th>
                        <th class="bg-gray w-140px">@trans('Crédito Inicial') (-)</th>
                    </tr>
                </thead>
                <?php $currency = Setting::get('app_currency'); ?>
                <tbody>
                    @foreach ($entities as $entity)
                    <tr>
                        <td>{{ $entity->code }}</th>
                        <td>
                            {{ $entity->billing_name }}
                            <div class="hide">
                                {{ $entity->name }}
                            </div>
                        </th>
                        <td>{{ strtoupper($entity->billing_country) }} {{ $entity->vat }}</td>
                        <td>
                            {{ Form::text('doc_date['.$entity->id.']', @$entity->sind_invoice->doc_date, ['class' => 'form-control input-dt datepicker']) }}
                        </td>
                        @if($entityType == 'providers')
                            <td>
                                @if(@$entity->sind_invoice->total_unpaid < @$entity->sind_invoice->total)
                                <div class="input-group input-group-money" data-toggle="tooltip" title="@trans('Já existem regularizações')">
                                    {{ Form::text('_sind_', @$entity->sind_invoice->total, ['class' => 'form-control text-right decimal', 'disabled']) }}
                                    <div class="input-group-addon">{{ $currency }}</div>
                                </div>
                                @else
                                <div class="input-group input-group-money">
                                    {{ Form::text('sind['.$entity->id.']', @$entity->sind_invoice->total, ['class' => 'form-control input-value text-right decimal'])}}
                                    <div class="input-group-addon">{{ $currency }}</div>
                                </div>
                                @endif
                            </td>
                            <td>
                                @if(@$entity->sinc_invoice->total_unpaid > @$entity->sind_invoice->total)
                                <div class="input-group input-group-money" data-toggle="tooltip" title="@trans('Já existem regularizações')">
                                    {{ Form::text('_sinc_', @$entity->sinc_invoice->total, ['class' => 'form-control  text-right decimal', 'disabled'] )}}
                                    <div class="input-group-addon">{{ $currency }}</div>
                                </div>
                                @else
                                <div class="input-group input-group-money">
                                    {{ Form::text('sinc['.$entity->id.']', @$entity->sinc_invoice->total, ['class' => 'form-control input-value text-right decimal'] )}}
                                    <div class="input-group-addon">{{ $currency }}</div>
                                </div>
                                @endif
                            </td>
                        @else
                            <td>
                                @if(!empty(@$entity->sind_invoice->doc_total_pending))
                                <div class="input-group input-group-money" data-toggle="tooltip" title="@trans('Já existem regularizações')">
                                    {{ Form::text('_sind_', @$entity->sind_invoice->doc_total, ['class' => 'form-control text-right decimal', 'disabled']) }}
                                    <div class="input-group-addon">{{ $currency }}</div>
                                </div>
                                @else
                                <div class="input-group input-group-money">
                                    {{ Form::text('sind['.$entity->id.']', @$entity->sind_invoice->doc_total, ['class' => 'form-control input-value text-right decimal'])}}
                                    <div class="input-group-addon">{{ $currency }}</div>
                                </div>
                                @endif
                            </td>
                            <td>
                                @if(!empty(@$entity->sinc_invoice->doc_total_pending))
                                <div class="input-group input-group-money" data-toggle="tooltip" title="@trans('Já existem regularizações')">
                                    {{ Form::text('_sinc_', @$entity->sinc_invoice->doc_total, ['class' => 'form-control  text-right decimal', 'disabled'] )}}
                                    <div class="input-group-addon">{{ $currency }}</div>
                                </div>
                                @else
                                <div class="input-group input-group-money">
                                    {{ Form::text('sinc['.$entity->id.']', @$entity->sinc_invoice->doc_total, ['class' => 'form-control input-value text-right decimal'] )}}
                                    <div class="input-group-addon">{{ $currency }}</div>
                                </div>
                                @endif
                            </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> @trans('A gravar...')">@trans('Gravar')</button>
</div>
{{ Form::hidden('entity', $entityType) }}
{{ Form::close() }}

<style>
     .modal .table tbody td {
        vertical-align: middle;
     }
</style>

<script>
    $('.modal .datepicker').datepicker(Init.datepicker());
    $('.modal .select2').select2(Init.select2());

    $(document).on('change', '.input-value', function(){
        var docDate = $('.modal [name="doc_date"]').val();
        $(this).closest('tr').find('.input-dt').val(docDate)
    })

    $(document).ready(function () {
      // Captura o evento keyup no elemento de entrada de texto
      $('.modal [name="filter"]').on('keyup', function () {
        var textoFiltro = $(this).val().toLowerCase(); // Obtém o valor do campo de filtro em minúsculas

        // Percorre cada linha da tabela
        $('.modal .table tbody tr').each(function () {
          var linha = $(this).text().toLowerCase(); // Obtém o texto da linha em minúsculas

          // Verifica se a linha contém o texto do filtro
          if (linha.indexOf(textoFiltro) !== -1) {
            $(this).show(); // Exibe a linha se contiver o texto do filtro
          } else {
            $(this).hide(); // Oculta a linha se não contiver o texto do filtro
          }
        });
      });
    });
   
</script>