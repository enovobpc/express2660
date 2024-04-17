@if(!@$saft->issued)
    <a href="{{ route('admin.invoices.saft.email', ['year'=> @$saft->year, 'month'=> @$saft->month, 'company' => $saft->company_id]) }}"
       data-toggle="modal"
       data-target="#modal-remote-xs"
       class="btn btn-block btn-xs btn-success">
        <i class="fas fa-check"></i> Emitir SAF-T
    </a>
@else
    <div class="btn-group" role="group">
        <a href="{{ route('admin.invoices.saft.download', ['year'=> @$saft->year, 'month'=> @$saft->month, 'company' => $saft->company_id]) }}"
           class="btn btn-xs btn-default">
            <i class="fas fa-download"></i> Download
        </a>
        <div class="btn-group" role="group">
            <a href="{{ route('admin.invoices.saft.download', ['year'=> @$saft->year, 'month'=> @$saft->month, 'company' => $saft->company_id]) }}"
               class="btn btn-xs btn-default dropdown-toggle"
               data-toggle="dropdown"
               aria-haspopup="true"
               aria-expanded="false">
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.invoices.saft.email', ['year'=> @$saft->year, 'month'=> @$saft->month, 'company' => $saft->company_id]) }}"
                       data-toggle="modal"
                       data-target="#modal-remote-xs">
                        <i class="fas fa-envelope"></i> Enviar por e-mail
                    </a>
                </li>
            </ul>
        </div>
    </div>
@endif
