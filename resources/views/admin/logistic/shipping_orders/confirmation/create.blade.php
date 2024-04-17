<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Picking Out')</h4>
</div>
<div class="modal-body">
    <div class="form-group form-group-lg">
        {{ Form::label('code', __('Indique a ordem a iniciar Picking Out'), ['class' => 'fs-15 m-b-10']) }}
        <div class="input-group input-group-lg">
            <div class="input-group-addon">
                <img style="height: 30px;" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNDgwIDQ4MCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDgwIDQ4MDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0OEgxNkM3LjE2OCw0OCwwLDU1LjE2OCwwLDY0djY0YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY4MGg0OGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNTUuMTY4LDg4LjgzMiw0OCw4MCw0OHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZD0iTTQ2NCwzMzZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NDhoLTQ4Yy04LjgzMiwwLTE2LDcuMTY4LTE2LDE2YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZoNjRjOC44MzIsMCwxNi03LjE2OCwxNi0xNnYtNjQNCgkJCUM0ODAsMzQzLjE2OCw0NzIuODMyLDMzNiw0NjQsMzM2eiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cGF0aCBkPSJNNDY0LDQ4aC02NGMtOC44MzIsMC0xNiw3LjE2OC0xNiwxNmMwLDguODMyLDcuMTY4LDE2LDE2LDE2aDQ4djQ4YzAsOC44MzIsNy4xNjgsMTYsMTYsMTZjOC44MzIsMCwxNi03LjE2OCwxNi0xNlY2NA0KCQkJQzQ4MCw1NS4xNjgsNDcyLjgzMiw0OCw0NjQsNDh6Ii8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik04MCw0MDBIMzJ2LTQ4YzAtOC44MzItNy4xNjgtMTYtMTYtMTZjLTguODMyLDAtMTYsNy4xNjgtMTYsMTZ2NjRjMCw4LjgzMiw3LjE2OCwxNiwxNiwxNmg2NGM4LjgzMiwwLDE2LTcuMTY4LDE2LTE2DQoJCQlDOTYsNDA3LjE2OCw4OC44MzIsNDAwLDgwLDQwMHoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iNjQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxOTIiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIyNTYiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzMjAiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjE5MiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIzODQiIHk9IjExMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjI1NiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KCTxnPg0KCQk8cmVjdCB4PSIxMjgiIHk9IjMzNiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIi8+DQoJPC9nPg0KPC9nPg0KPGc+DQoJPGc+DQoJCTxyZWN0IHg9IjE5MiIgeT0iMzM2IiB3aWR0aD0iMzIiIGhlaWdodD0iMzIiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCgk8Zz4NCgkJPHJlY3QgeD0iMzIwIiB5PSIzMzYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K" />
            </div>
            {{ Form::text('code', '', ['class' => 'form-control', 'placeholder' => __('N.º Ordem ou Código de envio...'), 'required']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="button" class="btn btn-primary btn-submit" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A iniciar picking...">@trans('Iniciar Picking Out')</button>
</div>

<style>
    .input-group-lg>.form-control, .input-group-lg>.input-group-addon, .input-group-lg>.input-group-btn>.btn {
        border-radius: 0;
    }
</style>

<script>

    $(document).ready(function (e) {
       $('#modal-remote-xs input[name=code]').focus()
    })

    $('#modal-remote-xs input[name=code]').keyup(function(e){
        if(e.keyCode == 13) {
            var code = $(this).val();
            if(code.length >= 9) {
                openPicking();
            }
        }
    });

    $('#modal-remote-xs .btn-submit').on('click', function(e){
        e.preventDefault();
        openPicking();
    });

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    function openPicking() {

        var code = $('#modal-remote-xs input[name=code]').val()

        $('#modal-remote-xs .bloading').show();
        $('.btn-submit').button('loading')
        $.get('{{ route('admin.logistic.shipping-orders.confirmation.create') }}', {code:code}, function(data){
            if(data.result) {
                $('#modal-remote-xs').modal('hide');
                $('#modal-remote-lg').modal('show');
                $('#modal-remote-lg .modal-content').html(data.html);
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function () {
            Growl.error500();
        }).always(function(){
            $('.btn-submit').button('reset')
        })
    }
</script>




