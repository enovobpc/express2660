<div class="action-buttons text-center">
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ trans('account/global.word.options') }}  <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('account.logistic.cart.order.show', $row->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-lg"
                   class="text-blue">
                    <i class="fas fa-fw fa-search"></i> {{ trans('account/global.word.details') }}
                </a>
            </li>
            @if(($row->status == 'pending' && !Auth::guard('customer')->user()->is_commercial ) || ($row->status == 'PENDING' && !Auth::guard('customer')->user()->is_commercial))
                <li>
                    <a href="{{ route('account.logistic.cart.order.destroy', $row->id) }}" data-method="delete" data-confirm="Confirma a remoção do registo selecionado?">
                        <i class="fas fa-fw fa-trash-alt" style="color:red"></i> Eliminar
                    </a>
                </li>
                
            @endif
        </ul>
    </div>
</div>