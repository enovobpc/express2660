{{ Form::model($category, $formOptions) }}
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
                {{ Form::select('customer_id', [$category->customer_id => @$category->customer->name], null, ['class' => 'form-control', 'data-placeholder' => '']) }}
            </div>
            <div class="form-group">
                {{ Form::label('family_id', __('Familia')) }} <i class="fas fa-spin fa-circle-notch bloading" style="display: none"></i>
                {{ Form::select('family_id', ['' => ''] + $families, $category->family_id, ['class' => 'form-control select2', 'data-placeholder' => '']) }}
            </div>
            <div class="form-group">
                {{ Form::label('name', __('Designação')) }}
                {{ Form::text('name', null, ['class' => 'form-control']) }}
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
        $.post("{{ route('admin.logistic.brands.getList', 'families') }}", {customer: $(this).val()}, function(data){
            $('[name="family_id"]').select2('destroy')
            $('[name="family_id"]').replaceWith(data.html)
        }).always(function(){
            $('[name="family_id"]').select2(Init.select2());
            $('.bloading').hide();
        })
    })
    
    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-categories').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                oTableCategories.draw(); //update datatable
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

