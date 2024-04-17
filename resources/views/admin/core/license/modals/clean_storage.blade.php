<div class="modal fade" id="files-storage" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('route' => array('admin.core.license.storage.clean'))) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Limpar dados em histórico</h4>
            </div>
            <div class="modal-body">
                <h4 class="m-t-0">Que dados em histórico pretende limpar?</h4>
                <hr class="m-t-5 m-b-10"/>
                <div class="row">
                    @foreach($storageDirectories as $filename => $file)
                    <?php
                        $checked = in_array($filename, ['sessions', 'invoices']) ? false : true;
                    ?>
                    <div class="col-sm-3">
                        <div class="checkbox m-t-0">
                            <label style="padding-left: 0">
                                {{ Form::checkbox('folders[]', $file['filepath'], $checked) }}
                                {{ ucfirst($filename) }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-close-clean-storage">Fechar</button>
                <button type="submit" class="btn btn-primary">Limpar Dados</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>