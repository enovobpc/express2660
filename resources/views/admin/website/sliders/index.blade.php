@section('title')
    Sliders
@stop

@section('content-header')
    Sliders
@stop

@section('breadcrumb')
<li class="active">
    Sliders
</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <ul class="filters-list list-inline pull-left margin-0">
                            <li>
                                <a href="{{ route('admin.website.sliders.create') }}" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal-remote-lg">
                                    <i class="fa fa-plus"></i> Novo Slider
                                </a>
                            </li>
                            <li>
                                @if(!$sliders->isEmpty())
                                    {{ Form::button('Gravar Ordenação', array('class' => 'btn btn-sm btn-primary',
                                    'id' => 'save-order',
                                    'data-loading-text' => 'Gravar...',
                                    'disabled' => true))}}
                                    {{--<span class="text-muted margin-left-15"><i class="fa fa-question-circle"></i> Arraste as imagens para as re-ordenar</span>--}}
                                @endif
                            </li>
                            <li class="fltr-primary w-150px">
                                <strong>Língua</strong><br class="visible-xs"/>
                                <div class="pull-left form-group-sm w-90px">
                                    {{ Form::select('locale', ['' => 'Todos'] + app_locales(), fltr_val(Request::all(), 'locale'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                            <li class="fltr-primary w-140px">
                                <strong>Visivel</strong><br class="visible-xs"/>
                                <div class="pull-left form-group-sm w-80px">
                                    {{ Form::select('visible', ['' => 'Todos', '1' => 'Visivel', '0' => 'Oculto'], fltr_val(Request::all(), 'visible'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <hr class="m-t-10 m-b-15"/>
                <div class="row">
                    <div class="col-xs-12">
                        @if($sliders->isEmpty())
                        <h4 class="text-center text-muted m-b-30">
                            <i class="fa fa-info-circle fs-20 m-b-5"></i><br/>
                            Não existem sliders para apresentar
                        </h4>
                        @else
                        <ul class="slider-thumbnail sortable">
                            @foreach ($sliders as $image)
                            @include('admin.website.sliders.partials.image')
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('plugins')
{{ HTML::script('vendor/html.sortable/dist/html.sortable.min.js')}}
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {

        $('.sortable').sortable({
            forcePlaceholderSize: true,
            placeholder: '<li><div class="width-300px"></div></li>'
        }).bind('sortupdate', function (e, ui) {
            $('#save-order').removeAttr('disabled');
        });

        //save new banner order
        $('#save-order').on('click', function (e) {
            e.preventDefault();

            $button = $(this);
            $button.button('loading');

            var dataList = $(".sortable > li").map(function () {
                return $(this).data("id");
            }).get();

            $.post("{{ route('admin.website.sliders.sort.update') }}", {'ids[]': dataList}, function (data) {
                $.bootstrapGrowl(data.message, {type: data.type, align: 'center', width: 'auto'});
            }).fail(function () {
                $.bootstrapGrowl('Ocorreu um erro interno ao gravar a ordenação.', {type: 'danger', align: 'center', width: 'auto'});
            }).always(function () {
                $button.button('reset');
            });

        });
    });

    $(document).on('change', '.filters-list select', function () {
        var url   = URL.current();
        var param = $(this).attr('name');
        var value = $(this).val();
        url = URL.updateParameter(url, param, value);
        window.location.href = url;
    })
</script>
@stop