<div class="input-group searchbox-customers w-100" data-target=".customers-data-list>li">
    <i class="fas fa-fw fa-search"></i>
    {{ Form::text('customer_name', null, ['class' => 'form-control', 'placeholder' => 'Procurar cliente ou prospect...', 'autocomplete' => 'search2','autofill' => 'search1']) }}
</div>
<div class="customers-list">
    <ul class="customers-data-list list-unstyled nicescroll">
        @foreach($customers as $customer)
            <li class="{{ empty($customer->map_lat) ? 'disabled' : '' }}" data-lat="{{ $customer->map_lat }}"
                data-lng="{{ $customer->map_lng }}"
                data-id="{{ $customer->id }}"
                data-html="<b>{{ $customer->name }}</b><br/>
                {{ $customer->address }}<br/>
                {{ $customer->zip_code }} {{ $customer->city }}<br/><a href='https://www.google.com/maps/dir/Current+Location/{{ $customer->map_lat }},{{ $customer->map_lng }}' target='blank' class='btn btn-sm btn-primary' style='margin-top: 10px; display: block; text-align: center;'>Iniciar Navegação</a>"
                @if(empty($customer->map_lat))
                    <span class="label label-default">Sem Localização</span><br/>
                @endif
                <b>{{ $customer->name }}</b><br/>
                {{ $customer->address }}<br/>
                {{ $customer->zip_code }} {{ $customer->city }}
            </li>
        @endforeach
    </ul>
</div>