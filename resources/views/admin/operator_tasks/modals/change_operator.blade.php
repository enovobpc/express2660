{{ Form::model($task, $formOptions) }}
<div class="modal-header">
    <button class="close" data-dismiss="modal" type="button">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Alterar Operador</h4>
</div>
<div class="modal-body">
    <div style="overflow: hidden; margin: -15px -15px 15px -15px">
        <div class="mtop-header">
            <div class="row row-5">
                <div class="col-sm-4">
                    <h4>Operadores</h4>
                </div>
                
                <ul class="list-inline pull-right">
                    <li style="width: 195px; padding: 0">
                        <div class="input-group input-group-sm" style="margin-bottom: -13px;">
                            <div class="input-group-addon">
                                <i class="fas fa-search"></i>
                            </div>
                            {{ Form::text('search', null, ['class' => 'form-control input-sm']) }}
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    

    <div class="row row-5">
        <div class="m-t-64 m-b-64 text-center" id="alert-no-operators" style="display: {{ !$operators->isEmpty() ? 'none' : '' }}">
            <h3 class="text-muted">
                <i class="fas fa-user fs-40"></i><br />
                Sem Operadores
            </h3>
            <p class="text-muted">
                Nenhum operador foi encontrado.
            </p>
        </div>

        @foreach ($operators as $operator)
            <div class="col-sm-3" style="margin-top: 10px">
                <button class="btn w-100 btn-operator {{ $operator->id == $task->operator_id ? 'btn-primary' : '' }}" data-value="{{ $operator->id }}" type="button">
                    {{ $operator->name }}
                </button>
            </div>
        @endforeach
    </div>
</div>
<div class="modal-footer">
    {{ Form::hidden('operator_id', $task->operator_id) }}
    <button class="btn btn-default" data-dismiss="modal" type="button">Fechar</button>
    <button class="btn btn-primary" type="submit">Confirmar</button>
</div>
{{ Form::close() }}

<script>
    $('#modal-change-operator .btn-operator').on('click', function() {
        var $this = $(this);
        var wasSelected = $this.hasClass('btn-primary');

        $('#modal-change-operator .btn-operator').removeClass('btn-primary');

        if (wasSelected) {
            $this.removeClass('btn-primary');
            $('#modal-change-operator [name="operator_id"]').val("");
        } else {
            $this.addClass('btn-primary');
            $('#modal-change-operator [name="operator_id"]').val($this.data('value'));
        }
    });

    $('#modal-change-operator [name="search"]').on('keyup', function() {
        var $this = $(this);
        var searchValue = $this.val().toLowerCase().trim();

        $('#alert-no-operators').hide();
        $('#modal-change-operator .btn-operator').parent().show();

        if (!searchValue) {
            return;
        }

        var countHidden = 0;
        $('#modal-change-operator .btn-operator').each(function(i, el) {
            var $el = $(el);
            if (!$el.html().toLowerCase().includes(searchValue)) {
                $el.parent().hide();
                countHidden++;
            }
        });

        // No operator found
        if (countHidden == {{ $operators->count() }}) {
            $('#alert-no-operators').show();
        }
    });
</script>

<style>
    .mtop-header {
        background: #f9f9f9;
        border-bottom: 1px solid #ccc;
        margin: 0 0 2px 0;
        padding: 10px 20px;
        box-shadow: 0 0px 3px #ccc;
    }
</style>