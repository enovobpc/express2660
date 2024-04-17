@if($row->customer_id)
<div class="editor-block" style="position: relative">
    <div class="edit-btn-group w-100">
        <h4 class="m-t-0 m-b-5 bold pull-left fs-14">{{ @$row->customer->code }}</h4><br/>
        <button class="btn btn-xs btn-default edit-customer-btn pull-left" style="padding: 0 3px;">
            <i class="fas fa-pencil-alt"></i> Alterar
        </button>
    </div>
    <div class="edit-customer m-b-3" style="width: 130px; display: none">
        {{ Form::select('customer_id', [], ['class' => 'form-control', 'style' => 'font-size: 14px']) }}
    </div>
</div>
@endif