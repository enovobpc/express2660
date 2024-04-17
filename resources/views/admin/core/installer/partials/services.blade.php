<div class="col-xs-12">
    <h4 class="bold m-t-0">4. SERVIÇOS DISPONÍVEIS</h4>
</div>
@foreach($services as $id => $service)
    <div class="col-sm-3">
        <div class="checkbox m-t-0">
            <label style="padding-left: 0">
                {{ Form::checkbox('services[]', $id) }}
                {{ $service }}
            </label>
        </div>
    </div>
@endforeach
<div class="clearfix"></div>