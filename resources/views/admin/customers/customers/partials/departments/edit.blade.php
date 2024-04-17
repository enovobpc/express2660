<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
@if($department->exists)
<div class="modal-body p-b-0">
    <div class="tabbable-line" style="margin: -15px">
        <ul class="nav nav-tabs">
            <li class="{{ Request::get('tab') == 'status' ? '' : 'active' }}">
                <a href="#tab-department-info" data-toggle="tab">
                    @trans('Informação')
                </a>
            </li>
            <li class="{{ Request::get('tab') == 'status' ? 'active' : '' }}">
                <a href="#tab-department-login" data-toggle="tab">
                    @trans('Área de Cliente')
                </a>
            </li>
        </ul>
        <div class="tab-content" style="padding: 15px; background: #fff">
            <div class="tab-pane active" id="tab-department-info" >
                @include('admin.customers.customers.partials.departments.info')
            </div>
            <div class="tab-pane" id="tab-department-login">
                @include('admin.customers.customers.partials.departments.login')
            </div>
        </div>
    </div>
</div>
@else
<div class="modal-body">
    @include('admin.customers.customers.partials.departments.info')
</div>
@endif

<script>
    $('.select2').select2(Init.select2());

    $('.modal [name="zip_code"], .modal [name="country"]').on('change', function() {
        var $form = $(this).closest('form');
        var zipCode = $form.find('[name="zip_code"]').val();
        var country = $form.find('[name="country"]').val();
        ZipCode.validateInput(country, zipCode, $form);
    })
</script>