@if(!hasModule('products'))
    @include('admin.partials.denied_message')
@else
<div class="box no-border">
    <div class="box-body">

        <h4 class="bold text-primary"><i class="fas fa-money"></i>@trans('Productos')</h4>
        @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables'))
        {{ Form::model($customer, ['route' => ['admin.customers.update', $customer->id], 'method' => 'PUT']) }}
        @endif

        <div class="row row-5">
            @if(@$products)
                @foreach($products as $product)
                    <div class="col-sm-2">
                        <div class="form-group">
                            <div class="input-group" style="margin-bottom: -1px">
                                <span class="input-group-addon text-uppercase" style="min-width: 50px;">{{ $product->name }}</span>
                                {{ Form::text('product_price['.$product->id.']', null, ['class' => 'form-control decimal', 'maxlength' => 5, 'placeholder' => $product->price ? 'PVP: ' . money($product->price, Setting::get('app_currency')) : '']) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'products'))
        {{ Form::hidden('seller_id') }}
        {{ Form::submit(__('Gravar preços produtos'), array('class' => 'btn btn-primary' ))}}
        {{ Form::close() }}
        @endif
    </div>
</div>
@endif