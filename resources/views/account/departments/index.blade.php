@section('title')
    Os meus Departamentos -
@stop

@section('account-content')
<h4 class="m-t-0 m-b-20">{{ trans('account/global.word.my-department') }}</h4>
<ul class="datatable-filters list-inline hide pull-left m-0" style="margin-left: -5px">
    <li>
        <a href="{{ route('account.departments.create') }}" class="btn btn-sm btn-success pull-right m-0" data-toggle="modal" data-target="#modal-remote-lg">
            <i class="fas fa-plus"></i> {{ trans('account/global.word.new-department') }}
        </a>
    </li>
</ul>
<div class="table-responsive w-100">
    <table id="datatable" class="table table-condensed table-hover">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th class="w-1"></th>
            <th class="w-1">{{ trans('account/global.word.code') }}</th>
            <th>{{ trans('account/global.word.department') }}</th>
            <th>{{ trans('account/global.word.contacts') }}</th>
            <th class="w-1">{{ trans('account/global.word.actions') }}</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            dom: "<'row row-0'<'col-sm-8 datatable-filters-area'><'col-sm-4'f><'col-sm-12 datatable-filters-area-extended'>>" +
                    "<'row row-0'<'col-sm-12'tr>>" +
                    "<'row row-0'<'col-sm-7'li><'col-sm-5'p>>",
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'photo', name: 'photo', orderable: false, searchable: false},
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'contacts', name: 'contacts', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'mobile', name: 'mobile', visible: false},
                {data: 'phone', name: 'phone', visible: false},
                {data: 'email', name: 'email', visible: false},
            ],
            ajax: {
                url: "{{ route('account.departments.datatable') }}",
                type: "POST"
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });
</script>
@stop