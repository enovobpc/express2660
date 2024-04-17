<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Respostas ao Formulário')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="list-group">
                @foreach($vehicles as $key => $vehicle)
                    <button class="list-group-item {{ !$key ? 'active' : '' }}" data-vehicle="{{ $vehicle->vehicle_id }}">
                        {{ $vehicle->vehicle->name }}
                    </button>
                @endforeach
            </div>
        </div>
        <div class="col-sm-9">
            <div class="answers-table">
                @include('admin.fleet.checklists.partials.answers_table')
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    </div>
</div>
<style>
    .list-group-item {
        padding: 8px 10px;
        outline: none;
    }

    .list-group-item.active,
    .list-group-item.active:hover,
    .list-group-item.active:focus {
        border: #777;
        background-color: #777;
    }
</style>

<script>
    $('.list-group-item').on('click', function(){
        var vehicleId = $(this).data('vehicle');

        $('.list-group-item').removeClass('active');
        $(this).addClass('active');

        $('.answers-table').html('<div class="text-center m-t-40"><i class="fas fa-spin fa-circle-notch"></i> A carregar...</div>')
        $.post('{{ route('admin.fleet.checklists.answer.load') }}', {vehicle:vehicleId}, function(data){
            $('.answers-table').html(data)
        })
    })
</script>