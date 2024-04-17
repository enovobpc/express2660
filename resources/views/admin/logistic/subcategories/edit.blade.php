{{ Form::model($subcategory, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('customer_id', __('Cliente Associado')) }}
                {{ Form::select('customer_id', [$subcategory->customer_id => @$subcategory->customer->name], null, ['class' => 'form-control', 'data-placeholder' => '']) }}
            </div>
            <div class="form-group">
                {{ Form::label('category_id', __('Categoria')) }} <i class="fas fa-spin fa-circle-notch bloading" style="display: none"></i>
                {{ Form::select('category_id', ['' => ''] + $categories, null, ['class' => 'form-control select2']) }}
            </div>
            <div class="form-group is-required">
                {{ Form::label('name', __('Designação')) }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}

<script>
    $('.modal .select2').select2(Init.select2());

    $(".modal select[name=customer_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.logistic.products.search.customer') }}")
    });

    $('.modal select[name=customer_id]').on('change', function() {
        $('.bloading').show();
        $.post("{{ route('admin.logistic.brands.getList', 'categories') }}", {customer: $(this).val()}, function(data){
            $('[name="category_id"]').select2('destroy')
            $('[name="category_id"]').replaceWith(data.html)
        }).always(function(){
            $('[name="category_id"]').select2(Init.select2());
            $('.bloading').hide();
        })
    })

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-subcategories').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                oTableSubcategories.draw(); //update datatable
                Growl.success(data.feedback)
                $('#modal-remote-xs').modal('hide');
            } else {
                Growl.error(data.feedback)
            }

        }).error(function () {
            Growl.error500();
        }).always(function(){
            $button.button('reset');
        })
    });
</script>

