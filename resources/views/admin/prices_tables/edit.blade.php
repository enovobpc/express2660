@section('title')
    Tabelas Preço Gerais
@stop

@section('content-header')
    @trans('Tabelas Preço Gerais')
    <small>
        @trans('Gerir Tabela')
    </small>
@stop

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.prices-tables.index') }}">
            @trans('Tabelas Preço Gerais')
        </a>
    </li>
    <li class="active">
        @trans('Gerir Tabela')
    </li>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box no-border m-b-15">
            <div class="box-body p-5">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="pull-left">
                            <h4 class="m-t-5 m-b-5 m-l-10 pull-left"><i class="fas fa-square" style="color: {{ $priceTable->color }}"></i> {{ $priceTable->name }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-5">
    <div class="col-md-3 col-lg-2">
        <div class="box box-solid">
            <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked">
                    <li class="active">
                        <a href="#tab-prices" data-toggle="tab"><i class="fas fa-fw fa-euro-sign"></i> @trans('Preços')</a>
                    </li>
                    <li>
                        <a href="#tab-info" data-toggle="tab"><i class="fas fa-fw fa-info-circle"></i> @trans('Dados Tabela')</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-9 col-lg-10">
        <div class="tab-content">
            <div class="tab-pane active" id="tab-prices">
                @include('admin.prices_tables.partials.prices')
            </div>
            <div class="tab-pane" id="tab-info">
                @include('admin.prices_tables.partials.info')
            </div>
        </div>
    </div>
</div>
@include('admin.prices_tables.partials.modals.import_prices')
@stop

@section('styles')
    {{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.css') }}
    {{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker-fontawesome.css') }}
@stop

@section('scripts')
    {{ Html::script('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.js') }}
    <script>
        $(document).ready(function () {
            $('select[name="color"]').simplecolorpicker({theme: 'fontawesome'});
        })


        $(document).on('change', '[name="origin_zone"]', function(){
            var zone = $(this).val();
            var unity = $(this).data('unity');
            var url = Url.current();
            url = Url.updateParameter(url, 'origin_zone', zone);
            url = Url.updateParameter(url, 'unity', unity);
            window.location = url;
        })

        $(document).on('click', '.prices-tables-toggle', function(e){
            e.preventDefault();
            if($('.panel-collapse.in').length) {
                $('.panel-collapse').removeClass('in');
            } else {
                $('.panel-collapse').addClass('in');
            }
        })

        $(document).on('click', '.btn-pricetb-adv-opts', function(e){
            e.preventDefault();
            $target = $(this).closest('.panel-group')


            if(!$target.find('.panel-collapse').hasClass('in')) {
                $target.find('.pricetb-adv-opts').show();
                $target.find('.panel-heading').trigger('click');
            } else {
                $target.find('.pricetb-adv-opts').slideToggle();
            }
        })

        /**
         * Update prices
         */
        $('.form-update-prices').on('submit', function(e){
            e.preventDefault()

            var newVal, val, percent;
            var $form = $(this);
            var percent = $form.find('[name="update_percent"]').val() / 100
            var $target = $('#' + $form.find('[name="update_target"]').val() + '-services');

            if(percent != 0 && percent != "") {

                $target.find('input[name*="price"]').each(function(){
                    val = $(this).val();

                    if(val != "") {
                        if($form.find('[name="update_signal"]').val() == 'sub') {
                            newVal = parseFloat(val) - (parseFloat(val) * percent);
                        } else {
                            newVal = parseFloat(val) + (parseFloat(val) * percent);
                        }

                        $(this).val(newVal.toFixed(2));
                    }
                })

                $.bootstrapGrowl('Atualizado com sucesso. Grave a tabela para gravar as alterações', {type: 'success', align: 'center', width: 'auto', delay: 8000});
            }
        })
    </script>
@stop