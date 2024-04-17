@section('title')
    Notícias e Publicações
@stop

@section('content-header')
    Notícias e Publicações
@stop

@section('breadcrumb')
    <li class="active">Notícias e Publicações</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-posts">
                        <li>
                            <a href="{{ route('admin.website.blog.posts.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Novo
                            </a>
                        </li>
                    </ul>
                    <div class="table-responsive">
                        <table id="datatable-posts" class="table table-striped table-dashed table-hover table-condensed">
                            <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-1"></th>
                                <th>Artigo</th>
                                <th class="w-60px">Data</th>
                                <th class="w-1">Partilhar</th>
                                <th class="w-1">
                                    <span data-toggle="tooltip" title="Artigo em destaque">
                                        <i class="fas fa-star"></i>
                                    </span>
                                </th>
                                <th class="w-1">
                                    <span data-toggle="tooltip" title="Artigo publicado">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                </th>
                                <th class="w-70px">Criado em</th>
                                <th class="w-60px">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="selected-rows-action hide">
                        {{ Form::open(array('route' => 'admin.website.blog.posts.selected.destroy')) }}
                        <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fa fa-trash"></i> Apagar Selecionados</button>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        var oTable;
        $(document).ready(function () {

            oTable = $('#datatable-posts').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'photo', name: 'photo', orderable: false, searchable: false},
                    {data: 'title', name: 'title'},
                    {data: 'date', name: 'date'},
                    {data: 'share', name: 'share', orderable: false, searchable: false},
                    {data: 'is_highlight', name: 'is_highlight', class:'text-center'},
                    {data: 'is_published', name: 'is_published', class:'text-center'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[4, "asc"]],
                ajax: {
                    url: "{{ route('admin.website.blog.posts.datatable') }}",
                    type: "POST",
                    data: function (d) {},
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('[data-target="#datatable-posts"] .filter-datatable').on('change', function (e) {
                oTable.draw();
                e.preventDefault();
            });
        });

    </script>
@stop