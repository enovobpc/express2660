@section('title')
    Registo de Erros
@stop

@section('content-header')
    Registo de Erros
@stop

@section('breadcrumb')
    <li class="active">Registo de Erros</li>
@stop

@section('content')
    <div class="row box-settings">
        @if(!$logFiles)
            <div class="col-xs-12">
                <div class="bx">
                    <div class="h-100"></div>
                    <div class="box-boy no-padding">
                        <div class="text-muted text-center m-t-50 m-b-50" style="color: #999;">
                            <i class="fas fa-thumbs-up fs-40"></i>
                            <h3>Não há registo de erros para apresentar.</h3>
                        </div>
                    </div>
                    <div class="h-100"></div>
                </div>
            </div>
        @else
            <div class="col-md-3 col-lg-2 p-r-0">
                <div class="box m-b-5">
                    <div class="box-body no-padding list-logs">
                        <ul class="nav nav-pills nav-stacked">
                            @foreach($logFiles as $key => $logFile)
                                <?php $fileSlug = base64_encode($logFile->getFilename()); ?>
                                <li class="{{ ((!Request::get('file') && $key==0) || $fileSlug == Request::get('file')) ? 'active' : '' }}">
                                    <a href="{{ route('admin.logs.errors.index', ['file' => $fileSlug]) }}">
                                        {{ $logFile->getFilename() }}
                                        <span class="badge pull-right">{{ @$totals[$logFile->getFilename()]['all'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <a href="{{ route('admin.logs.errors.destroy.all') }}" class="btn btn-sm btn-default btn-block"
                   data-method="post"
                   data-confirm="Confirma a remoção de todos os ficheiros de registo?">
                    <i class="fas fa-trash-alt"></i> Eliminar todos os registos
                </a>
            </div>
            <div class="col-md-9 col-lg-10">
                <div class="tab-content">
                    <div class="active tab-pane">
                        <div class="box no-border m-b-10">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <h4 class="bold text-uppercase m-t-5 m-b-0">
                                            <i class="fas fa-file"></i> {{ base64_decode($slug) }}
                                            &bull; <small>{{ human_filesize(filesize(storage_path('logs/' . base64_decode($slug) ))) }}</small>
                                        </h4>
                                    </div>
                                    <div class="col-sm-4">
                                        <ul class="list-inline m-0 pull-right">
                                            <li>
                                                <a href="{{ route('admin.logs.errors.download', $slug) }}" class="btn btn-sm btn-default">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.logs.errors.destroy', $slug) }}" class="btn btn-sm btn-danger"
                                                   data-method="delete"
                                                   data-confirm="Confirma a remoção do ficheiro selecionado?" class="text-red">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="active tab-pane">
                        <div class="box no-border">
                            <div class="box-body">
                                @if($logs === null)
                                    <div class="text-muted text-center m-t-50 m-b-50">
                                        <i class="fas fa-file bigger-300"></i>
                                        <h4>
                                            O ficheiro é maior que 50Mb.
                                            <br/>
                                            A consulta só está disponível por download.
                                        </h4>
                                        <a href="{{ route('admin.logs.errors.download', $slug) }}" class="btn btn-sm btn-default m-t-10">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                @else
                                    <ul class="datatable-filters list-inline hide pull-left" data-target="#table-log" style="margin-bottom: -27px; margin-top: 5px;">
                                        @foreach(trans('admin/log_viewer.levels') as $level => $levelName)
                                            @if(@$totals[base64_decode($slug)][$level])
                                                <li class="filter-levels" data-target="{{ $level }}" style="cursor: pointer;">
                                                    <span class="level-tab text-uppercase bold" style="color: {{ trans('admin/log_viewer.colors.' . $level) }}">
                                                        <i class="{{ trans('admin/log_viewer.icons.' . $level) }}"></i> {{ $levelName }}
                                                        <span class="badge" style="margin-top: -5px; display: inline-block; background: {{ trans('admin/log_viewer.colors.' . $level) }}">{{ @$totals[base64_decode($slug)][$level] }}</span>
                                                    </span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                <div class="table-responsive">
                                    <table id="table-log" class="table table-striped table-condensed table-dashed table-hover">
                                        <thead>
                                            <tr>
                                                <th class="w-1">Env</th>
                                                <th class="w-1">Nível</th>
                                                <th class="w-1">Hora</th>
                                                <th>Erro</th>
                                                <th class="w-1"></th>
                                                <th class="hide"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($logs as $key => $log)
                                            <tr>
                                                <td class="text">
                                                    @if($log['context'] == 'local')
                                                        <span class="label bg-purple">{{ ucwords($log['context']) }}</span>
                                                    @elseif($log['context'] == 'production')
                                                        <span class="label bg-green">{{ ucwords($log['context']) }}</span>
                                                    @else
                                                        <span class="label bg-blue">{{ ucwords($log['context']) }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="label" style="background: {{ $log['level_color'] }}">
                                                        <i class="{{ $log['level_icon'] }}" aria-hidden="true"></i>
                                                        {{ trans('admin/log_viewer.levels.' . $log['level']) }}
                                                    </span>
                                                </td>
                                                <td class="date">
                                                    {{{ date('H:i:s', strtotime($log['date'])) }}}
                                                </td>
                                                <td class="text">
                                                    <span class="text-header">{{{ $log['text'] }}}</span>
                                                    @if(isset($log['in_file']))
                                                        <div><i class="text-muted">{{{ str_replace(':999999', '', $log['in_file']) }}}</i></div>
                                                    @endif

                                                    @if($log['stack'])
                                                        <div><small class="stack" id="stack{{{$key}}}" style="display: none; white-space: pre-wrap;">{{{ trim($log['stack']) }}}</small></div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($log['stack'])
                                                        <a class="btn btn-default btn-xs btn-expand" data-display="#stack{{ $key }}">
                                                            <i class="fas fa-plus"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="hide">{{ $log['level'] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
    </div>
@stop

@section('styles')
    <style>
        .list-logs{
            max-height: 305px;
            overflow-y: scroll;
        }

        .box-title {
            font-size: 15px !important;
            font-weight: bold;
            text-transform: uppercase;
        }

        .box-header {
            background: #ddd;
            border-bottom: 1px solid #ccc;
            border-radius: 3px 3px 0 0;
        }

        .text-header {
            color: #000;
            font-size: 14px;
            line-height: 17px;
            display: block;
            word-break: break-word;
        }

        .level-tab {
            border: 1px solid #fff;
            padding: 4px 4px;
            border-radius: 3px;
        }

        .level-tab:hover {
            border: 1px solid #eee;
        }

        .level-tab.active {
            border: 1px solid #ccc;
            background: #f9f9f9
        }
    </style>
@stop

@section('scripts')
    <script>
        var oTable;

        $(document).on('click', '.btn-expand', function(){
            $($(this).data('display')).toggle();
        });

        $(document).ready(function(){
            oTable = $('#table-log').DataTable({
                "order": [2, 'desc'],
                "stateSave": true,
                "serverSide": false,
                "stateSaveCallback": function (settings, data) {
                    window.localStorage.setItem("datatable", JSON.stringify(data));

                    filterVal = data.columns[5].search.search;

                    if(filterVal == '') {
                        filterVal = 'all';
                    }

                    $('.level-tab').removeClass('active');
                    $('[data-target="'+filterVal+'"]').find('.level-tab').addClass('active');
                },
                "stateLoadCallback": function (settings) {
                    var data = JSON.parse(window.localStorage.getItem("datatable"));
                    if (data) data.start = 0;
                    return data;
                }
            })
        });

        $(document).on('click', '.filter-levels', function(){
            var target = $(this).data('target');

            if(target != 'all') {
                oTable.column(5)
                    .search(target)
                    .draw();
            } else {
                oTable.search('')
                    .columns().search('')
                    .draw();
            }
        })
    </script>
@stop