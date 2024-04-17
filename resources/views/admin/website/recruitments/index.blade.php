@section('title')
    Candidaturas Emprego
@stop

@section('content-header')
    Candidaturas Emprego
@stop

@section('breadcrumb')
<li class="active">Candidaturas Emprego</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                        <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                    </button>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li>
                            <strong>Área</strong><br/>
                            {{ Form::select('area', array('' => 'Todas') + trans('admin/recruitment.areas'), Request::has('area') ? Request::get('area') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>Género</strong><br/>
                            {{ Form::select('gender', array('' => 'Todos', 'm' => 'Masculino', 'f' => 'Feminino'), Request::has('gender') ? Request::get('gender') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>Cidade</strong> <br/>
                            {{ Form::select('city', array('' => 'Qualquer') + $cities, Request::has('driving_licence') ? Request::get('driving_licence') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>Dispon.</strong><br/>
                            {{ Form::select('availability', array('' => 'Qualquer') + trans('admin/recruitment.availability'), Request::has('availability') ? Request::get('availability') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>Experiência</strong> <br/>
                            {{ Form::select('has_experience', array('' => 'Qualquer', '1' => 'Com Experiência', '0' => 'Sem Experiência'), Request::has('has_experience') ? Request::get('has_experience') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>Situação Prof.</strong> <br/>
                            {{ Form::select('profissional_situation', array('' => 'Qualquer', '1' => 'Empregado', '0' => 'Desempregado'), Request::has('profissional_situation') ? Request::get('profissional_situation') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>Habilitações</strong> <br/>
                            {{ Form::select('qualifications', array('' => 'Qualquer')+ trans('admin/recruitment.qualifications'), Request::has('qualifications') ? Request::get('qualifications') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>Carta</strong> <br/>
                            {{ Form::select('driving_licence', array('' => 'Qualquer') + trans('admin/recruitment.driving-license'), Request::has('driving_licence') ? Request::get('driving_licence') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                    </ul>
                </div>
                
                <div class="table-responsive">
                    <table id="datatable" class="table table-condensed table-striped table-dashed table-hover">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th>Área e Cargo</th>
                                <th>Candidato</th>
                                <th>Experiência</th>
                                <th class="w-70px">Criado em</th>
                                <th class="w-20px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.website.recruitments.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
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
        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'area', name: 'area'},
                {data: 'name', name: 'name'},
                {data: 'experience', name: 'experience', orderable: false, searchable: false},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'formation_area', name: 'formation_area', visible: false},
                {data: 'company', name: 'company', visible: false},
                {data: 'company_role', name: 'company_role', visible: false},
            ],
            ajax: {
                url: "{{ route('admin.website.recruitments.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.area = $('select[name=area]').val();
                    d.gender = $('select[name=gender]').val();
                    d.city = $('select[name=city]').val();
                    d.availability = $('select[name=availability]').val();
                    d.has_experience = $('select[name=has_experience]').val();
                    d.professional_situation = $('select[name=professional_situation]').val();
                    d.qualifications = $('select[name=qualifications]').val();
                    d.driving_licence = $('select[name=driving_licence]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });

</script>
@stop