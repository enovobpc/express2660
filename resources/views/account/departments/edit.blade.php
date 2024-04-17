<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
@if($department->exists)
<div class="modal-body p-b-0">
    <div class="tabbable-line" style="margin-top: -15px">
        <ul class="nav nav-tabs">
            <li class="{{ Request::get('tab') == 'status' ? '' : 'active' }}">
                <a href="#tab-department-info" data-toggle="tab">
                    Informação
                </a>
            </li>
            <li class="{{ Request::get('tab') == 'status' ? 'active' : '' }}">
                <a href="#tab-department-login" data-toggle="tab">
                    Área de Cliente
                </a>
            </li>
        </ul>
        <div class="tab-content m-b-0">
            <div class="tab-pane active" id="tab-department-info">
                @include('account.departments.partials.info')
            </div>
            <div class="tab-pane" id="tab-department-login">
                @include('account.departments.partials.login')
            </div>
        </div>
    </div>
</div>
@else
<div class="modal-body">
    @include('account.departments.partials.info')
</div>
@endif

<script>
    $('.select2').select2(Init.select2());
</script>