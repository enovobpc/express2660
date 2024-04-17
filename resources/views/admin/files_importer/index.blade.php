@section('title')
    Importador de Ficheiros
@stop

@section('content-header')
    Importador de Ficheiros
@stop

@section('breadcrumb')
    <li class="active">@trans('Importador de Ficheiros')</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-file-importer" data-toggle="tab">@trans('Importar Ficheiro')</a></li>
                    <li><a href="#tab-models" data-toggle="tab">@trans('Modelos de Importação')</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-file-importer">
                        @include('admin.files_importer.partials.importer')
                    </div>
                    <div class="tab-pane" id="tab-models">
                        @include('admin.files_importer.partials.models')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $('.datepicker').datepicker(Init.datepicker());
        $(document).ready(function () {

            var oTable2 = $('#datatable-models').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'name', name: 'name'},
                    {data: 'type', name: 'type'},
                    {data: 'customer_code', name: 'customer_code', class:'text-center', orderable: false, searchable: false},
                    {data: 'available_customers', name: 'available_customers', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                ajax: {
                    url: "{{ route('admin.importer.models.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.type = $('[name="model_type"]').val();
                    },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('.filter-datatable').on('change', function (e) {
                oTable2.draw();
                e.preventDefault();
            });
        });

        $(document).find('.item-row .preview-checkbox').each(function(){
            $(this).trigger('click');
            $(this).trigger('click');
            updateCheckboxes($(this));
        });

        $(document).on('change', '.preview-checkbox', function(){
            updateCheckboxes($(this));
        })

        function updateCheckboxes(thisObj) {
            var field = thisObj.data('field');
            var $targetInput = $('[name="'+field+'"]');

            newData = [];
            $('[data-field="'+field+'"]').each(function(){
                if($(this).is(':checked')) {
                    newData.push($(this).data('row-id'));
                }
            });

            $targetInput.val(newData.join(','));
        }

        $(document).on('change', '[name="import_model"]', function(){
            var type = $(this).find(':selected').data('type');

            if(type == 'shipments_fast' || type == 'shipments_logistic') {
                type = 'shipments';
            }

            $('[data-type]').hide();

            $('[data-type] .is-required').each(function(){
                $(this).find('input, select').prop('required', false)
            })

            $('[data-type="'+type+'"]').show();
            $('[data-type="'+type+'"] .is-required').each(function(){
                $(this).find('input, select').prop('required', true)
            })

            /*if(type == 'shipments') {
                $('[name="provider_id"]').prop('required', false);
            } else {
                $('[name="provider_id"]').prop('required', false);
            }*/
        })

        @if(!$hasErrors && !empty(@$previewRows))
            $(document).ready(function(){
                $('[name="import_model"]').trigger('change');
            })
        @endif
    </script>
@stop

@section('styles')
    <style>
        .table-preview tr td,
        .table-preview tr th {
            white-space: nowrap;
        }
    </style>
@stop