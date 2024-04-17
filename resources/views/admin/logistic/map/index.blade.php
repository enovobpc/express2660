@section('title')
    Mapa de Armazém
@stop

@section('content-header')
    Mapa de Armazém
@stop

@section('breadcrumb')
<li class="active">@trans('Gestão Logística')</li>
<li class="active">@trans('Mapa de Armazém')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-warehouse" data-toggle="tab">@trans('Planta de Armazém')</a></li>
                {{--@if(Auth::user()->perm('logistic_warehouses'))
                <li><a href="#tab-warehouses" data-toggle="tab">Armazéns</a></li>
                @endif--}}
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-warehouse">
                    @include('admin.logistic.map.tabs.warehouse')
                </div>
                {{--@if(Auth::user()->perm('logistic_warehouses'))
                <div class="tab-pane" id="tab-warehouses">
                    @include('admin.logistic.locations.partials.warehouses')
                </div>
                @endif--}}
            </div>
        </div>
    </div>
</div>
    <style>
        .map-container {
            position: relative;
            width: 100%;
            height: 600px;
            border: 1px solid #333;
        }

        .map-tools {
            border: 1px solid #ccc;
            border-radius: 3px;
            margin-bottom: 10px;
        }

        .map-item-box {
            border: 1px solid #ece4ab;
            height: 30px;
            width: 80px;
            margin: 10px;
            background: #fdf3cfcc;
            font-size: 12px;
            padding: 7px;
            line-height: 12px;
            text-align: center;
        }

        .resize-drag {
            position: absolute;
            background-color: #29e;
            color: white;
            font-size: 12px;
            font-family: sans-serif;
            border-radius: 2px;
            padding: 3px;
            touch-action: none;
            box-sizing: border-box;
            width: 100px;
        }

        .resize-drag input {
            color: #000;
        }
    </style>
@stop

@section('scripts')
{{ Html::script('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.js') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.css') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker-fontawesome.css') }}
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script type="text/javascript">

    $(document).ready(function () {
        $('select[name="color"]').simplecolorpicker({theme: 'fontawesome'});
        $('#modal-remote .select2').select2(Init.select2());
    })

    /**
     * event on click
     */
    $(document).on('click', '.resize-drag', function(){
        var id = $(this).attr('id');
        $('[name="selected_obj"]').val(id);

        var title = $(this).find('.title').html()
        $('[name="box_title"]').val(title)

    })

    /**
     * TOOLS AREA
     */
    //COLOR
    $(document).on('change', '[name="color"]', function(){
        var value = $(this).val();
        var $obj = $('#' + $('[name="selected_obj"]').val());
        $obj.css('background-color', value);
        $obj.find('[name="color"]').val(value)
    })

    //TEXT
    $(document).on('change keyup', '[name="box_title"]', function(){
        var value = $(this).val();
        var $obj = $('#' + $('[name="selected_obj"]').val());
        $obj.find('.title').html(value)
        $obj.find('[name="title"]').val(value)
    })

    /**
     * Add new item
     */
    $(document).on('click', '.map-item-box', function () {
        var $item = $('.recize-model').clone();

        objId = Str.random(32);

        $item.removeClass('recize-model')
        $item.attr('id', objId)
        $('.map-container').append($item);
    })


    interact('.resize-drag')
        .resizable({
            // resize from all edges and corners
            edges: {
                //left: true,
                right: true,
                bottom: true,
                //top: true
            },

            listeners: {
                move (event) {
                    var target = event.target
                    var x = (parseFloat(target.getAttribute('data-x')) || 0)
                    var y = (parseFloat(target.getAttribute('data-y')) || 0)
                    var $obj = $('#'+target.getAttribute('id'))

                    console.log('ola = ' + $obj.attr('id'));

                    // update the element's style
                    target.style.width = event.rect.width + 'px'
                    target.style.height = event.rect.height + 'px'

                    // translate when resizing from top or left edges
                    x += event.deltaRect.left
                    y += event.deltaRect.top

                    target.style.webkitTransform = target.style.transform =
                        'translate(' + x + 'px,' + y + 'px)'

                    target.setAttribute('data-x', x)
                    target.setAttribute('data-y', y)

                    $obj.find('[name="left"]').val(x);
                    $obj.find('[name="top"]').val(x);
                    $obj.find('[name="width"]').val(event.rect.width);
                    $obj.find('[name="height"]').val(event.rect.height);
                    //target.textContent = Math.round(event.rect.width) + '\u00D7' + Math.round(event.rect.height)
                }
            },
            modifiers: [
                // keep the edges inside the parent
                interact.modifiers.restrictEdges({
                    outer: 'parent'
                }),

                // minimum size
                interact.modifiers.restrictSize({
                    min: { width: 20, height: 20 }
                })
            ],

            inertia: true
        })
        .draggable({
            listeners: { move: window.dragMoveListener },
            inertia: true,
            modifiers: [
                interact.modifiers.restrictRect({
                    restriction: 'parent',
                    endOnly: true
                })
            ]
        })

    function dragMoveListener (event) {
        var target = event.target
        // keep the dragged position in the data-x/data-y attributes
        var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx
        var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy
        var $obj = $('#'+target.getAttribute('id'))


        // translate the element
        target.style.webkitTransform =
            target.style.transform =
                'translate(' + x + 'px, ' + y + 'px)'

        // update the posiion attributes
        target.setAttribute('data-x', x)
        target.setAttribute('data-y', y)



        $obj.find('[name="left"]').val(x);
        $obj.find('[name="top"]').val(x);
        $obj.find('[name="width"]').val(event.rect.width);
        $obj.find('[name="height"]').val(event.rect.height);
    }

    // this function is used later in the resizing and gesture demos
    window.dragMoveListener = dragMoveListener
</script>
@stop