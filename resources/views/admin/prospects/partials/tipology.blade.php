{{ Form::model($prospect, $formOptions) }}
<div class="box no-border">
    <div class="box-body">
        @include('admin.prospects.partials.tipology_form')
    </div>
</div>
@if($prospect->exists)
{{ Form::hidden('average_weight') }}
{{ Form::select('enabled_services[]', $servicesList, null, ['class' => 'form-control hide', 'multiple' => true]) }}
@endif
{{ Form::close() }}
