@if($row->charge_price)
    <div class="w-130px editor-block" style="position: relative">
        <div class="edit-btn-group w-100">
            <h4 class="m-t-0 m-b-5 bold pull-left fs-14">{{ money($row->charge_price, Setting::get('app_currency')) }}</h4>
            <button class="btn btn-xs btn-default pull-right m-l-3 edit-obs" style="padding: 0 3px;">
                Obs
            </button>
            <button class="btn btn-xs btn-default pull-right m-l-5 edit-price-btn" style="padding: 0 3px;">
                <i class="fas fa-pencil-alt"></i>
            </button>

        </div>
        <div class="conferrer-popup" style="display: none">
            <div class="col-sm-6">
                <div class="form-group">
                    {{ Form::label('obs_refund', 'Obs. Internas') }}
                    {{ Form::textarea('obs_refund', null, ['class' => 'form-control w-100', 'style' => 'font-size: 14px', 'rows' => 2, 'placeholder' => 'Observações']) }}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {{ Form::label('obs_customer', 'Obs. Cliente') }}
                    {{ Form::textarea('obs_customer', null, ['class' => 'form-control w-100', 'style' => 'font-size: 14px', 'rows' => 2, 'placeholder' => 'Observações']) }}
                </div>
            </div>
        </div>
        <div class="input-group input-group-sm edit-price m-b-3" style="width: 130px; display: none">
            <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
            {{ Form::text('charge_price', $row->charge_price, ['class' => 'form-control', 'style' => 'font-size: 14px']) }}
        </div>
        {{ Form::select('refund_payment_method', ['' => ''] + trans('admin/refunds.payment-methods'), null, array('class' => 'form-control input-sm select2 w-100')) }}
    </div>
@endif