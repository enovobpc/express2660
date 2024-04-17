<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span class="font-size-32px" aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
    <h4 class="modal-title">Ordenar</h4>
</div>
<div class="modal-body">
    <button type="button" class="btn btn-xs btn-default alphabetically-sort" data-sort="asc">
        <i class="fa fa-sort-alpha-asc"></i> Alfabéticamente
    </button>
    <button type="button" class="btn btn-xs btn-default alphabetically-sort" data-sort="desc">
        <i class="fa fa-sort-alpha-desc"></i> Alfabéticamente
    </button>
    <div class="spacer-15"></div>
    <ul class="list-sort-vertical list-unstyled sortable">
        @foreach($items as $key => $item)
        <li data-id="{{ $item->id }}">{{ str_limit($item->message, 30) }}</li>
        @endforeach
    </ul>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="button" class="btn btn-primary save-sort" data-loading-text="Guardar..." disabled>Guardar</button>
</div>

{{ Html::script('/vendor/html.sortable/dist/html.sortable.min.js')}}
<script>

    $('.sortable').sortable({
        forcePlaceholderSize: true,
        placeholder: '<li><div class="width-300px"></div></li>'
    }).bind('sortupdate', function (e, ui) {
        $('.save-sort').removeAttr('disabled');
    });

    /**
     * save sort
     */
    $('.save-sort').on('click', function (e) {
        e.preventDefault();

        $button = $(this);
        $button.button('loading');

        //get array of ordered IDs
        var dataList = $(".sortable > li").map(function () {
            return $(this).data("id");
        }).get();


        $.post("{{ $route }}", {'ids[]': dataList}, function (data){
            $.bootstrapGrowl(data.message, {
                type: data.type,  
                align: 'center', 
                width: 'auto', 
                delay: 8000
            });
            $(".sortable").sortable("destroy");
            $('#modal-remote').modal('hide');
            oTable.draw(); //update datatable

        }).fail(function () {
            $.bootstrapGrowl("Ocorreu um erro tente mais tarde", {
                type: 'danger',
                align: 'center', 
                width: 'auto', 
                delay: 8000
            });
        }).always(function () {
            $button.button('reset');
        });
    });
    
    $('.alphabetically-sort').on('click', function () {
        var x = 1, y = -1;
        if($(this).data('sort') == 'asc') {
            x = -1; y = 1;
        }

        var sortedDivs = $('.list-sort-vertical li').sort(function (a, b) {
            return (a.textContent < b.textContent) ? x : y;
        });
        
        $(".list-sort-vertical").html(sortedDivs);
        $('.save-sort').removeAttr('disabled');
        $('.sortable').sortable();
    });
    
</script>