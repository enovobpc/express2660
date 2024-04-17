<?php
if(@$targetSources) {
    $totalSources = count(@$targetSources);
    $parts = $totalSources > 5 ? roundUp($totalSources/3) : $totalSources;
    $targetSources = array_chunk($targetSources, $parts, true);
    $parts = count($targetSources);
}
?>
@if(empty(@$targetSources))
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="m-t-100 m-b-100">
                    <h4 class="text-muted text-center">
                        <i class="fas fa-info-circle"></i>
                        @if(@$invoice->exists)
                            Não é possível imputar custos do tipo <span>{{ @$invoice->type->name }}</span>
                        @else
                            Não há custos para imputar<br/>
                            <small>Primeiro grave ou preencha os campos da fatura.</small>
                        @endif
                    </h4>
                </div>
            </div>
        </div>
    </div>
@else
<div>
    <div class="row">
        <div class="col-sm-9">
            <h4 class="m-0 m-b-5 bold">Imputar e distribuir custos</h4>
            <p class="text-muted">Distribua o valor da fatura por várias rubricas</p>
        </div>
        <div class="col-sm-3">
            <h3 class="m-0 text-right" style="margin-top: -10px">
                <small>Por imputar</small><br/>
                <span class="total-unsigned">{{ @$invoice->assigned_targets ? (number($invoice->subtotal - array_sum($invoice->assigned_targets))) : '0.00' }}</span>{{Setting::get('app_currency') }}
            </h3>
        </div>
        <div>
        @foreach($targetSources as $targetSource)
            <div class="col-sm-4">
                <table class="table table-condensed">
                    <tr>
                        <td class="bg-gray-light bold">Rúbrica</td>
                        <td class="bg-gray-light bold w-120px">Valor a Imputar</td>
                    </tr>
                    @foreach($targetSource as $sourceId => $sourceName)
                        <tr>
                            <td class="vertical-align-middle">
                                @if(@$targetType == 'Shipment')
                                    <b>{{ @$sourceName['tracking_code'] }}</b> &bullet; <small>{{ @$sourceName['date']  }}</small><br/>
                                    <small>{{ @$sourceName['sender_name'] }}</small>
                                @else
                                    {{ $sourceName }}
                                @endif
                            </td>
                            <td>
                                <div class="input-group">
                                    @if(@$targetType == 'Shipment')
                                        <?php $sourceId = $sourceName['id'] ?>
                                        {{ Form::text('assigned_targets['.$sourceId.']', @$invoice->assigned_targets[$sourceId], ['class' => 'form-control decimal input-sm assigned-amount']) }}
                                    @else
                                        {{ Form::text('assigned_targets['.$sourceId.']', @$invoice->assigned_targets[$sourceId], ['class' => 'form-control decimal input-sm assigned-amount']) }}
                                    @endif
                                    <div class="input-group-addon">€</div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endforeach
        </div>
    </div>
</div>
@endif