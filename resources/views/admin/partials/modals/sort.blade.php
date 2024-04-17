<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span><span class="sr-only">Fechar</span></button>
    <h4 class="modal-title">@trans('Ordenar')</h4>
</div>
<div class="modal-body">
    <button type="button" class="btn btn-xs btn-default alphabetically-sort" data-sort="asc">
        <i class="fas fa-sort-alpha-down"></i> @trans('Alfabéticamente') [A-Z]
    </button>
    <button type="button" class="btn btn-xs btn-default alphabetically-sort" data-sort="desc">
        <i class="fas fa-sort-alpha-up"></i> @trans('Alfabéticamente') [Z-A]
    </button>
    <div class="sp-15"></div>
    <ul class="list-sort-vertical list-unstyled sortable">
        <?php $pos = 0; ?>
        @foreach($items as $key => $item)
            <?php $pos++; ?>
            <li data-id="{{ $item->id }}">
                <div class="pull-left">{{ $item->name }}</div>
                <div class="pull-right text-muted"><i>#{{ $pos }}</i></div>
                <div class="clearfix"></div>
            </li>
        @endforeach
    </ul>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="button" class="btn btn-primary save-sort" data-loading-text="@trans('Aguarde')..." disabled>@trans('Gravar')</button>
</div>
{{ Html::script('/vendor/html.sortable/dist/html.sortable.min.js')}}

<style>
    ul.sortable {
        cursor: move;
    }
</style>

<script>
    var oTable;

    $('.sortable').sortable({
        forcePlaceholderSize: true,
        placeholder: '<li><div class="w-300px"></div></li>'
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
        var dataList = $button.closest('.modal').find(".sortable > li").map(function () {
            return $(this).data("id");
        }).get();

        $.post("{{ $route }}", {'ids[]': dataList}, function (data){
            if(data.result) {
                Growl.success(data.message)
                $(".sortable").sortable("destroy");
                $('#modal-remote').modal('hide');
                oTable.draw(); //update datatable
            } else {
                Growl.error(data.message)
            }

        }).fail(function () {
            Growl.error500()
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