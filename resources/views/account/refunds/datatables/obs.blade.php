@if(@$row->refund_control->customer_obs)
    <span data-toggle="tooltip" title="{{ @$row->refund_control->customer_obs }}">
        {{ str_limit($row->refund_control->customer_obs, 20) }}
    </span>
@endif

@if(@$row->refund_control->filepath)
    @if($row->refund_control->obs)
        <br/>
    @endif
    <a href="{{ asset($row->refund_control->filepath) }}" target="_blank">
        <i class="fas fa-file"></i> Ver Comprovativo
    </a>
@endif