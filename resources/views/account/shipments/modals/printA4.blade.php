{{ Form::open(['route' => ['account.shipments.get.labels', 0], 'method' => 'GET', 'target' => '_blank', 'class' => 'printerA4']) }}
<div class="modal-header">
    <h4 class="modal-title">Imprimir etiquetas A4</h4>
</div>
<div class="modal-body">
    <h4 class="m-t-0 text-center m-b-25">Escolha a posição onde pretende iniciar a impressão na folha A4.</h4>
    @if(Setting::get('shipment_label_a4') == '8')
    <div class="text-center">
        <div class="lblA4-preview">
           <div class="lblA4-block lblA4-8-block active" data-start="1">1</div>
           <div class="lblA4-block lblA4-8-block" data-start="2">2</div>
           <div class="lblA4-block lblA4-8-block" data-start="3">3</div>
           <div class="lblA4-block lblA4-8-block" data-start="4">4</div>
           <div class="lblA4-block lblA4-8-block" data-start="5">5</div>
           <div class="lblA4-block lblA4-8-block" data-start="6">6</div>
           <div class="lblA4-block lblA4-8-block" data-start="7">7</div>
           <div class="lblA4-block lblA4-8-block" data-start="8">8</div>
            <div class="clearfix"></div>
        </div>
    </div>
    @else
        <div class="text-center">
            <div class="lblA4-preview">
                <div class="lblA4-block lblA4-4-block active" data-start="1">1</div>
                <div class="lblA4-block lblA4-4-block" data-start="2">2</div>
                <div class="lblA4-block lblA4-4-block" data-start="3">3</div>
                <div class="lblA4-block lblA4-4-block" data-start="4">4</div>
                <div class="clearfix"></div>
            </div>
        </div>
    @endif
</div>
<div class="modal-footer">
    @foreach($ids as $key => $value)
        <input type="hidden" name="id[]" value="{{ $value }}">
    @endforeach
    {{ Form::hidden('label_start', 1) }}
    {{ Form::hidden('label_format', 'A4') }}
    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-primary"><i class="fas fa-print"></i> Imprimir</button>
</div>
{{ Form::close() }}
<style>
    .lblA4-preview {
        width: 222px;
        height: 280px;
        box-shadow: 0 0 7px #ccc;
        border: 1px solid #ccc;
        display: inline-block;
    }

    .lblA4-block {
        float: left;
        border: 1px dashed #ddd;
        font-size: 30px;
        text-align: center;
        cursor: pointer;
        color: #777;
    }

    .lblA4-block.lblA4-8-block {
        width: 110px;
        height: 70px;
        padding: 15px !important;
    }

    .lblA4-block.lblA4-4-block {
        width: 110px;
        height: 139px;
        padding: 54px 25px;
    }

    .lblA4-block.lblA4-8-block.active{
        padding: 13px !important;
    }

    .lblA4-block.lblA4-4-block.active{
        padding: 52px 25px;
    }

    .lblA4-block.active,
    .lblA4-block.active:hover {
        background: #53baff;
        border: 3px solid #0853a2;
        color: #0853a2;
    }

    .lblA4-block:hover {
        background: #ccc;
        color: #333;
    }
</style>
<script>
    $('.modal .lblA4-block').on('click', function () {
        var start = $(this).data('start');
        $('[name="label_start"]').val(start);
        $('.lblA4-block').removeClass('active');
        $(this).addClass('active');
    })

    $('.printerA4 [type="submit"]').on('click', function (e) {
        e.preventDefault();
        $(this).closest('form').submit();
        $('#modal-remote-xs').modal('hide');
    })
</script>