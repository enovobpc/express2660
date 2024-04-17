<div class="box no-border m-b-15">
    <div class="box-body p-5">
        <div class="row">
            <div class="col-sm-10">
                <div class="pull-left m-l-10">
                    <h3 class="m-0">
                        <b>{{ $ticket->code }} - {{ $ticket->subject }}</b>
                    </h3>
                    <p class="m-t-5 m-b-5 text-muted">
                        @if($ticket->customer_id)
                            <i class="fas fa-user"></i> {{ $ticket->customer->name }} |
                        @else
                            @if($ticket->name || $ticket->email)
                            <i class="fas fa-envelope"></i> {{ $ticket->name }} ({{ $ticket->email }}) |
                            @endif
                        @endif
                        <i class="far fa-clock"></i> @trans('Criado em') {{ $ticket->created_at }}
                    </p>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="pull-right">
                    <span class="label label-{{ trans('admin/customers_support.categories-labels.'.$ticket->category) }} pull-right m-r-10 m-t-10 fs-20">
                        {{ trans('admin/customers_support.categories.'.$ticket->category) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
