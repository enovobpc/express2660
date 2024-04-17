@section('title')
Viaturas
@stop

@section('content-header')
    Viaturas
    <small>
        @trans('Criar viatura')
    </small>
@stop

@section('breadcrumb')
    <li class="active">@trans('Gestão de Frota')</li>
    <li>
        <a href="{{ route('admin.fleet.vehicles.index') }}">
            @trans('Viaturas')
        </a>
    </li>
    <li class="active">
        @trans('Criar viatura')
    </li>
@stop

@section('content')
    <div class="row row-5">
        <div class="col-md-12">
            @include('admin.fleet.vehicles.partials.info')
        </div>
    </div>
@stop

@section('scripts')
<script type="text/javascript">

    $('[data-dismiss="fileinput"]').on('click', function () {
        $('[name=delete_photo]').val(1);
    })

    $('[name="brand_id"]').on('change', function(){

        $('.brand-loading').show();

        $.post('{{ route('admin.fleet.vehicles.get.brand-models') }}', {'brand' : $(this).val()}, function(data){
            $('#model-id').html(data);
        }).fail(function(){
            $('[name="model_id"]').html([]);
        }).always(function(){
            $('.brand-loading').hide();
            $('.select2').select2(Init.select2());
        });
    })
</script>
@stop